<?php
/**
 * ranking
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
if (USER_ID === false) exit("Please <a href='login.wrapper.php'>login</a>");
$t = new vemplator();
$t->assign('USER_ID', USER_ID);

// Get number of users
$row       = 'COUNT(  `user_id` ) AS  `usercount`';
$result    = selectFromTable($row, USERS_TABLE);
$usercount = $result['usercount'];

$currentPage = 1;
$t->assign('currentPage', $currentPage);

$t->assign('fromRank', ($currentPage-1)*100+1);
$t->assign('toRank', ($currentPage)*100);

$t->assign('maxPages', ceil($usercount/100));
$t->assign('nrOfUsers', $usercount);

// Get users with ranks
$query  = 'SELECT  `rank` , a.`user_id` ,  `pageRank` ,  `'.USER_NAME_COLUMN.'` AS ';
$query .= '`user_name` ';
$query .= 'FROM  `chess_userAdditionalInformation` AS b,  `chess_users` AS a ';
$query .= 'WHERE b.`user_id` = a.`user_id` ';
$query .= 'ORDER BY  `rank` ';
$query .= 'LIMIT 100';
$result = selectDirect($query);
$t->assign('ranking', $result);

echo $t->output('ranking.html');
?>
