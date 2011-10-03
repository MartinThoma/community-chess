<?php
/**
 * create tournaments
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
require_once 'wrapper.inc.php';
if (USER_ID === false) exit("Please <a href='login.wrapper.php'>login</a>");
$t = new vemplator();
$t->assign('USER_ID', USER_ID);

if (isset($_POST['tournamentName'])) {
    $tournamentName = mysql_real_escape_string($_POST['tournamentName']);
    $description    = mysql_real_escape_string($_POST['description']);
    $password       = md5($_POST['password']);
    $closingDate    = mysql_real_escape_string($_POST['closingDate']);
    $finishedDate   = mysql_real_escape_string($_POST['finishedDate']);

    if ($tournamentName == '') {
        $t->assign('noTournamentName', true);
    } else {
        $keyValue                 = array();
        $keyValue['name']         = $tournamentName;
        $keyValue['description']  = $description;
        $keyValue['password']     = $password;
        $keyValue['closingDate']  = $closingDate;
        $keyValue['finishedDate'] = $finishedDate;
        insertIntoTable($keyValue, TOURNAMENTS_TABLE);
    }
}

if (isset($_GET['challengeUserID'])) {
    $user_id      = (int) $_GET['challengeUserID'];
    $tournamentID = (int) $_GET['tournamentID'];

    $cond           = 'WHERE `user_id` = '.$user_id.' AND `user_id` != '.USER_ID;
    $row            = selectFromTable(array(USER_NAME_COLUMN), USERS_TABLE, $cond);
    $challengedUser = $row['user_name'];
    if ($row !== false) {
        $cond  = "WHERE (`whiteUserID` = ".USER_ID." AND `blackUserID`=$user_id)";
        $cond .= " OR (`whiteUserID` = $user_id AND `blackUserID`=".USER_ID.") ";
        $cond .= "AND tournamentID=$tournamentID";
        $row   = selectFromTable(array('id'), GAMES_TABLE, $cond);
        if ($row !== false) {
            $t->assign('alreadyChallengedPlayer', $challengedUser);
            $t->assign('alreadyChallengedGameID', $row['id']);
        } else {
            // Do both players participate in tournament?
            $rows    = array('user_id', 'gamesWon', 'gamesPlayed');
            $cond    = "WHERE tournamentID=$tournamentID AND (user_id=".USER_ID;
            $cond   .= " OR `user_id`=".$user_id.")";
            $results =selectFromTable($rows, TOURNAMENT_PLAYERS_TABLE, $cond, 2);
            if (count($results)<2) {
                exit("Either you or your opponent is not part of the tournament.");
            }
            // Have both players played the same number of games?
            if ($results[0]['gamesPlayed'] != $results[1]['gamesPlayed']) {
                exit("You haven't won the same number of mathes as ".
                     "your opponent.");
            }
            // Are you both still in the tournament (no lost games)?
            if ($results[0]['gamesWon'] != $results[0]['gamesPlayed']) {
                exit("You have lost at least one game.");
            }



            $cond   = "WHERE `user_id` = ".USER_ID." OR `user_id`=$user_id";      
            $rows   = array('user_id', 'software_id');  
            $result = selectFromTable($rows, SOFTWARE_USER_TABLE, $condition, 2);

            if ($result[0]['id'] == USER_ID) {
                $whitePlayerSoftwareID = $result[0]['software_id'];
                $blackPlayerSoftwareID = $result[1]['software_id'];
            } else {
                $blackPlayerSoftwareID = $result[0]['software_id'];
                $whitePlayerSoftwareID = $result[1]['software_id'];
            }
            $keyValuePairs = array('whiteUserID'=>USER_ID, 
                               'blackUserID'=>$user_id,
                               'whitePlayerSoftwareID'=>$whitePlayerSoftwareID,
                               'blackPlayerSoftwareID'=>$blackPlayerSoftwareID);
            insertIntoTable($keyValuePairs, GAMES_TABLE);

            $t->assign('startedGameUserID', $user_id);
            $t->assign('startedGamePlayerUsername', $challengedUser);
        }
    } else {
        $t->assign('incorrectID', true);
    }
}

if (isset($_GET['enterID'])) {
    $tournamentID = (int) $_GET['enterID'];
    if (isset($_GET['password'])) {
        $pass = md5($_GET['password']);
    } else {
        $pass = md5('');
    }
    $cond   = "WHERE id=$tournamentID AND password='".$pass."' ";
    $cond  .= "AND closingDate > NOW()";
    $result = selectFromTable(array('id'), TOURNAMENTS_TABLE, $cond);
    if ($result['id'] != $tournamentID)
        exit("Wrong password or tournament is already closed.");

    $keyValue = array('tournamentID'=>$tournamentID, 'user_id'=>USER_ID);
    $id       = insertIntoTable($keyValue, TOURNAMENT_PLAYERS_TABLE);
    if ($id > 0) {
        $t->assign('joinedTournamentID', $tournamentID);
    } else {
        $t->assign('joinTournamentFailed', true);
    }
}

if (isset($_GET['deleteParticipation'])) {
    $tournamentID = (int) $_GET['deleteParticipation'];
    $cond         = "WHERE tournamentID=$tournamentID AND `user_id`=".USER_ID;
    $result       = selectFromTable(array('id'), TOURNAMENT_PLAYERS_TABLE, $cond);
    deleteFromTable(TOURNAMENT_PLAYERS_TABLE, $result['id']);
}

$cond   = "WHERE `user_id`=".USER_ID;
$result = selectFromTable(array('tournamentID'), 
                          TOURNAMENT_PLAYERS_TABLE, $cond, 100);

$myParticipations = array();
foreach ($result as $row) {
    $myParticipations[] = $row['tournamentID'];
}

if (isset($_GET['getDetails'])) {
    $id = (int) $_GET['getDetails'];
    $t->assign('detailsTournamentID', $id);

    $rows    = array();
    $rows[]  = 'id';
    $rows[]  = 'user_id';
    $rows[]  = 'tournamentNumber';
    $rows[]  = 'joinedDate';
    $rows[]  = 'gamesWon';
    $rows[]  = 'gamesPlayed';
    $cond    = "WHERE tournamentID=$id";
    $results = selectFromTable($rows, TOURNAMENT_PLAYERS_TABLE, $cond, 100);
    $t->assign('detailsPlayers', $results);
}

$time = time() + 7*24*60*60;
$t->assign('closingDate', date("Y-m-d H:i:s", $time));
$time+= 7*24*60*60;
$t->assign('finishedDate', date("Y-m-d H:i:s", $time));

$rows    = array('id','name','password','description','initiationDate');
$rows[]  = 'closingDate';
$rows[]  = 'finishedDate';
$rows[]  = 'status';
$cond    = "ORDER BY `initiationDate` DESC";
$results = selectFromTable($rows, TOURNAMENTS_TABLE, $cond, 100);

$doIParticipate   = array();
$isPasswordNeeded = array();
$t->assign('allTournaments', $results);
$emptyMD5 = "d41d8cd98f00b204e9800998ecf8427e";
foreach ($results as $result) {
    $doIParticipate[]   = in_array($result['id'], $myParticipations);
    $isPasswordNeeded[] = ($result['password'] != $emptyMD5);
}
$t->assign('doIParticipate', $doIParticipate);
$t->assign('isPasswordNeeded', $isPasswordNeeded);
echo $t->output('tournaments.html');
?>
