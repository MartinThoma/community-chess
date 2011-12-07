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
    $stmt = $conn->prepare('SELECT `id` FROM '.SOFTWARE_TABLE.' WHERE '.
        '`adminUserID` = :uid AND `id` = :sid LIMIT 1');
    $stmt->bindValue(":uid", USER_ID);
    $stmt->bindValue(":sid", $softwareID);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['id'] != $softwareID) {
        exit('You are not admin of this software!');
    }

    $stmt = $conn->prepare('SELECT `user_id` FROM '.USERS_TABLE.' WHERE '.
        '`user_name` = :uname LIMIT 1');
    $stmt->bindValue(":uname", $_GET['addTeammate']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result !== false) {
        $stmt = $conn->prepare('INSERT INTO `'.SOFTWARE_DEVELOPER_TABLE.'` '.
            '(`user_id`, `softwareID`, `task`) VALUES '.
            '(:uid, :softwareID, :task)');
        $stmt->bindValue(":uid", (int) $result['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(":softwareID", $softwareID, PDO::PARAM_INT);
        $stmt->bindValue(":task", $_GET['task']);
        $stmt->execute();

        $msg[] = "Added '".$_GET['addTeammate']."' as a '$task'.";
    } else {
        $msg[] = "The user '".$_GET['addTeammate']."' is not in the database.";
    }
}
if (isset($_GET['deleteTeammate'])) {
    $teammateID = (int) $_GET['deleteTeammate'];
    $softwareID = (int) $_GET['softwareID'];

    // Is player admin of this software?
    $stmt = $conn->prepare('SELECT `id` FROM '.SOFTWARE_TABLE.' WHERE '.
        '`adminUserID` = :uid AND `id` = :sid LIMIT 1');
    $stmt->bindValue(":uid", USER_ID);
    $stmt->bindValue(":sid", $softwareID);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['id'] != $softwareID) {
        exit("You are not admin of this software!");
    }

    $stmt = $conn->prepare('SELECT `id` FROM '.SOFTWARE_DEVELOPER_TABLE.' WHERE '.
        '`user_id` = :uid AND `softwareID` = :sid AND `task` != "Admin" LIMIT 1');
    $stmt->bindValue(":uid", $teammateID);
    $stmt->bindValue(":sid", $softwareID);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("DELETE FROM `".SOFTWARE_DEVELOPER_TABLE." ".
                           "` WHERE `id` = :id LIMIT 1");
    $stmt->bindValue(':id', $result['id'], PDO::PARAM_INT);
    $stmt->execute();

}

if (isset($_GET['setCurrent'])) {
    $stmt = $conn->prepare('UPDATE `'.USERS_TABLE.'` SET '.
                           'software_id = :sid '.
                           'WHERE `user_id` = :uid LIMIT 1');
    $stmt->bindValue(":sid", (int) $_GET['setCurrent'], PDO::PARAM_INT);
    $stmt->bindValue(":uid", USER_ID, PDO::PARAM_INT);
    $stmt->execute();
}
if (isset($_POST['newSoftwareName'])) {
    if (isset($_POST['lastVersionID'])) {
        $lastVersionID = (int) $_POST['lastVersionID'];
    } else {
        $lastVersionID = 0;
    }

    $stmt = $conn->prepare('INSERT INTO `'.SOFTWARE_TABLE.'` '.
        '(`name`, `adminUserID`, `version`, `lastVersionID`, `changelog`) '.
        'VALUES (:name, :admin_id, :version, :lv_id, :changelog)');
    $stmt->bindValue(":name", $_POST['newSoftwareName']);
    $stmt->bindValue(":admin_id", USER_ID, PDO::PARAM_INT);
    $stmt->bindValue(":version", $_POST['version']);
    $stmt->bindValue(":lv_id", $lastVersionID);
    $stmt->bindValue(":changelog", $_POST['changelog']);
    $stmt->execute();

    $stmt = $conn->prepare('INSERT INTO `'.SOFTWARE_DEVELOPER_TABLE.'` '.
        '(`user_id`, `softwareID`, `task`) VALUES (:uid, :sid, :task)');
    $stmt->bindValue(":uid", USER_ID, PDO::PARAM_INT);
    $stmt->bindValue(":sid", $softwareID, PDO::PARAM_INT);
    $stmt->bindValue(":task", 'Admin');
    $stmt->execute();
}

$currentSoftwareID = getUserSoftwareID(USER_ID);
$t->assign('currentSoftwareID', $currentSoftwareID);

$stmt = $conn->prepare('SELECT `id`, `name` FROM '.LANGUAGES_TABLE.' '.
                       'ORDER BY  `name` ASC LIMIT 100');
$stmt->bindValue(":uid", $teammateID);
$stmt->bindValue(":sid", $softwareID);
$stmt->execute();
$languages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$langIndex = array();
foreach ($languages as $lang) {
    $langIndex[$lang['id']] = $lang['name'];
}

$stmt = $conn->prepare('SELECT `softwareID` FROM '.SOFTWARE_DEVELOPER_TABLE.' '.
                       'WHERE `user_id`=:uid LIMIT 10');
$stmt->bindValue(":uid", USER_ID);
$stmt->execute();
$softwareIds = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($softwareIds) > 0) {
    $softwareArray = array();
    $rows          = array('id', 'name', 'version');
    foreach ($softwareIds as $id) {
        $id   = $id['softwareID'];
        $stmt = $conn->prepare('SELECT `id`, `name`, `version` '.
                'FROM '.SOFTWARE_TABLE.' WHERE `id`=:id LIMIT 1');
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $basicInformation = $stmt->fetch(PDO::FETCH_ASSOC);

        // List of teammates
        $stmt = $conn->prepare('SELECT `user_id`, `task` '.
                'FROM '.SOFTWARE_DEVELOPER_TABLE.' '.
                'WHERE `softwareID`=:id LIMIT 100');
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $userIDs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $players = array();
        foreach ($userIDs as $uID) {
            $stmt = $conn->prepare('SELECT `user_id`, `user_name` '.
                    'FROM '.USERS_TABLE.' '.
                    'WHERE `user_id`=:uid LIMIT 1');
            $stmt->bindValue(":uid", $uID['user_id']);
            $stmt->execute();
            $player    = $stmt->fetch(PDO::FETCH_ASSOC);
            $players[] = array_merge($player, array('task'=>$uID['task']));
        }
        // Languages
        $stmt = $conn->prepare('SELECT `languageID` '.
                'FROM '.SOFTWARE_DEVELOPER_TABLE.' '.
                'WHERE `softwareID`=:sid LIMIT 10');
        $stmt->bindValue(":sid", $id);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
