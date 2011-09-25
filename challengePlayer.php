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

if (isset($_GET['user_id'])) {
    challengeUser($_GET['user_id'], $t);
} else {
    $rows = array('user_id', 'user_name');
    $cond = "WHERE `user_id` != ".USER_ID;
    $rows = selectFromTable($rows, USERS_TABLE, $cond, 10);

    $t->assign('possibleOpponents', $rows);
}
echo $t->output('challengePlayer.html');
?>
