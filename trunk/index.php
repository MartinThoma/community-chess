<?
/**
 * @author: Martin Thoma
 * */

require ('wrapper.inc.php');

if(USER_ID === false){echo 'Please <a href="login.wrapper.php">login</a>.';}
else {
    echo 'Logged in with USER_ID '.USER_ID.'.<br/>';
?>
Navigation:<br/>
<ul>
    <li><a href="status.php">status.php</a>: 
        A list of all current / past games</li>
    <li><a href="challengePlayer.php">challengePlayer.php</a>:
        Challenge a player</li>
    <li><a href="playChess.php">playChess.php</a></li>
    <li><a href="board.php">board.php</a></li>
    <li><a href="submitMove.php">submitMove.php</a></li>
</ul>
<?
}

?>
