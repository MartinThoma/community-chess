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

$stmt = $conn->prepare('SELECT `id` FROM '.GAMES_TABLE.' WHERE '.
                       '(`whiteUserID`=:uid OR `blackUserID`=:uid) '.
                       'AND `outcome` = -1 LIMIT 100');
$stmt->bindValue(":uid", USER_ID, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$t->assign('currentGames', $rows);


$stmt = $conn->prepare('SELECT `id` FROM '.GAMES_TABLE.' WHERE '.
                       '(`whiteUserID`=:uid OR `blackUserID`=:uid) '.
                       'AND `outcome` > -1 LIMIT 100');
$stmt->bindValue(":uid", USER_ID, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$t->assign('pastGames', $rows);

echo $t->output('status.html');
?>
