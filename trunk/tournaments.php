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

    $keyValue = array('name'=>$tournamentName, 'description'=>$description);
    insertIntoTable($keyValue, "chess_turnaments");
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

<input type="submit" />
</fieldset>
</form>

<h1>All tournaments</h1>
<?
$table = 'chess_turnaments';
$rows  = array('id','name','description');
$cond  = "ORDER BY `initiationDate` DESC";
$results = selectFromTable($rows, $table, $cond, 20);
?>

