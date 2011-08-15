<?php
require_once 'wrapper.inc.php';
require_once 'chess.inc.php';
if (USER_ID === false) exit('Please <a href="login.wrapper.php">login</a>');

/******************************************************************************
 * Get CURRENT_GAME_ID and some game-relevant variables                       *  
 ******************************************************************************/
if (isset($_GET['gameID'])) {
    $gameID = intval($_GET['gameID']);
    $row    = array('currentBoard','whoseTurnIsIt', 'whitePlayerID', 
                   'blackPlayerID', 'moveList', 'noCaptureAndPawnMoves', 'id');
    $cond   = 'WHERE (`whitePlayerID` = '.USER_ID.' OR `blackPlayerID` = ';
    $cond  .= USER_ID.') AND `id` = '.$gameID;
    $result = selectFromTable($row, 'chess_currentGames', $cond);

    if ($result !== false) {
        $currentBoard          = $result['currentBoard'];
        $whoseTurnIsIt         = $result['whoseTurnIsIt'];
        $moveList              = $result['moveList'];
        $noCaptureAndPawnMoves = $result['noCaptureAndPawnMoves'];
        $gameID                = $result['id'];
        if ($whoseTurnIsIt == 0) {
            $whoseTurnIsItLanguage = 'white';
        } else {
            $whoseTurnIsItLanguage = 'black';
        }
        if ($result['whitePlayerID'] == USER_ID) {
            $yourColor     = 'white';
            $opponentColor = 'black';
        } else {
            $yourColor     = 'black';
            $opponentColor = 'white';
        }
        define('CURRENT_GAME_ID', $gameID);
    }
}

if (!defined('CURRENT_GAME_ID')) {
    exit('ERROR: Wrong gameID.');
}

/******************************************************************************
 * Get MOVE as Number-Notation:                                               *
 * http://code.google.com/p/community-chess/wiki/NotationOfMoves              *  
 ******************************************************************************/

if (isset($_GET['move'])) {
    $array       = getValidMoveQuery($_GET['move']);
    $move        = $array[0];
    $from_index  = $array[1];
    $to_index    = $array[2];
    $from_x      = $array[3];
    $from_y      = $array[4];
    $to_x        = $array[5];
    $to_y        = $array[6];
    define('MOVE', $move);
}

if (isset($_GET['pgn'])) {
    // insert Code as soon as possible
    // define MOVE in my notation
}

if (isset($_GET['iccfalpha'])) {
    $array       = getValidMoveQueryFromICCFalpha($_GET['iccfalpha']);
    $move        = $array[0];
    $from_index  = $array[1];
    $to_index    = $array[2];
    $from_x      = $array[3];
    $from_y      = $array[4];
    $to_x        = $array[5];
    $to_y        = $array[6];
    define('MOVE', $move);
}

if (defined('MOVE')) {
    processMove($whoseTurnIsItLanguage, $currentBoard, $moveList, 
                     $yourColor, $opponentColor,
                     $from_index, $to_index, 
                     $from_x, $from_y, $to_x, $to_y);
}

if (isset($_GET['claim50MoveRule'])) {
    if ($noCaptureAndPawnMoves >= 100) {
        finishGame(2);
        exit('Game finished. Draw. You claimed draw by the fifty-move rule.');
    } else {
        exit("ERROR: The last $noCaptureAndPawnMoves were no capture or pawn ".
                    'moves. At least 100 have to be made before you can claim '.
                    'draw by fifty-move rule.');
    }
}

