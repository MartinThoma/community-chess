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

if (isset($_GET['username']) and isset($_GET['authkey'])) {
    $username = mysql_real_escape_string($_GET['username']);
    $authkey  = mysql_real_escape_string($_GET['authkey']);
    if ($authkey == WECHALL_AUTH_KEY) {
        // Get all user information
        $condition = "WHERE `user_name` = '$username'";
        $result    = selectFromTable(array('user_id'), USERS_TABLE, $condition);
        if ($result == false) {
            echo 'ERROR:Username not found';
        } else {
            $condition = "WHERE `user_id` = '".$result['user_id']."'";
            $result    = selectFromTable(array('rank', 'pageRank'), 
                                         USER_INFO_TABLE, $condition);
            $rank      = $result['rank'];
            $pageRank  = $result['pageRank'];

            // Get number of users
            $row       = 'COUNT(  `user_id` ) AS  `usercount`';
            $result    = selectFromTable($row, USERS_TABLE);
            $usercount = $result['usercount'];

            // Make the WeChall output
            $score        = $pageRank;
            $maxscore     = 1; // PageRank
            $challssolved = 0; // community chess has currently no challenges
            $challcount   = 0; // community chess has currently no challenges
            echo "$username:$rank:$score:$maxscore:";
            echo "$challssolved:$challcount:$usercount";
        }
    } else {
        echo 'ERROR: Your authkey was not correct.';
    }
} else {
    echo 'ERROR: You did not send "username" and "authkey" via GET.';
}

?>
