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
if (USER_ID === false) exit("Please <a href='login.wrapper.php'>login</a>");
$t = new vemplator();
$t->assign('USER_ID', USER_ID);

if (isset($_GET['playerID'])) {
    $id             = intval($_GET['playerID']);
    $cond           = 'WHERE `id` = '.$id.' AND `id` != '.USER_ID;
    $row            = selectFromTable(array('uname'), 'chess_players', $cond);
    $challengedUser = $row['uname'];
    if ($row !== false) {
        $cond = "WHERE `whitePlayerID` = ".USER_ID." AND `blackPlayerID`=$id";
        $row  = selectFromTable(array('id'), 'chess_currentGames', $cond);
        if ($row !== false) {
            $t->assign('alreadyChallengedPlayer', $challengedUser);
            $t->assign('alreadyChallengedGameID', $row['id']);
        } else {
            $cond   = "WHERE `id` = ".USER_ID." OR `id`=$id";      
            $rows   = array('id', 'currentChessSoftware');  
            $result = selectFromTable($rows, "chess_players", $cond, 2);

            if ($result[0]['id'] == USER_ID) {
                $whitePlayerSoftwareID = $result[0]['currentChessSoftware'];
                $blackPlayerSoftwareID = $result[1]['currentChessSoftware'];
            } else {
                $blackPlayerSoftwareID = $result[0]['currentChessSoftware'];
                $whitePlayerSoftwareID = $result[1]['currentChessSoftware'];
            }
            $keyValuePairs = array('whitePlayerID'=>USER_ID, 
                               'blackPlayerID'=>$id,
                               'whitePlayerSoftwareID'=>$whitePlayerSoftwareID,
                               'blackPlayerSoftwareID'=>$blackPlayerSoftwareID);
            insertIntoTable($keyValuePairs, 'chess_currentGames');

            $t->assign('startedGamePlayerID', $id);
            $t->assign('startedGamePlayerUsername', $challengedUser);
        }
    } else {
        $t->assign('incorrectID', true);
    }
} else {
    $rows = array('id', 'uname');
    $cond = "WHERE `id` != ".USER_ID;
    $rows = selectFromTable($rows, 'chess_players', $cond, 10);

    $t->assign('possibleOpponents', $rows);
}
echo $t->output('challengePlayer.html');
?>
