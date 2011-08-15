<?php
/**
 * @author: Martin Thoma
 * The following functions could / should be replaced by yours.
 * */
error_reporting(E_ALL);
if(!isset($_SESSION)){session_start();}
define('MYSQL_HOST',     'localhost');
define('MYSQL_USER',     'chessuser');
define('MYSQL_PASS',     'localpass');
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
function getUserID()
{
    /* Return the UserID if the User is logged in.
                         else return false */

    /* Begin of code which can be replaced by your code */
    if (!isset($_SESSION['UserId']) OR !isset($_SESSION['Password'])) {
        return false;
    };

    $uid    = mysql_real_escape_string($_SESSION['UserId']);
    $upass  = mysql_real_escape_string($_SESSION['Password']);

    $condition = "WHERE id='$uid' AND upass='$upass'";
    $row = selectFromTable(array('id'), 'chess_players', $condition);

    if ($row['id'] === $_SESSION['UserId'] AND $row['id'] > 0){
        return $row['id'];
    } else {
        return false;
    }
    /* End of code which can be replaced by your code */
}

function getUserSoftwareID($UserID)
{
    $c = "WHERE id='$UserID'";
    $row = selectFromTable(array('currentChessSoftware'), 'chess_players', $c);
    return $row['currentChessSoftware'];
}

function msg($text)
{
    return '<div class="infobox">'.$text.'</div>';
}

function selectFromTable($rows, $table, $condition, $limit = 1)
{
    /* note that row is an array of all rows. I never use "*"
       This function should return the associative array 
       Its always only the first row 
       $condition might also have ORDER BY */
    /* Begin of code which can be replaced by your code */
    $row    = implode(",", $rows);
    $query  = "SELECT $row FROM `$table` $condition LIMIT $limit"; 
    $result = mysql_query($query);
    if ($limit == 1){
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

function insertIntoTable($keyValuePairs, $table)
{
    /* Returns ID of inserted item */
    /* Begin of code which can be replaced by your code */
    $query = "INSERT INTO  $table (";
    $query.= implode(",", array_keys($keyValuePairs));
    $query.= ") ";
    $query.= "VALUES (";
    $query.= "'".implode("','", array_values($keyValuePairs))."'";
    $query.= ");";
    mysql_query($query);
    return mysql_insert_id();
    /* End of code which can be replaced by your code */
}

function updateDataInTable($table, $keyValue, $cond)
{
    /* $query = INSERT INTO `$table` (`key1` ,`key2`, ...) 
                VALUES ('value1', 'value2'); */
    /* Begin of code which can be replaced by your code */
    $query = "UPDATE  `$table` SET  ";

    $values= "";
    foreach($keyValue as $key=>$value){
        if ($value == 'CURRENT_TIMESTAMP' or 
           substr($value, 0, 6) == 'CONCAT' or
           substr($value, 0, 1) == '(' or
           substr($value, 0, 1) == '`'){
            $values.= ", `$key` = $value";
        } else {
            $values.= ", `$key` = '$value'";
        }
    }
    // remove first ","
    $query.= substr($values, 2);
    $query.= " ".$cond;
    mysql_query($query);

    return 0;
    /* End of code which can be replaced by your code */
}

function deleteFromTable($table, $id)
{
    /* Begin of code which can be replaced by your code */
    $table = mysql_real_escape_string($table);
    $id = intval($id);
    $query = "DELETE FROM `$table` WHERE `$table`.`id` = $id LIMIT 1";
    mysql_query($query);

    return 0;
    /* End of code which can be replaced by your code */
}

?>
