<?php
/**
 * all functions needed for playing a chess game are here
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

/******************************************************************************
 * constants                                                                  *
 ******************************************************************************/
define('ERR_CAPTURE_OWN', 'ERROR: You may not capture your own chess pieces.');
define('ERR_PAWN_CAPTURE_MOVE', 'ERROR: You may only make the pawn capture '.
       'move if a chess piece of the opponent is on the target field.');
define('ERR_MOVE_QUERY_LENGTH', 'ERROR: Your move-query should have 4 or 5 '.
       'characters.');
define('ERR_NO_VALID_MOVE', 'ERROR: From (%u | %u) to (%u | %u) is no valid '.
       'move for a %s.');
define('ERR_NOT_YOUR_PIECE', 'ERROR: The chess piece on field (%u | %u) is %s.'.
       ' It belongs to your opponent. You are %s. Your chess pieces have %s '.
       'letters.');
/******************************************************************************
 * helper functions                                                           *
 ******************************************************************************/
/** This function processes the game. All relevant data has to be submitted via
 *  $_GET:
 *  The following data is needed: $_GET['gameID']
 *  The following data is optional: $_GET['move'], $_GET['iccfalpha'], 
 *  $_GET['claim50MoveRule'], $_GET['claimThreefoldRepetition']
 * 
 * @param object $t template-object
 *
 * @return string the new board
 */
function chessMain($t)
{
    /**************************************************************************
     * Get CURRENT_GAME_ID and some game-relevant variables                   *  
     **************************************************************************/
    if (isset($_GET['gameID'])) {
        global $conn;
        $stmt = $conn->prepare('SELECT `currentBoard`, `whoseTurnIsIt`, '.
                '`whiteUserID`, `blackUserID`, `moveList`, '.
                '`noCaptureAndPawnMoves`, `id` FROM '.GAMES_TABLE.' '.
                'WHERE (`whiteUserID` = :uid OR `blackUserID` = :uid) '.
                'AND `id` = :gid LIMIT 1');
        $stmt->bindValue(":uid", USER_ID, PDO::PARAM_INT);
        $stmt->bindValue(":gid", (int) $_GET['gameID'], PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

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
            if ($result['whiteUserID'] == USER_ID) {
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

    /**************************************************************************
     * Get MOVE as Number-Notation:                                           *
     * http://code.google.com/p/community-chess/wiki/NotationOfMoves          *  
     **************************************************************************/

    if (isset($_GET['move'])) {
        $array      = getValidMoveQuery($_GET['move']);
        $move       = $array[0];
        $from_index = $array[1];
        $to_index   = $array[2];
        $from_x     = $array[3];
        $from_y     = $array[4];
        $to_x       = $array[5];
        $to_y       = $array[6];
        define('MOVE', $move);
    }

    if (isset($_GET['from']) and isset($_GET['to'])) {
        $from       = (int) $_GET['from'];
        $to         = (int) $_GET['to'];
        $array      = getValidMoveQuery($from.$to);
        $move       = $array[0];
        $from_index = $array[1];
        $to_index   = $array[2];
        $from_x     = $array[3];
        $from_y     = $array[4];
        $to_x       = $array[5];
        $to_y       = $array[6];
        define('MOVE', $move);
    }

    if (isset($_GET['pgn'])) {
        // TODO: insert Code as soon as possible
        // define MOVE in my notation
    }

    if (isset($_GET['iccfalpha'])) {
        $array      = getValidMoveQueryFromICCFalpha($_GET['iccfalpha']);
        $move       = $array[0];
        $from_index = $array[1];
        $to_index   = $array[2];
        $from_x     = $array[3];
        $from_y     = $array[4];
        $to_x       = $array[5];
        $to_y       = $array[6];
        define('MOVE', $move);
    }

    if (defined('MOVE')) {
        processMove($whoseTurnIsItLanguage, $currentBoard, $moveList, 
                         $yourColor, $opponentColor,
                         $from_index, $to_index, 
                         $from_x, $from_y, $to_x, $to_y);
    }

    if (isset($_GET['giveUp'])) {
        // draw, as the 50 move rule was claimed
        if ($yourColor == 'white') $outcome = 1;
        else                       $outcome = 0;
        finishGame($outcome);
        exit('Game finished. You lost, as you gave up.');
    }

    if (isset($_GET['claim50MoveRule'])) {
        if ($noCaptureAndPawnMoves >= 100) {
            // draw, as the 50 move rule was claimed
            finishGame(2);
            exit('Game finished. Draw. You claimed draw by the fifty-move rule.');
        } else {
            exit("ERROR: The last $noCaptureAndPawnMoves were no capture or pawn ".
                        'moves. At least 100 have to be made before you can claim '.
                        'draw by fifty-move rule.');
        }
    }

    if (isset($_GET['claimThreefoldRepetition'])) {
        global $conn;
        $stmt = $conn->prepare('SELECT `currentBoard`, `moveList`, '.
                '`whiteCastlingKingsidePossible`, '.
                '`whiteCastlingQueensidePossible`, '.
                '`blackCastlingKingsidePossible`, '.
                '`blackCastlingQueensidePossible` FROM '.GAMES_TABLE.' '.
                'WHERE `id` = '.CURRENT_GAME_ID.' LIMIT 1');
        $stmt->bindValue(":uid", USER_ID, PDO::PARAM_INT);
        $stmt->bindValue(":gid", (int) $_GET['gameID'], PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

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
                // TODO: Check indexLeft
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

        $stmt = $conn->prepare('SELECT `id` FROM '.
                GAMES_THREEFOLD_REPETITION_TABLE.' '.
                'WHERE `gameID` = :gid '.
                'AND `board` = :board'.
                'AND `whiteCastlingKingsidePossible` = :wckingside '.
                'AND `whiteCastlingQueensidePossible`= :wcqueenside '.
                'AND `blackCastlingKingsidePossible` = :bckingside '.
                'AND `blackCastlingQueensidePossible`= :bcqueenside '.
                'AND `enPassantPossible` = :enpassant '.
                'LIMIT 4');
        $stmt->bindValue(":gid", CURRENT_GAME_ID, PDO::PARAM_INT);
        $stmt->bindValue(":board", $result['currentBoard']);
        $stmt->bindValue(":wcqueenside", 
                    $result['whiteCastlingQueensidePossible'], PDO::PARAM_INT);
        $stmt->bindValue(":wckingside", 
                    $result['whiteCastlingKingsidePossible'], PDO::PARAM_INT);
        $stmt->bindValue(":bcqueenside", 
                    $result['blackCastlingQueensidePossible'], PDO::PARAM_INT);
        $stmt->bindValue(":bckingside", 
                    $result['blackCastlingKingsidePossible'], PDO::PARAM_INT);
        // TODO: Check if en passant gets defined
        $stmt->bindValue(":enpassant", $enPassant, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) >= 3) {
            // draw, as threefold repetition was claimed
            finishGame(2);
            exit('Game finished. Draw. You claimed draw by threefold repetition.');
        } else {
            exit('ERROR: Threefold repetition may only be claimed if exactly the '.
                        'same situation appeared at least three times. '.
                        'The current situation appered only '.count($result).' '.
                        'times');
        }
    }

    $row    = array('currentBoard','whoseTurnIsIt', 'whiteUserID', 
                   'blackUserID');
    $cond   = 'WHERE (`whiteUserID` = '.USER_ID.' OR `blackUserID` = ';
    $cond  .= USER_ID.') AND `id` = '.CURRENT_GAME_ID;
    $result = selectFromTable($row, GAMES_TABLE, $cond);

    if ($result !== false) {
        $currentBoard  = $result['currentBoard'];
        $whoseTurnIsIt = $result['whoseTurnIsIt'];
        if ($whoseTurnIsIt == 0) {
            $whoseTurnIsItLanguage = 'white';
        } else {
            $whoseTurnIsItLanguage = 'black';
        }
        if ($result['whiteUserID'] == USER_ID) {
            $yourColor     = 'white';
            $opponentColor = 'black';
        } else {
            $yourColor     = 'black';
            $opponentColor = 'white';
        }
    }

    $t->assign('from', false);
    if (isset($_GET['from'])) {
        $from  = (int) $_GET['from'];
        $y     = $from % 10;
        $x     = ($from - $y)/10;
        $index = ($x - 1) + (($y-1)*8);
        $piece = getPieceByIndex($currentBoard, $index);
        if (isMyPiece($piece, $yourColor)) {

            $t->assign('from', $from);
        }
    }

    if (isPlayerCheck($currentBoard, $yourColor))          $youCheck = true;
    else                                                   $youCheck = false;
    if (isPlayerCheck($currentBoard, $opponentColor)) $opponentCheck = true;
    else                                              $opponentCheck = false;

    $board = array();
    for ($y=0;$y<8;$y++) {
        $line = array();
        for ($x=0;$x<8;$x++) {
            $line[] = substr($currentBoard, $x+8*$y, 1);
        }
        $board[] = $line;
    }
    $t->assign('board', $board);
    $t->assign('line1', substr($currentBoard, 56, 8));
    $t->assign('line2', substr($currentBoard, 48, 8));
    $t->assign('line3', substr($currentBoard, 40, 8));
    $t->assign('line4', substr($currentBoard, 32, 8));
    $t->assign('line5', substr($currentBoard, 24, 8));
    $t->assign('line6', substr($currentBoard, 16, 8));
    $t->assign('line7', substr($currentBoard, 8, 8));
    $t->assign('line8', substr($currentBoard, 0, 8));

    $t->assign('whoseTurnIsItLanguage', $whoseTurnIsItLanguage);
    $t->assign('yourColor', $yourColor);
    $t->assign('youCheck', $youCheck);
    $t->assign('youCheck', $youCheck);
    $t->assign('yourTurn', ($whoseTurnIsItLanguage == $yourColor));
    $t->assign('opponentCheck', $opponentCheck);
    $t->assign('CURRENT_GAME_ID', CURRENT_GAME_ID);
    return $currentBoard;
}



/** This function makes all checks before the actuall move
 *
 * @param string $whoseTurnIsItLanguage either 'white' or 'black'
 * @param string $currentBoard          the board as a single string
 * @param string $moveList              a list of all moves
 * @param string $yourColor             either 'white' or 'black'
 * @param string $opponentColor         either 'white' or 'black'
 * @param int    $from_index            has to be in [0;63]
 * @param int    $to_index              has to be in [0;63]
 * @param int    $from_x                has to be in [0;7]
 * @param int    $from_y                has to be in [0;7]
 * @param int    $to_x                  has to be in [0;7]
 * @param int    $to_y                  has to be in [0;7]
 *
 * @return array
 */
function processMove($whoseTurnIsItLanguage, $currentBoard, $moveList, 
                     $yourColor, $opponentColor,
                     $from_index, $to_index, 
                     $from_x, $from_y, $to_x, $to_y)
{
    // Is it your turn?
    if ($whoseTurnIsItLanguage != $yourColor) {
        exit("ERROR: It's not your turn");
    }
    // Is one of your chess pieces on the from-field?
    $piece = getPieceByIndex($currentBoard, $from_index);
    if ($piece == '0') {
        exit("ERROR: No chess piece on field ($from_x | $from_y).");
    }
    if ($yourColor == 'white' and !(isMyPiece($piece, $yourColor)) ) {
        exit(sprintf(ERR_NOT_YOUR_PIECE, $from_x, $from_y, $piece, 
                                        $yourColor, 'capital'));
    }
    if ($yourColor == 'black' and !(isMyPiece($piece, $yourColor)) ) {
        exit(sprintf(ERR_NOT_YOUR_PIECE, $from_x, $from_y, $piece, 
                                        $yourColor, 'lower-case'));
    }
    // Can the chess piece make this move?
    $piece_lower = strtolower($piece);
    $en_passant  = false;
    if ($piece_lower == 'q') {
        isQueenMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                         $yourColor);
    } else if ($piece_lower == 'p') {
        $en_passant = isPawnMoveValid($from_x, $from_y, $to_x, $to_y, 
                                      $currentBoard, $yourColor, $moveList);
    } else if ($piece_lower == 'r') {
        isRookMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                        $yourColor);
    } else if ($piece_lower == 'b') {
        isBishopMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                          $yourColor);
    } else if ($piece_lower == 'k') {
        isKingMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                        $yourColor);
    } else if ($piece_lower == 'n') {
        isKnightMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                          $yourColor);
    } else {
        exit('ERROR: You wanted to move this piece: '.$piece_lower);
    }
    // Do you set yourself check with this move?
    $newBoard = getNewBoard($currentBoard, $from_index, $to_index);
    if (isPlayerCheck($newBoard, $yourColor)) {
        exit('ERROR: You may not be check at end of your turn!');
    }
    // Everything is ok => move!
    $currentBoard = makeMove($from_index, $to_index, 
                             $currentBoard, MOVE, $yourColor, $en_passant);
    // Check for:
    if ( !hasValidMoves($currentBoard, $opponentColor) ) {
        if ( isPlayerCheck($currentBoard, $opponentColor) ) {
            if ($yourColor == 'white') $outcome = 0;
            else                       $outcome = 1;

            $msg = 'Checkmate.';
        } else {
            $outcome = 2;
            $msg     = "$opponentColor has no valid moves but is not check. ".
                 'Draw.';
        }
        // The opponent has no valid moves left. If he is check, the current player 
        // won. Else it is draw.
        finishGame($outcome);
        exit($msg.' Game finished.');
    }
}
/** This function checks if the move query is valid. If it is or if it could be
 *  corrected, the move query is returned. If not, the function stops script 
 *  execution.
 *
 * @param string $move The move-query. Should look like 1213 or 4567.
 *
 * @return array
 */
