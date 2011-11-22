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
    $stmt = $conn->prepare('SELECT `software_id` FROM '.USERS_TABLE.' '.
                           'WHERE `user_id`=:uid LIMIT 1');
    $stmt->bindValue(":uid", $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row['software_id'];
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
    $user_id = (int) $user_id;

    global $conn;
    $stmt = $conn->prepare('SELECT `user_name` FROM '.USERS_TABLE.' '.
                           'WHERE `user_id` = :uid '.
                            'AND `user_id` != '.USER_ID.' LIMIT 1');
    $stmt->bindValue(":uid", (int) $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $challengedUser = $row['user_name'];
    if ($row !== false and $row !== null) {
        $stmt = $conn->prepare('SELECT `id` FROM '.GAMES_TABLE.' '.
                               'WHERE `whiteUserID` = '.USER_ID.' '.
                               'AND `blackUserID` :uid '.
                               'AND `outcome` = -1 LIMIT 1');
        $stmt->bindValue(":uid", (int) $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row !== false and $row !== null) {
            $t->assign('alreadyChallengedPlayer', $challengedUser);
            $t->assign('alreadyChallengedGameID', $row['id']);
            return "ERROR:You have already challenged this player. ".
                   "This Game has the gameID ".$row['id'].".";
        } else {
            $stmt = $conn->prepare('SELECT `user_id`, `software_id` FROM '.
                                    USERS_TABLE.' '.'WHERE '.
                                    '`user_id` = :uid1 OR `user_id`=:uid2 '.
                                    'LIMIT 2');
            $stmt->bindValue(":uid1", (int) USER_ID, PDO::PARAM_INT);
            $stmt->bindValue(":uid2", (int) $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result[0]['user_id'] == USER_ID) {
                $whitePlayerSoftwareID = $result[0]['software_id'];
                $blackPlayerSoftwareID = $result[1]['software_id'];
            } else {
                $blackPlayerSoftwareID = $result[0]['software_id'];
                $whitePlayerSoftwareID = $result[1]['software_id'];
            }

            $stmt = $conn->prepare('INSERT INTO `'.GAMES_TABLE.'` '.
                '(`whiteUserID`, `blackUserID`, `whitePlayerSoftwareID`, '.
                '`blackPlayerSoftwareID`, `moveList`) VALUES '.
                '(:uid1, :uid2, :usid1, :usid2, "")');
            $stmt->bindValue(":uid1", USER_ID, PDO::PARAM_INT);
            $stmt->bindValue(":uid2", $user_id, PDO::PARAM_INT);
            $stmt->bindValue(":usid1", $whitePlayerSoftwareID);
            $stmt->bindValue(":usid2", $blackPlayerSoftwareID);
            $stmt->execute();

            $stmt = $conn->prepare('SELECT `id` FROM '.GAMES_TABLE.' WHERE '.
                '`whiteUserID` = :uid1 AND `blackUserID` = :uid2 AND '.
                '`whitePlayerSoftwareID` = :usid1 AND '.
                '`blackPlayerSoftwareID` = :usid2 AND `moveList` = "" LIMIT 1');
            $stmt->bindValue(":uid1", USER_ID, PDO::PARAM_INT);
            $stmt->bindValue(":uid2", $user_id, PDO::PARAM_INT);
            $stmt->bindValue(":usid1", $whitePlayerSoftwareID);
            $stmt->bindValue(":usid2", $blackPlayerSoftwareID);
            $stmt->execute();
            $row    = $stmt->fetch(PDO::FETCH_ASSOC);
            $gameID = $row['id'];

            $t->assign('startedGamePlayerID', $user_id);
            $t->assign('startedGamePlayerUsername', $challengedUser);
            $t->assign('startedGameID', $gameID);
            return $gameID;
        }
    } else {
        $t->assign('incorrectID', true);
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
        $cond = '';
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
