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
    $username = mysql_real_escape_string($_GET['username']);
    $email    = mysql_real_escape_string($_GET['email']);
    $authkey  = mysql_real_escape_string($_GET['authkey']);
    if ($authkey == WECHALL_AUTH_KEY) {
        $condition = "WHERE `user_name` = '$username' AND `user_email` = '$email'";
        $result    = selectFromTable(array('user_id'), USERS_TABLE, $condition);
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
