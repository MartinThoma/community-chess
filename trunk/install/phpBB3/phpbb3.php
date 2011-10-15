<?php
/**
 * The following file helps to integrate Community Chess into phpBB3
 *
 * PHP Version 5
 *
 * @category Web_Services
 * @package  Community-chess
 * @author   Another autor and Martin Thoma <info@martin-thoma.de>
 * @license  http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version  SVN: <svn_id>
 * @link     http://code.google.com/p/community-chess/
 */

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : 'forum/';
$phpEx           = substr(strrchr(__FILE__, '.'), 1);

require $phpbb_root_path . 'common.' . $phpEx;

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();
?>
