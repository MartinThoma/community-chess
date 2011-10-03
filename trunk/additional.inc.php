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
    $c   = "WHERE `user_id`='$user_id'";
    $row = selectFromTable(array('software_id'), SOFTWARE_USER_TABLE, $c);
    return $row['software_id'];
}

/** This function checks if a row for the user in SOFTWARE_USER_TABLE does exist
 *
 * @param int $user_id the id of the user who gets the table check
 *
 * @return int always 0
 */
function checkSoftwareTableEntry($user_id)
{
    $cond = 'WHERE `user_id` = '.$user_id;
    $row  = selectFromTable(array('software_id'), SOFTWARE_USER_TABLE, $cond);
    if ($row == false) {
        $keyValuePairs = array();
        $keyValuePairs['user_id']     = $user_id;
        $keyValuePairs['software_id'] = 0;
        insertIntoTable($keyValuePairs, SOFTWARE_USER_TABLE);
    }
}

/** This function makes user-challenges
 * 
 * @param int    $user_id the challenged user_id
 * @param object $t       template-object
 *
 * @return string message with the result
 */
function challengeUser($user_id, $t)
{
    $id             = (int) $user_id;
    $cond           = 'WHERE `user_id` = '.$id.' AND `user_id` != '.USER_ID;
    $row            = selectFromTable(array(USER_NAME_COLUMN), USERS_TABLE, $cond);
    $challengedUser = $row[USER_NAME_COLUMN];
    if ($row !== false) {
        $cond = 'WHERE `whiteUserID` = '.USER_ID." AND `blackUserID`=$id ";
        $cond.= 'AND `outcome` = -1';
        $row  = selectFromTable(array('id'), GAMES_TABLE, $cond);
        if ($row !== false) {
            $t->assign('alreadyChallengedPlayer', $challengedUser);
            $t->assign('alreadyChallengedGameID', $row['id']);
            return "ERROR:You have already challenged this player. ".
                   "This Game has the gameID ".$row['id'].".";
        } else {
            // Maybe one of the rows doesn't exist?
            checkSoftwareTableEntry(USER_ID);
            checkSoftwareTableEntry($id);

            $cond   = "WHERE `user_id` = ".USER_ID." OR `user_id`=$id";      
            $rows   = array('user_id', 'software_id');  
            $result = selectFromTable($rows, SOFTWARE_USER_TABLE, $cond, 2);

            if ($result[0]['user_id'] == USER_ID) {
                $whitePlayerSoftwareID = $result[0]['software_id'];
                $blackPlayerSoftwareID = $result[1]['software_id'];
            } else {
                $blackPlayerSoftwareID = $result[0]['software_id'];
                $whitePlayerSoftwareID = $result[1]['software_id'];
            }
            $keyValuePairs = array('whiteUserID'=>USER_ID, 
                               'blackUserID'=>$id,
                               'whitePlayerSoftwareID'=>$whitePlayerSoftwareID,
                               'blackPlayerSoftwareID'=>$blackPlayerSoftwareID,
                               'moveList'=>'');

            $gameID = insertIntoTable($keyValuePairs, GAMES_TABLE);

            $t->assign('startedGamePlayerID', $id);
            $t->assign('startedGamePlayerUsername', $challengedUser);
            $t->assign('startedGameID', $gameID);
            return "New game started with gameID $gameID.";
        }
    } else {
        $t->assign('incorrectID', true);
    }
}

/** Almost like the PageRank algorithm
 *  $inlinkScore isn't devided by outgoing links of the loser
 * 
 * @param array $winnerArray    array of arrays winner => loser
 * @param array $loserArray     array of arrays loser  => winner
 * @param int   $repeatPR       how often should the PR-algorithm be applied?
 * @param float $dampingFactor  damping factor of PR
 * @param float $initialisation doesn't really matter
 *
 * @return array playerID=>Ranking
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

/** Calculate the PageRank for all participants in the given tournament.
 * 
 * @param array $tournamentID int The id of a tournament which should get new values
 *
 * @return boolean true if tournament existed, false if not
 */
function triggerPageRank($tournamentID)
{
    $rows = array('user_id', 'whiteUserID', 'blackUserID', 'outcome');
    $cond = 'WHERE `tournamentID` = '.$tournamentID." `outcome` >= 0";
    $rows = selectFromTable($rows, GAMES_TABLE, $cond, 100);
    $winners = array();
    $losers  = array();
    foreach($rows as $row){
        if ($row['outcome'] == 0) {
            $winners[$row['whiteuserID']][] = $row['blackUserID'];
            $losers[$row['blackUserID']][]  = $row['whiteuserID'];
        } else if ($row['outcome'] == 1) {
            $losers[$row['whiteuserID']][]  = $row['blackUserID'];
            $winners[$row['blackUserID']][] = $row['whiteuserID'];
        } else if ($row['outcome'] == 2) {
            $losers[$row['whiteuserID']][]  = $row['blackUserID'];
            $losers[$row['blackUserID']][]  = $row['whiteuserID'];

            $winners[$row['whiteuserID']][] = $row['blackUserID'];
            $winners[$row['blackUserID']][] = $row['whiteuserID'];
        } else {
            exit("triggerPageRank should have outcome ".$row['outcome']);
        }
    }
    return true;
}

?>
