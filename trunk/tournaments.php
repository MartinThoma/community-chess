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

    $keyValue                = array();
    $keyValue['name']        = $tournamentName;
    $keyValue['description'] = $description;
    $keyValue['password']    = $password;
    $keyValue['closingDate'] = $closingDate;
    insertIntoTable($keyValue, "chess_turnaments");
}

if (isset($_GET['challengePlayerID'])) {
    $id           = intval($_GET['challengePlayerID']);
    $tournamentID = intval($_GET['tournamentID']);

    $cond           = 'WHERE `id` = '.$id.' AND `id` != '.USER_ID;
    $row            = selectFromTable(array('uname'), 'chess_players', $cond);
    $challengedUser = $row['uname'];
    if ($row !== false) {
        $cond  = "WHERE (`whitePlayerID` = ".USER_ID." AND `blackPlayerID`=$id)";
        $cond .= " OR (`whitePlayerID` = $id AND `blackPlayerID`=".USER_ID.") ";
        $cond .= "AND tournamentID=$tournamentID";
        $row   = selectFromTable(array('id'), 'chess_currentGames', $cond);
        if ($row !== false) {
            $t->assign('alreadyChallengedPlayer', $challengedUser);
            $t->assign('alreadyChallengedGameID', $row['id']);
        } else {
            // Do both players participate in tournament?
            $rows    = array('playerID', 'gamesWon', 'gamesPlayed');
            $cond    = "WHERE turnamentID=$tournamentID AND (playerID=".USER_ID;
            $cond   .= " OR playerID=".$id.")";
            $results =selectFromTable($rows, 'chess_turnamentPlayers', $cond, 2);
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



            $cond   = "WHERE `id` = ".USER_ID." OR `id`=$id";      
            $rows   = array('id', 'currentChessSoftware');  
            $result = selectFromTable($rows, "chess_players", $condition, 2);

            if ($result[0]['id'] == USER_ID) {
                $whitePlayerSoftwareID = $result[0]['currentChessSoftware'];
                $blackPlayerSoftwareID = $result[1]['currentChessSoftware'];
            } else {
                $blackPlayerSoftwareID = $result[0]['currentChessSoftware'];
                $whitePlayerSoftwareID = $result[1]['currentChessSoftware'];
            }
            $keyValuePairs = array('whitePlayerID'=>USER_ID, 
                               'blackPlayerID'=>$id,
                               'whitePlayerSoftwareID'=>$whitePlayerSoftwareID,
                               'blackPlayerSoftwareID'=>$blackPlayerSoftwareID);
            insertIntoTable($keyValuePairs, 'chess_currentGames');

            $t->assign('startedGamePlayerID', $id);
            $t->assign('startedGamePlayerUsername', $challengedUser);
        }
    } else {
        $t->assign('incorrectID', true);
    }
}

if (isset($_GET['enterID'])) {
    $tournamentID = intval($_GET['enterID']);
    if (isset($_GET['password'])) {
        $pass = md5($_GET['password']);
    } else {
        $pass = md5('');
    }
    $cond   = "WHERE id=$tournamentID AND password='".$pass."' ";
    $cond  .= "AND closingDate > NOW()";
    $result = selectFromTable(array('id'), 'chess_turnaments', $cond);
    if ($result['id'] != $tournamentID)
        exit("Wrong password or tournament is already closed.");

    $keyValue = array('turnamentID'=>$tournamentID, 'playerID'=>USER_ID);
    $id       = insertIntoTable($keyValue, "chess_turnamentPlayers");
    if ($id > 0) {
        $t->assign('joinedTournamentID', $tournamentID);
    } else {
        $t->assign('joinTournamentFailed', true);
    }
}

if (isset($_GET['deleteParticipation'])) {
    $tournamentID = intval($_GET['deleteParticipation']);
    $cond         = "WHERE turnamentID=$tournamentID AND playerID=".USER_ID;
    $result       = selectFromTable(array('id'), 'chess_turnamentPlayers', $cond);
    deleteFromTable('chess_turnamentPlayers', $result['id']);
}

$cond   = "WHERE `playerID`=".USER_ID;
$result = selectFromTable(array('turnamentID'), 
                          'chess_turnamentPlayers', $cond, 100);

$myParticipations = array();
foreach ($result as $row) {
    $myParticipations[] = $row['turnamentID'];
}

if (isset($_GET['getDetails'])) {
    $id = intval($_GET['getDetails']);
    $t->assign('detailsTournamentID', $id);

    $rows    = array();
    $rows[]  = 'id';
    $rows[]  = 'playerID';
    $rows[]  = 'turnamentNumber';
    $rows[]  = 'joinedDate';
    $rows[]  = 'gamesWon';
    $rows[]  = 'gamesPlayed';
    $cond    = "WHERE turnamentID=$id";
    $results = selectFromTable($rows, 'chess_turnamentPlayers', $cond, 100);
    $t->assign('detailsPlayers', $results);
}

$time = mktime() + 7*24*60*60;
$t->assign('closingDate', date("Y-m-d H:i:s", $time));

$rows    = array('id','name','password','description','initiationDate');
$rows[]  = 'closingDate';
$rows[]  = 'status';
$cond    = "ORDER BY `initiationDate` DESC";
$results = selectFromTable($rows, 'chess_turnaments', $cond, 100);

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
