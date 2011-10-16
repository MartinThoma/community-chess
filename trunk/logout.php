<?php
/**
 * logout
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

if (!isset($_SESSION)) session_start();

if (session_destroy() ) {
    echo "Logged out successfully.";
} else {
    echo "Error occured while logging out.";
}

?>