function getValidMoveQuery($move)
{
    preg_match('/^([1-8]{2})([1-8]{2})([bnpqrBNPQR]?)$/', $move, $matches);

    // Is the move-query well formed?
    if (count($matches) == 0) {
        exit(ERR_MOVE_QUERY_LENGTH + " Yours was '".$move."'");
    } else {
        $move = $matches[0];
    }

    $from = $matches[1];
    $to   = $matches[2];

    if (strlen($move) == 5) $promotion = $matches[3];
    else                    $promotion = '';

    $from_y = $from % 10;
    $from_x = ($from-$from_y)/10;
    $to_y   = $to % 10;
    $to_x   = ($to-$to_y)/10;
    if (!(1 <= $from_x and $from_x <= 8 and 1 <= $from_y and $from_y <= 8)) {
        exit('ERROR: Your from-coordinates were wrong.');
    } else {
        $from_index = getIndex($from_x, $from_y);
    }
    if (!(1 <= $to_x and $to_x <= 8 and 1 <= $to_y and $to_y <= 8)) {
        exit('ERROR: Your to-coordinates were wrong.');
    } else {
        $to_index = getIndex($to_x, $to_y);
    }
    if ($from_index == $to_index) {
        exit('ERROR: You have to move.');
    }
    return array($move, $from_index, $to_index, $from_x, $from_y, $to_x, $to_y);
}
/** This function checks if the move query is valid. If it is or if it could be
 *  corrected, the move query is returned. If not, the function stops script 
 *  execution.
 *
 * @param string $move The move-query. Should look like 1213 or 4567.
 *
 * @return array
 */
function getValidMoveQueryFromICCFalpha($move)
{
    $search  = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h');
    $replace = array(1,2,3,4,5,6,7,8);
    $move    = str_replace($search, $replace, $move);

    // like the rest of "if (isset($_GET['move'])) {"
    // Is the move-query well formed?
    if (strlen($move) > 5 or strlen($move) < 4 ) {
        exit(ERR_MOVE_QUERY_LENGTH.
             " Yours was:'$move' (".strlen($move).' characters).');
    }
    $from = substr($move, 0, 2);
    $to   = substr($move, 2, 2);
    if (strlen($move) == 5) {
        $promotion = substr($move, 4, 1);
    } else {
        $promotion = '';
    }

    $from_y = $from % 10;
    $from_x = ($from-$from_y)/10;
    $to_y   = $to % 10;
    $to_x   = ($to-$to_y)/10;

    if (!(1 <= $from_x and $from_x <= 8 and 1 <= $from_y and $from_y <= 8)) {
        exit('ERROR: Your from-coordinates were wrong.');
    } else {
        $from_index = getIndex($from_x, $from_y);
    }

    if (!(1 <= $to_x and $to_x <= 8 and 1 <= $to_y and $to_y <= 8)) {
        exit('ERROR: Your to-coordinates were wrong.');
    } else {
        $to_index = getIndex($to_x, $to_y);
    }

    if ($from_index == $to_index) {
        exit('ERROR: You have to move.');
    }
    return array($move, $from_index, $to_index, $from_x, $from_y, $to_x, $to_y);
}
/** This function returns the index of a given chess field
 *
 * @param int $x the x-coordinate. Has to be in [0;7]
 * @param int $y the y-coordinate. Has to be in [0;7]
 *
 * @return integer The index is in [0;63]
 */
