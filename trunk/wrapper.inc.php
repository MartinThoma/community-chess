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

$dsn = 'mysql:dbname='.DB_DATABASE.';charset=UTF-8;host='.DB_HOST;

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
    /* Begin of code which can be replaced by your code */
    if (!isset($_SESSION['user_id'])) {
        return false;
    };

    $user_id   = sqlEscape($_SESSION['user_id']);
    $condition = "WHERE `user_id`='$user_id' ";
    $row       = selectFromTable(array('user_id'), USERS_TABLE, $condition);

    if ($row['user_id'] === $_SESSION['user_id'] AND $row['user_id'] > 0) {
        return $row['user_id'];
    } else {
        return false;
    }
    /* End of code which can be replaced by your code */
}

/** Escape string used in sql query
 *
 * @param string $sql the string which should be escaped
 *
 * @return string
 */
function sqlEscape($string)
{
    /* Begin of code which can be replaced by your code */
    // Source: http://www.php.net/manual/de/function.mysql-real-escape-string.php#101248
    if(is_array($string)) 
        return array_map(__METHOD__, $string); 

    if(!empty($string) && is_string($string)) { 
        return str_replace(
            array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), 
            array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), 
            $string); 
    } 

    return $string; 
    /* End of code which can be replaced by your code */
}

/** This function selects some rows from the specified table and returns
 *  the associative array
 *
 * @param array  $rows      all rows which should be selected. I never use "*"
 * @param string $table     the table from which the rows should be selected
 * @param string $condition the condition which rows should be selected
                            might also have "ORDER BY"
 * @param int    $limit     how many rows get selected at maximum
 *
 * @return array
 */
function selectFromTable($rows, $table, $condition = '', $limit = 1)
{
    /* Begin of code which can be replaced by your code */
    if (is_array($rows)) {
        $row   = implode("`,`", $rows);
        $query = "SELECT `$row` FROM `$table` $condition LIMIT $limit";
    } else {
        $query = "SELECT $rows FROM `$table` $condition LIMIT $limit";
    }

    global $conn;
    $sth = $conn->query($query);

    if ($limit == 1) {
        $row = $sth->fetch(PDO::FETCH_ASSOC);
    } else {
        $row = $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    return $row;
    /* End of code which can be replaced by your code */
}

/** This function returns the name of the row which is id of table
 *
 * @param string  $table
 *
 * @return string id row
 */
function getIdRow($table)
{
    if ($table == CHALLENGES_TABLE) {
        $row = 'challenge_id';
    } else if ($table == USERS_TABLE) {
        $row = 'user_id';
    } else if ($table == USER_INFO_TABLE) {
        $row = 'user_id';
    } else if ($table == USERS_OPENID) {
        $row = 'userOpenID_id';
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

/** This function inserts something into a table
 *
 * @param array  $keyValuePairs all key=>values
 * @param string $table         the table in which the row should be inserted
 *
 * @return int the id of the last inserted item
 */
function insertIntoTable($keyValuePairs, $table)
{
    /* Begin of code which can be replaced by your code */
    global $conn;

    $query  = "INSERT INTO  $table (";
    $query .= implode(",", array_keys($keyValuePairs));
    $query .= ") ";
    $query .= "VALUES (";
    $query .= "'".implode("','", array_values($keyValuePairs))."'";
    $query .= ");";
    $sth = $conn->query($query);

    $condition = '';
    $i = 0;
    foreach($keyValuePairs as $key=>$val) {
        if ($i > 0) {
            $condition .= ' AND ';
        } else {
            $i++;
        }
        $condition .= "$key = '$val'";
    }

    $query = "SELECT ".getIdRow($table)." FROM $table WHERE $condition";
    $sth = $conn->query($query);
    $row = $sth->fetch(PDO::FETCH_ASSOC);

    return $row[getIdRow($table)];
    /* End of code which can be replaced by your code */
}

/** This function uptates some rows in a table
 *  $query = INSERT INTO `$table` (`key1` ,`key2`, ...) 
 *                         VALUES ('value1', 'value2');
 *
 * @param string $table     the table from which the rows should be updated
 * @param array  $keyValue  all key=>values
 * @param string $condition the condition which rows should be updated
 *
 * @return int always 0
 */
function updateDataInTable($table, $keyValue, $condition)
{

    /* Begin of code which can be replaced by your code */
    global $conn;

    $query  = "UPDATE  `$table` SET  ";
    $values = "";
    foreach ($keyValue as $key=>$value) {
        if ($value == 'CURRENT_TIMESTAMP' or 
           substr($value, 0, 6) == 'CONCAT' or
           substr($value, 0, 1) == '(' or
           substr($value, 0, 1) == '`') {
            $values .= ", `$key` = $value";
        } else {
            $values .= ", `$key` = '$value'";
        }
    }
    // remove first ","
    $query .= substr($values, 2);
    $query .= " ".$condition;
    $conn->query($query);

    return 0;
    /* End of code which can be replaced by your code */
}

/** This function logs the user in. The session variables get stored.
 * 
 * @param string  $user_name     the username
 * @param string  $user_password the plaintext password
 * @param boolean $redirect      should the user be redirected to index.php?
 *
 * @return string session_id()
 */
function login($user_name, $user_password, $redirect = true)
{
    $condition  = 'WHERE `user_name`="'.sqlEscape($user_name);
    $condition .= '" AND user_password="'.md5($user_password).'"';
    $row        = selectFromTable(array('user_id'), USERS_TABLE, $condition);
    if ($row !== false) {
        $_SESSION['user_id'] = $row['user_id'];
        if ($redirect) {
            header('Location: index.php');
        }
    }
    return session_id();
}

?>
