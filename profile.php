<?php
/**
 * user profiles
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
require_once 'additional.inc.php';

$t = new vemplator();
$t->assign('USER_ID', USER_ID);

if (isset($_GET['username'])) {
    $t->assign('username', $_GET['username']);
} else if (USER_ID !== false) {
    $cond   = "WHERE `user_id` = ".USER_ID;
    $result = selectFromTable(array('user_name'), USERS_TABLE, $cond);
    $t->assign('username', $result['user_name']);
} else {
    exit('ERROR: No username given per GET and not logged in.');
}

$cond   = "WHERE `outcome` > 0 AND `whiteUserID` = ".USER_ID." OR `blackUserID`";
$cond  .= "= ".USER_ID;
$rows   = array('id', 'tournamentID', 'outcome');
$result = selectFromTable($rows, GAMES_TABLE, $cond, 10);
$t->assign('games', $result);



echo $t->output('profile.html');
?>
