<?php
/**
 * The following functions are needed at several places.
 *
 * PHP Version 5
 *
 * @category Web_Services
 * @package  Community-chess
 * @author   Martin Thoma <info@martin-thoma.de>
 * @license  http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version  SVN: <svn_id>
 * @link     http://code.google.com/p/community-chess/
 */

/** This function gets the Software-ID of the User
 *
 * @param int $user_id the ID of the user
 *
 * @return int
 */
function getUserSoftwareID($user_id)
{
    global $conn;
    $stmt = $conn->prepare('SELECT `softwareID` FROM '.USERS_TABLE.' '.
                           'WHERE `user_id`=:uid LIMIT 1');
    $stmt->bindValue(":uid", $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row['softwareID'];
}

/** This function makes user-challenges
 * 
 * @param int    $user_id the challenged user_id
 * @param object $t       template-object
 *
 * @return int the new game id
 */
function challengeUser($user_id, $t)
{
    global $conn;
    $stmt = $conn->prepare("CALL ChallengeUser(?,?,@startedGamePlayerUsername,@gameID,@incorrectID,@alreadyChallengedPlayer)");
    $test = USER_ID;
    $stmt->bindParam(1, $user_id);
    $stmt->bindParam(2, $test);
    $returnValue = $stmt->execute();

    if ($returnValue !== true) {
        print_r("Something went wrong with stored procedure 'ChallengeUser'.");
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $t->assign('gameID', $row['gameID']);
    $t->assign('incorrectID', $row['incorrectID']);

    if ($row['alreadyChallengedPlayer']) {
        $t->assign('alreadyChallengedPlayer', $row['startedGamePlayerUsername']);
        return "ERROR:You have already challenged this player. ".
               "This Game has the gameID ".$row['gameID'].".";
    } else if ($row['incorrectID']) {
        return "ERROR:The user-id ".$user_id."was not valid";
    } else {
        return $row['gameID'];
    }
}

/** Almost like the PageRank algorithm
 *  $inlinkScore isn't devided by outgoing links of the loser.
 *  You have to make sure that every userID is a key in winnerArray AND loserArray
 * 
 * @param array $winnerArray    array of arrays winner => loser
 * @param array $loserArray     array of arrays loser  => winner
 * @param int   $repeatPR       how often should the PR-algorithm be applied?
 * @param float $dampingFactor  damping factor of PR
 * @param float $initialisation doesn't really matter
 *
 * @return array playerID=>PageRank
 */
function pageRank($winnerArray, $loserArray, $repeatPR=20, $dampingFactor=0.85, 
                   $initialisation=1.0)
{
    $winners   = array_keys($winnerArray);
    $losers    = array_keys($loserArray);
    $playerIDs = array_unique(array_merge($winners, $losers));
    // Initialise all Players with pagerank = INITIALISATION
    $players = array();
    foreach ($playerIDs as $id) {
        $players[$id] = $initialisation;
    }
    // Calculate summand
    $summand = (1 - $dampingFactor)/count($players);


    for ($i=0; $i<$repeatPR; $i++) {
        foreach ($players as $playerID=>$playerPR) {
            // How often did this player either lose or get a draw?
            $outlinks = count($loserArray[$playerID]) + 1;
            // How often did this player win or get draw?
            $inlinkScore = 0;
            foreach ($winnerArray[$playerID] as $loserID) {
                $inlinkScore += $players[$loserID];
            }
            $players[$playerID] = $summand + 
                                  $dampingFactor * ($inlinkScore / $outlinks);
        }
    }

    return $players;
}

/** Calculate the PageRank for either all players in a tournament or all players.
 *  This function only generates the graph.
 * 
 * @param array $tournamentID int The id of a tournament which should get new values.
 *                                If this parameter is 0, the global rank should be
 *                                recalculated
 *
 * @return boolean true if tournament existed, false if not
 */
function triggerPageRank($tournamentID = 0)
{
    $rows = array('whiteUserID', 'blackUserID', 'outcome');

    // TODO: Get this into the prepared statement
    if ($tournamentID == 0) {
        $cond = 'WHERE `outcome` >= 0';
    } else {
        $cond = 'WHERE `tournamentID` = '.$tournamentID." `outcome` >= 0";
    }

    // get all UserIDs:
    global $conn;

    $stmt = $conn->prepare('SELECT `user_id` FROM '.USERS_TABLE);
    $stmt->execute();
    $userIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $conn->prepare('SELECT `whiteUserID`, `blackUserID`, `outcome` '.
                           'FROM '.GAMES_TABLE.' '.$cond);
    $stmt->execute();
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $winners = array();
    $losers  = array();
    foreach ($userIDs as $userID) {
        $winners[$userID] = array();
        $losers[$userID]  = array();
    }

    foreach ($games as $game) {
        if ($game['outcome'] == 0) {
            $winners[$game['whiteUserID']][] = $game['blackUserID'];
            $losers[$game['blackUserID']][]  = $game['whiteUserID'];
        } else if ($game['outcome'] == 1) {
            $losers[$game['whiteUserID']][]  = $game['blackUserID'];
            $winners[$game['blackUserID']][] = $game['whiteUserID'];
        } else if ($game['outcome'] == 2) {
            $losers[$game['whiteUserID']][] = $game['blackUserID'];
            $losers[$game['blackUserID']][] = $game['whiteUserID'];

            $winners[$game['whiteUserID']][] = $game['blackUserID'];
            $winners[$game['blackUserID']][] = $game['whiteUserID'];
        } else {
            exit("triggerPageRank should also have the outcome ".$game['outcome']);
        }
    }
    // Now calculate the PageRank array($userID=>$rank)
    $pageRank = pageRank($winners, $losers);

    // Recalculate `rank` in USERS_TABLE
    $pageRanks      = array_unique(array_values($pageRank));
    $rankTopageRank = array();
    $rank           = 1;
    while (count($pageRanks) > 0) {
        $rankTopageRank[$rank] = max($pageRanks);
        // remove the highes PR from the array
        $pageRanks = array_diff($pageRanks, array(max($pageRanks)));
        $rank++;
    }

    // Write the PageRank into the database
    // TODO: This should be done in one query - perhaps with a transaction?
    //$conn->beginTransaction();
    $stmt = $conn->prepare('UPDATE `'.USERS_TABLE.'` SET '.
                           'pageRank = :pageRank, '.
                           'rank = :rank '.
                           'WHERE `user_id` = :uid LIMIT 1');
    foreach ($pageRank as $userID=>$rank) {
        $stmt->bindValue(":pageRank", $rank, PDO::PARAM_INT);
        $stmt->bindValue(":rank", array_search($rank, $rankTopageRank), 
                                  PDO::PARAM_INT);
        $stmt->bindValue(":uid", $userID, PDO::PARAM_INT);
        $stmt->execute();
    }
    //$conn->commit();

    return true;
}

?>