function getIndex($x, $y)
{
    return ($y-1) * 8 + ($x-1);
}

/** This function returns an array with the x- and y-coordinates
 *
 * @param int $index the index. Has to be in [0;63]
 *
 * @return array The coordinates. array[0] = x, array[1] = y.
 */
function getCoordinates($index)
{
    $x = $index % 8;
    $y = ($index - $x)/8;
    ++$x;
    ++$y;
    return array($x, $y);
}

/** This function checks if a given field is valid
 *
 * @param int $x the x-coordinate. Has to be in [0;7]
 * @param int $y the y-coordinate. Has to be in [0;7]
 *
 * @return bool if the field is valid => true, else => false
 */
function isPositionValid($x, $y)
{
    if (1 <= $x and $x <= 8 and 1 <= $y and $y <= 8) return true;
    else return false;
}

/** This function returns a single character representation of a chess piece
 *
 * @param string $board the board as a single string
 * @param int    $index the index. Has to be in [0;63]
 *
 * @return char The chess piece or '0'
 */
function getPieceByIndex($board, $index)
{
    return substr($board, $index, 1);
}

/** This function returns a new board as a single string
 *
 * @param string $currentBoard the board as a single string
 * @param int    $from_index   has to be in [0;63]
 * @param int    $to_index     has to be in [0;63]
 *
 * @return string The new chess board
 */
function getNewBoard($currentBoard, $from_index, $to_index)
{
    $piece    = getPieceByIndex($currentBoard, $from_index);
    $newBoard = substr($currentBoard, 0, $from_index).'0'.
                substr($currentBoard, $from_index+1);
    $newBoard = substr($newBoard, 0, $to_index).$piece.
                substr($newBoard, $to_index+1);
    return $newBoard;
}

/** This function checks if the given piece is mine.
 *
 * @param char   $piece   the board as a single string
 * @param string $myColor either 'black' or 'white'
 *
 * @return bool
 */
function isMyPiece($piece, $myColor)
{
    if ( ( (ord($piece) < 96 and $myColor == 'white') or
          (ord($piece) > 96 and $myColor == 'black')    )  and $piece != '0') {
        return true;
    } else {
        return false;
    }
}

/** This function checks if the given piece is one of the opponents pieces.
 *
 * @param char   $piece   the board as a single string
 * @param string $myColor either 'black' or 'white'
 *
 * @return bool
 */
function isOpponentsPiece($piece, $myColor)
{
    if ( ( (ord($piece) < 96 and $myColor == 'black') or
          (ord($piece) > 96 and $myColor == 'white')    )  and $piece != '0') {
        return true;
    } else {
        return false;
    }
}

/** This function returns a 2-dimensional array:
 * array[0] = array('0','0','P')              to the top
 * array[1] = array('p')                      to the right
 * array[2] = array('0','0','0','0','0', 'Q') to the bottom
 * array[3] = array('N')                      to the left
 * 
 * @param string $board the board as a single string
 * @param int    $x     the x-coordinate. Has to be in [0;7]
 * @param int    $y     the y-coordinate. Has to be in [0;7]
 *
 * @return array
 */
function getAllStraightFields($board, $x, $y)
{
    $straights = array(0=>array(), 1=>array(), 2=>array(), 3=>array());

    // Which moves could a rook possibly make?
    // top
    $tmp_x = $x; $tmp_y = $y;
    for ($tmp_y=$y+1; $tmp_y <= 8; $tmp_y++) {
        $index          = getIndex($tmp_x, $tmp_y);
        $piece          = getPieceByIndex($board, $index);
        $straights[0][] = $piece;
        if ($piece != '0') break;
    }
    // right
    $tmp_x = $x; $tmp_y = $y;
    for ($tmp_x=$x+1; $tmp_x <= 8; $tmp_x++) {
        $index          = getIndex($tmp_x, $tmp_y);
        $piece          = getPieceByIndex($board, $index);
        $straights[1][] = $piece;
        if ($piece != '0') break;
    }
    // down
    $tmp_x = $x; $tmp_y = $y;
    for ($tmp_y=$y-1; $tmp_y >= 1; $tmp_y--) {
        $index          = getIndex($tmp_x, $tmp_y);
        $piece          = getPieceByIndex($board, $index);
        $straights[2][] = $piece;
        if ($piece != '0') break;
    }
    // left
    $tmp_x = $x; $tmp_y = $y;
    for ($tmp_x=$x-1; $tmp_x >= 1; $tmp_x--) {
        $index          = getIndex($tmp_x, $tmp_y);
        $piece          = getPieceByIndex($board, $index);
        $straights[3][] = $piece;
        if ($piece != '0') break;
    }
    return $straights;
}

/** This function returns a 2-dimensional array:
 * array[0] = array('0','0','P')              to the top-right
 * array[1] = array('p')                      to the bottom-right
 * array[2] = array('0','0','0','0','0', 'Q') to the top-left
 * array[3] = array('N')                      to the bottom-left
 * 
 * @param string $board the board as a single string
 * @param int    $x     the x-coordinate. Has to be in [0;7]
 * @param int    $y     the y-coordinate. Has to be in [0;7]
 *
 * @return array
 */
function getAllDiagonalFields($board, $x, $y)
{
    $diagonals = array(0=>array(), 1=>array(), 2=>array(), 3=>array());

    // Which moves could a bishop possibly make?
    // diagonal right up
    for ($i=1; $i <= 8 - max($x, $y); $i++) {
        $tmp_x          = $x + $i;
        $tmp_y          = $y + $i;
        $index          = getIndex($tmp_x, $tmp_y);
        $piece          = getPieceByIndex($board, $index);
        $diagonals[0][] = $piece;
        if ($piece != '0') break;
    }
    // diagonal right down
    for ($i=1; $i <= 8 - max($x, 9 - $y); $i++) {
        $tmp_x          = $x + $i;
        $tmp_y          = $y - $i;
        $index          = getIndex($tmp_x, $tmp_y);
        $piece          = getPieceByIndex($board, $index);
        $diagonals[1][] = $piece;
        if ($piece != '0') break;
    }
    // diagonal left up
    for ($i=1; $i <= 8 - max(9-$x, $y); $i++) {
        $tmp_x          = $x - $i;
        $tmp_y          = $y + $i;
        $index          = getIndex($tmp_x, $tmp_y);
        $piece          = getPieceByIndex($board, $index);
        $diagonals[2][] = $piece;
        if ($piece != '0') break;
    }
    // diagonal left down
    for ($i=1; $i <= min($x, $y)-1; $i++) {
        $tmp_x          = $x - $i;
        $tmp_y          = $y - $i;
        $index          = getIndex($tmp_x, $tmp_y);
        $piece          = getPieceByIndex($board, $index);
        $diagonals[3][] = $piece;
        if ($piece != '0') break;
    }
    return $diagonals;
}

/** This function finishes a game and makes the needed database-stuff
 * 
 * @param int $outcome 0: White won; 1: Black won; 2: Draw
 *
 * @return true
 */
