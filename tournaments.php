<?
/**
 * @author: Martin Thoma
 * specify the chess software
 * */
require ('wrapper.inc.php');
if(USER_ID === false){exit("Please <a href='login.wrapper.php'>login</a>");}

if(isset($_POST['tournamentName'])){
    $tournamentName = mysql_real_escape_string($_POST['tournamentName']);
    $description    = mysql_real_escape_string($_POST['description']);
    $password       = md5($_POST['password']);
    $closingDate    = mysql_real_escape_string($_POST['closingDate']);

    $keyValue = array('name'=>$tournamentName, 'description'=>$description);
    $keyValue['password'] = $password;
    $keyValue['closingDate'] = $closingDate;
    insertIntoTable($keyValue, "chess_turnaments");
}

if(isset($_GET['challengePlayerID'])){
    $id = intval($_GET['challengePlayerID']);
    $tournamentID = intval($_GET['tournamentID']);

    $cond  = 'WHERE `id` = '.$id.' AND `id` != '.USER_ID;
    $row = selectFromTable(array('uname'), 'chess_players', $cond);
    $challengedUser = $row['uname'];
    if($row !== false){
        $cond = "WHERE (`whitePlayerID` = ".USER_ID." AND `blackPlayerID`=$id)";
        $cond.= " OR (`whitePlayerID` = $id AND `blackPlayerID`=".USER_ID.") ";
        $cond.= "AND tournamentID=$tournamentID";
        $row = selectFromTable(array('id'), 'chess_currentGames', $cond);
        if($row !== false){
            echo "You've already challenged the player '".$challengedUser."'. ";
            echo "The current game has the id '".$row['id']."'.";
        } else {
            #Do both players participate in tournament?
            $rows = array('playerID', 'gamesWon', 'gamesPlayed');
            $cond = "WHERE turnamentID=$tournamentID AND (playerID=".USER_ID;
            $cond.= " OR playerID=".$id.")";
            $results=selectFromTable($rows, 'chess_turnamentPlayers', $cond, 2);
            if(count($results)<2){
                exit("Either you or your opponent is not part of the tournament.");
            }
            #Have both players played the same number of games?
            if($results[0]['gamesPlayed'] != $results[1]['gamesPlayed']){
                exit("You haven't won the same number of mathes as ".
                     "your opponent.");
            }
            #Are you both still in the tournament (no lost games)?
            if($results[0]['gamesWon'] != $results[0]['gamesPlayed']){
                exit("You have lost at least one game.");
            }



            $cond  = "WHERE `id` = ".USER_ID." OR `id`=$id";      
            $rows  = array('id', 'currentChessSoftware');  
            $result = selectFromTable($rows, "chess_players", $condition, 2);

            if($result[0]['id'] == USER_ID){
                $whitePlayerSoftwareID = $result[0]['currentChessSoftware'];
                $blackPlayerSoftwareID = $result[1]['currentChessSoftware'];
            } else {
                $blackPlayerSoftwareID = $result[0]['currentChessSoftware'];
                $whitePlayerSoftwareID = $result[1]['currentChessSoftware'];
            }
            $keyValuePairs= array('whitePlayerID'=>USER_ID, 
                               'blackPlayerID'=>$id,
                               'whitePlayerSoftwareID'=>$whitePlayerSoftwareID,
                               'blackPlayerSoftwareID'=>$blackPlayerSoftwareID);
            insertIntoTable($keyValuePairs, 'chess_currentGames');

            echo "You have started a game against the player with the ID ".$id;
            echo ". The Username of this Player is '$challengedUser'.";
        }
    } else {
        echo "You have selected an incorrect ID.";
    }




}

if(isset($_GET['enterID'])){
    $tournamentID = intval($_GET['enterID']);
    $pass = md5($_GET['password']);
    $cond = "WHERE id=$tournamentID AND password='".$pass."' AND closingDate > NOW()";
    $result = selectFromTable(array('id'), 'chess_turnaments', $cond);
    if($result['id'] != $tournamentID){exit("Wrong password or tournament is already closed.");}

    $keyValue = array('turnamentID'=>$tournamentID, 'playerID'=>USER_ID);
    $id = insertIntoTable($keyValue, "chess_turnamentPlayers");
    if($id > 0){
        echo msg("You joined the tournament with the ID $tournamentID");
    } else{
        echo msg("I couldn't add you to the tournament. Do you already participate?");
    }
}

