<?
/**
 * @author: Martin Thoma
 * play a game
 * */

require ('wrapper.inc.php');
if(USER_ID === false){exit("Please <a href='login.wrapper.php'>login</a>");}

function getIndex($x, $y){
    return ($y-1)*8+($x-1);
}

function isPositionValid($x, $y) {
    if (1 <= $x and $x <= 8 and 1 <= $y and $y <= 8){ return true;}
    else {return false;}
}

function hasValidMoves($board, $color){
    #implementieren
    for($i=0;$i<63;$i++){
        $piece = substr($board,$i,1);
        if($piece != '0' and ord($piece) > 96 and $color == 'white'){
            #make move: $newBoard = $board;
            if (!isPlayerCheck($newBoard, 'white')){return true;}
        }
        if($piece != '0' and ord($piece) > 96 and $color == 'black'){
            #make move: $newBoard = $board;
            if (!isPlayerCheck($newBoard, 'white')){return true;}
        }
    }
    return false;
}

function isPlayerCheck($newBoard, $yourColor){
    if ($yourColor == 'white'){
        $king_index = strpos($newBoard, 'K');
    } else {
        $king_index = strpos($newBoard, 'k');
    }
    $king_x = $king_index % 8;
    $king_y = ($king_index - $king_x)/8;
    $king_x += 1;
    $king_y += 1;

    # danger from top?
    for($tmp_y = $king_y + 1; $tmp_y < 8; $tmp_y++){
        if(  substr($newBoard,getIndex($king_x, $tmp_y),1) != '0'  ) {break;}
    }
    $piece = substr($newBoard,getIndex($king_x, $tmp_y),1);
    if ($piece != '0' and ord($piece) > 96 and $yourColor == 'white'){
        if($piece == 'k'){exit("SOFTWARE-ERROR: How can a king face a king?a");}
        else if ($piece == 'q'){return true;}
        else if ($piece == 'r'){return true;}
    } else if ($piece != '0' and ord($piece) < 96 and $yourColor == 'black'){
        if($piece == 'K'){exit("SOFTWARE-ERROR: How can a king face a king?b");}
        else if ($piece == 'Q'){return true;}
        else if ($piece == 'R'){return true;}
    }

    # danger from bottom?
    for($tmp_y = $king_y - 1; $tmp_y > 1; $tmp_y--){
        if(  substr($newBoard,getIndex($king_x, $tmp_y),1) != '0'  ) {break;}
    }
    $piece = substr($newBoard,getIndex($king_x, $tmp_y),1);
    if ($piece != '0' and ord($piece) > 96 and $yourColor == 'white'){
        if($piece == 'k'){exit("SOFTWARE-ERROR: How can a king face a king?c".$tmp_y);}
        else if ($piece == 'q'){return true;}
        else if ($piece == 'r'){return true;}
    } else if ($piece != '0' and ord($piece) < 96 and $yourColor == 'black'){
        if($piece == 'K'){exit("SOFTWARE-ERROR: How can a king face a king?d");}
        else if ($piece == 'Q'){return true;}
        else if ($piece == 'R'){return true;}
    }

    # danger from right?
    for($tmp_x = $king_x + 1; $tmp_x < 8; $tmp_x++){
        if(  substr($newBoard,getIndex($king_x, $tmp_y),1) != '0'  ) {break;}
    }
    $piece = substr($newBoard,getIndex($king_x, $tmp_y),1);
    if ($piece != '0' and ord($piece) > 96 and $yourColor == 'white'){
        if($piece == 'k'){exit("SOFTWARE-ERROR: How can a king face a king?e");}
        else if ($piece == 'q'){return true;}
        else if ($piece == 'r'){return true;}
    } else if ($piece != '0' and ord($piece) < 96 and $yourColor == 'black'){
        if($piece == 'K'){exit("SOFTWARE-ERROR: How can a king face a king?f");}
        else if ($piece == 'Q'){return true;}
        else if ($piece == 'R'){return true;}
    }

    # danger from left?
    for($tmp_x = $king_x - 1; $tmp_x > 1; $tmp_x--){
        if(  substr($newBoard,getIndex($king_x, $tmp_y),1) != '0'  ) {break;}
    }
    $piece = substr($newBoard,getIndex($king_x, $tmp_y),1);
    if ($piece != '0' and ord($piece) > 96 and $yourColor == 'white'){
        if($piece == 'k'){exit("SOFTWARE-ERROR: How can a king face a king?g");}
        else if ($piece == 'q'){return true;}
        else if ($piece == 'r'){return true;}
    } else if ($piece != '0' and ord($piece) < 96 and $yourColor == 'black'){
        if($piece == 'K'){exit("SOFTWARE-ERROR: How can a king face a king?h");}
        else if ($piece == 'Q'){return true;}
        else if ($piece == 'R'){return true;}
    }


    # danger from diagonal right top?
    for($i=1; $i <= 8 - max($king_x, $king_y); $i++){
        $tmp_x = $king_x + $i;
        $tmp_y = $king_y + $i;
        $piece = substr($newBoard,getIndex($tmp_x, $tmp_y),1);
        if($piece != '0') {break;}
    }
    if ($piece != '0' and ord($piece) > 96 and $yourColor == 'white'){
        if($piece == 'k'){exit("SOFTWARE-ERROR: How can a king face a king?i");}
        else if ($piece == 'q'){return true;}
        else if ($piece == 'b'){return true;}
        else if ($piece == 'p'){return true;}
    } else if ($piece != '0' and ord($piece) < 96 and $yourColor == 'black'){
        if($piece == 'K'){exit("SOFTWARE-ERROR: How can a king face a king?j");}
        else if ($piece == 'Q'){return true;}
        else if ($piece == 'B'){return true;}
    }
    # danger from diagonal left top?
    for($i=1; $i <= 8 - max(9-$king_x, $king_y); $i++){
        $tmp_x = $king_x - $i;
        $tmp_y = $king_y + $i;
        $piece = substr($newBoard,getIndex($tmp_x, $tmp_y),1);
        if($piece != '0') {break;}
    }
    if ($piece != '0' and ord($piece) > 96 and $yourColor == 'white'){
        if($piece == 'k'){exit("SOFTWARE-ERROR: How can a king face a king?k");}
        else if ($piece == 'q'){return true;}
        else if ($piece == 'b'){return true;}
        else if ($piece == 'p'){return true;}
    } else if ($piece != '0' and ord($piece) < 96 and $yourColor == 'black'){
        if($piece == 'K'){exit("SOFTWARE-ERROR: How can a king face a king?l");}
        else if ($piece == 'Q'){return true;}
        else if ($piece == 'B'){return true;}
    }
    # danger from diagonal right bottom?
    for($i=1; $i <= 8 - max($king_x, 9 - $king_y); $i++){
        $tmp_x = $king_x + $i;
        $tmp_y = $king_y - $i;
        $piece = substr($newBoard,getIndex($tmp_x, $tmp_y),1);
        if($piece != '0') {break;}
    }
    if ($piece != '0' and ord($piece) > 96 and $yourColor == 'white'){
        if($piece == 'k'){exit("SOFTWARE-ERROR: How can a king face a king?m");}
        else if ($piece == 'q'){return true;}
        else if ($piece == 'b'){return true;}
    } else if ($piece != '0' and ord($piece) < 96 and $yourColor == 'black'){
        if($piece == 'K'){exit("SOFTWARE-ERROR: How can a king face a king?n");}
        else if ($piece == 'Q'){return true;}
        else if ($piece == 'B'){return true;}
        else if ($piece == 'P'){return true;}
    }
    # danger from diagonal left bottom?
    for($i=1; $i <= min($king_x, $king_y)-1; $i++){
        $tmp_x = $king_x - $i;
        $tmp_y = $king_y - $i;
        $piece = substr($newBoard,getIndex($tmp_x, $tmp_y),1);
        if($piece != '0') {break;}
    }
    if ($piece != '0' and ord($piece) > 96 and $yourColor == 'white'){
        if($piece == 'k'){exit("SOFTWARE-ERROR: How can a king face a king?o");}
        else if ($piece == 'q'){return true;}
        else if ($piece == 'b'){return true;}
        else if ($piece == 'p'){return true;}
    } else if ($piece != '0' and ord($piece) < 96 and $yourColor == 'black'){
        if($piece == 'K'){exit("SOFTWARE-ERROR: How can a king face a king?p");}
        else if ($piece == 'Q'){return true;}
        else if ($piece == 'B'){return true;}
        else if ($piece == 'P'){return true;}
    }

    # danger from knights?
    # from very top left?
    $tmp_x = $king_x - 1;
    $tmp_y = $king_y + 2;
    if(isPositionValid($tmp_x, $tmp_y)){
        $piece = substr($newBoard,getIndex($tmp_x, $tmp_y),1);
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
        $piece = substr($newBoard,getIndex($tmp_x, $tmp_y),1);
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
        $piece = substr($newBoard,getIndex($tmp_x, $tmp_y),1);
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
        $piece = substr($newBoard,getIndex($tmp_x, $tmp_y),1);
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
        $piece = substr($newBoard,getIndex($tmp_x, $tmp_y),1);
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
        $piece = substr($newBoard,getIndex($tmp_x, $tmp_y),1);
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
        $piece = substr($newBoard,getIndex($tmp_x, $tmp_y),1);
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
        $piece = substr($newBoard,getIndex($tmp_x, $tmp_y),1);
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
    $c7 = (($to_y - $from_y) ==-2) and (($to_x - $from_x) ==1); # 2 down,1 right
    $c8 = (($to_y - $from_y) ==-1) and (($to_x - $from_x) ==2); # 1 down,2 right
    if ($c1 or $c2 or $c3 or $c4 or $c5 or $c6 or $c7 or $c8) {
        # Everything is ok.
    } else {
        exit("ERROR: From ($from_x | $from_y) to ($to_x | $to_y) is no valid
              move for a knight.");
    }

    $index = getIndex($to_x, $to_y);
    $piece = substr($currentBoard, $index, 1);
    if ($piece != '0') {
        if( ($yourColor == 'white' and ord($piece) < 96) or 
            ($yourColor == 'black' and ord($piece) > 96)) {
            exit("ERROR: You may not capture your own chess pieces.");
        }
    }
}


function isKingMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                                    $yourColor){
    if (abs($from_x - $to_x) <= 1 and abs($from_y - $to_y) <= 1){
        # Everything is ok.
    } else {
        exit("ERROR: From ($from_x | $from_y) to ($to_x | $to_y) is no valid
              move for a king.");
    }

    $index = getIndex($to_x, $to_y);
    $piece = substr($currentBoard, $index, 1);
    if ($piece != '0') {
        if( ($yourColor == 'white' and ord($piece) < 96) or 
            ($yourColor == 'black' and ord($piece) > 96)) {
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
                $piece = substr($currentBoard, $index, 1);
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
                $piece = substr($currentBoard, $index, 1);
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
    $piece = substr($currentBoard, $index, 1);
    if ($piece != '0') {
        if( ($yourColor == 'white' and ord($piece) < 96) or 
            ($yourColor == 'black' and ord($piece) > 96)) {
            exit("ERROR: You may not capture your own chess pieces.");
        }
    }
}

function isRookMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                                    $yourColor){
    #Castling will be implemented later: http://en.wikipedia.org/wiki/Castling
    if ($from_x == $to_x) {
        #moving straight up / down
        if($from_y < $to_y){
            #is moving up
            for($y_tmp=$from_y+1; $y_tmp < $to_y; $y_tmp++){
                $index = getIndex($from_x, $y_tmp);
                $piece = substr($currentBoard, $index, 1);
                if($piece != '0'){
                    exit("ERROR: On ($from_x | $y_tmp) is $piece.");
                }
            }
        } else {
            #is moving down
            for($y_tmp=$from_y-1; $y_tmp > $to_y; $y_tmp--){
                $index = getIndex($from_x, $y_tmp);
                $piece = substr($currentBoard, $index, 1);
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
                $piece = substr($currentBoard, $index, 1);
                if($piece != '0'){
                    exit("ERROR: On ($x_tmp | $from_y) is $piece.");
                }
            }
        } else {
            #is moving left
            for($x_tmp=$from_x-1; $x_tmp > $to_x; $x_tmp--){
                $index = getIndex($x_tmp, $from_y);
                $piece = substr($currentBoard, $index, 1);
                if($piece != '0'){
                    exit("ERROR: On ($x_tmp | $from_y) is $piece.");
                }
            }
        }

    } else {
        exit("ERROR: From ($from_x | $from_y) to ($to_x | $to_y) is no valid
              move for a rook.");
    }


    $index = getIndex($to_x, $to_y);
    $piece = substr($currentBoard, $index, 1);
    if ($piece != '0') {
        if( ($yourColor == 'white' and ord($piece) < 96) or 
            ($yourColor == 'black' and ord($piece) > 96)) {
            exit("ERROR: You may not capture your own chess pieces.");
        }
    }
}

function isPawnMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, 
                                                                    $yourColor){
    #to implement: http://en.wikipedia.org/wiki/En_passant
    if($from_x == $to_x and abs($from_y - $to_y) <= 2){
        #moving up / down
        $index = getIndex($to_x, $to_y);
        $piece = substr($currentBoard, $index, 1);
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
                exit("ERROR: Pawns may only move two if they are on their home
                      row.");
        }
    } else if(abs($from_x-$to_x) == 1 and abs($from_y-$to_y) == 1){
        # pawns capturing move
        $index = getIndex($to_x, $to_y);
        $piece_target = substr($currentBoard, $index, 1);
        if($yourColor == 'white'){
            if($from_y > $to_y) {
                exit("ERROR: White may only move up with pawns.");
            }
            if($piece_target == '0' or ord($piece_target) < 96) {
                exit("ERROR: You may only make the pawn capture move if 
                      a chess piece of the opponent is on the target field.");
            }
        } else {
            if($from_y < $to_y) {
                exit("ERROR: Black may only move down with pawns.");
            }
            if($piece_target == '0' or ord($piece_target) > 96) {
                exit("ERROR: You may only make the pawn capture move if 
                      a chess piece of the opponent is on the target field.");
            }
        }
    } else {
        exit("ERROR: From ($from_x | $from_y) to ($to_x | $to_y) is no valid
              move for a pawn.");
    }

    $index = getIndex($to_x, $to_y);
    $piece = substr($currentBoard, $index, 1);
    if ($piece != '0') {
        if( ($yourColor == 'white' and ord($piece) < 96) or 
            ($yourColor == 'black' and ord($piece) > 96)) {
            exit("ERROR: You may not capture your own chess pieces.");
        }
    }

}

function isQueenMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, $yourColor){
    if ($from_x == $to_x) {
        #moving straight up / down
        if($from_y < $to_y){
            #is moving up
            for($y_tmp=$from_y+1; $y_tmp < $to_y; $y_tmp++){
                $index = getIndex($from_x, $y_tmp);
                $piece = substr($currentBoard, $index, 1);
                if($piece != '0'){
                    exit("ERROR: On ($from_x | $y_tmp) is $piece.");
                }
            }
        } else {
            #is moving down
            for($y_tmp=$from_y-1; $y_tmp > $to_y; $y_tmp--){
                $index = getIndex($from_x, $y_tmp);
                $piece = substr($currentBoard, $index, 1);
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
                $piece = substr($currentBoard, $index, 1);
                if($piece != '0'){
                    exit("ERROR: On ($x_tmp | $from_y) is $piece.");
                }
            }
        } else {
            #is moving left
            for($x_tmp=$from_x-1; $x_tmp > $to_x; $x_tmp--){
                $index = getIndex($x_tmp, $from_y);
                $piece = substr($currentBoard, $index, 1);
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
                $piece = substr($currentBoard, $index, 1);
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
                $piece = substr($currentBoard, $index, 1);
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
    $piece = substr($currentBoard, $index, 1);
    if ($piece != '0') {
        if( ($yourColor == 'white' and ord($piece) < 96) or 
            ($yourColor == 'black' and ord($piece) > 96)) {
            exit("ERROR: You may not capture your own chess pieces.");
        }
    }

}

function makeMove($from_index, $to_index, $currentBoard, $move){
    # Implement Promotion: http://en.wikipedia.org/wiki/Promotion_(chess)
    $piece        = substr($currentBoard, $from_index, 1);
    $currentBoard = substr($currentBoard, 0, $from_index)."0"
                                          .substr($currentBoard, $from_index+1);
    $currentBoard = substr($currentBoard, 0, $to_index).$piece
                                          .substr($currentBoard, $to_index+1);
    $table = "chess_currentGames";
    $cond  = "WHERE  `chess_currentGames`.`id` =".CURRENT_GAME_ID;
    $query = "UPDATE  `$table` SET  ";
    $query.= "`currentBoard` =  '$currentBoard', ";
    $query.= "`moveList` = CONCAT(`moveList`,'$move\n'), ";
    $query.= "`whoseTurnIsIt` =  ((`whoseTurnIsIt` + 1)%2), ";
    $query.= "`lastMove` = CURRENT_TIMESTAMP ";
    $query.= $cond;
    updateDataInDatabase($query, $table);
}









if(isset($_GET['gameID'])){
    $gameID = intval($_GET['gameID']);
    $table = "chess_currentGames";
    $row   = array("currentBoard","whoseTurnIsIt", "whitePlayerID", "blackPlayerID");
    $cond  = "WHERE (`whitePlayerID` = ".USER_ID." OR `blackPlayerID` = ".USER_ID.") AND `id` = ".$gameID;
    $query = "SELECT `currentBoard`, `whoseTurnIsIt`, `blackPlayerID`, `whitePlayerID`  FROM `$table` $cond";
    $result = selectFromDatabase($query, $row, $table, $condition, $limit = 1);

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
        } else {
            $yourColor = 'black';
        }
        define('CURRENT_GAME_ID', $gameID);
    }
}

if(!defined('CURRENT_GAME_ID')){exit('ERROR: Wrong gameID.');}

if(isset($_GET['move'])){
    $move = mysql_real_escape_string($_GET['move']);
    # Is it your turn?
    if($whoseTurnIsItLanguage != $yourColor){exit("ERROR: It's not your turn");}
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

    # Is one of your chess pieces on the from-field?
    $piece = $currentBoard[$from_index];
    if($piece == '0'){exit("ERROR: No chess piece on field ($from_x | $from_y).");}
    if($yourColor == 'white' and ord($piece) > 96){
        exit("ERROR: The chess piece on field ($from_x | $from_y) is $piece.
              It belongs to your opponent. You are white. Your chess pieces
              have capital letters.");
    }
    if($yourColor == 'black' and ord($piece) < 96){
        exit("ERROR: The chess piece on field ($from_x | $from_y) is $piece.
              It belongs to your opponent. You are black. Your chess pieces
              have lower-case letters.");
    }
    # Can the chess piece make this move?
    $piece_lower = strtolower($piece);
    if ($piece_lower == 'q'){
        isQueenMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, $yourColor);
    } else if ($piece_lower == 'p') {
        isPawnMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, $yourColor);
    } else if ($piece_lower == 'r') {
        isRookMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, $yourColor);
    } else if ($piece_lower == 'b') {
        isBishopMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, $yourColor);
    } else if ($piece_lower == 'k') {
        isKingMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, $yourColor);
    } else if ($piece_lower == 'n') {
        isKnightMoveValid($from_x, $from_y, $to_x, $to_y, $currentBoard, $yourColor);
    } else {
        exit("Not implemented jet:".$piece_lower);
    }

    # Do you set yourself check with this move?
    $piece    = substr($currentBoard, $from_index, 1);
    $newBoard = substr($currentBoard, 0, $from_index)."0".substr($currentBoard, $from_index+1);
    $newBoard = substr($newBoard, 0, $to_index).$piece.substr($newBoard, $to_index+1);
    if (isPlayerCheck($newBoard, $yourColor)){
        exit("ERROR: You set yourself check!");
    }
    # Everything is ok => move!
    makeMove($from_index, $to_index, $currentBoard, $move);
    # Check for:
        # Threefold repetition: http://en.wikipedia.org/wiki/Threefold_repetition
        # Stalemate: http://en.wikipedia.org/wiki/Stalemate
        # 50-move rule: http://en.wikipedia.org/wiki/Fifty-move_rule
        # Check: http://en.wikipedia.org/wiki/Check_(chess)
}


if(isset($_GET['gameID'])){
    $gameID = intval($_GET['gameID']);
    $table = "chess_currentGames";
    $row   = array("currentBoard","whoseTurnIsIt", "whitePlayerID", 
                   "blackPlayerID");
    $cond  = "WHERE (`whitePlayerID` = ".USER_ID." OR `blackPlayerID` = ";
    $cond .= USER_ID.") AND `id` = ".$gameID;
    $query = "SELECT `currentBoard`, `whoseTurnIsIt`, `blackPlayerID`, ";
    $query.= "`whitePlayerID`  FROM `$table` $cond";
    $result = selectFromDatabase($query, $row, $table, $condition, $limit = 1);

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
        } else {
            $yourColor = 'black';
        }
        define('CURRENT_GAME_ID', $gameID);
    }
}



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
echo "You are: ".$yourColor;

?>