function finishGame($outcome)
{
    $rows      = array('moveList', 'whiteUserID', 'blackUserID', 
                       'whitePlayerSoftwareID', 'blackPlayerSoftwareID', 
                       'whoseTurnIsIt', 'startTime', 'lastMove');
    $condition = 'WHERE `id` = '.CURRENT_GAME_ID;

    $keyValue['outcome']  = $outcome;
    $keyValue['lastMove'] = date('Y-m-d H:i:s');

    updateDataInTable(GAMES_TABLE, $keyValue, $condition);

    // Was this game a tournament game?
    $cond   = 'WHERE `id` = '.CURRENT_GAME_ID;
    $rows   = array('tournamentID', 'whiteUserID', 'blackUserID');
    $result = selectFromTable($rows, GAMES_TABLE, $cond);
    if ($result['tournamentID'] != 0) {
        if (($outcome == 0 and $result['whiteUserID'] == USER_ID) or 
            ($outcome == 1 and $result['blackUserID'] == USER_ID)) {
            // The player won
            $keyValue                = array();
            $keyValue['gamesWon']    = "`gamesWon` + 1";
            $keyValue['gamesPlayed'] = "`gamesPlayed` + 1";

            $condition  = "WHERE `tournamentID` = ".$result['tournamentID']." ";
            $condition .= "AND `user_id` = ".USER_ID;
            updateDataInTable(TOURNAMENT_PLAYERS_TABLE, $keyValue, $condition);
        } else if (($outcome == 1 and $result['whiteUserID'] == USER_ID) or 
                   ($outcome == 0 and $result['blackUserID'] == USER_ID)) {
            // The player lost
            $keyValue                = array();
            $keyValue['gamesPlayed'] = "`gamesPlayed` + 1";

            $condition  = "WHERE `tournamentID` = ".$result['tournamentID']." ";
            $condition .= "AND `user_id` = ".USER_ID;
            updateDataInTable(TOURNAMENT_PLAYERS_TABLE, $keyValue, $condition);
        } else if ($outcome == 2) {
            // Draw
            $keyValue                = array();
            $keyValue['gamesPlayed'] = "`gamesPlayed` + 1";

            $condition  = "WHERE `tournamentID` = ".$result['tournamentID']." ";
            $condition .= "AND `user_id` = ".USER_ID;
            updateDataInTable(TOURNAMENT_PLAYERS_TABLE, $keyValue, $condition);
        } else {
            exit('You had an $outcome of '.$outcome.'. '.
                 'Please ask info@martin-thoma.de '.
                 '(subject:Community Chess fix) to fix this!');
        }
        triggerPageRank($result['tournamentID']);
    }

    // Now the PageRank has to be recalculated
    triggerPageRank();

    return true;
}
/******************************************************************************
 * chess game relevant functions                                              *
 ******************************************************************************/
/** This function checks if the given color (player) has valid moves
 * 
 * @param string $board the current board as a single string
 * @param string $color either 'white' or 'black'
 *
 * @return bool
 */
function hasValidMoves($board, $color)
{
    for ($from_index = 0; $from_index < 63; $from_index++) {
        $piece = getPieceByIndex($board, $from_index);
        $coord = getCoordinates($from_index);

        if (isMyPiece($piece, $color)) {
            if (strtoupper($piece) == 'P') {
                // Which moves could a pawn possibly make?
                if ($color == 'white') $colorMul = 1;
                else                   $colorMul = -1;
                // Which moves could a pawn possibly make?
                if ( ($coord[1] < 8 and $color == 'white') or
                    ($coord[1] > 1 and $color == 'black')) {
                    // one straight up / down
                    $to_index    = getIndex($coord[0], $coord[1]+1*$colorMul);
                    $targetpiece = getPieceByIndex($board, $to_index);
                    if ($targetpiece == '0') {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)) return true;
                    }

                    if ($coord[0] > 1) {
                        // diagonal left capturing
                        $to_index    = getIndex($coord[0]-1, $coord[1]+1*$colorMul);
                        $targetpiece = getPieceByIndex($board, $to_index);
                        if (isOpponentsPiece($targetpiece, $color)) {
                            $newBoard = getNewBoard($board, $from_index, 
                                                                     $to_index);
                            if (!isPlayerCheck($newBoard, $color)) return true;
                        }
                    }
                    if ($coord[0] < 8) {
                        // diagonal right capturing
                        $to_index    = getIndex($coord[0]+1, $coord[1]+1*$colorMul);
                        $targetpiece = getPieceByIndex($board, $to_index);
                        if (isOpponentsPiece($targetpiece, $color)) {
                            $newBoard = getNewBoard($board, $from_index, 
                                                                     $to_index);
                            if (!isPlayerCheck($newBoard, $color)) return true;
                        }
                    }
                }
                if (($coord[1] == 2 and $color == 'white') or
                   ($coord[1] == 7 and $color == 'black')    ) {
                    // two straight up in home row
                    $to_index    = getIndex($coord[0], $coord[1]+2*$colorMul);
                    $field1      = getPieceByIndex($board, $to_index+8);
                    $targetpiece = getPieceByIndex($board, $to_index);
                    if ($targetpiece == '0' and $field1 == '0') {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)) return true;
                    }
                }
            } else if (strtoupper($piece) == 'N') {
                // Which moves could a knight possibly make?
                $knight_moves = array(-17, -15, -10, -6, 6, 10, 15, 17);
                foreach ($knight_moves as $add) {
                    $to_index = $from_index + $add;
                    if (!(0<=$to_index and $to_index <= 63))
                        continue;
                    $from_coord = getCoordinates($from_index);
                    $to_coord   = getCoordinates($to_index);
                    if ( (abs($from_coord[0] - $to_coord[0]) + 
                         abs($from_coord[1] - $to_coord[1])    ) == 3) {
                        $targetPiece = getPieceByIndex($board, $to_index);
                        if ($targetPiece == '0' or 
                           isOpponentsPiece($targetPiece, $color)  ) {
                            $newBoard = getNewBoard($board, $from_index, 
                                                                 $to_index);
                            if (!isPlayerCheck($newBoard, $color)) return true;
                        }
                    }
                }
            } 

            if (strtoupper($piece) == 'B' or strtoupper($piece) == 'Q') {
                // Which moves could a bishop possibly make?
                // (Queen can do the same)
                $dia = getAllDiagonalFields($board, $coord[0], $coord[1]);
                // diagonal right up
                foreach ($dia[0] as $key=>$piece) {
                    $x        = $coord[0] + $key + 1;
                    $y        = $coord[1] + $key + 1;
                    $to_index = getIndex($x, $y);
                    if ($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)) return true;
                    }
                }

                // diagonal right down
                foreach ($dia[1] as $key=>$piece) {
                    $x        = $coord[0] + $key + 1;
                    $y        = $coord[1] - $key - 1;
                    $to_index = getIndex($x, $y);
                    if ($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)) return true;
                    }
                }

                // diagonal left up
                foreach ($dia[2] as $key=>$piece) {
                    $x        = $coord[0] - $key - 1;
                    $y        = $coord[1] + $key + 1;
                    $to_index = getIndex($x, $y);
                    if ($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)) return true;
                    }
                }

                // diagonal left down
                foreach ($dia[3] as $key=>$piece) {
                    $x        = $coord[0] - $key - 1;
                    $y        = $coord[1] - $key - 1;
                    $to_index = getIndex($x, $y);
                    if ($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)) return true;
                    }
                }
            }

            if (strtoupper($piece) == 'R' or strtoupper($piece) == 'Q') {
                // Which moves could a rook possibly make? 
                // (Queen can do the same)
                $straight = getAllStraightFields($board, $coord[0], $coord[1]);
                // top
                foreach ($straight[0] as $key=>$piece) {
                    $x        = $coord[0];
                    $y        = $coord[1] + $key + 1;
                    $to_index = getIndex($x, $y);
                    if ($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)) return true;
                    }
                }
                // right
                foreach ($straight[1] as $key=>$piece) {
                    $x        = $coord[0] + $key + 1;
                    $y        = $coord[1];
                    $to_index = getIndex($x, $y);
                    if ($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)) return true;
                    }
                }
                // down
                foreach ($straight[2] as $key=>$piece) {
                    $x        = $coord[0];
                    $y        = $coord[1] - $key - 1;
                    $to_index = getIndex($x, $y);
                    if ($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if(!isPlayerCheck($newBoard, $color)) return true;
                    }
                }
                // left
                foreach ($straight[3] as $key=>$piece) {
                    $x        = $coord[0] - $key - 1;
                    $y        = $coord[1];
                    $to_index = getIndex($x, $y);
                    if ($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)) return true;
                    }
                }
            } else if (strtoupper($piece) == 'K') {
                // Which moves could a king possibly make?
                $kings_moves = array(-9,-8-7,-1,1,7,8,9);
                foreach ($kings_moves as $add) {
                    $to_index = $from_index + $add;
                    if (!(0 <= $to_index and $to_index <= 63)) continue;
                    $from_coord = getCoordinates($from_index);
                    $to_coord   = getCoordinates($to_index);
                    $tmp_x      = $to_coord[0];
                    $tmp_y      = $to_coord[1];
                    if (!(abs($tmp_x-$from_coord[0]) + 
                          abs($tmp_y-$from_coord[1])    <= 2))
                        continue;
                    $piece = getPieceByIndex($board, getIndex($tmp_x, $tmp_y));
                    if ($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)) return true;
                    }
                }
            }
        }
    }
    return false;
}

