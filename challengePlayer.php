<?php
/**
 * get a list of links to players you can challenge
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

if (isset($_GET['playerID'])) {
    challengeUser($_GET['playerID'], $t);
} else {
    $rows = array('id', 'uname');
    $cond = "WHERE `id` != ".USER_ID;
    $rows = selectFromTable($rows, 'chess_players', $cond, 10);

    $t->assign('possibleOpponents', $rows);
}
echo $t->output('challengePlayer.html');
?>
