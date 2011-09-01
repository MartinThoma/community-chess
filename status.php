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

$condition = "WHERE (`whiteUserID`=".USER_ID." OR `blackUserID`=".USER_ID.") ";
$conAppend = "AND `outcome` = -1";

$rows = selectFromTable(array('id'), 'chess_games', $condition.$conAppend, 100);
$t->assign('currentGames', $rows);

$conAppend = "AND `outcome` > -1";

$rows = selectFromTable(array('id'), 'chess_games', $condition.$conAppend, 100);
$t->assign('pastGames', $rows);

echo $t->output('status.html');
?>
