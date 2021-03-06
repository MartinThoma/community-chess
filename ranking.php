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
// global $conn;
$stmt = $conn->prepare('SELECT COUNT(`user_id`) AS `usercount` FROM '.
                        USERS_TABLE.' LIMIT 1');
$stmt->execute();
$result    = $stmt->fetch(PDO::FETCH_ASSOC);
$usercount = $result['usercount'];

$currentPage = 1;
$t->assign('currentPage', $currentPage);

$t->assign('fromRank', ($currentPage-1)*100+1);
$t->assign('toRank', ($currentPage)*100);

$t->assign('maxPages', ceil($usercount/100));
$t->assign('nrOfUsers', $usercount);

// Get users with ranks
$query  = 'SELECT  `rank` , `user_id` ,  `pageRank` ,  `username` ';
$query .= 'FROM `'.USERS_TABLE.'` WHERE `rank` > 0 ';
$query .= 'ORDER BY  `rank` ';
$query .= 'LIMIT 100';
$sth    = $conn->query($query);
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

$t->assign('ranking', $result);

echo $t->output('ranking.html');
?>