/** This function checks if a chess piece of the opponent threatens the king
 *  directly (rook or queen)
 * 
 * @param char   $piece     the current board as a single string
 * @param string $yourColor either 'white' or 'black'
 *
 * @return bool
 */
function isStraightDanger($piece, $yourColor)
{
    if ( isOpponentsPiece($piece, $yourColor) ) {
        if      (strtolower($piece) == 'q') return true;
        else if (strtolower($piece) == 'r') return true;
    }
    return false;
}

/** This function checks if a chess piece of the opponent threatens the king
 *  diagonally (bishop or queen)
 * 
 * @param char   $piece        the current board as a single string
 * @param string $yourColor    either 'white' or 'black'
 * @param bool   $comesFromTop it the piece comming from top or bottom?
 * @param int    $abs_king     number of diagonal fields from king to piece 
 *                             (in [1;7])
 *
 * @return bool
 */
function isDiagonalDanger($piece, $yourColor, $comesFromTop, $abs_king)
{
    if ( isOpponentsPiece($piece, $yourColor) ) {
        if      (strtolower($piece) == 'q') return true;
        else if (strtolower($piece) == 'b') return true;
        else if (strtolower($piece) == 'p' and 
                 (($comesFromTop  and $yourColor == 'white') or
                  (!$comesFromTop and $yourColor == 'black')) and
                  ($abs_king == 1) ) return true;
    }
    return false;
}

/** This function checks if the given player is check
 * 
 * @param string $newBoard  the board
 * @param string $yourColor either 'white' or 'black'
 *
 * @return bool
 */
function isPlayerCheck($newBoard, $yourColor)
{
    if ($yourColor == 'white') {
        $king_index = strpos($newBoard, 'K');
    } else {
        $king_index = strpos($newBoard, 'k');
    }
    $coord  = getCoordinates($king_index);
    $king_x = $coord[0];
    $king_y = $coord[1];

    $fields = getAllStraightFields($newBoard, $king_x, $king_y);
    // danger from top?
    if (isStraightDanger(end($fields[0]), $yourColor)) return true;
    // danger from right
    if (isStraightDanger(end($fields[1]), $yourColor)) return true;
    // danger from bottom
    if (isStraightDanger(end($fields[2]), $yourColor)) return true;
    // danger from left
    if (isStraightDanger(end($fields[3]), $yourColor)) return true;

    $fields = getAllDiagonalFields($newBoard, $king_x, $king_y);
    // danger from diagonal right top?
    if (isDiagonalDanger(end($fields[0]), $yourColor, true, count($fields[0])) or
        // danger from diagonal right bottom?
        isDiagonalDanger(end($fields[1]), $yourColor, false, count($fields[1])) or
        // danger from diagonal left top?
        isDiagonalDanger(end($fields[2]), $yourColor, true, count($fields[2])) or
        // danger from diagonal left bottom?
        isDiagonalDanger(end($fields[3]), $yourColor, false, count($fields[3])) ) {
        return true;
    }

    // danger from knights?
    $knight_moves = array(-17, -15, -10, -6, 6, 10, 15, 17);
    foreach ($knight_moves as $add) {
        $knight_index = $king_index + $add;
        if (!(0<= $knight_index and $knight_index <= 63)) continue;
        $knight_coord = getCoordinates($knight_index);
        $tmp_x        = $knight_coord[0];
        $tmp_y        = $knight_coord[1];
        if (abs($king_x - $tmp_x) + abs($king_y - $tmp_y) == 3) {
            if (!isPositionValid($tmp_x, $tmp_y)) continue;
            $piece = getPieceByIndex($newBoard, getIndex($tmp_x, $tmp_y));
            if ($piece == 'n' and $yourColor == 'white') return true;
            else if ($piece == 'N' and $yourColor == 'black') return true;
        }
    }
    return false;
}

/** This function checks if a given knight move is valid
 * 
 * @param int    $from_x       has to be in [0;7]
 * @param int    $from_y       has to be in [0;7]
 * @param int    $to_x         has to be in [0;7]
 * @param int    $to_y         has to be in [0;7]
 * @param string $currentBoard the board
 * @param string $yourColor    either 'white' or 'black'
 *
 * @return bool
 */
function isKnightMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, $yourColor)
{
    if (abs($to_y - $from_y) + abs($to_x - $from_x) == 3) {
        // Everything is ok.
    } else {
        exit(sprintf(ERR_NO_VALID_MOVE, $from_x, $from_y, $to_x, $to_y, 'knight'));
    }

    $index = getIndex($to_x, $to_y);
    $piece = getPieceByIndex($currentBoard, $index);
    if ($piece != '0') {
        if ( isMyPiece($piece, $yourColor) ) {
            exit(ERR_CAPTURE_OWN);
        }
    }
}

/** This function checks if a given king move is valid
 * 
 * @param int    $from_x       has to be in [0;7]
 * @param int    $from_y       has to be in [0;7]
 * @param int    $to_x         has to be in [0;7]
 * @param int    $to_y         has to be in [0;7]
 * @param string $currentBoard the board
 * @param string $yourColor    either 'white' or 'black'
 *
 * @return bool
 */
function isKingMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, $yourColor)
{
    if (abs($from_x - $to_x) <= 1 and abs($from_y - $to_y) <= 1) {
        // Everything is ok, standard king move
    } else if (abs($from_x - $to_x) == 2 and ($from_y - $to_y) == 0) {
        // The Player wants to do castling. Is this valid?
        if ( ($yourColor == 'white' and $from_x ==  4) or
            ($yourColor == 'black' and $from_x == 60)    ) {
            $rows   = array('whiteCastlingKingsidePossible', 
                           'whiteCastlingQueensidePossible',
                           'blackCastlingKingsidePossible',
                           'blackCastlingQueensidePossible');
            $cond   = 'WHERE `id` = '.CURRENT_GAME_ID;
            $result = selectFromTable($rows, GAMES_TABLE, $cond);

            $c1 = ($to_x == 2) and ($result['whiteCastlingQueensidePossible']==0);
            $c2 = ($to_x == 6) and ($result['whiteCastlingKingsidePossible'] ==0);
            $c3 = ($to_x ==58) and ($result['blackCastlingQueensidePossible']==0);
            $c4 = ($to_x ==62) and ($result['blackCastlingKingsidePossible'] ==0);

            if ( ($yourColor == 'white' and ($c1 or $c2)) or
                ($yourColor == 'black' and ($c3 or $c4))     ) {
                exit('ERROR: You\'ve already moved your King or your rook');
            }
            // Is anything in between King and Rook?
            if ($from_x < $to_x) {
                for ($x_tmp = $from_x+1; $x_tmp < $to_x; $x_tmp++) {
                    $index = getIndex($x_tmp, $from_y);
                    $piece = getPieceByIndex($currentBoard, $index);
                    if ($piece != '0') {
                        exit("ERROR: $piece is between your King and your ".
                                    'rook. Castling is not possible.');
                    }
                }
            } else {
                for ($x_tmp = $from_x-1; $x_tmp > $to_x; $x_tmp--) {
                    $index = getIndex($x_tmp, $from_y);
                    $piece = getPieceByIndex($currentBoard, $index);
                    if ($piece != '0') {
                        exit("ERROR: $piece is between your King and your ".
                                    'rook. Castling is not possible.');
                    }
                }
            }

            // Is player currently in check or will he move through check?
            if ($from_x < $to_x) {
                for ($i=0; $i <= ($to_x-$from_x); $i++) {
                    $newBoard =
                        getNewBoard($currentBoard, $from_index, $to_index + $i);
                    if (isPlayerCheck($newBoard, $yourColor)) {
                        exit('ERROR: You may only use castling if you are not '.
                             'in check or moving through check.');
                    }
                }
            }
        } else {
            exit('ERROR: Castling is only possible, if you didn\'t move your'.
                        'King before.');
        }
    } else {
        exit(sprintf(ERR_NO_VALID_MOVE, $from_x, $from_y, $to_x, $to_y, 'king'));
    }

    $index = getIndex($to_x, $to_y);
    $piece = getPieceByIndex($currentBoard, $index);
    if ($piece != '0') {
        if ( isMyPiece($piece, $yourColor) ) {
            exit(ERR_CAPTURE_OWN);
        }
    }
}

