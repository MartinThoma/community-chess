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
require_once 'additional.inc.php';
if (USER_ID === false) exit("Please <a href='login.wrapper.php'>login</a>");
$t = new vemplator();
$t->assign('USER_ID', USER_ID);

if (isset($_POST['tournamentName'])) {
    $tournamentName = sqlEscape($_POST['tournamentName']);
    $description    = sqlEscape($_POST['description']);
    $password       = md5($_POST['password']);
    $closingDate    = sqlEscape($_POST['closingDate']);
    $finishedDate   = sqlEscape($_POST['finishedDate']);

    if ($tournamentName == '') {
        $t->assign('noTournamentName', true);
    } else {
        $keyValue                 = array();
        $keyValue['name']         = $tournamentName;
        $keyValue['description']  = $description;
        $keyValue['password']     = $password;
        $keyValue['closingDate']  = date('Y-m-d H:i:s', strtotime($closingDate));
        $keyValue['finishedDate'] = date('Y-m-d H:i:s', strtotime($finishedDate));
        insertIntoTable($keyValue, TOURNAMENTS_TABLE);
    }
}

if (isset($_GET['challengeUserID'])) {
    $tournamentID = (int) $_GET['tournamentID'];
    $rows         = array('closingDate');
    $cond         = "WHERE `id`=$tournamentID";
    $result       = selectFromTable($rows, TOURNAMENTS_TABLE, $cond);
    if (strtotime($result['closingDate']) > time()) {
        // Tournament didn't begin. Don't try hacking it.
        exit();
    }


    $user_id        = (int) $_GET['challengeUserID'];
    $cond           = 'WHERE `user_id` = '.$user_id.' AND `user_id` != '.USER_ID;
    $row            = selectFromTable(array('user_name'), USERS_TABLE, $cond);
    $challengedUser = $row['user_name'];
    if ($row !== false) {
        $cond  = "WHERE ((`whiteUserID` = ".USER_ID." AND `blackUserID`=$user_id)";
        $cond .= " OR (`whiteUserID` = $user_id AND `blackUserID`=".USER_ID.")) ";
        $cond .= "AND `tournamentID`=$tournamentID AND `outcome` = -1";
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

            $condition = "WHERE `user_id` = ".USER_ID." OR `user_id`=$user_id";      
            $rows      = array('user_id', 'software_id');  
            $result    = selectFromTable($rows, USERS_TABLE, $condition, 2);

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
    // participants may only exit if the tournament hasn't started jet
    $rows   = array('closingDate');
    $cond   = "WHERE `id`=$tournamentID";
    $result = selectFromTable($rows, TOURNAMENTS_TABLE, $cond);
    if (strtotime($result['closingDate']) > time()) {
        $cond   = "WHERE tournamentID=$tournamentID AND `user_id`=".USER_ID;
        $result = selectFromTable(array('id'), TOURNAMENT_PLAYERS_TABLE, $cond);

        $stmt = $conn->prepare("DELETE FROM `".TOURNAMENT_PLAYERS_TABLE.
                               "` WHERE `id` = :id LIMIT 1");
        $stmt->bindValue(':id', $result['id'], PDO::PARAM_INT);
        $stmt->execute();

    } else {
        $t->assign('cantExitStartedAlready', true);
    }
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


    $rows   = array('closingDate');
    $cond   = "WHERE `id`=$id";
    $result = selectFromTable($rows, TOURNAMENTS_TABLE, $cond);
    if (strtotime($result['closingDate']) > time()) {
        $t->assign('tournamentDidntBeginn', true);
    }

    $rows    = array();
    $rows[]  = 'id';
    $rows[]  = 'user_id';
    $rows[]  = 'tournamentNumber';
    $rows[]  = 'joinedDate';
    $rows[]  = 'gamesWon';
    $rows[]  = 'gamesPlayed';
    $rows[]  = 'pageRank';
    $cond    = "WHERE tournamentID=$id";
    $results = selectFromTable($rows, TOURNAMENT_PLAYERS_TABLE, $cond, 100);
    $t->assign('detailsPlayers', $results);
}


/* closingDate client side validation *********************************************/
$time = time() + 1*60*60;
$t->assign('closingDateMin', date("Y-m-d\TH:i\Z", $time));
$time = time() + 7*24*60*60;
$t->assign('closingDate', date("Y-m-d\TH:i\Z", $time));
$time = time() + 1*31*24*60*60;
$t->assign('closingDateMax', date("Y-m-d\TH:i\Z", $time));
/* finishedDate client side validation *********************************************/
$time = time() + 3*24*60*60;
$t->assign('finishedDateMin', date("Y-m-d\TH:i\Z", $time));
$time = time() + 14*24*60*60;
$t->assign('finishedDate', date("Y-m-d\TH:i\Z", $time));
$time = time() + 2*31*24*60*60;
$t->assign('finishedDateMax', date("Y-m-d\TH:i\Z", $time));


$rows    = array('id','name','password','description','initiationDate');
$rows[]  = 'closingDate';
$rows[]  = 'finishedDate';
$cond    = "ORDER BY `initiationDate` DESC";
$results = selectFromTable($rows, TOURNAMENTS_TABLE, $cond, 100);
$rows    = count($results);
for ($i=0; $i<$rows; $i++) {
    if (strtotime($results[$i]['finishedDate']) < time()) {
        $status = 'Finished';
    } else if (strtotime($results[$i]['closingDate']) < time()) {
        $status = 'Matching Phase';
    } else {
        $status = 'openForInvitations';
    }
    $results[$i]['status'] = $status;
}

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
