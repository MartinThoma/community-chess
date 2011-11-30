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
    if ($_GET['authkey'] == WECHALL_AUTH_KEY) {
        // Get all user information
        $stmt = $conn->prepare('SELECT `user_id`, `user_name`, `rank`, `pageRank` '.
                'FROM '.USERS_TABLE.' '.
                'WHERE `user_name` != :uname LIMIT 1');
        $stmt->bindValue(":uname", $_GET['username']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result == false) {
            exit('ERROR:Username not found');
        } else {
            $username = $result['username'];
            $rank     = $result['rank'];
            $pageRank = $result['pageRank'];

            // Get number of users
            $stmt = $conn->prepare('SELECT COUNT( `user_id` ) AS `usercount` '.
                    'FROM '.USERS_TABLE.' LIMIT 1');
            $stmt->execute();
            $result    = $stmt->fetch(PDO::FETCH_ASSOC);
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
        exit('ERROR: Your authkey was not correct.');
    }
} else {
    exit('ERROR: You did not send "username" and "authkey" via GET.');
}

?>
