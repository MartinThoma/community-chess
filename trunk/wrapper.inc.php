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

/******************************************************************************/
/* Table Names (constants)                                                    */
/******************************************************************************/
$table_prefix = 'chess_';
define('USERS_TABLE',                      $table_prefix.'users');
define('GAMES_TABLE',                      $table_prefix.'games');
define('TURNAMENTS_TABLE',                 $table_prefix.'turnaments');
define('TURNAMENT_PLAYERS_TABLE',          $table_prefix.'turnamentPlayers');
define('SOFTWARE_TABLE',                   $table_prefix.'software');
define('SOFTWARE_USER_TABLE',              $table_prefix.'softwareUsers');
define('SOFTWARE_DEVELOPER_TABLE',         $table_prefix.'softwareDeveloper');
define('SOFTWARE_LANGUAGES_TABLE',         $table_prefix.'softwareLanguages');
define('LANGUAGES_TABLE',                  $table_prefix.'languages');
define('GAMES_THREEFOLD_REPETITION_TABLE', $table_prefix.'gamesThreefoldRepetition');
/******************************************************************************/
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

    $user_id       = mysql_real_escape_string($_SESSION['user_id']);
    $condition     = "WHERE `user_id`='$user_id' ";
    $row           = selectFromTable(array('user_id'), USERS_TABLE, $condition);

    if ($row['user_id'] === $_SESSION['user_id'] AND $row['user_id'] > 0) {
        return $row['user_id'];
    } else {
        return false;
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
    $row    = implode("`,`", $rows);
    $query  = "SELECT `$row` FROM `$table` $condition LIMIT $limit";
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
    $table = mysql_real_escape_string($table);
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
    $condition  = 'WHERE user_name="'.mysql_real_escape_string($user_name);
    $condition .= '" AND user_password="'.md5($user_password).'"';
    $row        = selectFromTable(array('user_id'), USERS_TABLE, $condition);
    if ($row !== false) {
        $_SESSION['user_id']       = $row['user_id'];
        if ($redirect) {
            header('Location: index.php');
        }
    }
    return session_id();
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
    $row            = selectFromTable(array('user_name'), USERS_TABLE, $cond);
    $challengedUser = $row['user_name'];
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
                               'blackPlayerSoftwareID'=>$blackPlayerSoftwareID);

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
?>
