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

if (isset($_POST['username'])) {
    login($_POST['username'], $_POST['password']);
}

echo $t->output('login.html');
?>
