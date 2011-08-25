<?php
/**
 * @author: Martin Thoma
 * running games, who is white/black
 * */

require ('wrapper.inc.php');
if (USER_ID === false){exit("Please <a href='login.wrapper.php'>login</a>");}
$t = new vemplator();

$condition = "WHERE `whitePlayerID`=".USER_ID." OR `blackPlayerID`=".USER_ID;

$rows = selectFromTable(array('id'), 'chess_currentGames', $condition);
$t->assign('currentGames', $rows);

$row = selectFromTable(array('id'), 'chess_pastGames', $condition);
$t->assign('pastGames', $row);

echo $t->output('status.html');
?>