if (isset($_GET['claimThreefoldRepetition'])) {
    $cond   = 'WHERE `id` = '.CURRENT_GAME_ID;
    $rows   = array('currentBoard',
                   'moveList',
                   'whiteCastlingKingsidePossible',
                   'whiteCastlingQueensidePossible',
                   'blackCastlingKingsidePossible',
                   'blackCastlingQueensidePossible');
    $result = selectFromTable($rows, 'chess_currentGames', $cond);

    /* is en passant possible now? */
    $moveList = explode("\n", trim($result['moveList']));
    $lastMove = end($moveList);

    $lastFromX = substr($lastMove, 0, 1);
    $lastFromY = substr($lastMove, 1, 1);
    $lastToX   = substr($lastMove, 2, 1);
    $lastToY   = substr($lastMove, 3, 1);

    $shouldBePawn = getPieceByIndex($currentBoard, getIndex($lastToX, $lastToY));

    if ($lastFromX == $lastToX and abs($lastFromY-$lastToY) == 2) {
        $isOpponentNext = false;
        if ($to_coord[0] - 1 >= 1) {
            $indexLeft = getIndex($lastToX-1, $lastToY);
            $pieceLeft = getPieceByIndex($currentBoard, $indexLeft);
            if ($pieceLeft == 'p' and $yourColor == 'white') {
                $isOpponentNext = true;
            }
            if ($pieceLeft == 'P' and $yourColor == 'black') {
                $isOpponentNext = true;
            }
        }
        if ($to_coord[0] + 1 <= 8) {
            $indexRight = getIndex($lastToX+1, $lastToY);
            $pieceRight = getPieceByIndex($currentBoard, $indexLeft);
            if ($pieceRight== 'p' and $yourColor == 'white') {
                $isOpponentNext = true;
            }
            if ($pieceRight== 'P' and $yourColor == 'black') {
                $isOpponentNext = true;
            }
        }
        if ($isOpponentNext and $wasPawn2move) {
            $enPassant = '1';
        } else {
            $enPassant = '0';
        }            
    }
    /* end en passant */

    $cond   = 'WHERE `gameID` = '.CURRENT_GAME_ID.' ';
    $cond  .= 'AND `board` = '.$result['currentBoard'];
    $cond  .= 'AND `whiteCastlingKingsidePossible` = ';
    $cond  .= $result['whiteCastlingKingsidePossible'];
    $cond  .= 'AND `whiteCastlingQueensidePossible`= ';
    $cond  .= $result['whiteCastlingQueensidePossible'];
    $cond  .= 'AND `blackCastlingKingsidePossible` = ';
    $cond  .= $result['blackCastlingKingsidePossible'];
    $cond  .= 'AND `blackCastlingQueensidePossible`= ';
    $cond  .= $result['blackCastlingQueensidePossible'];
    $cond  .= 'AND `enPassantPossible` = '.$enPassant;
    $result = selectFromTable(array('id'), 
                                'chess_currentGamesThreefoldRepetition', 
                                $cond, 4);
    if (count($result) >= 3) {
        finishGame(2);
        exit('Game finished. Draw. You claimed draw by threefold repetition.');
    } else {
        exit('ERROR: Threefold repetition may only be claimed if exactly the '.
                    'same situation appeared at least three times. '.
                    'The current situation appered only '.count($result).' '.
                    'times');
    }
}

$row    = array('currentBoard','whoseTurnIsIt', 'whitePlayerID', 
               'blackPlayerID');
$cond   = 'WHERE (`whitePlayerID` = '.USER_ID.' OR `blackPlayerID` = ';
$cond  .= USER_ID.') AND `id` = '.CURRENT_GAME_ID;
$result = selectFromTable($row, 'chess_currentGames', $cond);

if ($result !== false) {
    $currentBoard  = $result['currentBoard'];
    $whoseTurnIsIt = $result['whoseTurnIsIt'];
    if ($whoseTurnIsIt == 0) {
        $whoseTurnIsItLanguage = 'white';
    } else {
        $whoseTurnIsItLanguage = 'black';
    }
    if ($result['whitePlayerID'] == USER_ID) {
        $yourColor     = 'white';
        $opponentColor = 'black';
    } else {
        $yourColor     = 'black';
        $opponentColor = 'white';
    }
}

if (isPlayerCheck($currentBoard, $yourColor))          $youCheck = 'Yes';
else                                                   $youCheck = 'No';
if (isPlayerCheck($currentBoard, $opponentColor)) $opponentCheck = 'Yes';
else                                              $opponentCheck = 'No';

echo 'Current Game Information:<pre><br/>';
echo substr($currentBoard, 56, 8).'<br/>';
echo substr($currentBoard, 48, 8).'<br/>';
echo substr($currentBoard, 40, 8).'<br/>';
echo substr($currentBoard, 32, 8).'<br/>';
echo substr($currentBoard, 24, 8).'<br/>';
echo substr($currentBoard, 16, 8).'<br/>';
echo substr($currentBoard, 8, 8).'<br/>';
echo substr($currentBoard, 0, 8).'<br/>';
echo '</pre><br/>';
echo "Next turn: $whoseTurnIsItLanguage<br/>";
echo "You are: $yourColor<br/>";
echo "You are check: $youCheck<br/>";
echo "Opponent is check: $opponentCheck.<br/>";
echo 'Game-ID: '.CURRENT_GAME_ID.'.<br/>';

?>