/** This function checks if a given bishop move is valid
 * 
 * @param int    $from_x       has to be in [0;7]
 * @param int    $from_y       has to be in [0;7]
 * @param int    $to_x         has to be in [0;7]
 * @param int    $to_y         has to be in [0;7]
 * @param string $currentBoard the board
 * @param string $yourColor    either 'white' or 'black'
 *
 * @return bool
 */
function isBishopMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, $yourColor)
{
    if (abs($from_x - $to_x) == abs($from_y - $to_y)) {
        // moving diagonal (same as queen)
        if ($from_y < $to_y) {
            // moving up
            if ($from_x < $to_x) {
                // moving right up
                for ($i=1; $i < ($to_y-$from_y); $i++) {
                    $x_tmp = $from_x + $i;
                    $y_tmp = $from_y + $i;
                    $index = getIndex($x_tmp, $y_tmp);
                    $piece = getPieceByIndex($currentBoard, $index);
                    if ($piece != '0') {
                        exit("ERROR: On ($x_tmp | $y_tmp) is $piece.");
                    }
                }
            } else {
                // moving left up
                for ($i=1; $i < ($to_y-$from_y); $i++) {
                    $x_tmp = $from_x - $i;
                    $y_tmp = $from_y + $i;
                    $index = getIndex($x_tmp, $y_tmp);
                    $piece = getPieceByIndex($currentBoard, $index);
                    if ($piece != '0') {
                        exit("ERROR: On ($x_tmp | $y_tmp) is $piece.");
                    }
                }
            }
        } else {
            // moving down
            if ($from_x < $to_x) {
                // moving right down
                for ($i=1; $i < ($from_y - $to_y); $i++) {
                    $x_tmp = $from_x + $i;
                    $y_tmp = $from_y - $i;
                    $index = getIndex($x_tmp, $y_tmp);
                    $piece = getPieceByIndex($currentBoard, $index);
                    if ($piece != '0') {
                        exit("ERROR: On ($x_tmp | $y_tmp) is $piece.");
                    }
                }
            } else {
                // moving left down
                for ($i=1; $i < ($from_y - $to_y); $i++) {
                    $x_tmp = $from_x - $i;
                    $y_tmp = $from_y - $i;
                    $index = getIndex($x_tmp, $y_tmp);
                    $piece = getPieceByIndex($currentBoard, $index);
                    if ($piece != '0') {
                        exit("ERROR: On ($x_tmp | $y_tmp) is $piece.");
                    }
                }
            }
        }
    } else {
        exit(sprintf(ERR_NO_VALID_MOVE, $from_x, $from_y, $to_x, $to_y, 'bishop'));
    }

    $index = getIndex($to_x, $to_y);
    $piece = getPieceByIndex($currentBoard, $index);
    if ($piece != '0') {
        if ( isMyPiece($piece, $yourColor) ) {
            exit(ERR_CAPTURE_OWN);
        }
    }
}

/** This function checks if a given rook move is valid
 * 
 * @param int    $from_x       has to be in [0;7]
 * @param int    $from_y       has to be in [0;7]
 * @param int    $to_x         has to be in [0;7]
 * @param int    $to_y         has to be in [0;7]
 * @param string $currentBoard the board
 * @param string $yourColor    either 'white' or 'black'
 *
 * @return bool
 */
function isRookMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, $yourColor)
{
    if ($from_x == $to_x) {
        // moving straight up / down
        if ($from_y < $to_y) {
            // is moving up
            for ($y_tmp=$from_y+1; $y_tmp < $to_y; $y_tmp++) {
                $index = getIndex($from_x, $y_tmp);
                $piece = getPieceByIndex($currentBoard, $index);
                if ($piece != '0') {
                    exit("ERROR: On ($from_x | $y_tmp) is $piece.");
                }
            }
        } else {
            // is moving down
            for ($y_tmp=$from_y-1; $y_tmp > $to_y; $y_tmp--) {
                $index = getIndex($from_x, $y_tmp);
                $piece = getPieceByIndex($currentBoard, $index);
                if ($piece != '0') {
                    exit("ERROR: On ($from_x | $y_tmp) is $piece.");
                }
            }
        }
    } else if ($from_y == $to_y) {
        // moving straight left / right
        if ($from_x < $to_x) {
            // is moving right
            for ($x_tmp=$from_x+1; $x_tmp < $to_x; $x_tmp++) {
                $index = getIndex($x_tmp, $from_y);
                $piece = getPieceByIndex($currentBoard, $index);
                if ($piece != '0') {
                    exit("ERROR: On ($x_tmp | $from_y) is $piece.");
                }
            }
        } else {
            // is moving left
            for ($x_tmp=$from_x-1; $x_tmp > $to_x; $x_tmp--) {
                $index = getIndex($x_tmp, $from_y);
                $piece = getPieceByIndex($currentBoard, $index);
                if ($piece != '0') {
                    exit("ERROR: On ($x_tmp | $from_y) is $piece.");
                }
            }
        }

    } else {
        exit(sprintf(ERR_NO_VALID_MOVE, $from_x, $from_y, $to_x, $to_y, 'rook'));
    }


    $index = getIndex($to_x, $to_y);
    $piece = getPieceByIndex($currentBoard, $index);
    if ($piece != '0') {
        if ( isMyPiece($piece, $yourColor) ) {
            exit(ERR_CAPTURE_OWN);
        }
    }
}

/** This function checks if a given pawn move is valid
 * 
 * @param int    $from_x       has to be in [0;7]
 * @param int    $from_y       has to be in [0;7]
 * @param int    $to_x         has to be in [0;7]
 * @param int    $to_y         has to be in [0;7]
 * @param string $currentBoard the board
 * @param string $yourColor    either 'white' or 'black'
 * @param string $moveList     a list of all moves
 *
 * @return string "en passant" if the move was "en passant"
 */
function isPawnMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                         $yourColor, $moveList)
{
    if ($from_x == $to_x and abs($from_y - $to_y) <= 2) {
        // moving up / down
        $index = getIndex($to_x, $to_y);
        $piece = getPieceByIndex($currentBoard, $index);
        if ($piece != '0') {
            exit('ERROR: A pawn can only capture by moving diagonal.');
        }
        if ($yourColor == 'white') {
            if ($from_y > $to_y)
                exit('ERROR: White may only move up with pawns.');
            if ($from_y == 2) $isOnHomeRow = true;
            else              $isOnHomeRow = false;
        } else {
            if ($from_y < $to_y) 
                exit('ERROR: Black may only move down with pawns.');
            if ($from_y == 7) $isOnHomeRow = true;
            else              $isOnHomeRow = false;
        }
        if (abs($from_y - $to_y) == 2 and $isOnHomeRow == false) {
                exit('ERROR: Pawns may only move two if they are on their home'.
                            ' row.');
        }
    } else if (abs($from_x-$to_x) == 1 and abs($from_y-$to_y) == 1) {
        // pawns capturing move
        $index        = getIndex($to_x, $to_y);
        $piece_target = getPieceByIndex($currentBoard, $index);

        // For en passant:
        $moveArray = explode("\n", trim($moveList));
        $lastMove  = end($moveArray);
        $lastFromX = substr($lastMove, 0, 1);
        $lastFromY = substr($lastMove, 1, 1);
        $lastToX   = substr($lastMove, 2, 1);
        $lastToY   = substr($lastMove, 3, 1);

        if ($piece_target == '0') {
            if ($yourColor == 'white') {
                if ($from_y > $to_y) {
                    exit('ERROR: White may only move up with pawns.');
                }

                $index        = getIndex($to_x, $to_y-1);
                $shouldBePawn = getPieceByIndex($currentBoard, $index);
                // was the last move done by the opponet a two-fields pawn of
                // and is the current player trying to capture this pawn?
                if ($lastFromX == $lastToX and $lastToX == $to_x and 
                    $lastFromY - $lastToY == 2 and
                    $shouldBePawn == 'p') {
                    return "en passant";
                } else {
                    exit(ERR_PAWN_CAPTURE_MOVE);
                }
            } else {
                if ($from_y < $to_y) {
                    exit('ERROR: Black may only move down with pawns.');
                }
                $index        = getIndex($to_x, $to_y+1);
                $shouldBePawn = getPieceByIndex($currentBoard, $index);
                if ($lastFromX == $lastToX and $lastToX == $to_x and 
                    $lastToY - $lastFromY == 2 and
                    $shouldBePawn == 'P') {
                    return "en passant";
                } else {
                    exit(ERR_PAWN_CAPTURE_MOVE);
                }
            }
        }
    } else {
        exit(sprintf(ERR_NO_VALID_MOVE, $from_x, $from_y, $to_x, $to_y, 'pawn'));
    }

    $index = getIndex($to_x, $to_y);
    $piece = getPieceByIndex($currentBoard, $index);
    if ($piece != '0') {
        if ( isMyPiece($piece, $yourColor) ) {
            exit(ERR_CAPTURE_OWN);
        }
    }

}

/** This function checks if a given queen move is valid
 * 
 * @param int    $from_x       has to be in [0;7]
 * @param int    $from_y       has to be in [0;7]
 * @param int    $to_x         has to be in [0;7]
 * @param int    $to_y         has to be in [0;7]
 * @param string $currentBoard the board
 * @param string $yourColor    either 'white' or 'black'
 *
 * @return bool
 */
function isQueenMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, $yourColor)
{
    if ($from_x == $to_x) {
        // moving straight up / down
        if ($from_y < $to_y) {
            // is moving up
            for ($y_tmp=$from_y+1; $y_tmp < $to_y; $y_tmp++) {
                $index = getIndex($from_x, $y_tmp);
                $piece = getPieceByIndex($currentBoard, $index);
                if ($piece != '0') {
                    exit("ERROR: On ($from_x | $y_tmp) is $piece.");
                }
            }
        } else {
            // is moving down
            for ($y_tmp=$from_y-1; $y_tmp > $to_y; $y_tmp--) {
                $index = getIndex($from_x, $y_tmp);
                $piece = getPieceByIndex($currentBoard, $index);
                if ($piece != '0') {
                    exit("ERROR: On ($from_x | $y_tmp) is $piece.");
                }
            }
        }
    } else if ($from_y == $to_y) {
        // moving straight left / right
        if ($from_x < $to_x) {
            // is moving right
            for ($x_tmp=$from_x+1; $x_tmp < $to_x; $x_tmp++) {
                $index = getIndex($x_tmp, $from_y);
                $piece = getPieceByIndex($currentBoard, $index);
                if ($piece != '0') {
                    exit("ERROR: On ($x_tmp | $from_y) is $piece.");
                }
            }
        } else {
            // is moving left
            for ($x_tmp=$from_x-1; $x_tmp > $to_x; $x_tmp--) {
                $index = getIndex($x_tmp, $from_y);
                $piece = getPieceByIndex($currentBoard, $index);
                if ($piece != '0') {
                    exit("ERROR: On ($x_tmp | $from_y) is $piece.");
                }
            }
        }
    } else if (abs($from_x - $to_x) == abs($from_y - $to_y)) {
        // moving diagonal (same as bishop)
        if ($from_y < $to_y) {
            // moving up
            if ($from_x < $to_x) {
                // moving right up
                for ($i=1; $i < ($to_y-$from_y); $i++) {
                    $x_tmp = $from_x + $i;
                    $y_tmp = $from_y + $i;
                    $index = getIndex($x_tmp, $y_tmp);
                    $piece = getPieceByIndex($currentBoard, $index);
                    if ($piece != '0') {
                        exit("ERROR: On ($x_tmp | $y_tmp) is $piece.");
                    }
                }
            } else {
                // moving left up
                for ($i=1; $i < ($to_y-$from_y); $i++) {
                    $x_tmp = $from_x - $i;
                    $y_tmp = $from_y + $i;
                    $index = getIndex($x_tmp, $y_tmp);
                    $piece = getPieceByIndex($currentBoard, $index);
                    if ($piece != '0') {
                        exit("ERROR: On ($x_tmp | $y_tmp) is $piece.");
                    }
                }
            }
        } else {
            // moving down
            if ($from_x < $to_x) {
                // moving right down
                for ($i=1; $i < ($from_y - $to_y); $i++) {
                    $x_tmp = $from_x + $i;
                    $y_tmp = $from_y - $i;
                    $index = getIndex($x_tmp, $y_tmp);
                    $piece = getPieceByIndex($currentBoard, $index);
                    if ($piece != '0') {
                        exit("ERROR: On ($x_tmp | $y_tmp) is $piece.");
                    }
                }
            } else {
                // moving left down
                for ($i=1; $i < ($from_y - $to_y); $i++) {
                    $x_tmp = $from_x - $i;
                    $y_tmp = $from_y - $i;
                    $index = getIndex($x_tmp, $y_tmp);
                    $piece = getPieceByIndex($currentBoard, $index);
                    if ($piece != '0') {
                        exit("ERROR: On ($x_tmp | $y_tmp) is $piece.");
                    }
                }
            }
        }
    } else {
        exit(sprintf(ERR_NO_VALID_MOVE, $from_x, $from_y, $to_x, $to_y, 'queen'));
    }


    $index = getIndex($to_x, $to_y);
    $piece = getPieceByIndex($currentBoard, $index);
    if ($piece != '0') {
        if ( isMyPiece($piece, $yourColor) ) {
            exit(ERR_CAPTURE_OWN);
        }
    }

}

/** This function submits the move to the database. All checks if this move is 
 *  possible have been done before. 
 *  All database-changes of the current game are done in this function.
 *  Nothing else.
 * 
 * @param int    $from_index   has to be in [0;63]
 * @param int    $to_index     has to be in [0;63]
 * @param string $currentBoard the board
 * @param string $move         4 or 5 characters with the move
 * @param string $yourColor    either 'white' or 'black'
 * @param string $en_passant   either 'en passant' or false
 *
 * @return bool
 */
