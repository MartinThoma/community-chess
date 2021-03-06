<?php
/**
 * The following functions could / should be replaced by yours.
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

require_once 'external/vemplator.php';
require_once 'constants.inc.php';

set_include_path('templates');

if (!isset($_SESSION)) session_start();

/* Connect to an ODBC database using driver invocation */
define('DB_HOST', 'localhost');
define('DB_USER', 'chessuser');
define('DB_PASS', 'localpass');
define('DB_DATABASE', 'chess');

$dsn = 'mysql:dbname='.DB_DATABASE.';charset=utf8;host='.DB_HOST;

try {
    $conn = new PDO($dsn, DB_USER, DB_PASS);
} catch (PDOException $e) {
    echo 'Could not connect to the database. ';
    echo 'Did you adjust the login credentials?<br/><br/>';
    echo 'Error-Message: <br/>'. $e->getMessage();
    exit();
}

define("USER_ID", getUserID());

/******************************************************************************/
/* functions                                                                  */
/******************************************************************************/
/** Return the user_id if the User is logged in. 
 *  If no user is logged in return false
 *
 * @return int
 */
function getUserID()
{
    global $conn;
    if (!isset($_SESSION['user_id'])) {
        return false;
    };

    $stmt = $conn->prepare('SELECT `user_id` FROM '.USERS_TABLE.' '.
            'WHERE `user_id`=:uid LIMIT 1');
    $stmt->bindValue(":uid", $_SESSION['user_id']);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row['user_id'] === $_SESSION['user_id'] AND $row['user_id'] > 0) {
        return (int) $row['user_id'];
    } else {
        return false;
    }
}

/** This function returns the name of the row which is id of table
 *
 * @param string $table the name of the Database table
 *
 * @return string id row
 */
function getIdRow($table)
{
    if ($table == USERS_TABLE) {
        $row = 'user_id';
    } else if ($table == USERS_OPENID) {
        $row = 'id';
    } else if ($table == TOURNAMENTS_TABLE) {
        $row = 'id';
    } else if ($table == TOURNAMENT_PLAYERS_TABLE) {
        $row = 'id';
    } else if ($table == GAMES_TABLE) {
        $row = 'id';
    } else if ($table == GAMES_THREEFOLD_REPETITION_TABLE) {
        $row = 'id';
    } else if ($table == SOFTWARE_TABLE) {
        $row = 'id';
    } else if ($table == SOFTWARE_DEVELOPER_TABLE) {
        $row = 'id';
    } else if ($table == SOFTWARE_LANGUAGES_TABLE) {
        $row = 'id';
    } else if ($table == LANGUAGES_TABLE) {
        $row = 'id';
    }
    return $row;
}

/** This function logs the user in. The session variables get stored.
 * 
 * @param string  $username     the username
 * @param string  $password the plaintext password
 * @param boolean $redirect      should the user be redirected to index.php?
 *
 * @return string session_id()
 */
function login($username, $password, $redirect = true)
{
    global $conn;
    $stmt = $conn->prepare('SELECT `user_id` FROM '.USERS_TABLE.' '.
            'WHERE `username`= :uname AND password= :upass');
    $stmt->bindValue(":uname", $username);
    $stmt->bindValue(":upass", md5($password));
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false) {
        $_SESSION['user_id'] = $row['user_id'];
        if ($redirect) {
            header('Location: index.php');
        }
    }
    return session_id();
}

?>
