<?php
/**
 * @author: Martin Thoma
 * */

require ('wrapper.inc.php');

if (USER_ID === false){echo 'Please <a href="login.wrapper.php">login</a>.';}
else {
    echo 'Logged in with USER_ID '.USER_ID.'.<br/>';
?>
Navigation:<br/>
<ul>
    <li><a href="status.php">status.php</a>: 
        A list of all current / past games</li>
    <li><a href="challengePlayer.php">challengePlayer.php</a>:
        Challenge a player</li>
    <li><a href="playChess.php">playChess.php</a>: play a game. You have to
        provide a gameID via GET.</li>
    <li><a href="my_software.php">my_software.php</a>: provide information 
        about the chess software you have written.</li>
    <li><a href="tournaments.php">tournaments.php</a>: create tournaments or
        participate in a tournament</li>
</ul>
<?php
}

?>
