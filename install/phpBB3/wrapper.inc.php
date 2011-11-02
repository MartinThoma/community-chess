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

require_once 'phpbb3.php';
require_once 'external/vemplator.php';
require_once 'constants.inc.php';

set_include_path('templates');

define("USER_ID", getUserID());

/******************************************************************************/
/* functions
*******************************************************************************/
/** Return the user_id if the User is logged in. 
 *  If no user is logged in return false
 *
 * @return int
 */
function getUserID()
{
    /* Begin of code which can be replaced by your code */
    global $user;
    if ($user->data['user_id'] == ANONYMOUS) {
        return false;
    } else {
        return $user->data['user_id'];
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
    global $db;

    return $db->sql_nextid();
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
    global $db;

    return $db->sql_escape($sql);
    /* End of code which can be replaced by your code */
}

/** This function selects some rows from the specified table and returns
 *  the associative array
 *
 * @param array  $query the sql query
 * @param string $limit the limit, either 1 or *
 *
 * @return array
 */
function selectDirect($query, $limit='*')
{
    /* Begin of code which can be replaced by your code */
    global $db;

    $result = $db->sql_query($query);
    if ($limit == 1) {
        $row = $db->sql_fetchrow($result);
    } else {
        $row = $db->sql_fetchrowset($result);
    }
    $db->sql_freeresult($result);
    return $row;
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
    global $db;

    if (is_array($rows)) {
        $row   = implode("`,`", $rows);
        $query = "SELECT `$row` FROM `$table` $condition LIMIT $limit";
    } else {
        $query = "SELECT $rows FROM `$table` $condition LIMIT $limit";
    }

    $result = $db->sql_query($query);
    if ($limit == 1) {
        $row = $db->sql_fetchrow($result);
    } else {
        $row = $db->sql_fetchrowset($result);
    }
    $db->sql_freeresult($result);
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
    global $db;

    $sql = 'INSERT INTO '.$table.' ' .$db->sql_build_array('INSERT', $keyValuePairs);
    $db->sql_query($sql);

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
    global $db;
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
    $db->sql_query($query);

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
    global $db;
    $table = sqlEscape($table);
    $id    = (int) $id;
    $query = "DELETE FROM `$table` WHERE `$table`.`id` = $id LIMIT 1";
    $db->sql_query($query);

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
    // TODO:Take a look at login.wrapper.php
    $condition  = 'WHERE '.USER_NAME_COLUMN.'="'.sqlEscape($user_name);
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
