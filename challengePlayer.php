<?php
/**
 * @author: Martin Thoma
 * get a list of links to players you can challenge
 * */

require ('wrapper.inc.php');
if (USER_ID === false){exit("Please <a href='login.wrapper.php'>login</a>");}

if (isset($_GET['playerID'])){
    $id    = intval($_GET['playerID']);
    $cond  = 'WHERE `id` = '.$id.' AND `id` != '.USER_ID;
    $row = selectFromTable(array('uname'), 'chess_players', $cond);
    $challengedUser = $row['uname'];
    if ($row !== false){
        $cond  = "WHERE `whitePlayerID` = ".USER_ID." AND `blackPlayerID`=$id";
        $row = selectFromTable(array('id'), 'chess_currentGames', $cond);
        if ($row !== false){
            echo "You've already challenged the player '".$challengedUser."'. ";
            echo "The current game has the id '".$row['id']."'.";
        } else {
            $cond  = "WHERE `id` = ".USER_ID." OR `id`=$id";      
            $rows  = array('id', 'currentChessSoftware');  
            $result = selectFromTable($rows, "chess_players", $condition, 2);

            if ($result[0]['id'] == USER_ID){
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
} else {
    $rows  = array('id', 'uname');
    $cond  = "WHERE `id` != ".USER_ID;
    $row = selectFromTable($rows, 'chess_players', $cond, 10);

    echo '<ul>';
    foreach($row as $playerArray){
      echo '<li><a href="challengePlayer.php?playerID='.$playerArray['id'].'">'.
      $playerArray['uname'].'</a></li>';
    }
    echo '</ul>';
}

?>
