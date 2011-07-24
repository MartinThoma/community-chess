<?
/**
 * @author: Martin Thoma
 * The following functions could / should be replaced by yours.
 * */
error_reporting(E_ALL);
session_start();
define('MYSQL_HOST',     'localhost');
define('MYSQL_USER',     'root');
define('MYSQL_PASS',     'fesonlatlor');
define('MYSQL_DATABASE', 'chess');


mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS) OR 
          die("Could not connect to database. Errormessage:".mysql_error());
mysql_select_db(MYSQL_DATABASE) OR 
          die("Could not use database, Errormessage: ".mysql_error());
mysql_set_charset('utf8'); 

define("USER_ID", getUserID() );


/******************************************************************************/
/* functions
*******************************************************************************/
function getUserID() {
    /* Return the UserID if the User is logged in.
                         else return false */

    /* Begin of code which can be replaced by your code */
    if (!isset($_SESSION['UserId']) OR !isset($_SESSION['Password'])) {
        return false;
    };

    $uid    = mysql_real_escape_string($_SESSION['UserId']);
    $upass  = mysql_real_escape_string($_SESSION['Password']);

    $table = 'chess_players';
    $condition = "WHERE id='$uid' AND upass='$upass'";
    $query = "SELECT id FROM `$table` $condition";
    $row = selectFromDatabase($query, 'id',$table, $condition);

    if($row['id'] === $_SESSION['UserId'] AND $row['id'] > 0){
        return $row['id'];
    } else {
        return false;
    }
    /* End of code which can be replaced by your code */
}

function selectFromDatabase($query, $row, $table, $condition, $limit = 1) {
    /* $query = SELECT $row FROM $table WHERE $condition;
       note that row is an array of all rows. I never use "*"
       This function should return the associative array 
       Its always only the first row */
    /* Begin of code which can be replaced by your code */
    $result = mysql_query($query);
    if($limit == 1){
        $row = mysql_fetch_assoc($result);
    } else {
        $row = array();
        while($a = mysql_fetch_assoc($result)){
            $row[] = $a;
        }
    }
    return $row;
    /* End of code which can be replaced by your code */
}

function insertIntoDatabase($query, $keyValuePairs, $table) {
    /* $query = INSERT INTO `$table` (`key1` ,`key2`, ...) 
                VALUES ('value1', 'value2'); */
    /* Begin of code which can be replaced by your code */
    mysql_query($query);

    return 0;
    /* End of code which can be replaced by your code */
}

function updateDataInDatabase($query, $table) {
    /* $query = INSERT INTO `$table` (`key1` ,`key2`, ...) 
                VALUES ('value1', 'value2'); */
    /* Begin of code which can be replaced by your code */
    mysql_query($query);

    return 0;
    /* End of code which can be replaced by your code */
}

function deleteFromDatabase($table, $id) {
    /* Begin of code which can be replaced by your code */
    $table = mysql_real_escape_string($table);
    $id = intval($id);
    $query = "DELETE FROM `$table` WHERE `$table`.`id` = $id LIMIT 1";
    mysql_query($query);

    return 0;
    /* End of code which can be replaced by your code */
}

    


?>
