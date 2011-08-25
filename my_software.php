<?php
/**
 * @author: Martin Thoma
 * specify the chess software
 * */
require ('wrapper.inc.php');
$t = new vemplator();
if (USER_ID === false){exit("Please <a href='login.wrapper.php'>login</a>");}
else{$t->assign('USER_ID', USER_ID);}
$msg = array();

if (isset($_GET['addLanguage'])) {
    $langName = mysql_real_escape_string($_GET['addLanguage']);
    $softwareID = intval($_GET['softwareID']);
    $cond = "WHERE `name`='".$langName."'";
    $result = selectFromTable(array('id', 'used'), "chess_languages", $cond);
    if ($result === false){
        $keyValue = array();
        $keyValue["name"] = $langName;
        $keyValue["used"] = 1;
        $langID = insertIntoTable($keyValue, "chess_languages");
    } else {
        $langID  = $result['id'];
        $keyValue= array("used" => $result['used']+1);
        updateDataInTable("chess_languages", $keyValue, "WHERE `id`=$langID");
    }
    $keyValuePairs = array();
    $keyValuePairs['softwareID'] = $softwareID;
    $keyValuePairs['languageID'] = $langID;
    insertIntoTable($keyValuePairs, "chess_softwareLangages");
} 
if (isset($_GET['deleteLang'])) {
    $langID = intval($_GET['deleteLang']);
    $softwareID = intval($_GET['softwareID']);

    // Is player admin of this software?
    $cond = "WHERE `adminPlayerID` = ".USER_ID." AND `id` = ".$softwareID;
    $result = selectFromTable(array('id'), "chess_software",  $cond);
    if ($result['id'] != $softwareID){
        exit("You are not admin of this software!");
    }

    $cond = "WHERE `softwareID`=$softwareID AND `languageID`=$langID";
    $result = selectFromTable(array('id'), "chess_softwareLangages", $cond);
    deleteFromTable("chess_softwareLangages", $result['id']);
    $keyValue= array("used" => "`used`-1");
    updateDataInTable("chess_languages", $keyValue, "WHERE `id`=$langID");
}

if (isset($_GET['addTeammate'])){
    $softwareID = intval($_GET['softwareID']);
    // Is player admin of this software?
    $cond = "WHERE `adminPlayerID` = ".USER_ID." AND `id` = ".$softwareID;
    $result = selectFromTable(array('id'), "chess_software",  $cond);
    if ($result['id'] != $softwareID){
        exit("You are not admin of this software!");
    }

    $username = mysql_real_escape_string($_GET['addTeammate']);
    $cond = "WHERE `uname`='$username'";
    $result = selectFromTable(array('id'), "chess_players", $cond);
    if ($result !== false){
        $task = mysql_real_escape_string($_GET['task']);
        $keyValuePairs = array();
        $keyValuePairs['playerID']   = $result['id'];
        $keyValuePairs['softwareID'] = $softwareID;
        $keyValuePairs['task'] = $task;
        insertIntoTable($keyValuePairs, "chess_softwareDeveloper");
        $msg[] = "Added '$username' as a '$task'.";
    } else {
        $msg[] = "The username '$username' was not in the database.";
    }
}
if (isset($_GET['deleteTeammate'])) {
    $teammateID = intval($_GET['deleteTeammate']);
    $softwareID = intval($_GET['softwareID']);

    // Is player admin of this software?
    $cond = "WHERE `adminPlayerID` = ".USER_ID." AND `id` = ".$softwareID;
    $result = selectFromTable(array('id'), "chess_software",  $cond);
    if ($result['id'] != $softwareID){
        exit("You are not admin of this software!");
    }

    $cond = "WHERE `playerID` = $teammateID AND `softwareID` = $softwareID ";
    $cond.= "AND `task` != 'Admin'";
    $result = selectFromTable(array('id'), "chess_softwareDeveloper",  $cond);
    deleteFromTable("chess_softwareDeveloper", $result['id']);
}

if (isset($_GET['setCurrent'])){
    $cond  = "WHERE  `chess_players`.`id` =".USER_ID;
    $keyValue = array("currentChessSoftware"=>intval($_GET['setCurrent']));
    updateDataInTable("chess_players",$keyValue, $cond);
}
if (isset($_POST['newSoftwareName'])){
    $name   = mysql_real_escape_string($_POST['newSoftwareName']);
    $version= mysql_real_escape_string($_POST['version']);
    $changelog= mysql_real_escape_string($_POST['changelog']);
    if (isset($_POST['lastVersionID'])){
        $lastVersionID = intval($_POST['lastVersionID']);
    } else {
        $lastVersionID = 0;
    }

    $keyValuePairs = array();
    $keyValuePairs['name']          = $name;
    $keyValuePairs['adminPlayerID'] = USER_ID;
    $keyValuePairs['version']       = $version;
    $keyValuePairs['lastVersionID'] = $lastVersionID;
    $keyValuePairs['changelog']     = $changelog;
    $softwareID = insertIntoTable($keyValuePairs, "chess_software");


    $keyValuePairs = array();
    $keyValuePairs['playerID'] = USER_ID;
    $keyValuePairs['softwareID'] = $softwareID;
    $keyValuePairs['task'] = 'Admin';

    insertIntoTable($keyValuePairs, "chess_softwareDeveloper");
}

$currentSoftwareID = getUserSoftwareID(USER_ID);
$t->assign('currentSoftwareID', $currentSoftwareID);

$cond = "ORDER BY  `chess_languages`.`name` ASC";
$languages= selectFromTable(array('id', 'name'), "chess_languages", $cond, 100);
$langIndex = array();
foreach($languages as $lang){
    $langIndex[$lang['id']] = $lang['name'];
}

$cond  = "WHERE `playerID`=".USER_ID;
$row = array('softwareID');
$softwareIds = selectFromTable($row, "chess_softwareDeveloper", $cond, 10);

if (count($softwareIds) > 0) {
    $softwareArray = array();
    $rows          = array('id', 'name', 'version');
    foreach($softwareIds as $id) {
        $id               = $id['softwareID'];
        $cond             = "WHERE `id` = $id";
        $basicInformation = selectFromTable($rows, "chess_software", $cond);
        // List of teammates
        $cond      = "WHERE `softwareID`=$id";
        $row       = array('playerID', 'task');
        $playerIDs = selectFromTable($row, "chess_softwareDeveloper", $cond, 100);
        $players   = array();
        foreach($playerIDs as $uID){
            $cond      = "WHERE `id`=".$uID['playerID'];
            $player    = selectFromTable(array('id', 'uname'), "chess_players", $cond);
            $players[] = array_merge($player, array('task'=>$uID['task']));
        }
        // Languages
        $cond = "WHERE `softwareID`=$id";
        $results=selectFromTable(array("languageID"), "chess_softwareLangages", $cond, 10);
        $languages = array();
        foreach($results as $langID){
            $languages[] = array('id'=>$langID['languageID'], 'name'=>$langIndex[$langID['languageID']]);
        }
        // Bring all together
        $softwareArray[]  = array_merge($basicInformation, 
                                        array('players'=>$players),
                                        array('languages'=>$languages)
                                       );
    }
    $t->assign('softwareArray', $softwareArray);
}

echo $t->output('my_software.html');
?>
