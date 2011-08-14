<?php
/**
 * @author: Martin Thoma
 * specify the chess software
 * */
require ('wrapper.inc.php');
if (USER_ID === false){exit("Please <a href='login.wrapper.php'>login</a>");}

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
        echo msg("Added '$username' as a '$task'.");
    } else {
        echo msg("The username '$username' was not in the database.");
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

$cond = "ORDER BY  `chess_languages`.`name` ASC";
$languages= selectFromTable(array('id', 'name'), "chess_languages", $cond, 100);
$langIndex = array();
foreach($languages as $lang){
    $langIndex[$lang['id']] = $lang['name'];
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="styling/default.css" />
</head>
<body>
<h1>Add a new one</h1>
<form method="post" action="my_software.php">
<fieldset>
<legend>Add a new software / version</legend>

<label for="newSoftwareName">Name of your Software</label>
<input type="text" name="newSoftwareName" id="newSoftwareName" 
       placeholder="Chessmaster "/>

<label for="version">Version</label>
<input type="text" name="version" id="version" placeholder="0.1.0"/>

<?php
$cond  = "WHERE `playerID`=".USER_ID;
$row = array('softwareID');
$softwareIds = selectFromTable($row, "chess_softwareDeveloper", $cond, 10);
if (count($softwareIds) > 0) {
?>
<label for="lastVersionID">lastVersionID</label>
<select name="lastVersionID" id="lastVersionID">
    <option value="0">New software</option>
<?php foreach($softwareIds as $id) {
    $cond  = "WHERE `id` = ".$id['softwareID'];
    $rows  = array('name', 'version');
    $result= selectFromTable($rows, "chess_software", $cond);
    echo '<option value="'.$id.'">'.$result['name'].' '.$result['version'];
    echo '</option>';
}
?>
</select>
<?php } ?>
<label for="changelog">Changelog</label>
<textarea name="changelog" id="changelog"></textarea>

<input type="submit" />
</fieldset>
</form>

<h1>List</h1>
<table>
<tr>
<th>Name of your Software</th>
<th>Version</th>
<th>action</th>
<th>team</th>
<th>add user by username</th>
<th>languages</th>
<th>add language</th>
</tr>

<?php
if ($currentSoftwareID == 0){echo '<tr class="current">';}
else {echo '<tr>';}
?>   
<td>Human player</td>
<td><a href="http://en.wikipedia.org/wiki/Human">Homo sapiens</a></td>
<td><a href="my_software?setCurrent=0">set to current</a></td>
<td>You</td>
<td>-</td>
<td>English</td>
<td>-</td>
</tr>
<?php foreach($softwareIds as $id) {
    $id = $id['softwareID'];
    $cond  = "WHERE `id` = ".$id['softwareID'];
    $rows  = array('name', 'version');
    $result= selectFromTable($rows, "chess_software", $cond);
    if ($id == $currentSoftwareID){
        echo '<tr class="current">';
    } else {
        echo '<tr>';
    }
    echo '<td>'.$result['name'].'</td><td>'.$result['version'].'</td>';
    echo '<td><a href="my_software?setCurrent='.$id.'">set to current</a></td>';

    // List of teammates
    $cond = "WHERE `softwareID`=$id";
    $row  = array('playerID', 'task');
    $playerIDs = selectFromTable($row, "chess_softwareDeveloper", $cond, 100);
    echo '<td><ul>';
    foreach($playerIDs as $uID){
        $cond = "WHERE `id`=".$uID['playerID'];
        $result = selectFromTable(array('uname'), "chess_players", $cond);
        echo "<li>".$result['uname']." (".$uID['task'].")";
        if ($uID['playerID'] != USER_ID){
            echo '<a href="my_software.php?deleteTeammate='.$uID['playerID'].'&softwareID='.$id.'">';
            echo '<img src="styling/delete.png" />';
            echo '</a>';
        }
        echo "</li>";
    }
    echo '</ul></td>';
    ?>
    <td>
    <form method="get" action="my_software.php">
        <input type="text" name="addTeammate"/>
        <input type="text" name="task" value="" placeholder="developer">
        <input type="hidden" name="softwareID" value="<?php echo $id;?>">
        <input type="submit" value="add"/>
    </form>
    </td>
    <?php
    echo '<td>';
    $cond = "WHERE `softwareID`=$id";
    $results=selectFromTable(array("languageID"), "chess_softwareLangages", $cond, 10);
    $i = 0;
    foreach($results as $langID){
        $i++;
        echo $langIndex[$langID['languageID']];
        echo '<a href="my_software.php?deleteLang='.$langID['languageID'].'&softwareID='.$id.'">';
        echo '<img src="styling/delete.png" />';
        echo '</a>';
        if ($i < count($results)){echo ", ";}
    }
    echo '</td>';
    ?>
    <td>
    <form method="get" action="my_software.php">
        <input type="text" name="addLanguage"/>
        <input type="hidden" name="softwareID" value="<?php echo $id;?>">
        <input type="submit" value="add"/>
    </form>
    </td>
    <?php
    echo '</tr>';
}
?>
</body>
</html>
