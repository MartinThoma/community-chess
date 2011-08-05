<?
/**
 * @author: Martin Thoma
 * let the user login. The whole file can get replaced by your login routine.
 * Other files in this project link to this file, so you should replace it with 
 * a redirection
 * */
session_start();
require ('wrapper.inc.php');

function login($uname, $upass) {
    $condition = 'WHERE uname="'.mysql_real_escape_string($uname).'" AND upass="'.md5($upass).'"';
    $row = selectFromTable(array('id'), 'chess_players', $condition);
    if ($id !== false){
        $_SESSION['UserId']   = $row['id'];
        $_SESSION['Password'] = md5($upass);
        header('Location: index.php');
    }
}

if(isset($_POST['username'])){
    $uname = $_POST['username'];
    $upass = $_POST['password'];
    login($uname, $upass);
}

?>

<form method="post">
Username: <input type="text" name="username" />
Password: <input type="password" name="password" />
<input type="submit"/>
</form>
