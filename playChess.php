<?
/**
 * @author: Martin Thoma
 * play a standard chess game
 * */

require ('wrapper.inc.php');
if(USER_ID === false){exit("Please <a href='login.wrapper.php'>login</a>");}

/******************************************************************************
 * helper functions                                                           *
 ******************************************************************************/
function getIndex($x, $y){
    return ($y-1) * 8 + ($x-1);
}

function getCoordinates($index){
    $x = $index % 8;
    $y = ($index - $x)/8;
    $x++;
    $y++;
    return array($x, $y);
}

function isPositionValid($x, $y) {
    if (1 <= $x and $x <= 8 and 1 <= $y and $y <= 8){return true;}
    else {return false;}
}

function getPieceByIndex($board, $index) {
    return substr($board,$index,1);
}

function getNewBoard($currentBoard, $from_index, $to_index){
    $piece    = getPieceByIndex($currentBoard, $from_index);
    $newBoard = substr($currentBoard, 0, $from_index)."0".
                substr($currentBoard, $from_index+1);
    $newBoard = substr($newBoard, 0, $to_index).$piece.
                substr($newBoard, $to_index+1);
    return $newBoard;
}

function isMyPiece($piece, $myColor) {
    if( ( (ord($piece) < 96 and $myColor == 'white') or
          (ord($piece) > 96 and $myColor == 'black')    )  and $piece != '0'){
        return true;
    } else {
        return false;
    }
}

function isOpponentsPiece($piece, $myColor) {
    if( ( (ord($piece) < 96 and $myColor == 'black') or
          (ord($piece) > 96 and $myColor == 'white')    )  and $piece != '0'){
        return true;
    } else {
        return false;
    }
}

function getAllStraightFields($board, $x, $y) {
    /**This function returns a 2-dimensional array:
     * array[0] = array('0','0','P') # to the top
     * array[1] = array('p')         # to the right
     * array[2] = array('0','0','0','0','0', 'Q') # to the bottom
     * array[3] = array('N')                      # to the left
     * */

    $straights = array(0=>array(), 1=>array(), 2=>array(), 3=>array());

    # Which moves could a rook possibly make?
    # top
    $tmp_x = $x; $tmp_y = $y;
    for($tmp_y=$y+1; $tmp_y <= 8; $tmp_y++){
        $index = getIndex($tmp_x, $tmp_y);
        $piece = getPieceByIndex($board,$index);
        $straights[0][] = $piece;
        if($piece != '0') {break;}
    }
    # right
    $tmp_x = $x; $tmp_y = $y;
    for($tmp_x=$x+1; $tmp_x <= 8; $tmp_x++){
        $index = getIndex($tmp_x, $tmp_y);
        $piece = getPieceByIndex($board,$index);
        $straights[1][] = $piece;
        if($piece != '0') {break;}
    }
    # down
    $tmp_x = $x; $tmp_y = $y;
    for($tmp_y=$y-1; $tmp_y >= 1; $tmp_y--){
        $index = getIndex($tmp_x, $tmp_y);
        $piece = getPieceByIndex($board,$index);
        $straights[2][] = $piece;
        if($piece != '0') {break;}
    }
    # left
    $tmp_x = $x; $tmp_y = $y;
    for($tmp_x=$x-1; $tmp_x >= 1; $tmp_x--){
        $index = getIndex($tmp_x, $tmp_y);
        $piece = getPieceByIndex($board,$index);
        $straights[3][] = $piece;
        if($piece != '0') {break;}
    }
    return $straights;
}

function getAllDiagonalFields($board, $x, $y) {
    /**This function returns a 2-dimensional array:
     * array[0] = array('0','0','P') # to the top-right
     * array[1] = array('p')         # to the bottom-right
     * array[2] = array('0','0','0','0','0', 'Q') # to the top-left
     * array[3] = array('N')                      # to the bottom-left
     * */

    $diagonals = array(0=>array(), 1=>array(), 2=>array(), 3=>array());

    # Which moves could a bishop possibly make?
    # diagonal right up
    for($i=1; $i <= 8 - max($x, $y); $i++){
        $tmp_x = $x + $i;
        $tmp_y = $y + $i;
        $index = getIndex($tmp_x, $tmp_y);
        $piece = getPieceByIndex($board,$index);
        $diagonals[0][] = $piece;
        if($piece != '0') {break;}
    }
    # diagonal right down
    for($i=1; $i <= 8 - max($x, 9 - $y); $i++){
        $tmp_x = $x + $i;
        $tmp_y = $y - $i;
        $index = getIndex($tmp_x, $tmp_y);
        $piece = getPieceByIndex($board,$index);
        $diagonals[1][] = $piece;
        if($piece != '0') {break;}
    }
    # diagonal left up
    for($i=1; $i <= 8 - max(9-$x, $y); $i++){
        $tmp_x = $x - $i;
        $tmp_y = $y + $i;
        $index = getIndex($tmp_x, $tmp_y);
        $piece = getPieceByIndex($board,$index);
        $diagonals[2][] = $piece;
        if($piece != '0') {break;}
    }
    # diagonal left down
    for($i=1; $i <= min($x, $y)-1; $i++){
        $tmp_x = $x - $i;
        $tmp_y = $y - $i;
        $index = getIndex($tmp_x, $tmp_y);
        $piece = getPieceByIndex($board,$index);
        $diagonals[3][] = $piece;
        if($piece != '0') {break;}
    }
    return $diagonals;
}
/******************************************************************************
 * chess game relevant functions                                              *
 ******************************************************************************/