if(isset($_GET['deleteParticipation'])){
    $tournamentID = intval($_GET['deleteParticipation']);
    $cond = "WHERE turnamentID=$tournamentID AND playerID=".USER_ID;
    $result = selectFromTable(array('id'), 'chess_turnamentPlayers', $cond);
    deleteFromTable('chess_turnamentPlayers', $result['id']);
}

$cond = "WHERE `playerID`=".USER_ID;
$result = selectFromTable(array('turnamentID'), 'chess_turnamentPlayers', $cond, 100);
$myParticipations = array();
foreach($result as $row){
    $myParticipations[] = $row['turnamentID'];
}

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="styling/default.css" />
</head>
<body>

<?
if (isset($_GET['getDetails'])){
    $id = intval($_GET['getDetails']);
    echo '<h1>Details of tournament</h1>';
    echo '<table>';
    echo '<tr>';
    echo '<th>playerID</th>';
    echo '<th>joinedDate</th>';
    echo '<th>games won / played</th>';
    echo '<th>challenge</th>';
    echo '</tr>';
    $rows = array();
    $rows[] = 'id';
    $rows[] = 'playerID';
    $rows[] = 'turnamentNumber';
    $rows[] = 'joinedDate';
    $rows[] = 'gamesWon';
    $rows[] = 'gamesPlayed';
    $cond   = "WHERE turnamentID=$id";
    $results = selectFromTable($rows, 'chess_turnamentPlayers', $cond, 100);
    foreach($results as $result){
        echo '<tr>';
        echo '<th>'.$result['playerID'].'</th>';
        echo '<th>'.$result['joinedDate'].'</th>';
        echo '<th>'.$result['gamesWon'].' / '.$result['gamesPlayed'].'</th>';
        echo '<th>';
        if($result['playerID'] != USER_ID){
            echo '<a href="tournaments.php?challengePlayerID=';
            echo $result['playerID'].'&tournamentID='.$id.'">challenge</a>';
        } else {
            echo '-';
        }
        echo '</th>';
        echo '</tr>';
    }
    echo '</table>';
}

?>


<h1>New tournament</h1>
<form method="post" action="tournaments.php">
<fieldset>
<legend>Initiate new tournament</legend>

<label for="tournamentName">Name</label>
<input type="text" id="tournamentName" name="tournamentName" />

<label for="description">description</label>
<input type="text" id="description" name="description" />

<label for="password">password (leaf blank for no password)</label>
<input type="password" id="password" name="password" />

<label for="closingDate">closingDate</label>
<input type="text" id="closingDate" name="closingDate" value="
<?
$time = mktime () + 7*24*60*60;
echo date("Y-m-d H:i:s", $time);

?>
" />

<input type="submit" />
</fieldset>
</form>

<h1>All tournaments</h1>
<table border="1">
<tr>
    <th>Name</th>
    <th>Password</th>
    <th>Description</th>
    <th>Initiation Date</th>
    <th>Closing Date</th>
    <th>Status</th>
    <th>action</th>
</tr>
<?
$rows  = array('id','name','password','description','initiationDate');
$rows[]= 'closingDate';
$rows[]= 'status';
$cond  = "ORDER BY `initiationDate` DESC";
$results = selectFromTable($rows, 'chess_turnaments', $cond, 100);
foreach($results as $result){
    if(in_array($result['id'], $myParticipations)){
        echo '<tr class="current">';
    } else {
        echo '<tr>';
    }
    echo "<td>".$result['name']."</td>";
    echo "<td>";
    if($result['password'] == "d41d8cd98f00b204e9800998ecf8427e"){
        echo "No";
    } else {
        echo "Yes";
    }
    echo "</td>";
    echo "<td>".$result['description']."</td>";
    echo "<td>".$result['initiationDate']."</td>";
    echo "<td>".$result['closingDate']."</td>";
    echo "<td>".$result['status']."</td>";
    echo '<td>';
    if($result['status'] == 'openForInvitations'){
        if(in_array($result['id'], $myParticipations)){
            echo '<a href="tournaments.php?deleteParticipation='.$result['id'].'">delete Participation</a>';
        } else {
            if($result['password'] == "d41d8cd98f00b204e9800998ecf8427e"){
                echo '<a href="tournaments.php?enterID='.$result['id'].'">participate</a>';
            } else {
                echo '<form method="get" action="tournaments.php">';
                echo '<input type="hidden" name="enterID" value="'.$result['id'].'"/>';
                echo '<input type="password" name="password" />';
                echo '<input type="submit" value="participate" />';
                echo '</form>';
            }
        }
    }
    echo ' <a href="tournaments.php?getDetails='.$result['id'].'">details</a>';
    echo '</td>';
    echo "</tr>";
}
?>
</body>
</html>
