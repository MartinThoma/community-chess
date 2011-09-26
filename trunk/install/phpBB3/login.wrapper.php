<?php 
include_once("phpbb.php"); 

$cp = $_GET['cp'];

if ($cp == "logout") {
    $user->session_kill();
    $user->session_begin();
    echo "Successfully Logged Out.";
}

?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Community Chess with phpBB3</title>
</head>
<body>

<?php

if ($user->data['user_id'] == ANONYMOUS) {
    ?>
    Welcome, anomalous!
    <form method="POST" action="../phpBB3/ucp.php?mode=login">
    Username: <input type="text" name="username" size="20"><br />
    Password: <input type="password" name="password" size="20"><br />
    Remember Me?: <input type="checkbox" name="autologin"><br />
    <input type="submit" value="Login" name="login">
    <input type="hidden" name="redirect" value="../community-chess/test.php">
    </form>
    <?php
} else {
    ?>
    Welcome back, <?php echo $user->data['username_clean']; ?> | You have <?php echo $user->data['user_unread_privmsg']; ?> new messages
    <a href="somefile.php?cp=logout">Log Out</a>
    <?php 
    print_r($user->data);
} ?>
</body>
</html>
