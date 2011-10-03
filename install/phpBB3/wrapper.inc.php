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

include_once 'phpbb3.php';
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
    if($user->data['user_id'] == ANONYMOUS) {
        return false;
    } else {
        return $user->data['user_id'];
    }
    /* End of code which can be replaced by your code */
}

/** This function gets the Software-ID of the User
 *
 * @param int $user_id the ID of the user
 *
 * @return int
 */
function getUserSoftwareID($user_id)
{
    $c   = "WHERE `user_id`='$user_id'";
    $row = selectFromTable(array('software_id'), SOFTWARE_USER_TABLE, $c);
    return $row['software_id'];
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
function selectFromTable($rows, $table, $condition, $limit = 1)
{
    /* Begin of code which can be replaced by your code */
    global $db;
    $row    = implode("`,`", $rows);
    $query  = "SELECT `$row` FROM `$table` $condition LIMIT $limit";
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

	$sql = 'INSERT INTO ' . $table . ' ' . $db->sql_build_array('INSERT', $keyValuePairs);
	$db->sql_query($sql);

    return mysql_insert_id();
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
    $table = mysql_real_escape_string($table);
    $id    = (int) $id;
    $query = "DELETE FROM `$table` WHERE `$table`.`id` = $id LIMIT 1";
    $db->sql_query($query);

    return 0;
    /* End of code which can be replaced by your code */
}

/** This function makes user-challenges
 * 
 * @param int    $user_id the challenged user_id
 * @param object $t       template-object
 *
 * @return string message with the result
 */
function challengeUser($user_id, $t)
{
    $id             = (int) $user_id;
    $cond           = 'WHERE `user_id` = '.$id.' AND `user_id` != '.USER_ID;
    $row            = selectFromTable(array(USER_NAME_COLUMN), USERS_TABLE, $cond);
    $challengedUser = $row[USER_NAME_COLUMN];
    if ($row !== false) {
        $cond = 'WHERE `whiteUserID` = '.USER_ID." AND `blackUserID`=$id ";
        $cond.= 'AND `outcome` = -1';
        $row  = selectFromTable(array('id'), GAMES_TABLE, $cond);
        if ($row !== false) {
            $t->assign('alreadyChallengedPlayer', $challengedUser);
            $t->assign('alreadyChallengedGameID', $row['id']);
            return "ERROR:You have already challenged this player. ".
                   "This Game has the gameID ".$row['id'].".";
        } else {
            $cond   = "WHERE `user_id` = ".USER_ID." OR `user_id`=$id";      
            $rows   = array('user_id', 'software_id');  
            $result = selectFromTable($rows, SOFTWARE_USER_TABLE, $cond, 2);

            // Maybe one of the rows doesn't exist?
            checkSoftwareTableEntry(USER_ID);
            checkSoftwareTableEntry($id);

            if ($result[0]['user_id'] == USER_ID) {
                $whitePlayerSoftwareID = $result[0]['software_id'];
                $blackPlayerSoftwareID = $result[1]['software_id'];
            } else {
                $blackPlayerSoftwareID = $result[0]['software_id'];
                $whitePlayerSoftwareID = $result[1]['software_id'];
            }
            $keyValuePairs = array('whiteUserID'=>USER_ID, 
                               'blackUserID'=>$id,
                               'whitePlayerSoftwareID'=>$whitePlayerSoftwareID,
                               'blackPlayerSoftwareID'=>$blackPlayerSoftwareID,
                               'moveList'=>'');

            $gameID = insertIntoTable($keyValuePairs, GAMES_TABLE);

            $t->assign('startedGamePlayerID', $id);
            $t->assign('startedGamePlayerUsername', $challengedUser);
            $t->assign('startedGameID', $gameID);
            return "New game started with gameID $gameID.";
        }
    } else {
        $t->assign('incorrectID', true);
    }
}

/** This function checks if a row for the user in SOFTWARE_USER_TABLE does exist
 *
 * @param int $user_id the id of the user who gets the table check
 *
 * @return int always 0
 */
function checkSoftwareTableEntry($user_id)
{
    $cond = 'WHERE `user_id` = '.$user_id;
    $row  = selectFromTable(array('software_id'), SOFTWARE_USER_TABLE, $cond);
    if ($row == false) {
        $keyValuePairs = array();
        $keyValuePairs['user_id']     = $user_id;
        $keyValuePairs['software_id'] = 0;
        insertIntoTable($keyValuePairs, SOFTWARE_USER_TABLE);
    }
}

?>