<?php
/**
 * specify your chess software
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
$msg = array();

if (isset($_GET['addLanguage'])) {
    $stmt = $conn->prepare('SELECT `id`, `used` FROM '.LANGUAGES_TABLE.' WHERE '.
        '`name` = :langName LIMIT 1');
    $stmt->bindValue(":langName", $_GET['addLanguage']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $langName   = sqlEscape($_GET['addLanguage']);
    $softwareID = (int) $_GET['softwareID'];

    if ($result === false) {
        $stmt = $conn->prepare('INSERT INTO `'.LANGUAGES_TABLE.'` '.
            '(`name`, `used`) VALUES (:name, 1)');
        $stmt->bindValue(":name", $_GET['addLanguage']);
        $stmt->execute();

        $stmt = $conn->prepare('SELECT `id` FROM '.LANGUAGES_TABLE.' WHERE '.
            '`name` = :langName LIMIT 1');
        $stmt->bindValue(":langName", $_GET['addLanguage']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $langID = $result['id'];
    } else {
        $stmt = $conn->prepare('UPDATE `'.LANGUAGES_TABLE.'` SET '.
                               'used = `used` + 1 '.
                               'WHERE `id` = :id LIMIT 1');
        $stmt->bindValue(":id", $result['id'], PDO::PARAM_INT);
        $stmt->execute();
    }

    $stmt = $conn->prepare('INSERT INTO `'.SOFTWARE_LANGUAGES_TABLE.'` '.
        '(`softwareID`, `languageID`) VALUES (:softwareID, :languageID)');
    $stmt->bindValue(':softwareID', (int) $_GET['softwareID'], PDO::PARAM_INT);
    $stmt->bindValue(':languageID', (int) $result['id'], PDO::PARAM_INT);
    $stmt->execute();
} 
if (isset($_GET['deleteLang'])) {
    $langID     = (int) $_GET['deleteLang'];
    $softwareID = (int) $_GET['softwareID'];

    // Check if the executing player is the admin.
    $stmt = $conn->prepare('SELECT `adminUserID` FROM '.SOFTWARE_TABLE.' WHERE '.
        '`id` = :softwareID LIMIT 1');
    $stmt->bindValue(":softwareID", (int) $_GET['softwareID'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result['adminUserID'] == USER_ID) {
        // Delete the language from the software 
        $stmt = $conn->prepare('DELETE FROM `'.SOFTWARE_LANGUAGES_TABLE.'` '.
            'WHERE `id` = :id LIMIT 1');

        $stmt->bindValue(':id', (int) $_GET['softwareID'], PDO::PARAM_INT);
        $stmt->execute();

        // Set the used software languages back.
        $stmt = $conn->prepare('UPDATE `'.LANGUAGES_TABLE.'` SET '.
                               'used = (`used` - 1) WHERE `id` = :id LIMIT 1');
        $stmt->bindValue(":id", (int) $_GET['deleteLang'], PDO::PARAM_INT);
        $stmt->execute();
    }
}

if (isset($_GET['addTeammate'])) {
    $softwareID = (int) $_GET['softwareID'];
    // Is player admin of this software?
    $cond   = "WHERE `adminUserID` = ".USER_ID." AND `id` = ".$softwareID;
    $result = selectFromTable(array('id'), SOFTWARE_TABLE, $cond);
    if ($result['id'] != $softwareID) {
        exit('You are not admin of this software!');
    }

    $user_name = sqlEscape($_GET['addTeammate']);
    $cond      = "WHERE `user_name`='$user_name'";
    $result    = selectFromTable(array('user_id'), USERS_TABLE, $cond);
    if ($result !== false) {
        $stmt = $conn->prepare('INSERT INTO `'.SOFTWARE_DEVELOPER_TABLE.'` '.
            '(`user_id`, `softwareID`, `task`) VALUES '.
            '(:uid, :softwareID, :task)');
        $stmt->bindValue(":uid", (int) $result['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(":softwareID", $softwareID, PDO::PARAM_INT);
        $stmt->bindValue(":task", $_GET['task']);
        $stmt->execute();

        $msg[] = "Added '$user_name' as a '$task'.";
    } else {
        $msg[] = "The username '$user_name' was not in the database.";
    }
}
if (isset($_GET['deleteTeammate'])) {
    $teammateID = (int) $_GET['deleteTeammate'];
    $softwareID = (int) $_GET['softwareID'];

    // Is player admin of this software?
    $cond   = "WHERE `adminUserID` = ".USER_ID." AND `id` = ".$softwareID;
    $result = selectFromTable(array('id'), SOFTWARE_TABLE, $cond);
    if ($result['id'] != $softwareID) {
        exit("You are not admin of this software!");
    }

    $cond   = "WHERE `user_id` = $teammateID AND `softwareID` = $softwareID ";
    $cond  .= "AND `task` != 'Admin'";
    $result = selectFromTable(array('id'), SOFTWARE_DEVELOPER_TABLE, $cond);

    $stmt = $conn->prepare("DELETE FROM `".SOFTWARE_DEVELOPER_TABLE." ".
                           "` WHERE `id` = :id LIMIT 1");
    $stmt->bindValue(':id', $result['id'], PDO::PARAM_INT);
    $stmt->execute();

}

if (isset($_GET['setCurrent'])) {
    $cond     = "WHERE  `user_id` =".USER_ID;
    $keyValue = array('software_id'=>(int) $_GET['setCurrent']);
    updateDataInTable(USERS_TABLE, $keyValue, $cond);
}
if (isset($_POST['newSoftwareName'])) {
    $name      = sqlEscape($_POST['newSoftwareName']);
    $version   = sqlEscape($_POST['version']);
    $changelog = sqlEscape($_POST['changelog']);
    if (isset($_POST['lastVersionID'])) {
        $lastVersionID = (int) $_POST['lastVersionID'];
    } else {
        $lastVersionID = 0;
    }

    $keyValuePairs                  = array();
    $keyValuePairs['name']          = $name;
    $keyValuePairs['adminUserID']   = USER_ID;
    $keyValuePairs['version']       = $version;
    $keyValuePairs['lastVersionID'] = $lastVersionID;
    $keyValuePairs['changelog']     = $changelog;

    $softwareID = insertIntoTable($keyValuePairs, SOFTWARE_TABLE);


    $keyValuePairs               = array();
    $keyValuePairs['user_id']    = USER_ID;
    $keyValuePairs['softwareID'] = $softwareID;
    $keyValuePairs['task']       = 'Admin';

    insertIntoTable($keyValuePairs, SOFTWARE_DEVELOPER_TABLE);
}

$currentSoftwareID = getUserSoftwareID(USER_ID);
$t->assign('currentSoftwareID', $currentSoftwareID);

$cond      = "ORDER BY  ".LANGUAGES_TABLE.".`name` ASC";
$languages = selectFromTable(array('id', 'name'), LANGUAGES_TABLE, $cond, 100);
$langIndex = array();
foreach ($languages as $lang) {
    $langIndex[$lang['id']] = $lang['name'];
}

$cond        = "WHERE `user_id`=".USER_ID;
$row         = array('softwareID');
$softwareIds = selectFromTable($row, SOFTWARE_DEVELOPER_TABLE, $cond, 10);

if (count($softwareIds) > 0) {
    $softwareArray = array();
    $rows          = array('id', 'name', 'version');
    foreach ($softwareIds as $id) {
        $id               = $id['softwareID'];
        $cond             = "WHERE `id` = $id";
        $basicInformation = selectFromTable($rows, SOFTWARE_TABLE, $cond);
        // List of teammates
        $cond    = "WHERE `softwareID`=$id";
        $row     = array('user_id', 'task');
        $userIDs = selectFromTable($row, SOFTWARE_DEVELOPER_TABLE, $cond, 100);
        $players = array();
        foreach ($userIDs as $uID) {
            $cond   = 'WHERE `user_id`='.$uID['user_id'];
            $player = selectFromTable(array('user_id', 'user_name'), 
                                         USERS_TABLE, $cond);
            // Quick'n dirt fix: 
            // The template tries to acces $possibleOpponents[$i]['user_name']:
            $fixedPlayer = array('user_id'=>$player['user_id'], 
                                   'user_name'=>$player['user_name']);
            $players[]   = array_merge($fixedPlayer, array('task'=>$uID['task']));
        }
        // Languages
        $cond      = "WHERE `softwareID`=$id";
        $results   = selectFromTable(array("languageID"), 
                                     SOFTWARE_LANGUAGES_TABLE, $cond, 10);
        $languages = array();
        foreach ($results as $langID) {
            $languages[] = array('id'=>$langID['languageID'], 
                                 'name'=>$langIndex[$langID['languageID']]);
        }
        // Bring all together
        $softwareArray[] = array_merge($basicInformation, 
                                        array('players'=>$players),
                                        array('languages'=>$languages));
    }
    $t->assign('softwareArray', $softwareArray);
}

echo $t->output('my_software.html');
?>
