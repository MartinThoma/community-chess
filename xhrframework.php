<?php
/**
 * This framework should give the developer one lightweight framework with which
 * he can access all available functions via GET-Requests.
 * The output will NOT have any html-tags.
 * Every error-message will begin with "ERROR:"
 * If a seperator is needed (lists), a :: is used.
 * action = {'login', 'getLastMoveTime', 'challengeUser', 'whoseTurnIsIt', 
 *           'listCurrentGames', 'listPastGames', 'getBoard'}
 * gameID : Is an Integer and needed for some actions
 * user_id: Is an Integer and needed for some actions
 * move   : Submit a move    (e.g.      move=1214 or      move=1718q)
 * iccfalpha: Submit a move (e.g. iccfalpha=a2a4 or iccfalpha=a7a8q)
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
if(!isset($_SESSION)) session_start(); 
require_once 'wrapper.inc.php';
require_once 'chess.inc.php';
$t = new vemplator(); //required for some functions

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'login') {
        exit(login($_GET['username'], $_GET['password'], false));
    }
}
if (USER_ID === false) exit("ERROR:You are not logged in.");

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'getLastMoveTime') {
        if (isset($_GET['gameID'])) {
            $gameID = intval($_GET['gameID']);
            $cond   = "WHERE `id`=$gameID AND (`whiteUserID` = ".USER_ID;
            $cond  .= " OR `blackUserID` = ".USER_ID.")";
            $result = selectFromTable(array('lastMove'), 
                            'chess_currentGames', 
                            $cond, 1);
            exit($result['lastMove']);
        } else {
            exit("ERROR:You have to provide a valid gameID");
        }
    } else if ($_GET['action'] == 'challengeUser') {
        if (isset($_GET['user_id'])) {
            $user_id = intval($_GET['user_id']);
            exit(challengeUser($_GET['user_id'], $t));
        } else {
            exit("ERROR:You have to specify a user_id.");
        }
    } else if ($_GET['action'] == 'whoseTurnIsIt') {
        if (isset($_GET['gameID'])) {
            $gameID = intval($_GET['gameID']);
            $cond   = "WHERE `id`=$gameID AND (`whiteUserID` = ".USER_ID;
            $cond  .= " OR `blackUserID` = ".USER_ID.")";
            $result = selectFromTable(array('whoseTurnIsIt'), 
                            'chess_currentGames', 
                            $cond, 1);

            $search  = array(0, 1);
            $replace = array('white', 'black');
            exit(str_replace($search, $replace, $result['whoseTurnIsIt']));
        } else {
            exit("ERROR:You have to provide a valid gameID");
        }
    } else if ($_GET['action'] == 'listCurrentGames') {
        $condition = "WHERE `whiteUserID`=".USER_ID." OR `blackUserID`=".USER_ID;
        $rows = selectFromTable(array('id'), 'chess_currentGames', $condition, 100);
        $IDs = array();
        foreach ($rows as $row) {
            $IDs[] = $row['id'];
        }
        exit(implode('::', $IDs));
    } else if ($_GET['action'] == 'listPastGames') {
        $condition = "WHERE `whiteUserID`=".USER_ID." OR `blackUserID`=".USER_ID;
        $rows = selectFromTable(array('id'), 'chess_pastGames', $condition, 100);
        $IDs = array();
        foreach ($rows as $row) {
            $IDs[] = $row['id'];
        }
        exit(implode('::', $IDs));
    } else if ($_GET['action'] == 'getBoard') {
        if (isset($_GET['gameID'])) {
            $gameID = intval($_GET['gameID']);
            $cond   = "WHERE `id`=$gameID AND (`whiteUserID` = ".USER_ID;
            $cond  .= " OR `blackUserID` = ".USER_ID.")";
            $result = selectFromTable(array('currentBoard'), 
                            'chess_currentGames', 
                            $cond, 1);
            exit($result['currentBoard']);
        } else {
            exit("ERROR:You have to provide a valid gameID");
        }
    }
}

if (isset($_GET['move']) or isset($_GET['iccfalpha'])) {
    if (isset($_GET['gameID'])) {
        exit(chessMain($t));
    } else {
        exit("ERROR:You have to provide a valid gameID");
    }
}
