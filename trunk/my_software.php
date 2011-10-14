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
    $langName   = sqlEscape($_GET['addLanguage']);
    $softwareID = (int) $_GET['softwareID'];
    $cond       = "WHERE `name`='".$langName."'";
    $result     = selectFromTable(array('id', 'used'), LANGUAGES_TABLE, $cond);
    if ($result === false) {
        $keyValue         = array();
        $keyValue["name"] = $langName;
        $keyValue["used"] = 1;
        $langID           = insertIntoTable($keyValue, LANGUAGES_TABLE);
    } else {
        $langID   = $result['id'];
        $keyValue = array("used" => $result['used']+1);
        updateDataInTable(LANGUAGES_TABLE, $keyValue, "WHERE `id`=$langID");
    }
    $keyValuePairs               = array();
    $keyValuePairs['softwareID'] = $softwareID;
    $keyValuePairs['languageID'] = $langID;
    insertIntoTable($keyValuePairs, SOFTWARE_LANGUAGES_TABLE);
} 
if (isset($_GET['deleteLang'])) {
    $langID     = (int) $_GET['deleteLang'];
    $softwareID = (int) $_GET['softwareID'];

    // Is player admin of this software?
    $cond   = "WHERE `adminUserID` = ".USER_ID." AND `id` = ".$softwareID;
    $result = selectFromTable(array('id'), SOFTWARE_TABLE, $cond);
    if ($result['id'] != $softwareID) {
        exit("You are not admin of this software!");
    }

    $cond   = "WHERE `softwareID`=$softwareID AND `languageID`=$langID";
    $result = selectFromTable(array('id'), SOFTWARE_LANGUAGES_TABLE, $cond);
    deleteFromTable(SOFTWARE_LANGUAGES_TABLE, $result['id']);
    $keyValue = array("used" => "`used`-1");
    updateDataInTable(LANGUAGES_TABLE, $keyValue, "WHERE `id`=$langID");
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
    $cond      = "WHERE `".USER_NAME_COLUMN."`='$user_name'";
    $result    = selectFromTable(array('user_id'), USERS_TABLE, $cond);
    if ($result !== false) {
        $task = sqlEscape($_GET['task']);

        $keyValuePairs               = array();
        $keyValuePairs['user_id']    = $result['user_id'];
        $keyValuePairs['softwareID'] = $softwareID;
        $keyValuePairs['task']       = $task;
        insertIntoTable($keyValuePairs, SOFTWARE_DEVELOPER_TABLE);
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
    deleteFromTable(SOFTWARE_DEVELOPER_TABLE, $result['id']);
}

if (isset($_GET['setCurrent'])) {
    $cond     = "WHERE  `user_id` =".USER_ID;
    $keyValue = array('software_id'=>(int) $_GET['setCurrent']);
    updateDataInTable(USER_INFO_TABLE, $keyValue, $cond);
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
            $player = selectFromTable(array('user_id', USER_NAME_COLUMN), 
                                         USERS_TABLE, $cond);
            // Quick'n dirt fix: 
            // The template tries to acces $possibleOpponents[$i]['user_name']:
            $fixedPlayer = array('user_id'=>$player['user_id'], 
                                   'user_name'=>$player[USER_NAME_COLUMN]);
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
