<?php
/**
 * @author: Martin Thoma
 * */

require ('wrapper.inc.php');
if (USER_ID === false){exit("Please <a href='login.wrapper.php'>login</a>");}
$t = new vemplator();
$t->assign('USER_ID', USER_ID);
echo $t->output('index.html');
?>
