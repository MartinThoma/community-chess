<?php
/**
 * let the user login. The whole file can get replaced by your login routine.
 * Other files in this project link to this file, so you should replace it with 
 * a redirection
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

if(!isset($_SESSION)) session_start(); 

require_once 'wrapper.inc.php';
$t = new vemplator();

/** This function logs the user in. The session variables get stored.
 * 
 * @param string $uname the username
 * @param string $upass the plaintext password
 *
 * @return None
 */
function login($uname, $upass)
{
    $condition  = 'WHERE uname="'.mysql_real_escape_string($uname);
    $condition .= '" AND upass="'.md5($upass).'"';
    $row        = selectFromTable(array('id'), 'chess_players', $condition);
    if ($row !== false) {
        $_SESSION['UserId']   = $row['id'];
        $_SESSION['Password'] = md5($upass);
        header('Location: index.php');
    }
}

if (isset($_POST['username'])) {
    $uname = $_POST['username'];
    $upass = $_POST['password'];
    login($uname, $upass);
}

echo $t->output('login.html');
?>
