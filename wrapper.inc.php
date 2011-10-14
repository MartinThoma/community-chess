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
define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'chessuser');
define('MYSQL_PASS', 'localpass');
define('MYSQL_DATABASE', 'chess');


mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS) OR 
          die("Could not connect to database. Errormessage:".mysql_error());
mysql_select_db(MYSQL_DATABASE) OR 
          die("Could not use database, Errormessage: ".mysql_error());
mysql_set_charset('utf8'); 

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

/** Get last inserted id after insert statement
 *
 * @return int id
 */
function lastInsertId()
{
    /* Begin of code which can be replaced by your code */
    return mysql_insert_id();
    /* End of code which can be replaced by your code */
}

/** Escape string used in sql query
 *
 * @param string $sql the string which should be escaped
 *
 * @return string
 */
function sqlEscape($sql)
{
    /* Begin of code which can be replaced by your code */
    return mysql_real_escape_string($sql);
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

    $result = mysql_query($query);
    if ($limit == 1) {
        $row = mysql_fetch_assoc($result);
    } else {
        $row = array();
        while ($a = mysql_fetch_assoc($result)) {
            $row[] = $a;
        }
    }
    return $row;
    /* End of code which can be replaced by your code */
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
    $query  = "INSERT INTO  $table (";
    $query .= implode(",", array_keys($keyValuePairs));
    $query .= ") ";
    $query .= "VALUES (";
    $query .= "'".implode("','", array_values($keyValuePairs))."'";
    $query .= ");";
    mysql_query($query);
    return lastInsertId();
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
    mysql_query($query);

    return 0;
    /* End of code which can be replaced by your code */
}

/** This function deletes one row from a table
 *
 * @param string $table the table from which the rows should be updated
 * @param int    $id    the id of the element which will be removed
 *
 * @return int always 0
 */
function deleteFromTable($table, $id)
{
    /* Begin of code which can be replaced by your code */
    $table = sqlEscape($table);
    $id    = (int) $id;
    $query = "DELETE FROM `$table` WHERE `$table`.`id` = $id LIMIT 1";
    mysql_query($query);

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
    $condition  = 'WHERE user_name="'.sqlEscape($user_name);
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
