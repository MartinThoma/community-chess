<?php
/**
 * running games, who is white/black
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
if (USER_ID === false) exit("Please <a href='login.wrapper.php'>login</a>");
$t = new vemplator();
$t->assign('USER_ID', USER_ID);

$condition = "WHERE `whitePlayerID`=".USER_ID." OR `blackPlayerID`=".USER_ID;

$rows = selectFromTable(array('id'), 'chess_currentGames', $condition);
$t->assign('currentGames', $rows);

$row = selectFromTable(array('id'), 'chess_pastGames', $condition);
$t->assign('pastGames', $row);

echo $t->output('status.html');
?>