function makeMove($from_index, $to_index, $currentBoard, $move, $yourColor, 
                  $en_passant) 
{
    $piece         = getPieceByIndex($currentBoard, $from_index);
    $capturedPiece = getPieceByIndex($currentBoard, $to_index);
    $to_coord      = getCoordinates($to_index);
    $from_coord    = getCoordinates($from_index);
    $cond          = 'WHERE  '.GAMES_TABLE.'.`id` ='.CURRENT_GAME_ID;

    $submissionTime = time();
    $rows           = array('timeLimit', 'lastMove');
    $result         = selectFromTable($rows, GAMES_TABLE, $cond);
    $timeNeeded     = $submissionTime - $result['lastMove'];
    if ($timeNeeded > $result['timeLimit'] and $result['timeLimit'] != 0) {
        if ($yourColor == 'white') {
            // The current player needed too much time for his move, so he lost
            // He is white, so black won the game => $outcome = 1
            finishGame(1);
        } else {
            // The current player needed too much time for his move, so he lost
            // He is black, so white won the game => $outcome = 0
            finishGame(0);
        }
        exit("Game finished. You lost. You needed too much time for your move. ".
             "You needed $timeNeeded seconds, but only ".$result['timeLimit'].
             " seconds were allowed per move.");
    }
    

    if ($piece == 'p' or $piece == 'P') {
        $pawnMoved = true;
    } else {
        $pawnMoved = false;
    }
    if ($capturedPiece != '0') {
        $captureMade = true;
    } else {
        $captureMade = false;
    }

    // Is this move en passant?
    if ( $en_passant == 'en passant' ) {
        if ($yourColor == 'white') {
            $currentBoard = substr($currentBoard, 0, $to_index-8).'0'.
                            substr($currentBoard, ($to_index-8)+1, 
                                         strlen($currentBoard)-($to_index-8)-1);
        } else {
            $currentBoard = substr($currentBoard, 0, $to_index+8).'0'.
                            substr($currentBoard, ($to_index-8)+1, 
                                         strlen($currentBoard)-($to_index+8)-1);
        }
    }
    // Is this move castling?
    if (    ($piece == 'K' or $piece == 'k') and 
        abs($from_coord[0] - $to_coord[0]) == 2  ) {
        // Move tower (only tower! The king will be moved in the rest of this 
        // function.
        if ($yourColor == 'white') {
            if ($to_index == 6) {
                // Kingside Castling for white
                $currentBoard = substr($currentBoard, 0, 7).'0'
                                          .substr($currentBoard, $from_index+1);
                $currentBoard = substr($currentBoard, 0, 5).'R'
                                          .substr($currentBoard, $to_index+1);
            } else {
                // Queenside Castling for white
                $currentBoard = substr($currentBoard, 0, 0).'0'
                                          .substr($currentBoard, $from_index+1);
                $currentBoard = substr($currentBoard, 0, 3).'R'
                                          .substr($currentBoard, $to_index+1);
            }
        } else {
            if ($to_index == 62) {
                // Kingside Castling for black
                $currentBoard = substr($currentBoard, 0, 63).'0'
                                          .substr($currentBoard, $from_index+1);
                $currentBoard = substr($currentBoard, 0, 61).'r'
                                          .substr($currentBoard, $to_index+1);
            } else {
                // Queenside Castling for black
                $currentBoard = substr($currentBoard, 0, 56).'0'
                                          .substr($currentBoard, $from_index+1);
                $currentBoard = substr($currentBoard, 0, 59).'r'
                                          .substr($currentBoard, $to_index+1);
            }
        }
        $keyValue                  = array();
        $keyValue['currentBoard']  = $currentBoard;
        $keyValue['moveList']      = "CONCAT(`moveList`,'$move\n')";
        $keyValue['whoseTurnIsIt'] = '((`whoseTurnIsIt` + 1)%2)';
        $keyValue['lastMove']      = 'CURRENT_TIMESTAMP';
        updateDataInTable(GAMES_TABLE, $keyValue, $cond);
    }

    // Is this piece relevant for castling?
    // White - Kingside Castling
    if ($piece == 'K' or ($piece == 'R' and $from_index == 7)) {
        $keyValue = array('whiteCastlingKingsidePossible' => 0);
        updateDataInTable(GAMES_TABLE, $keyValue, $cond);
    }
    // White - Queenside Castling
    if ($piece == 'K' or ($piece == 'R' and $from_index == 0)) {
        $keyValue = array('whiteCastlingQueensidePossible' => 0);
        updateDataInTable(GAMES_TABLE, $keyValue, $cond);
    }
    // Black - Kingside Castling
    if ($piece == 'K' or ($piece == 'R' and $from_index == 63)) {
        $keyValue = array('blackCastlingKingsidePossible' => 0);
        updateDataInTable(GAMES_TABLE, $keyValue, $cond);
    }
    // Black - Queenside Castling
    if ($piece == 'K' or ($piece == 'R' and $from_index == 56)) {
        $keyValue = array('blackCastlingQueensidePossible' => 0);
        updateDataInTable(GAMES_TABLE, $keyValue, $cond);
    }

    /* Promotion */
    if (strlen($move) == 5) {
        $promotion = strtolower(substr($move, 4, 1));
        if (!($promotion == 'q' or $promotion == 'r' or $promotion == 'b' 
                               or $promotion == 'n')) {
            exit('ERROR: You can only promote to queen (q), rook (r), '.
                        'bishop (b) or knight (n)');
        }
        if ($yourColor == 'white') $promotion = strtoupper($promotion);
        if (! ( ($piece == 'p' and $to_coord[1] == 1) or
               ($piece == 'P' and $to_coord[1] == 8)    )) {
            exit('ERROR: You may only promote when your pawn reaches the '.
                        'first line of the opponent.');
        }
        $move  = substr($move, 0, 4).$promotion;
        $piece = $promotion;
    }
    if ( ($piece == 'p' and $to_coord[1] == 1 and $promotion == '') or
        ($piece == 'P' and $to_coord[1] == 8 and $promotion == '')    ) {
        exit('ERROR: You have to promote. '.
                    'Add a single letter at the move-request.');
    }

    /* Now update the database with move */
    $currentBoard              = substr($currentBoard, 0, $from_index).'0'
                                          .substr($currentBoard, $from_index+1);
    $currentBoard              = substr($currentBoard, 0, $to_index).$piece
                                          .substr($currentBoard, $to_index+1);
    $keyValue['currentBoard']  = $currentBoard;
    $keyValue['moveList']      = "CONCAT(`moveList`,'$move\n')";
    $keyValue['whoseTurnIsIt'] = '((`whoseTurnIsIt` + 1)%2)';
    $keyValue['lastMove']      = 'CURRENT_TIMESTAMP';

    if ($pawnMoved == false and $captureMade == false) {    
        $keyValue['noCaptureAndPawnMoves'] = '`noCaptureAndPawnMoves` + 1 ';
    } else {
        $keyValue['noCaptureAndPawnMoves'] = '0';
    }

    updateDataInTable(GAMES_TABLE, $keyValue, $cond);

    /* Get all data for the threefold repetition table*/
    /* Castling? */
    $rows   = array('whiteCastlingKingsidePossible',
                    'whiteCastlingQueensidePossible',
                    'blackCastlingKingsidePossible',
                    'blackCastlingQueensidePossible');
    $result = selectFromTable($rows, GAMES_TABLE, $cond);
    /* Is en passant possible? */
    /* was last move a pawn-2move? */
    if ($pawnMoved and abs($from_coord[1]-$to_coord[1]) == 2) {
        $wasPawn2move = true;
    } else {
        $wasPawn2move = false;
    }
    /* is left/right a pawn of the opponent?*/    
    $isOpponentNext = false;
    if ($to_coord[0] - 1 >= 1) {
        $indexLeft = getIndex($to_coord[0]-1, $to_coord[1]);
        $pieceLeft = getPieceByIndex($currentBoard, $indexLeft);

        if ($pieceLeft == 'p' and $yourColor == 'white') $isOpponentNext = true;
        if ($pieceLeft == 'P' and $yourColor == 'black') $isOpponentNext = true;
    }
    if ($to_coord[0] + 1 <= 8) {
        $indexRight = getIndex($to_coord[0]+1, $to_coord[1]);
        $pieceRight = getPieceByIndex($currentBoard, $indexRight);

        if ($pieceRight== 'p' and $yourColor == 'white') $isOpponentNext = true;
        if ($pieceRight== 'P' and $yourColor == 'black') $isOpponentNext = true;
    }

    if ($isOpponentNext and $wasPawn2move) $enPassant = '1';
    else                                   $enPassant = '0';

    /* Insert the new situation into GAMES_THREEFOLD_REPETITION_TABLE */
    $keyValuePairs                                   = array();
    $keyValuePairs['gameID']                         = CURRENT_GAME_ID;
    $keyValuePairs['board']                          = $currentBoard;
    $keyValuePairs['whiteCastlingKingsidePossible']  = 
                                       $result['whiteCastlingKingsidePossible'];
    $keyValuePairs['whiteCastlingQueensidePossible'] = 
                                      $result['whiteCastlingQueensidePossible'];
    $keyValuePairs['blackCastlingKingsidePossible']  = 
                                       $result['blackCastlingKingsidePossible'];
    $keyValuePairs['blackCastlingQueensidePossible'] = 
                                      $result['blackCastlingQueensidePossible'];
    $keyValuePairs['enPassantPossible']              = $enPassant;
    insertIntoTable($keyValuePairs, GAMES_THREEFOLD_REPETITION_TABLE);

    return $currentBoard;
}

?>
