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
require_once 'additional.inc.php';
$t = new vemplator(); //required for challengeUser() and ChessMain()

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'login') {
        exit(login($_GET['username'], $_GET['password'], false));
    }
}
if (USER_ID === false) exit("ERROR: You are not logged in.");

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'getLastMoveTime') {
        if (isset($_GET['gameID'])) {
            $stmt = $conn->prepare("SELECT `lastMove` FROM ".GAMES_TABLE.
                            " WHERE `id`=:gameID AND (`whiteUserID` = :uid".
                            " OR `blackUserID` = :uid) LIMIT 1");
            $stmt->bindValue(':gameID', $_GET['gameID'], PDO::PARAM_INT);
            $stmt->bindValue(':uid', USER_ID, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            exit($result['lastMove']);
        } else {
            exit("ERROR:You have to provide a valid gameID");
        }
    } else if ($_GET['action'] == 'challengeUser') {
        if (isset($_GET['user_id'])) {
            $user_id = (int) $_GET['user_id'];
            exit(challengeUser($_GET['user_id'], $t));
        } else {
            exit("ERROR:You have to specify a user_id.");
        }
    } else if ($_GET['action'] == 'whoseTurnIsIt') {
        if (isset($_GET['gameID'])) {
            $stmt = $conn->prepare("SELECT `whoseTurnIsIt` FROM ".GAMES_TABLE.
                            " WHERE `id`=:gameID AND (`whiteUserID` = :uid".
                            " OR `blackUserID` = :uid) LIMIT 1");
            $stmt->bindValue(':gameID', $_GET['gameID'], PDO::PARAM_INT);
            $stmt->bindValue(':uid', USER_ID, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $search  = array(0, 1);
            $replace = array('white', 'black');
            exit(str_replace($search, $replace, $result['whoseTurnIsIt']));
        } else {
            exit("ERROR: You have to provide a valid gameID.");
        }
    } else if ($_GET['action'] == 'getPlayerIDs') {
        $stmt = $conn->prepare("SELECT `user_id` FROM ".USERS_TABLE.
                        " WHERE `user_id` != :uid LIMIT 100");
        $stmt->bindValue(':uid', USER_ID, PDO::PARAM_INT);
        $stmt->execute();

        exit(implode('::', $stmt->fetchAll(PDO::FETCH_COLUMN)));
    } else if ($_GET['action'] == 'listCurrentGames') {
        $stmt = $conn->prepare("SELECT `id` FROM ".GAMES_TABLE." ".
         "WHERE (`whiteUserID`=:uid OR `blackUserID`=:uid) AND `outcome` = -1");
        $stmt->bindValue(':uid', USER_ID, PDO::PARAM_INT);
        $stmt->execute();

        exit(implode('::', $stmt->fetchAll(PDO::FETCH_COLUMN)));
    } else if ($_GET['action'] == 'listPastGames') {
        $stmt = $conn->prepare("SELECT `id` FROM ".GAMES_TABLE.
                " WHERE (`whiteUserID`=:uid OR `blackUserID`=:uid)".
                " AND `outcome` > -1 LIMIT 100");
        $stmt->bindValue(':uid', USER_ID, PDO::PARAM_INT);
        $stmt->execute();

        exit(implode('::', $stmt->fetchAll(PDO::FETCH_COLUMN)));
    } else if ($_GET['action'] == 'getBoard') {
        if (isset($_GET['gameID'])) {
            $stmt = $conn->prepare("SELECT `currentBoard` FROM ".GAMES_TABLE.
                " WHERE `id`=:gameID AND (`whiteUserID` = :uid".
                " OR `blackUserID` = :uid)");
            $stmt->bindValue(':uid', USER_ID, PDO::PARAM_INT);
            $stmt->bindValue(':gameID', $_GET['gameID'], PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            exit($result['currentBoard']);
        } else {
            exit("ERROR: You have to provide a valid gameID.");
        }
    } else if ($_GET['action'] == 'whoAmI') {
        if (isset($_GET['gameID'])) {
            $stmt = $conn->prepare("SELECT `whiteUserID`, `blackUserID` FROM ".
                GAMES_TABLE." WHERE `id`=:gameID AND (`whiteUserID` = :uid".
                " OR `blackUserID` = :uid) LIMIT 1");
            $stmt->bindValue(':uid', USER_ID, PDO::PARAM_INT);
            $stmt->bindValue(':gameID', $_GET['gameID'], PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['whiteUserID'] == USER_ID) {
                $whoAmI = 'white';
            } else if ($result['blackUserID'] == USER_ID) {
                $whoAmI = 'black';
            } else {
                $whoAmI = 'None';
            }
            exit($whoAmI);
        } else {
            exit("ERROR: You have to provide a valid gameID.");
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
