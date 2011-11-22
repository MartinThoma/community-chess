<?php
/**
 * get a list of links to players you can challenge
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

require_once 'wrapper.inc.php';
require_once 'additional.inc.php';
require_once 'i18n.inc.php';

if (USER_ID === false) exit(_("Please <a href='login.wrapper.php'>login</a>"));
$t = new vemplator();
$t->assign('USER_ID', USER_ID);

if (isset($_GET['user_id'])) {
    challengeUser($_GET['user_id'], $t);
} else {
    // look at the fix if you change something here!
    $stmt = $conn->prepare('SELECT `user_id`, `user_name` FROM '.USERS_TABLE.' '.
            'WHERE `user_id` != :uid '.
            'LIMIT 10');
    $stmt->bindValue(":uid", USER_ID);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Quick'n dirt fix: 
    // The template tries to acces $possibleOpponents[$i]['user_name']:
    $fixedRows = array();
    foreach ($rows as $row) {
        $fixedRows[] = array('user_id'=>$row['user_id'], 
                           'user_name'=>$row['user_name']);
    }

    $t->assign('possibleOpponents', $fixedRows);
}
echo $t->output('challengePlayer.html');
?>
