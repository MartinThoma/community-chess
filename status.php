<?php
/**
 * @author: Martin Thoma
 * running games, who is white/black
 * */

require ('wrapper.inc.php');
if (USER_ID === false){exit("Please <a href='login.wrapper.php'>login</a>");}

$condition = "WHERE `whitePlayerID`=".USER_ID." OR `blackPlayerID`=".USER_ID;

$row = selectFromTable(array('id'), 'chess_currentGames', $condition);
echo 'Current game-IDs:<br/>';
foreach($row as $gameId){
    echo $gameId."<br/>";
}

$row = selectFromTable(array('id'), 'chess_pastGames', $condition);
echo 'Past game-IDs:<br/>';
foreach($row as $gameId){
    echo $gameId."<br/>";
}

?>