function hasValidMoves($board, $color){
    for($from_index = 0; $from_index < 63; $from_index++){
        $piece = getPieceByIndex($board,$from_index);
        $coord = getCoordinates($from_index);

        if(isMyPiece($piece, $color)) {
            if(strtoupper($piece) == 'P'){
                # Which moves could a pawn possibly make?
                if ($color == 'white'){$mul = 1;}
                else {$mul = -1;}
                # Which moves could a pawn possibly make?
                if( ($coord[1] < 8 and $color == 'white') or
                    ($coord[1] > 1 and $color == 'black')){
                    #one straight up / down
                    $to_index = getIndex($coord[0],$coord[1]+1*$mul);
                    $targetpiece = getPieceByIndex($board,$to_index);
                    if($targetpiece == '0'){
                        $newBoard = getNewBoard($board, $from_index, 
                                                        $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }

                    if($coord[0] > 1){
                        # diagonal left capturing
                        $to_index    = getIndex($coord[0]-1, $coord[1]+1*$mul);
                        $targetpiece = getPieceByIndex($board,$to_index);
                        if(isOpponentsPiece($targetpiece, $color)){
                        $newBoard = getNewBoard($board, $from_index, 
                                                        $to_index);
                            if (!isPlayerCheck($newBoard, $color)){return true;}
                        }
                    }
                    if($coord[0] < 8){
                        # diagonal right capturing
                        $to_index = getIndex($coord[0]+1, $coord[1]+1*$mul);
                        $targetpiece = getPieceByIndex($board,$to_index);
                        if(isOpponentsPiece($targetpiece, $color)){
                        $newBoard = getNewBoard($board, $from_index, 
                                                        $to_index);
                            if (!isPlayerCheck($newBoard, $color)){return true;}
                        }
                    }
                }
                if(($coord[1] == 2 and $color == 'white') or
                   ($coord[1] == 7 and $color == 'black')    ){
                    #two straight up in home row
                    $to_index = getIndex($coord[0],$coord[1]+2*$mul);
                    $field1   = getPieceByIndex($board,$to_index+8);
                    $targetpiece = getPieceByIndex($board,$to_index);
                    if($targetpiece == '0' and $field1 == '0'){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
            } else if(strtoupper($piece) == 'N') {
                # Which moves could a knight possibly make?
                if(  isPositionValid($coord[0]+1,$coord[1]+2)  ){
                    $to_index    = getIndex($coord[0]+1, $coord[1]+2);
                    $targetPiece = getPieceByIndex($board,$to_index);
                    if($targetPiece == '0'
                       or isOpponentsPiece($targetPiece, $color)  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                if(  isPositionValid($coord[0]+1,$coord[1]-2)  ){
                    $to_index    = getIndex($coord[0]+1, $coord[1]-2);
                    $targetPiece = getPieceByIndex($board,$to_index);
                    if($targetPiece == '0'
                       or isOpponentsPiece($targetPiece, $color)  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                if(  isPositionValid($coord[0]+2,$coord[1]+1)  ){
                    $to_index    = getIndex($coord[0]+2, $coord[1]+1);
                    $targetPiece = getPieceByIndex($board,$to_index);
                    if($targetPiece == '0' 
                       or isOpponentsPiece($targetPiece, $color)  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                if(  isPositionValid($coord[0]+2,$coord[1]-1)  ){
                    $to_index    = getIndex($coord[0]+2, $coord[1]-1);
                    $targetPiece = getPieceByIndex($board,$to_index);
                    if($targetPiece == '0' 
                       or isOpponentsPiece($targetPiece, $color)  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                if(  isPositionValid($coord[0]-2,$coord[1]-1)  ){
                    $to_index    = getIndex($coord[0]-2, $coord[1]-1);
                    $targetPiece = getPieceByIndex($board,$to_index);
                    if($targetPiece == '0'
                       or isOpponentsPiece($targetPiece, $color)  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                if(  isPositionValid($coord[0]-2,$coord[1]+1)  ){
                    $to_index    = getIndex($coord[0]-2, $coord[1]+1);
                    $targetPiece = getPieceByIndex($board,$to_index);
                    if($targetPiece == '0'
                       or isOpponentsPiece($targetPiece, $color)  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                if(  isPositionValid($coord[0]-1,$coord[1]+2)  ){
                    $to_index    = getIndex($coord[0]-1, $coord[1]+2);
                    $targetPiece = getPieceByIndex($board,$to_index);
                    if($targetPiece == '0' 
                       or isOpponentsPiece($targetPiece, $color)  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                if(  isPositionValid($coord[0]-1,$coord[1]-2)  ){
                    $to_index    = getIndex($coord[0]-1, $coord[1]-2);
                    $targetPiece = getPieceByIndex($board,$to_index);
                    if($targetPiece == '0' 
                       or isOpponentsPiece($targetPiece, $color)  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
            } else if(strtoupper($piece) == 'B' or strtoupper($piece) == 'Q'){
                # Which moves could a bishop possibly make?
                # (Queen can do the same)
                $dia = getAllDiagonalFields($board, $coord[0], $coord[1]);
                # diagonal right up
                foreach($dia[0] as $key=>$piece){
                    $x = $coord[0] + $key + 1;
                    $y = $coord[1] + $key + 1;
                    $to_index = getIndex($x, $y);
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard=getNewBoard($board,$from_index,$to_index);
                        if(!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }

                # diagonal right down
                foreach($dia[1] as $key=>$piece){
                    $x = $coord[0] + $key + 1;
                    $y = $coord[1] - $key - 1;
                    $to_index = getIndex($x, $y);
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard=getNewBoard($board,$from_index,$to_index);
                        if(!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }

                # diagonal left up
                foreach($dia[2] as $key=>$piece){
                    $x = $coord[0] - $key - 1;
                    $y = $coord[1] + $key + 1;
                    $to_index = getIndex($x, $y);
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard=getNewBoard($board,$from_index,$to_index);
                        if(!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }

                # diagonal left down
                foreach($dia[3] as $key=>$piece){
                    $x = $coord[0] - $key - 1;
                    $y = $coord[1] - $key - 1;
                    $to_index = getIndex($x, $y);
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard=getNewBoard($board,$from_index,$to_index);
                        if(!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
            } else if(strtoupper($piece) == 'R' or strtoupper($piece) == 'Q') {
                # Which moves could a rook possibly make? 
                # (Queen can do the same)
                $straight = getAllStraightFields($board, $coord[0], $coord[1]);
                # top
                foreach($straight[0] as $key=>$piece) {
                    $x = $coord[0];
                    $y = $coord[1] + $key + 1;
                    $to_index = getIndex($x, $y);
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard=getNewBoard($board,$from_index,$to_index);
                        if(!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                # right
                foreach($straight[1] as $key=>$piece) {
                    $x = $coord[0] + $key + 1;
                    $y = $coord[1];
                    $to_index = getIndex($x, $y);
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard=getNewBoard($board,$from_index,$to_index);
                        if(!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                # down
                foreach($straight[2] as $key=>$piece) {
                    $x = $coord[0];
                    $y = $coord[1] - $key - 1;
                    $to_index = getIndex($x, $y);
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard=getNewBoard($board,$from_index,$to_index);
                        if(!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                # left
                foreach($straight[3] as $key=>$piece) {
                    $x = $coord[0] - $key - 1;
                    $y = $coord[1];
                    $to_index = getIndex($x, $y);
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard=getNewBoard($board,$from_index,$to_index);
                        if(!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
            } else if(strtoupper($piece) == 'K') {
                # Which moves could a king possibly make?
                $tmp_x = $coord[0]+0;
                $tmp_y = $coord[1]+1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = getPieceByIndex($board,getIndex($tmp_x, $tmp_y));
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                $tmp_x = $coord[0]+1;
                $tmp_y = $coord[1]+1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = getPieceByIndex($board,getIndex($tmp_x, $tmp_y));
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                $tmp_x = $coord[0]+1;
                $tmp_y = $coord[1]+0;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = getPieceByIndex($board,getIndex($tmp_x, $tmp_y));
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                $tmp_x = $coord[0]+1;
                $tmp_y = $coord[1]-1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = getPieceByIndex($board,getIndex($tmp_x, $tmp_y));
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                $tmp_x = $coord[0]+0;
                $tmp_y = $coord[1]-1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = getPieceByIndex($board,getIndex($tmp_x, $tmp_y));
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                $tmp_x = $coord[0]-1;
                $tmp_y = $coord[1]-1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = getPieceByIndex($board,getIndex($tmp_x, $tmp_y));
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                $tmp_x = $coord[0]-1;
                $tmp_y = $coord[1]-0;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = getPieceByIndex($board,getIndex($tmp_x, $tmp_y));
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
                $tmp_x = $coord[0]-1;
                $tmp_y = $coord[1]+1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = getPieceByIndex($board,getIndex($tmp_x, $tmp_y));
                    if($piece == '0' or isOpponentsPiece($piece, $color)) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, $color)){return true;}
                    }
                }
            }
        }
    }
    return false;
}

function isStraightDanger($piece, $yourColor) {
    if ( isOpponentsPiece($piece, $yourColor) and $yourColor == 'white'){
        if($piece == 'k'){exit("SOFTWARE-ERROR: How can a king face a king?a");}
        else if ($piece == 'q'){return true;}
        else if ($piece == 'r'){return true;}
    } else if ( isOpponentsPiece($piece, $yourColor) and $yourColor == 'black'){
        if($piece == 'K'){exit("SOFTWARE-ERROR: How can a king face a king?b");}
        else if ($piece == 'Q'){return true;}
        else if ($piece == 'R'){return true;}
    }
    return false;
}

function isDiagonalDanger($piece, $yourColor){
    if ( isOpponentsPiece($piece, $yourColor) and $yourColor == 'white'){
        if($piece == 'k'){exit("SOFTWARE-ERROR: How can a king face a king?i");}
        else if ($piece == 'q'){return true;}
        else if ($piece == 'b'){return true;}
        else if ($piece == 'p' and abs($tmp_x-$king_x)==1){return true;}
    } else if ( isOpponentsPiece($piece, $yourColor) and $yourColor == 'black'){
        if($piece == 'K'){exit("SOFTWARE-ERROR: How can a king face a king?j");}
        else if ($piece == 'Q'){return true;}
        else if ($piece == 'B'){return true;}
    }
    return false;
}

function isPlayerCheck($newBoard, $yourColor){
    if ($yourColor == 'white'){
        $king_index = strpos($newBoard, 'K');
    } else {
        $king_index = strpos($newBoard, 'k');
    }
    $coord  = getCoordinates($king_index);
    $king_x = $coord[0];
    $king_y = $coord[1];

    $fields = getAllStraightFields($newBoard, $king_x, $king_y);
    # danger from top?
    if(isStraightDanger(end($fields[0]), $yourColor)){return true;}
    # danger from right
    if(isStraightDanger(end($fields[1]), $yourColor)){return true;}
    # danger from bottom
    if(isStraightDanger(end($fields[2]), $yourColor)){return true;}
    # danger from left
    if(isStraightDanger(end($fields[3]), $yourColor)){return true;}

    $fields = getAllDiagonalFields($newBoard, $king_x, $king_y);
    # danger from diagonal right top?
    if(isStraightDanger(end($fields[0]), $yourColor)){return true;}
    # danger from diagonal right bottom?
    if(isStraightDanger(end($fields[1]), $yourColor)){return true;}
    # danger from diagonal left top?
    if(isStraightDanger(end($fields[2]), $yourColor)){return true;}
    # danger from diagonal left bottom?
    if(isStraightDanger(end($fields[3]), $yourColor)){return true;}

    # danger from knights?
    # from very top left?
    $tmp_x = $king_x - 1;
    $tmp_y = $king_y + 2;
    if(isPositionValid($tmp_x, $tmp_y)){
        $piece = getPieceByIndex($newBoard,getIndex($tmp_x, $tmp_y));
        if($piece == 'n' and $yourColor == 'white'){
            return true;
        } else if ($piece == 'N' and $yourColor == 'black'){
            return true;
        }
    }
    # from very top right?
    $tmp_x = $king_x + 1;
    $tmp_y = $king_y + 2;
    if(isPositionValid($tmp_x, $tmp_y)){
        $piece = getPieceByIndex($newBoard,getIndex($tmp_x, $tmp_y));
        if($piece == 'n' and $yourColor == 'white'){
            return true;
        } else if ($piece == 'N' and $yourColor == 'black'){
            return true;
        }
    }
    # from top right?
    $tmp_x = $king_x + 2;
    $tmp_y = $king_y + 1;
    if(isPositionValid($tmp_x, $tmp_y)){
        $piece = getPieceByIndex($newBoard,getIndex($tmp_x, $tmp_y));
        if($piece == 'n' and $yourColor == 'white'){
            return true;
        } else if ($piece == 'N' and $yourColor == 'black'){
            return true;
        }
    }
    # from bottom right?
    $tmp_x = $king_x + 2;
    $tmp_y = $king_y - 1;
    if(isPositionValid($tmp_x, $tmp_y)){
        $piece = getPieceByIndex($newBoard,getIndex($tmp_x, $tmp_y));
        if($piece == 'n' and $yourColor == 'white'){
            return true;
        } else if ($piece == 'N' and $yourColor == 'black'){
            return true;
        }
    }
    # from very bottom right?
    $tmp_x = $king_x + 1;
    $tmp_y = $king_y - 2;
    if(isPositionValid($tmp_x, $tmp_y)){
        $piece = getPieceByIndex($newBoard,getIndex($tmp_x, $tmp_y));
        if($piece == 'n' and $yourColor == 'white'){
            return true;
        } else if ($piece == 'N' and $yourColor == 'black'){
            return true;
        }
    }

    # from very bottom left?
    $tmp_x = $king_x - 1;
    $tmp_y = $king_y - 2;
    if(isPositionValid($tmp_x, $tmp_y)){
        $piece = getPieceByIndex($newBoard,getIndex($tmp_x, $tmp_y));
        if($piece == 'n' and $yourColor == 'white'){
            return true;
        } else if ($piece == 'N' and $yourColor == 'black'){
            return true;
        }
    }

    # from bottom left?
    $tmp_x = $king_x - 2;
    $tmp_y = $king_y - 1;
    if(isPositionValid($tmp_x, $tmp_y)){
        $piece = getPieceByIndex($newBoard,getIndex($tmp_x, $tmp_y));
        if($piece == 'n' and $yourColor == 'white'){
            return true;
        } else if ($piece == 'N' and $yourColor == 'black'){
            return true;
        }
    }

    # from top left?
    $tmp_x = $king_x - 2;
    $tmp_y = $king_y + 1;
    if(isPositionValid($tmp_x, $tmp_y)){
        $piece = getPieceByIndex($newBoard,getIndex($tmp_x, $tmp_y));
        if($piece == 'n' and $yourColor == 'white'){
            return true;
        } else if ($piece == 'N' and $yourColor == 'black'){
            return true;
        }
    }
    return false;
}

function isKnightMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                                    $yourColor){
    $c1 = (($to_y - $from_y) == 2) and (($to_x - $from_x) == 1); # 2 top,1 right
    $c2 = (($to_y - $from_y) == 1) and (($to_x - $from_x) == 2); # 1 top,2 right
    $c3 = (($to_y - $from_y) == 2) and (($to_x - $from_x) ==-1); # 2 top,1 left
    $c4 = (($to_y - $from_y) == 1) and (($to_x - $from_x) ==-2); # 1 top,2 left
    $c5 = (($to_y - $from_y) ==-1) and (($to_x - $from_x) ==-2); # 1 down,2 left
    $c6 = (($to_y - $from_y) ==-2) and (($to_x - $from_x) ==-1); # 2 down,1 left
    $c7 = (($to_y - $from_y) ==-2) and (($to_x - $from_x) ==1);  #2 down,1 right
    $c8 = (($to_y - $from_y) ==-1) and (($to_x - $from_x) ==2);  #1 down,2 right
    if ($c1 or $c2 or $c3 or $c4 or $c5 or $c6 or $c7 or $c8) {
        # Everything is ok.
    } else {
        exit("ERROR: From ($from_x | $from_y) to ($to_x | $to_y) is no valid ".
                    "move for a knight.");
    }

    $index = getIndex($to_x, $to_y);
    $piece = getPieceByIndex($currentBoard, $index);
    if ($piece != '0') {
        if( isMyPiece($piece, $yourColor) ) {
            exit("ERROR: You may not capture your own chess pieces.");
        }
    }
}


function isKingMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                                    $yourColor){
    if (abs($from_x - $to_x) <= 1 and abs($from_y - $to_y) <= 1){
        # Everything is ok, standard king move
    } else if (abs($from_x - $to_x) == 2 and ($from_y - $to_y) == 0) {
        # castling?
        if( ($yourColor == 'white' and $from_x ==  4) or
            ($yourColor == 'black' and $from_x == 60)    ){
            $rows  = array('whiteCastlingKingsidePossible', 
                           'whiteCastlingQueensidePossible',
                           'blackCastlingKingsidePossible',
                           'blackCastlingQueensidePossible');
            $cond  = "WHERE `id` = ".CURRENT_GAME_ID;
            $result= selectFromTable($rows, "chess_currentGames", $cond);
            if($yourColor == 'white'){
                if($to_x == 2){
                    if($result['whiteCastlingQueensidePossible'] == 0){
                        exit("ERROR: You've already moved your King or your ".
                                    "rook");
                    }
                }
                if($to_x == 6){
                    if($result['whiteCastlingKingsidePossible'] == 0){
                        exit("ERROR: You've allready moved your King or your ".
                                    "rook");
                    }
                }
            } else {
                if($to_x == 58){
                    if($result['blackCastlingQueensidePossible'] == 0){
                        exit("ERROR: You've already moved your King or your ".
                                    "rook");
                    }
                }
                if($to_x == 62){
                    if($result['blackCastlingKingsidePossible'] == 0){
                        exit("ERROR: You've already moved your King or your ".
                                    "rook");
                    }
                }
            }
            # Is anything in between King and Rook?
            if($from_x < $to_x){
                for($x_tmp = $from_x+1; $x_tmp < $to_x; $x_tmp++){
                    $index = getIndex($x_tmp, $from_y);
                    $piece = getPieceByIndex($currentBoard, $index);
                    if($piece != '0'){
                        exit("ERROR: $piece is between your King and your ".
                                    "rook. Castling is not possible.");
                    }
                }
            } else {
                for($x_tmp = $from_x-1; $x_tmp > $to_x; $x_tmp--){
                    $index = getIndex($x_tmp, $from_y);
                    $piece = getPieceByIndex($currentBoard, $index);
                    if($piece != '0'){
                        exit("ERROR: $piece is between your King and your ".
                                    "rook. Castling is not possible.");
                    }
                }
            }

            # Is player currently in chess?
            if(isPlayerCheck($currentBoard, $yourColor)){
                exit("ERROR: You may only use castling if you are not check.");
            }
        } else {
            exit("ERROR: Castling is only possible, if you didn't move your".
                        "King before.");
        }
    } else {
        exit("ERROR: From ($from_x | $from_y) to ($to_x | $to_y) is no valid".
                    "move for a king.");
    }

    $index = getIndex($to_x, $to_y);
    $piece = getPieceByIndex($currentBoard, $index);
    if ($piece != '0') {
        if( isMyPiece($piece, $color) ) {
            exit("ERROR: You may not capture your own chess pieces.");
        }
    }
}

function isBishopMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                                    $yourColor){
    if (abs($from_x - $to_x) == abs($from_y - $to_y)) {
        #moving diagonal
        if ($from_x < $to_x) {
            #moving up
            for($i=1; $i < ($to_x-$from_x);$i++){
                $x_tmp = $from_x + $i;
                $y_tmp = $from_y + $i;
                $index = getIndex($x_tmp, $from_y);
                $piece = getPieceByIndex($currentBoard, $index);
                if($piece != '0'){
                    exit("ERROR: On ($x_tmp | $y_tmp) is $piece.");
                }
            }
        } else {
            #moving down
            for($i=1; $i < ($from_x - $to_x);$i++){
                $x_tmp = $from_x - $i;
                $y_tmp = $from_y - $i;
                $index = getIndex($x_tmp, $y_tmp);
                $piece = getPieceByIndex($currentBoard, $index);
                if($piece != '0'){
                    exit("ERROR: On ($x_tmp | $y_tmp) is $piece.");
                }
            }
        }
    } else {
        exit("ERROR: From ($from_x | $from_y) to ($to_x | $to_y) is no valid
              move for a bishop.");
    }

    $index = getIndex($to_x, $to_y);
    $piece = getPieceByIndex($currentBoard, $index);
    if ($piece != '0') {
        if( isMyPiece($piece, $yourColor) ) {
            exit("ERROR: You may not capture your own chess pieces.");
        }
    }
}

function isRookMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                                    $yourColor){
    if ($from_x == $to_x) {
        #moving straight up / down
        if($from_y < $to_y){
            #is moving up
            for($y_tmp=$from_y+1; $y_tmp < $to_y; $y_tmp++){
                $index = getIndex($from_x, $y_tmp);
                $piece = getPieceByIndex($currentBoard, $index);
                if($piece != '0'){
                    exit("ERROR: On ($from_x | $y_tmp) is $piece.");
                }
            }
        } else {
            #is moving down
            for($y_tmp=$from_y-1; $y_tmp > $to_y; $y_tmp--){
                $index = getIndex($from_x, $y_tmp);
                $piece = getPieceByIndex($currentBoard, $index);
                if($piece != '0'){
                    exit("ERROR: On ($from_x | $y_tmp) is $piece.");
                }
            }
        }
    } else if ($from_y == $to_y) {
        #moving straight left / right
        if($from_x < $to_x){
            #is moving right
            for($x_tmp=$from_x+1; $x_tmp < $to_x; $x_tmp++){
                $index = getIndex($x_tmp, $from_y);
                $piece = getPieceByIndex($currentBoard, $index);
                if($piece != '0'){
                    exit("ERROR: On ($x_tmp | $from_y) is $piece.");
                }
            }
        } else {
            #is moving left
            for($x_tmp=$from_x-1; $x_tmp > $to_x; $x_tmp--){
                $index = getIndex($x_tmp, $from_y);
                $piece = getPieceByIndex($currentBoard, $index);
                if($piece != '0'){
                    exit("ERROR: On ($x_tmp | $from_y) is $piece.");
                }
            }
        }

    } else {
        exit("ERROR: From ($from_x | $from_y) to ($to_x | $to_y) is no valid ".
                    "move for a rook.");
    }


    $index = getIndex($to_x, $to_y);
    $piece = getPieceByIndex($currentBoard, $index);
    if ($piece != '0') {
        if( isMyPiece($piece, $yourColor) ) {
            exit("ERROR: You may not capture your own chess pieces.");
        }
    }
}

function isPawnMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                         $yourColor, $moveList){
    if($from_x == $to_x and abs($from_y - $to_y) <= 2){
        #moving up / down
        $index = getIndex($to_x, $to_y);
        $piece = getPieceByIndex($currentBoard, $index);
        if($piece != '0'){exit("A pawn can only capture by moving diagonal!");}
        if($yourColor == 'white'){
            if($from_y > $to_y) {
                exit("ERROR: White may only move up with pawns.");
            }
            if($from_y == 2) {$isOnHomeRow = true;} else {$isOnHomeRow = false;}
        } else {
            if($from_y < $to_y) {
                exit("ERROR: Black may only move down with pawns.");
            }
            if($from_y == 7) {$isOnHomeRow = true;} else {$isOnHomeRow = false;}
        }
        if(abs($from_y - $to_y) == 2 and $isOnHomeRow == false){
                exit("ERROR: Pawns may only move two if they are on their home".
                            " row.");
        }
    } else if(abs($from_x-$to_x) == 1 and abs($from_y-$to_y) == 1){
        # pawns capturing move
        $index = getIndex($to_x, $to_y);
        $piece_target = getPieceByIndex($currentBoard, $index);

        # For en passant:
        $moveArray = explode("\n", trim($moveList));
        $lastMove = end($moveArray);
        $lastFromX= substr($lastMove, 0, 1);
        $lastFromY= substr($lastMove, 1, 1);
        $lastToX  = substr($lastMove, 2, 1);
        $lastToY  = substr($lastMove, 3, 1);


        if($yourColor == 'white'){
            if($from_y > $to_y) {
                exit("ERROR: White may only move up with pawns.");
            }
            if($piece_target == '0' or isMyPiece($piece_target, $yourColor)) {
                $shouldBePawn = substr($currentBoard, 
                                       getIndex($to_x, $to_y-1), 1);
                if ($lastFromX == $lastToX and $lastToX == $to_x and 
                    $lastFromY - $lastToY == 2 and
                    $shouldBePawn == 'p'){
                    # en passant
                } else {
                    exit("ERROR: You may only make the pawn capture move if a ".
                            "chess piece of the opponent is on the target ".
                            "field.");
                }
            }
        } else {
            if($from_y < $to_y) {
                exit("ERROR: Black may only move down with pawns.");
            }
            if($piece_target == '0' or isMyPiece($piece_target, $yourColor)) {
                $shouldBePawn = getPieceByIndex($currentBoard, 
                                       getIndex($to_x, $to_y+1));
                if ($lastFromX == $lastToX and $lastToX == $to_x and 
                    $lastToY - $lastFromY == 2 and
                    $shouldBePawn == 'P'){
                    # en passant
                } else {
                    exit("ERROR: You may only make the pawn capture move if a ".
                            "chess piece of the opponent is on the target ".
                            "field.");
                }
            }
        }
    } else {
        exit("ERROR: From ($from_x | $from_y) to ($to_x | $to_y) is no valid
              move for a pawn.");
    }

    $index = getIndex($to_x, $to_y);
    $piece = getPieceByIndex($currentBoard, $index);
    if ($piece != '0') {
        if( isMyPiece($piece, $yourColor) ) {
            exit("ERROR: You may not capture your own chess pieces.");
        }
    }

}

function isQueenMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                          $yourColor){
    if ($from_x == $to_x) {
        #moving straight up / down
        if($from_y < $to_y){
            #is moving up
            for($y_tmp=$from_y+1; $y_tmp < $to_y; $y_tmp++){
                $index = getIndex($from_x, $y_tmp);
                $piece = getPieceByIndex($currentBoard, $index);
                if($piece != '0'){
                    exit("ERROR: On ($from_x | $y_tmp) is $piece.");
                }
            }
        } else {
            #is moving down
            for($y_tmp=$from_y-1; $y_tmp > $to_y; $y_tmp--){
                $index = getIndex($from_x, $y_tmp);
                $piece = getPieceByIndex($currentBoard, $index);
                if($piece != '0'){
                    exit("ERROR: On ($from_x | $y_tmp) is $piece.");
                }
            }
        }
    } else if ($from_y == $to_y) {
        #moving straight left / right
        if($from_x < $to_x){
            #is moving right
            for($x_tmp=$from_x+1; $x_tmp < $to_x; $x_tmp++){
                $index = getIndex($x_tmp, $from_y);
                $piece = getPieceByIndex($currentBoard, $index);
                if($piece != '0'){
                    exit("ERROR: On ($x_tmp | $from_y) is $piece.");
                }
            }
        } else {
            #is moving left
            for($x_tmp=$from_x-1; $x_tmp > $to_x; $x_tmp--){
                $index = getIndex($x_tmp, $from_y);
                $piece = getPieceByIndex($currentBoard, $index);
                if($piece != '0'){
                    exit("ERROR: On ($x_tmp | $from_y) is $piece.");
                }
            }
        }
    } else if (abs($from_x - $to_x) == abs($from_y - $to_y)) {
        #moving diagonal
        if ($from_x < $to_x) {
            #moving up
            for($i=1; $i < ($to_x-$from_x);$i++){
                $x_tmp = $from_x + $i;
                $y_tmp = $from_y + $i;
                $index = getIndex($x_tmp, $from_y);
                $piece = getPieceByIndex($currentBoard, $index);
                if($piece != '0'){
                    exit("ERROR: On ($x_tmp | $y_tmp) is $piece.");
                }
            }
        } else {
            #moving down
            for($i=1; $i < ($from_x - $to_x);$i++){
                $x_tmp = $from_x - $i;
                $y_tmp = $from_y - $i;
                $index = getIndex($x_tmp, $from_y);
                $piece = getPieceByIndex($currentBoard, $index);
                if($piece != '0'){
                    exit("ERROR: On ($x_tmp | $y_tmp) is $piece.");
                }
            }
        }
    } else {
        exit("ERROR: From ($from_x | $from_y) to ($to_x | $to_y) is no valid
              move for a queen.");
    }


    $index = getIndex($to_x, $to_y);
    $piece = getPieceByIndex($currentBoard, $index);
    if ($piece != '0') {
        if( isMyPiece($piece, $yourColor) ) {
            exit("ERROR: You may not capture your own chess pieces.");
        }
    }

}

function makeMove($from_index, $to_index, $currentBoard, $move, $color){
    /* This function submits the move to the database. All checks if this move
       is possible have been done before. 
       All database-changes of the current game are done in this function.
       Nothing else. */

    $piece        = getPieceByIndex($currentBoard, $from_index);
    $capturedPiece= getPieceByIndex($currentBoard, $to_index);
    $to_coord     = getCoordinates($to_index);
    $from_coord   = getCoordinates($from_index);
    $cond         = "WHERE  `chess_currentGames`.`id` =".CURRENT_GAME_ID;

    if ($piece == 'p' or $piece == 'P'){
        $pawnMoved = true;
    } else {
        $pawnMoved = false;
    }
    if ($capturedPiece != '0') {
        $captureMade = true;
    } else {
        $captureMade = false;
    }

    // Is this move castling?
    if(    ($piece == 'K' or $piece == 'k') and 
        abs($from_coord[0] - $to_coord[0]) == 2  ){
        // Move tower (only tower! The king will be moved in the rest of this 
        // function.
        if($color == 'white'){
            if($to_index == 6){
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
            if($to_index == 62){
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
        $keyValue = array();
        $keyValue['currentBoard'] = $currentBoard;
        $keyValue['moveList']     = "CONCAT(`moveList`,'$move\n')";
        $keyValue['whoseTurnIsIt']= "((`whoseTurnIsIt` + 1)%2)";
        $keyValue['lastMove']     = "CURRENT_TIMESTAMP";
        updateDataInTable("chess_currentGames", $keyValue, $cond);
    }

    // Is this piece relevant for castling?
    // White - Kingside Castling
    if ($piece == 'K' or ($piece == 'R' and $from_index == 7)){
        $keyValue = array();
        $keyValue['whiteCastlingKingsidePossible'] = 0;
        updateDataInTable("chess_currentGames", $keyValue, $cond);
    }
    // White - Queenside Castling
    if ($piece == 'K' or ($piece == 'R' and $from_index == 0)){
        $keyValue = array();
        $keyValue['whiteCastlingQueensidePossible'] = 0;
        updateDataInTable("chess_currentGames", $keyValue, $cond);
    }
    // Black - Kingside Castling
    if ($piece == 'K' or ($piece == 'R' and $from_index == 63)){
        $keyValue = array();
        $keyValue['blackCastlingKingsidePossible'] = 0;
        updateDataInTable("chess_currentGames", $keyValue, $cond);
    }
    // Black - Queenside Castling
    if ($piece == 'K' or ($piece == 'R' and $from_index == 56)){
        $keyValue = array();
        $keyValue['blackCastlingQueensidePossible'] = 0;
        updateDataInTable("chess_currentGames", $keyValue, $cond);
    }

    /* Promotion */
    if(strlen($move) == 5){
        $promotion    = strtolower(substr($move, 4,1));
        if(!($promotion == 'q' or $promotion == 'r' or $promotion == 'b' 
                               or $promotion == 'n')){
            exit("ERROR: You can only promote to queen (q), rook (r),
                  bishop (b) or knight (n)");
        }
        if($color == 'white'){$promotion = strtoupper($promotion);}
        if(! ( ($piece == 'p' and $to_coord[1] == 1) or
               ($piece == 'P' and $to_coord[1] == 8)    )){
            exit("ERROR: You may only promote when your pawn reaches the 
                         first line of the opponent.");
        }
        $move = substr($move,0,4).$promotion;
        $piece = $promotion;
    }
    if( ($piece == 'p' and $to_coord[1] == 1 and $promotion == '') or
        ($piece == 'P' and $to_coord[1] == 8 and $promotion == '')    ){
        exit("ERROR: You have to promote. 
                     Add a single letter at the move-request");
    }

    /* Now update the database with move */
    $currentBoard = substr($currentBoard, 0, $from_index)."0"
                                          .substr($currentBoard, $from_index+1);
    $currentBoard = substr($currentBoard, 0, $to_index).$piece
                                          .substr($currentBoard, $to_index+1);
    $keyValue['currentBoard'] = $currentBoard;
    $keyValue['moveList']     = "CONCAT(`moveList`,'$move\n')";
    $keyValue['whoseTurnIsIt']= "((`whoseTurnIsIt` + 1)%2)";
    $keyValue['lastMove']     = "CURRENT_TIMESTAMP";

    if($pawnMoved == false and $captureMade == false) {    
        $keyValue['noCaptureAndPawnMoves'] = "`noCaptureAndPawnMoves` + 1 ";
    } else {
        $keyValue['noCaptureAndPawnMoves'] = "0";
    }

    updateDataInTable("chess_currentGames", $keyValue, $cond);

    /* Get all data for the threefold repetition table*/
    /* Castling? */
    $rows  = array('whiteCastlingKingsidePossible',
                   'whiteCastlingQueensidePossible',
                   'blackCastlingKingsidePossible',
                   'blackCastlingQueensidePossible');
    $result= selectFromTable($rows, "chess_currentGames", $cond);
    /* Is en passant possible? */
        /* was last move a pawn-2move? */
        if($pawnMoved and abs($from_coord[1]-$to_coord[1]) == 2){
            $wasPawn2move = true;
        } else {
            $wasPawn2move = false;
        }
        /* is left/right a pawn of the opponent?*/    
        $isOpponentNext = false;
        if($to_coord[0] - 1 >= 1){
            $indexLeft = getIndex($to_coord[0]-1, $to_coord[1]);
            $pieceLeft = getPieceByIndex($currentBoard,$indexLeft);
            if($pieceLeft == 'p' and $color == 'white'){$isOpponentNext = true;}
            if($pieceLeft == 'P' and $color == 'black'){$isOpponentNext = true;}
        }
        if($to_coord[0] + 1 <= 8){
            $indexRight= getIndex($to_coord[0]+1, $to_coord[1]);
            $pieceRight= getPieceByIndex($currentBoard,$indexLeft);
            if($pieceRight== 'p' and $color == 'white'){$isOpponentNext = true;}
            if($pieceRight== 'P' and $color == 'black'){$isOpponentNext = true;}
        }
        if($isOpponentNext and $wasPawn2move){
            $enPassant = '1';
        } else {
            $enPassant = '0';
        }
    /* Insert the new situation into chess_currentGamesThreefoldRepetition */
    $keyValuePairs = array();
    $keyValuePairs['gameID'] = CURRENT_GAME_ID;
    $keyValuePairs['board']  = $currentBoard;
    $keyValuePairs['whiteCastlingKingsidePossible']  = 
                                       $result['whiteCastlingKingsidePossible'];
    $keyValuePairs['whiteCastlingQueensidePossible'] = 
                                      $result['whiteCastlingQueensidePossible'];
    $keyValuePairs['blackCastlingKingsidePossible']  = 
                                       $result['blackCastlingKingsidePossible'];
    $keyValuePairs['blackCastlingQueensidePossible'] = 
                                      $result['blackCastlingQueensidePossible'];
    $keyValuePairs['enPassantPossible'] = $enPassant;
    insertIntoTable($keyValuePairs, "chess_currentGamesThreefoldRepetition");

    return $currentBoard;
}
/******************************************************************************
 * End of function definitions.                                               *
 * Get CURRENT_GAME_ID and some game-relevant variables                       *  
 ******************************************************************************/
if(isset($_GET['gameID'])) {
    $gameID= intval($_GET['gameID']);
    $row   = array("currentBoard","whoseTurnIsIt", "whitePlayerID", 
                   "blackPlayerID", "moveList", "noCaptureAndPawnMoves", "id");
    $cond  = "WHERE (`whitePlayerID` = ".USER_ID." OR `blackPlayerID` = ";
    $cond .= USER_ID.") AND `id` = ".$gameID;
    $result = selectFromTable($row, "chess_currentGames", $cond);

    if($result !== false){
        $currentBoard  = $result['currentBoard'];
        $whoseTurnIsIt = $result['whoseTurnIsIt'];
        $moveList      = $result['moveList'];
        $noCaptureAndPawnMoves = $result['noCaptureAndPawnMoves'];
        $gameID        = $result['id'];
        if($whoseTurnIsIt == 0){
            $whoseTurnIsItLanguage = 'white';
        } else {
            $whoseTurnIsItLanguage = 'black';
        }
        if($result['whitePlayerID'] == USER_ID){
            $yourColor = 'white';
            $opponentColor = 'black';
        } else {
            $yourColor = 'black';
            $opponentColor = 'white';
        }
        define('CURRENT_GAME_ID', $gameID);
    }
}

if(!defined('CURRENT_GAME_ID')){exit('ERROR: Wrong gameID.');}

/******************************************************************************
 * Get MOVE as Number-Notation:                                               *
 * http://code.google.com/p/community-chess/wiki/NotationOfMoves              *  
 ******************************************************************************/

if(isset($_GET['move'])){
    $move = mysql_real_escape_string($_GET['move']);
    # Is the move-query well formed?
    if(strlen($move) > 5 or strlen($move) < 4 ){
        exit("ERROR: Your move-query should have 4 or 5 characters.");
    }
    $from = intval(substr($move, 0, 2));
    $to   = intval(substr($move, 2, 2));
    if(strlen($move) == 5){
        $promotion = substr($move, 4, 1);
    } else {
        $promotion = '';
    }
    $from_y = $from % 10;
    $from_x = ($from-$from_y)/10;
    $to_y = $to % 10;
    $to_x = ($to-$to_y)/10;
    if (!(1 <= $from_x and $from_x <= 8 and 1 <= $from_y and $from_y <= 8)){
        exit("ERROR: Your from-coordinates were wrong.");
    } else {$from_index = getIndex($from_x, $from_y);}
    if (!(1 <= $to_x and $to_x <= 8 and 1 <= $to_y and $to_y <= 8)){
        exit("ERROR: Your to-coordinates were wrong.");
    } else {$to_index = getIndex($to_x, $to_y);}
    if ($from_index == $to_index){exit("ERROR: You have to move.");}

    define('MOVE', $move);
}

if(isset($_GET['pgn'])){
    # insert Code as soon as possible
    # define MOVE in my notation
}

if (defined('MOVE')) {
    # Is it your turn?
    if($whoseTurnIsItLanguage != $yourColor){exit("ERROR: It's not your turn");}
    # Is one of your chess pieces on the from-field?
    $piece = getPieceByIndex($currentBoard,$from_index);
    if($piece == '0'){
        exit("ERROR: No chess piece on field ($from_x | $from_y).");
    }
    if($yourColor == 'white' and !(isMyPiece($piece, $yourColor)) ){
        exit("ERROR: The chess piece on field ($from_x | $from_y) is $piece.
              It belongs to your opponent. You are $yourColor. Your chess pieces
              have capital letters.");
    }
    if($yourColor == 'black' and !(isMyPiece($piece, $yourColor)) ){
        exit("ERROR: The chess piece on field ($from_x | $from_y) is $piece.
              It belongs to your opponent. You are $yourColor. Your chess pieces
              have lower-case letters.");
    }
    # Can the chess piece make this move?
    $piece_lower = strtolower($piece);
    if ($piece_lower == 'q'){
        isQueenMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                         $yourColor);
    } else if ($piece_lower == 'p') {
        isPawnMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                        $yourColor, $moveList);
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
        exit("SOFTWARE-ERROR: This piece should not be there: ".$piece_lower);
    }

    # Do you set yourself check with this move?
    $newBoard = getNewBoard($currentBoard, $from_index, $to_index);
    if (isPlayerCheck($newBoard, $yourColor)){
        exit("ERROR: You may not be check at end of your turn!");
    }
    # Everything is ok => move!
    $currentBoard = makeMove($from_index, $to_index, $currentBoard, MOVE);
    # Check for:
        if ( !hasValidMoves($currentBoard, $opponentColor) ){ 
            if ( isPlayerCheck($currentBoard, $opponentColor) ){
                if($yourColor == 'white'){$outcome = 0;}
                else {$outcome = 1;}
                echo "Checkmate.";
            } else {
                $outcome = 2;
                echo "$opponentColor has no valid moves but is not check. ".
                     "Draw.";
            }
            $row = array('moveList', 'whitePlayerID', 'blackPlayerID', 
                         'whitePlayerSoftwareID', 'blackPlayerSoftwareID', 
                         'whoseTurnIsIt', 'startTime', 'lastMove');
            $condition = "WHERE `id` = ".CURRENT_GAME_ID;
            $result= selectFromTable($row, "chess_currentGames", $condition);
            $moveList = $result['moveList'];
            $whitePlayerID = $result['whitePlayerID'];
            $blackPlayerID = $result['blackPlayerID'];
            $whitePlayerSoftwareID = $result['whitePlayerSoftwareID'];
            $blackPlayerSoftwareID = $result['blackPlayerSoftwareID'];
            $startTime = $result['startTime'];
            $endTime   = $result['endTime'];

            deleteFromTable("chess_currentGames", CURRENT_GAME_ID);

            $keyValue   = array();
            $keyValue['moveList'] = $moveList;
            $keyValue['whitePlayerID'] = $whitePlayerID;
            $keyValue['blackPlayerID'] = $blackPlayerID;
            $keyValue['whitePlayerSoftwareID'] = $whitePlayerSoftwareID;
            $keyValue['blackPlayerSoftwareID'] = $blackPlayerSoftwareID;
            $keyValue['outcome']   = $outcome;
            $keyValue['startTime'] = $startTime;
            $keyValue['endTime']   = $endTime;
            insertIntoTable($keyValue, "chess_pastGames");
            exit("Game finished.");
        }
}

if(isset($_GET['claim50MoveRule'])){
    if($noCaptureAndPawnMoves >= 100){
        $row = array('moveList', 'whitePlayerID', 'blackPlayerID', 
                     'whitePlayerSoftwareID', 'blackPlayerSoftwareID', 
                     'whoseTurnIsIt', 'startTime', 'lastMove');
        $condition = "WHERE `id` = ".CURRENT_GAME_ID;
        $result = selectFromTable($row, "chess_currentGames", $condition);
        $moveList  = $result['moveList'];
        $whitePlayerID = $result['whitePlayerID'];
        $blackPlayerID = $result['blackPlayerID'];
        $whitePlayerSoftwareID = $result['whitePlayerSoftwareID'];
        $blackPlayerSoftwareID = $result['blackPlayerSoftwareID'];
        $startTime = $result['startTime'];
        $endTime   = $result['endTime'];

        deleteFromTable("chess_currentGames", CURRENT_GAME_ID);

        $keyValue   = array();
        $keyValue['moveList'] = $moveList;
        $keyValue['whitePlayerID'] = $whitePlayerID;
        $keyValue['blackPlayerID'] = $blackPlayerID;
        $keyValue['whitePlayerSoftwareID'] = $whitePlayerSoftwareID;
        $keyValue['blackPlayerSoftwareID'] = $blackPlayerSoftwareID;
        $keyValue['outcome']   = $outcome;
        $keyValue['startTime'] = $startTime;
        $keyValue['endTime']   = $endTime;
        insertIntoTable($keyValue, "chess_pastGames");

        exit("Game finished. Draw. You claimed draw by the fifty-move rule.");
    } else {
        exit("ERROR: The last $noCaptureAndPawnMoves were no capture or pawn ".
                    "moves. At least 100 have to be made before you can claim ".
                    "draw by fifty-move rule.");
    }
}

if(isset($_GET['claimThreefoldRepetition'])){
    $cond  = "WHERE `id` = ".CURRENT_GAME_ID;
    $rows  = array('currentBoard',
                   'moveList',
                   'whiteCastlingKingsidePossible',
                   'whiteCastlingQueensidePossible',
                   'blackCastlingKingsidePossible',
                   'blackCastlingQueensidePossible');
    $result= selectFromTable($rows, "chess_currentGames", $cond);

    /* is en passant possible now? */
    $moveList = explode("\n", trim($result['moveList']));
    $lastMove = end($moveList);

    $lastFromX= substr($lastMove, 0, 1);
    $lastFromY= substr($lastMove, 1, 1);
    $lastToX  = substr($lastMove, 2, 1);
    $lastToY  = substr($lastMove, 3, 1);

    $index    = getIndex($lastToX, $lastToY);
    $shouldBePawn = substr($currentBoard, $index, 1);

    if($lastFromX == $lastToX and abs($lastFromY-$lastToY) == 2){
        $isOpponentNext = false;
        if($to_coord[0] - 1 >= 1){
            $indexLeft = getIndex($lastToX-1, $lastToY);
            $pieceLeft = getPieceByIndex($currentBoard,$indexLeft);
            if($pieceLeft == 'p' and $yourColor == 'white'){
                $isOpponentNext = true;
            }
            if($pieceLeft == 'P' and $yourColor == 'black'){
                $isOpponentNext = true;
            }
        }
        if($to_coord[0] + 1 <= 8){
            $indexRight= getIndex($lastToX+1, $lastToY);
            $pieceRight= getPieceByIndex($currentBoard,$indexLeft);
            if($pieceRight== 'p' and $yourColor == 'white'){
                $isOpponentNext = true;
            }
            if($pieceRight== 'P' and $yourColor == 'black'){
                $isOpponentNext = true;
            }
        }
        if($isOpponentNext and $wasPawn2move){
            $enPassant = '1';
        } else {
            $enPassant = '0';
        }            
    }
    /* end en passant */

    $cond  = "WHERE `gameID` = ".CURRENT_GAME_ID." ";
    $cond .= "AND `board` = ".$result['currentBoard'];
    $cond .= "AND `whiteCastlingKingsidePossible` = ";
    $cond .= $result['whiteCastlingKingsidePossible'];
    $cond .= "AND `whiteCastlingQueensidePossible`= ";
    $cond .= $result['whiteCastlingQueensidePossible'];
    $cond .= "AND `blackCastlingKingsidePossible` = ";
    $cond .= $result['blackCastlingKingsidePossible'];
    $cond .= "AND `blackCastlingQueensidePossible`= ";
    $cond .= $result['blackCastlingQueensidePossible'];
    $cond .= "AND `enPassantPossible` = ".$enPassant;
    $result= selectFromTable(array('id'), 
                                "chess_currentGamesThreefoldRepetition", 
                                $cond, 4);
    if(count($result) >= 3){
        $rows = array('moveList', 'whitePlayerID', 'blackPlayerID', 
                     'whitePlayerSoftwareID', 'blackPlayerSoftwareID', 
                     'whoseTurnIsIt', 'startTime', 'lastMove');
        $condition = "WHERE `id` = ".CURRENT_GAME_ID;
        $result = selectFromTable($rows, "chess_currentGames", $condition);
        $moveList  = $result['moveList'];
        $whitePlayerID = $result['whitePlayerID'];
        $blackPlayerID = $result['blackPlayerID'];
        $whitePlayerSoftwareID = $result['whitePlayerSoftwareID'];
        $blackPlayerSoftwareID = $result['blackPlayerSoftwareID'];
        $startTime = $result['startTime'];
        $endTime   = $result['endTime'];

        deleteFromTable("chess_currentGames", CURRENT_GAME_ID);

        $keyValue = array();
        $keyValue['moveList'] = $moveList;
        $keyValue['whitePlayerID'] = $whitePlayerID;
        $keyValue['blackPlayerID'] = $blackPlayerID;
        $keyValue['whitePlayerSoftwareID'] = $whitePlayerSoftwareID;
        $keyValue['blackPlayerSoftwareID'] = $blackPlayerSoftwareID;
        $keyValue['outcome']   = $outcome;
        $keyValue['startTime'] = $startTime;
        $keyValue['endTime']   = $endTime;
        insertIntoTable($keyValue, "chess_pastGames");
        exit("Game finished. Draw. You claimed draw by threefold repetition.");
    } else {
        exit("ERROR: Threefold repetition may only be claimed if exactly the ".
                    "same situation appeared at least three times. ".
                    "The current situation appered only ".count($result)." ".
                    "times");
    }
}

$row   = array("currentBoard","whoseTurnIsIt", "whitePlayerID", 
               "blackPlayerID");
$cond  = "WHERE (`whitePlayerID` = ".USER_ID." OR `blackPlayerID` = ";
$cond .= USER_ID.") AND `id` = ".CURRENT_GAME_ID;
$result= selectFromTable($row, "chess_currentGames", $cond);

if($result !== false){
    $currentBoard  = $result['currentBoard'];
    $whoseTurnIsIt = $result['whoseTurnIsIt'];
    if($whoseTurnIsIt == 0){
        $whoseTurnIsItLanguage = 'white';
    } else {
        $whoseTurnIsItLanguage = 'black';
    }
    if($result['whitePlayerID'] == USER_ID){
        $yourColor = 'white';
        $opponentColor = 'black';
    } else {
        $yourColor = 'black';
        $opponentColor = 'white';
    }
}

if(isPlayerCheck($currentBoard, $yourColor)){$youCheck = 'Yes';}
else {$youCheck = 'No';}
if(isPlayerCheck($currentBoard, $opponentColor)){$opponentCheck = 'Yes';}
else {$opponentCheck = 'No';}

echo "Current Game Information:<br/>";
echo "<pre>";
echo substr ($currentBoard ,56, 8)."<br/>";
echo substr ($currentBoard ,48, 8)."<br/>";
echo substr ($currentBoard ,40, 8)."<br/>";
echo substr ($currentBoard ,32, 8)."<br/>";
echo substr ($currentBoard ,24, 8)."<br/>";
echo substr ($currentBoard ,16, 8)."<br/>";
echo substr ($currentBoard , 8, 8)."<br/>";
echo substr ($currentBoard , 0, 8)."<br/>";
echo "</pre>";
echo "Next turn: ".$whoseTurnIsItLanguage."<br/>";
echo "You are: ".$yourColor."<br/>";
echo "You are check: $youCheck.<br/>";
echo "Opponent is check: $opponentCheck.<br/>";
echo "Game-ID: ".CURRENT_GAME_ID.".<br/>";

?>
