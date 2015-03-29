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
    if ($_POST['tournamentName'] == '') {
        $t->assign('noTournamentName', true);
    } else {
        $stmt = $conn->prepare('INSERT INTO `'.TOURNAMENTS_TABLE.'` '.
            '(`name`, `description`, `password`, `closingDate`, `finishedDate`) '.
            'VALUES (:name, :description, :password, :closingDate, :finishedDate)');
        $stmt->bindValue(":name", $_POST['tournamentName']);
        $stmt->bindValue(":description", $_POST['description']);
        $stmt->bindValue(":password", md5($_POST['password']));
        $stmt->bindValue(":closingDate", 
                        date('Y-m-d H:i:s', strtotime($_POST['closingDate'])));
        $stmt->bindValue(":finishedDate", 
                        date('Y-m-d H:i:s', strtotime($_POST['finishedDate'])));
        $stmt->execute();
    }
}

if (isset($_GET['challengeUserID'])) {
    $stmt = $conn->prepare('SELECT `closingDate` FROM '.TOURNAMENTS_TABLE.' '.
                           'WHERE `id`=:tid LIMIT 1');
    $stmt->bindValue(":uid", (int) $_GET['tournamentID'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $tournamentID = (int) $_GET['tournamentID'];
    if (strtotime($result['closingDate']) > time()) {
        // Tournament didn't begin. Don't try hacking it.
        exit();
    }


    $stmt = $conn->prepare('SELECT `username` FROM '.USERS_TABLE.' '.
                           'WHERE `user_id` !=:uid1 AND 
							`user_id` = :uid2 LIMIT 1');
    $stmt->bindValue(":uid1", USER_ID, PDO::PARAM_INT);
    $stmt->bindValue(":uid2", (int) $_GET['challengeUserID'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $challengedUser = $result['username'];
    if ($row !== false) {
        $stmt = $conn->prepare('SELECT `id` FROM '.GAMES_TABLE.' '.
                    'WHERE ((`whiteUserID` = :uid1 AND `blackUserID`=:uid2) '.
                    'OR (`whiteUserID` = :uid2 AND `blackUserID`=:uid1)) '.
                    'AND `tournamentID`= :tid AND `outcome` = -1');
        $stmt->bindValue(":uid1", USER_ID, PDO::PARAM_INT);
        $stmt->bindValue(":uid2", (int) $_GET['challengeUserID'], PDO::PARAM_INT);
        $stmt->bindValue(":tid", (int) $_GET['tournamentID'], PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result !== false) {
            $t->assign('alreadyChallengedPlayer', $challengedUser);
            $t->assign('alreadyChallengedGameID', $row['id']);
        } else {
            // Do both players participate in tournament?
            $stmt = $conn->prepare('SELECT `user_id`, `gamesWon`, `gamesPlayed` '.
                        'FROM '.TOURNAMENT_PLAYERS_TABLE.' '.
                        'WHERE tournamentID=:tid AND (user_id= :uid1 OR '.
                        '`user_id`= :uid2) LIMIT 2');
            $stmt->bindValue(":uid1", USER_ID, PDO::PARAM_INT);
            $stmt->bindValue(":uid2", (int) $_GET['challengeUserID']);
            $stmt->bindValue(":tid", (int) $_GET['tournamentID']);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

            $stmt = $conn->prepare('SELECT `user_id`, `softwareID` '.
                        'FROM '.USERS_TABLE.' '.
                        'WHERE user_id= :uid1 OR `user_id`= :uid2 LIMIT 2');
            $stmt->bindValue(":uid1", USER_ID, PDO::PARAM_INT);
            $stmt->bindValue(":uid2", (int) $_GET['challengeUserID']);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result[0]['id'] == USER_ID) {
                $whitePlayerSoftwareID = $result[0]['softwareID'];
                $blackPlayerSoftwareID = $result[1]['softwareID'];
            } else {
                $blackPlayerSoftwareID = $result[0]['softwareID'];
                $whitePlayerSoftwareID = $result[1]['softwareID'];
            }

            $stmt = $conn->prepare('INSERT INTO `'.GAMES_TABLE.'` '.
                '(`whiteUserID`, `blackUserID`, `whitePlayerSoftwareID`, '.
                '`blackPlayerSoftwareID`) '.
                'VALUES (:wid, :bid, :w_sid, :b_sid)');
            $stmt->bindValue(":wid", USER_ID, PDO::PARAM_INT);
            $stmt->bindValue(":bid", $user_id, PDO::PARAM_INT);
            $stmt->bindValue(":w_sid", $whitePlayerSoftwareID, PDO::PARAM_INT);
            $stmt->bindValue(":b_sid", $blackPlayerSoftwareID, PDO::PARAM_INT);
            $stmt->execute();

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
    $stmt = $conn->prepare('SELECT `id` FROM '.TOURNAMENTS_TABLE.' '.
                'WHERE `id`=:tid AND `password`=:pass AND closingDate > NOW()');
    $stmt->bindValue(":tid", (int) $_GET['enterID'], PDO::PARAM_INT);
    $stmt->bindValue(":pass", $pass);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['id'] != $tournamentID)
        exit("Wrong password or tournament is already closed.");

    $stmt = $conn->prepare('INSERT INTO `'.TOURNAMENT_PLAYERS_TABLE.'` '.
        '(`tournamentID`, `user_id`) VALUES (:tid, :uid)');
    $stmt->bindValue(":tid", $tournamentID, PDO::PARAM_INT);
    $stmt->bindValue(":uid", USER_ID, PDO::PARAM_INT);
    $stmt->execute();

    if ($id > 0) {
        $t->assign('joinedTournamentID', $tournamentID);
    } else {
        $t->assign('joinTournamentFailed', true);
    }
}

if (isset($_GET['deleteParticipation'])) {
    $tournamentID = (int) $_GET['deleteParticipation'];
    // participants may only exit if the tournament hasn't started jet
    $stmt = $conn->prepare('SELECT `closingDate` FROM '.TOURNAMENTS_TABLE.' '.
                'WHERE `id`=:tid');
    $stmt->bindValue(":tid", (int) $_GET['deleteParticipation'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (strtotime($result['closingDate']) > time()) {
        $stmt = $conn->prepare('SELECT `id` FROM '.TOURNAMENT_PLAYERS_TABLE.' '.
                    'WHERE `tournamentID`=:tid AND `user_id` = :uid LIMIT 1');
        $stmt->bindValue(":tid", (int) $_GET['deleteParticipation']);
        $stmt->bindValue(":uid", USER_ID, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $conn->prepare("DELETE FROM `".TOURNAMENT_PLAYERS_TABLE.
                               "` WHERE `id` = :id LIMIT 1");
        $stmt->bindValue(':id', $result['id'], PDO::PARAM_INT);
        $stmt->execute();

    } else {
        $t->assign('cantExitStartedAlready', true);
    }
}

$stmt = $conn->prepare('SELECT `tournamentID` FROM '.TOURNAMENT_PLAYERS_TABLE.' '.
            'WHERE `user_id`=:uid LIMIT 100');
$stmt->bindValue(":uid", USER_ID, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$myParticipations = array();
foreach ($result as $row) {
    $myParticipations[] = $row['tournamentID'];
}

if (isset($_GET['getDetails'])) {
    // TODO: Something is wrong here since r185. Fix it
    $id = (int) $_GET['getDetails'];
    $t->assign('detailsTournamentID', $id);

    $stmt = $conn->prepare('SELECT `closingDate` FROM '.TOURNAMENTS_TABLE.' '.
                'WHERE `id`=:id LIMIT 100');
    $stmt->bindValue(":id", (int) $_GET['getDetails'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (strtotime($result['closingDate']) > time()) {
        $t->assign('tournamentDidntBeginn', true);
    }

    $stmt = $conn->prepare('SELECT `id`, `user_id`, `tournamentNumber`, '.
            '`joinedDate`, `gamesWon`, `gamesPlayed`, `pageRank` '.
            'FROM '.TOURNAMENT_PLAYERS_TABLE.' '.
            'WHERE tournamentID=:tid LIMIT 100');
    $stmt->bindValue(":tid", (int) $_GET['getDetails']);
    $stmt->execute();
    $results = $stmt->fetch(PDO::FETCH_ASSOC);

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


$stmt = $conn->prepare('SELECT `id`, `name`, `password`, `description`, '.
        '`initiationDate`, `closingDate`, `finishedDate` '.
        'FROM '.TOURNAMENTS_TABLE.' '.
        'ORDER BY `initiationDate` DESC LIMIT 100');
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$rows = count($results);
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
