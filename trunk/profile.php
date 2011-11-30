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
    $stmt = $conn->prepare('SELECT `user_name` FROM '.USERS_TABLE.' '.
            'WHERE `user_id` != :uid LIMIT 1');
    $stmt->bindValue(":uid", USER_ID);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $t->assign('username', $row['user_name']);
} else {
    exit('ERROR: No username given per GET and not logged in.');
}

$stmt = $conn->prepare('SELECT `id`, `tournamentID`, `outcome` FROM '.
        USERS_TABLE.' WHERE `outcome` > 0 AND '.
        '`whiteUserID` = :uid OR `blackUserID` = :uid LIMIT 10');
$stmt->bindValue(":uid", USER_ID);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$t->assign('games', $result);

echo $t->output('profile.html');
?>
