<?
/**
 * @author: Martin Thoma
 * get a list of links to players you can challenge
 * */

require ('wrapper.inc.php');
if(USER_ID === false){exit("Please <a href='login.wrapper.php'>login</a>");}

if(isset($_GET['playerID'])){
    $id    = intval($_GET['playerID']);
    $table = 'chess_players';
    $cond  = 'WHERE `id` = '.$id.' AND `id` != '.USER_ID;
    $query = "SELECT `uname` FROM `$table` $cond LIMIT 1";
    $row = selectFromDatabase($query, array('uname'), $table, $cond);
    $challengedUser = $row['uname'];
    if($row !== false){
        $table = 'chess_currentGames';
        $cond  = "WHERE `whitePlayerID` = ".USER_ID." AND `blackPlayerID`=$id";
        $query = "SELECT `id` FROM `$table` $cond";
        $row = selectFromDatabase($query, array('id'), $table, $cond);
        if($row !== false){
            echo "You've already challenged the player '".$challengedUser."'. ";
            echo "The current game has the id '".$row['id']."'.";
        } else {
            $table = "chess_players";    
            $cond  = "WHERE `id` = ".USER_ID." OR `id`=$id";      
            $rows  = array('id', 'currentChessSoftware');  
            $query = "SELECT `id`, `currentChessSoftware` ";
            $query.= "FROM `$table` $cond";
            $result = selectFromDatabase($query, $rows, $table, $condition, 2);

            if($result[0]['id'] == USER_ID){
                $whitePlayerSoftwareID = $result[0]['currentChessSoftware'];
                $blackPlayerSoftwareID = $result[1]['currentChessSoftware'];
            } else {
                $blackPlayerSoftwareID = $result[0]['currentChessSoftware'];
                $whitePlayerSoftwareID = $result[1]['currentChessSoftware'];
            }
            $table = 'chess_currentGames';
            $keyValuePairs= array('whitePlayerID'=>USER_ID, 
                                  'blackPlayerID'=>$id,
                                  'whitePlayerSoftwareID'=>$whitePlayerSoftwareID,
                                  'blackPlayerSoftwareID'=>$blackPlayerSoftwareID);
            $query = "INSERT INTO `$table` (`whitePlayerID` ,`blackPlayerID`, ";
            $query.= "`whitePlayerSoftwareID`, `blackPlayerSoftwareID`) ";
            $query.= "VALUES ('".USER_ID."',  '$id', '$whitePlayerSoftwareID', '$blackPlayerSoftwareID');";

            insertIntoDatabase($query, $keyValuePairs, $table);

            echo "You have started a game against the player with the ID ".$id.". ";
            echo "The Username of this Player is '$challengedUser'.";
        }
    } else {
        echo "You have selected an incorrect ID.";
    }
} else {
    $table = 'chess_players';
    $rows  = array('id', 'uname');
    $cond  = "WHERE `id` != ".USER_ID;
    $query = "SELECT  `id` ,  `uname` FROM  `$table` $cond LIMIT 10";
    $row = selectFromDatabase($query, $rows, $table, '', 10);

    echo '<ul>';
    foreach($row as $playerArray){
        echo '<li><a href="challengePlayer.php?playerID='.$playerArray['id'].'">'.$playerArray['uname'].'</a></li>';
    }
    echo '</ul>';
}

?>
