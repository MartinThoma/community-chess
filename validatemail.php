<?php
/**
 * WeChall integration, described in http://www.wechall.net/join_us
 * This script tests if a user/email combination is in the database
 * You need to know the authkey
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

if (isset($_GET['username']) and isset($_GET['email']) and isset($_GET['authkey'])) {
    if ($_GET['authkey'] == WECHALL_AUTH_KEY) {
        $stmt = $conn->prepare('SELECT `user_id` FROM '.USERS_TABLE.' '.
                'WHERE `username` = :username AND `email` = :email '.
                'LIMIT 1');
        $stmt->bindValue(":username", $_GET['username']);
        $stmt->bindValue(":email", $_GET['email']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result == false) {
            echo '0';
        } else {
            echo '1';
        }
    } else {
        echo 'ERROR: Your authkey was not correct.';
    }
} else {
    echo 'ERROR: You did not send "username", "email" and "authkey" via GET.';
}

?>
