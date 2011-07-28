<?
/**
 * @author: Martin Thoma
 * play a standard chess game
 * */

require ('wrapper.inc.php');
if(USER_ID === false){exit("Please <a href='login.wrapper.php'>login</a>");}

function getIndex($x, $y){
    return ($y-1)*8+($x-1);
}

function getCoordinates($index){
    $x = $index % 8;
    $y = ($index - $x)/8;
    $x++;
    $y++;
    return array($x, $y);
}

function isPositionValid($x, $y) {
    if (1 <= $x and $x <= 8 and 1 <= $y and $y <= 8){ return true;}
    else {return false;}
}

function getNewBoard($currentBoard, $from_index, $to_index){
    $piece    = substr($currentBoard, $from_index, 1);
    $newBoard = substr($currentBoard, 0, $from_index)."0".
                substr($currentBoard, $from_index+1);
    $newBoard = substr($newBoard, 0, $to_index).$piece.
                substr($newBoard, $to_index+1);
    return $newBoard;
}

function hasValidMoves($board, $color){
    for($from_index = 0; $from_index < 63; $from_index++){
        $piece = substr($board,$from_index,1);
        $coord = getCoordinates($from_index);
        if($piece != '0' and ord($piece) < 96 and $color == 'white'){
            if($piece == 'P'){
                # Which moves could a pawn possibly make?
                if($coord[1] > 1){
                    $to_index = getIndex($coord[0],$coord[1]+1);
                    $targetpiece = substr($board,$to_index,1);
                    if($targetpiece == '0'){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                if($coord[1] == 2){
                    $to_index = getIndex($coord[0],$coord[1]+2);
                    $field1   = substr($board,$to_index+8,1);
                    $targetpiece = substr($board,$to_index,1);
                    if($targetpiece == '0' and $field1 == '0'){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }

                if($coord[0] > 1 and $coord[1] > 1){
                    $to_index = getIndex($coord[0]-1, $coord[1]+1);
                    $targetpiece = substr($board,$to_index,1);
                    if(ord($targetpiece)> 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                if($coord[0] < 8 and $coord[1] > 1){
                    $to_index = getIndex($coord[0]+1, $coord[1]+1);
                    $targetpiece = substr($board,$to_index,1);
                    if(ord($targetpiece) > 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
            }
            if($piece == 'N'){
                # Which moves could a knight possibly make?
                if(  isPositionValid($coord[0]+1,$coord[1]+2)  ){
                    $to_index    = getIndex($coord[0]+1, $coord[1]+2);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) > 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                if(  isPositionValid($coord[0]+1,$coord[1]-2)  ){
                    $to_index    = getIndex($coord[0]+1, $coord[1]-2);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) > 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                if(  isPositionValid($coord[0]+2,$coord[1]+1)  ){
                    $to_index    = getIndex($coord[0]+2, $coord[1]+1);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) > 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                if(  isPositionValid($coord[0]+2,$coord[1]-1)  ){
                    $to_index    = getIndex($coord[0]+2, $coord[1]-1);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) > 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                if(  isPositionValid($coord[0]-2,$coord[1]-1)  ){
                    $to_index    = getIndex($coord[0]-2, $coord[1]-1);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) > 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                if(  isPositionValid($coord[0]-2,$coord[1]+1)  ){
                    $to_index    = getIndex($coord[0]-2, $coord[1]+1);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) > 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                if(  isPositionValid($coord[0]-1,$coord[1]+2)  ){
                    $to_index    = getIndex($coord[0]-1, $coord[1]+2);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) > 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                if(  isPositionValid($coord[0]-1,$coord[1]-2)  ){
                    $to_index    = getIndex($coord[0]-1, $coord[1]-2);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) > 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
            }
            if($piece == 'B'){
                # Which moves could a bishop possibly make?
                # diagonal right up
                for($i=1; $i <= 8 - max($coord[0], $coord[1]); $i++){
                    $tmp_x = $coord[0] + $i;
                    $tmp_y = $coord[1] + $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                # diagonal right down
                for($i=1; $i <= 8 - max($coord[0], 9 - $coord[1]); $i++){
                    $tmp_x = $coord[0] + $i;
                    $tmp_y = $coord[1] - $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                # diagonal left up
                for($i=1; $i <= 8 - max(9-$coord[0], $coord[1]); $i++){
                    $tmp_x = $coord[0] - $i;
                    $tmp_y = $coord[1] + $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                # diagonal left down
                for($i=1; $i <= min($coord[0], $coord[1])-1; $i++){
                    $tmp_x = $coord[0] - $i;
                    $tmp_y = $coord[1] - $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
            }
            if($piece == 'R'){
                # Which moves could a rook possibly make?
                # top
                $tmp_x = $coord[0];
                for($tmp_y=$coord[1]+1; $i <= 8; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                # down
                for($tmp_y=$coord[1]-1; $i >= 1; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                # left
                $tmp_y = $coord[1];
                for($tmp_x=$coord[1]-1; $i >= 1; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                # right
                for($tmp_x=$coord[1]+1; $i <= 8; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
            }
            if($piece == 'Q'){
                # Which moves could a queen possibly make?
                # All of rook:
                # top
                $tmp_x = $coord[0];
                for($tmp_y=$coord[1]+1; $i <= 8; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                # down
                for($tmp_y=$coord[1]-1; $i >= 1; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                # left
                $tmp_y = $coord[1];
                for($tmp_x=$coord[1]-1; $i >= 1; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                # right
                for($tmp_x=$coord[1]+1; $i <= 8; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                # All of bishop
                # diagonal right up
                for($i=1; $i <= 8 - max($coord[0], $coord[1]); $i++){
                    $tmp_x = $coord[0] + $i;
                    $tmp_y = $coord[1] + $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                # diagonal right down
                for($i=1; $i <= 8 - max($coord[0], 9 - $coord[1]); $i++){
                    $tmp_x = $coord[0] + $i;
                    $tmp_y = $coord[1] - $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                # diagonal left up
                for($i=1; $i <= 8 - max(9-$coord[0], $coord[1]); $i++){
                    $tmp_x = $coord[0] - $i;
                    $tmp_y = $coord[1] + $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                # diagonal left down
                for($i=1; $i <= min($coord[0], $coord[1])-1; $i++){
                    $tmp_x = $coord[0] - $i;
                    $tmp_y = $coord[1] - $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) > 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'white')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
            }
            if($piece == 'K'){
                # Which moves could a king possibly make?
                $tmp_x = $coord[0]+0;
                $tmp_y = $coord[1]+1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) > 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                $tmp_x = $coord[0]+1;
                $tmp_y = $coord[1]+1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) > 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                $tmp_x = $coord[0]+1;
                $tmp_y = $coord[1]+0;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) > 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                $tmp_x = $coord[0]+1;
                $tmp_y = $coord[1]-1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) > 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                $tmp_x = $coord[0]+0;
                $tmp_y = $coord[1]-1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) > 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                $tmp_x = $coord[0]-1;
                $tmp_y = $coord[1]-1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) > 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                $tmp_x = $coord[0]-1;
                $tmp_y = $coord[1]-0;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) > 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
                $tmp_x = $coord[0]-1;
                $tmp_y = $coord[1]+1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) > 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'white')){return true;}
                    }
                }
            }
        }
        if($piece != '0' and ord($piece) > 97 and $color == 'black'){
            #only the following is different to the white-part:
            # * always when a comparison like ord($piece)>97 is made, it is 
            #   changed to ord($piece)<97
            # * all "white" strings are changed to "black"
            # * all $piece == 'P' parts have to be lower-case: $piece == 'p'
            # * the pawns
            #   * may only move down, not up like for white
            #   * have another home row

            if($piece == 'p'){
                # Which moves could a pawn possibly make?
                if($coord[1] > 1){
                    $to_index = getIndex($coord[0],$coord[1]-1);
                    $targetpiece = substr($board,$to_index,1);
                    if($targetpiece == '0'){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                if($coord[1] == 7){
                    $to_index = getIndex($coord[0],$coord[1]-2);
                    $field1   = substr($board,$to_index-8,1);
                    $targetpiece = substr($board,$to_index,1);
                    if($targetpiece == '0' and $field1 == '0'){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }

                if($coord[0] > 1 and $coord[1] > 1){
                    $to_index = getIndex($coord[0]-1, $coord[1]-1);
                    $targetpiece = substr($board,$to_index,1);
                    if(ord($targetpiece)< 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                if($coord[0] < 8 and $coord[1] > 1){
                    $to_index = getIndex($coord[0]+1, $coord[1]-1);
                    $targetpiece = substr($board,$to_index,1);
                    if(ord($targetpiece) < 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
            }
            if($piece == 'n'){
                # Which moves could a knight possibly make?
                if(  isPositionValid($coord[0]+1,$coord[1]+2)  ){
                    $to_index    = getIndex($coord[0]+1, $coord[1]+2);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) < 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                if(  isPositionValid($coord[0]+1,$coord[1]-2)  ){
                    $to_index    = getIndex($coord[0]+1, $coord[1]-2);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) < 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                if(  isPositionValid($coord[0]+2,$coord[1]+1)  ){
                    $to_index    = getIndex($coord[0]+2, $coord[1]+1);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) < 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                if(  isPositionValid($coord[0]+2,$coord[1]-1)  ){
                    $to_index    = getIndex($coord[0]+2, $coord[1]-1);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) < 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                if(  isPositionValid($coord[0]-2,$coord[1]-1)  ){
                    $to_index    = getIndex($coord[0]-2, $coord[1]-1);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) < 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                if(  isPositionValid($coord[0]-2,$coord[1]+1)  ){
                    $to_index    = getIndex($coord[0]-2, $coord[1]+1);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) < 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                if(  isPositionValid($coord[0]-1,$coord[1]+2)  ){
                    $to_index    = getIndex($coord[0]-1, $coord[1]+2);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) < 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                if(  isPositionValid($coord[0]-1,$coord[1]-2)  ){
                    $to_index    = getIndex($coord[0]-1, $coord[1]-2);
                    $targetPiece = substr($board,$to_index,1);
                    if(  $targetPiece == '0' or ord($targetPiece) < 97  ) {
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
            }
            if($piece == 'b'){
                # Which moves could a bishop possibly make?
                # diagonal right up
                for($i=1; $i <= 8 - max($coord[0], $coord[1]); $i++){
                    $tmp_x = $coord[0] + $i;
                    $tmp_y = $coord[1] + $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                # diagonal right down
                for($i=1; $i <= 8 - max($coord[0], 9 - $coord[1]); $i++){
                    $tmp_x = $coord[0] + $i;
                    $tmp_y = $coord[1] - $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                # diagonal left up
                for($i=1; $i <= 8 - max(9-$coord[0], $coord[1]); $i++){
                    $tmp_x = $coord[0] - $i;
                    $tmp_y = $coord[1] + $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                # diagonal left down
                for($i=1; $i <= min($coord[0], $coord[1])-1; $i++){
                    $tmp_x = $coord[0] - $i;
                    $tmp_y = $coord[1] - $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
            }
            if($piece == 'r'){
                # Which moves could a rook possibly make?
                # top
                $tmp_x = $coord[0];
                for($tmp_y=$coord[1]+1; $i <= 8; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                # down
                for($tmp_y=$coord[1]-1; $i >= 1; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                # left
                $tmp_y = $coord[1];
                for($tmp_x=$coord[1]-1; $i >= 1; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                # right
                for($tmp_x=$coord[1]+1; $i <= 8; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
            }
            if($piece == 'q'){
                # Which moves could a queen possibly make?
                # All of rook:
                # top
                $tmp_x = $coord[0];
                for($tmp_y=$coord[1]+1; $i <= 8; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                # down
                for($tmp_y=$coord[1]-1; $i >= 1; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                # left
                $tmp_y = $coord[1];
                for($tmp_x=$coord[1]-1; $i >= 1; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                # right
                for($tmp_x=$coord[1]+1; $i <= 8; $i++){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                # All of bishop
                # diagonal right up
                for($i=1; $i <= 8 - max($coord[0], $coord[1]); $i++){
                    $tmp_x = $coord[0] + $i;
                    $tmp_y = $coord[1] + $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                # diagonal right down
                for($i=1; $i <= 8 - max($coord[0], 9 - $coord[1]); $i++){
                    $tmp_x = $coord[0] + $i;
                    $tmp_y = $coord[1] - $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                # diagonal left up
                for($i=1; $i <= 8 - max(9-$coord[0], $coord[1]); $i++){
                    $tmp_x = $coord[0] - $i;
                    $tmp_y = $coord[1] + $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                # diagonal left down
                for($i=1; $i <= min($coord[0], $coord[1])-1; $i++){
                    $tmp_x = $coord[0] - $i;
                    $tmp_y = $coord[1] - $i;
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece != '0') {
                        if(ord($piece) < 97){
                            $newBoard=getNewBoard($board,$from_index,$to_index);
                            if(!isPlayerCheck($newBoard, 'black')){return true;}
                        }
                        break;
                    } else{
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
            }
            if($piece == 'k'){
                # Which moves could a king possibly make?
                $tmp_x = $coord[0]+0;
                $tmp_y = $coord[1]+1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) < 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                $tmp_x = $coord[0]+1;
                $tmp_y = $coord[1]+1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) < 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                $tmp_x = $coord[0]+1;
                $tmp_y = $coord[1]+0;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) < 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                $tmp_x = $coord[0]+1;
                $tmp_y = $coord[1]-1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) < 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                $tmp_x = $coord[0]+0;
                $tmp_y = $coord[1]-1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) < 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                $tmp_x = $coord[0]-1;
                $tmp_y = $coord[1]-1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) < 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                $tmp_x = $coord[0]-1;
                $tmp_y = $coord[1]-0;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) < 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
                $tmp_x = $coord[0]-1;
                $tmp_y = $coord[1]+1;
                if(isPositionValid($tmp_x, $tmp_y)){
                    $to_index = getIndex($tmp_x, $tmp_y);
                    $piece = substr($board,getIndex($tmp_x, $tmp_y),1);
                    if($piece == '0' or ord($piece) < 97){
                        $newBoard = getNewBoard($board, $from_index, $to_index);
                        if (!isPlayerCheck($newBoard, 'black')){return true;}
                    }
                }
            }
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
    $coord  = getCoordinates($king_index);
    $king_x = $coord[0];
    $king_y = $coord[1];

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
    for($tmp_y = $king_y - 1; $tmp_y > 2; $tmp_y--){
        if(  substr($newBoard,getIndex($king_x, $tmp_y),1) != '0'  ) {break;}
    }
    if($tmp_y<1){$tmp_y=1;}
    $piece = substr($newBoard,getIndex($king_x, $tmp_y),1);
    if ($piece != '0' and ord($piece) > 96 and $yourColor == 'white'){
        if($piece == 'k'){exit("SOFTWARE-ERROR: How can a king face a king?c");}
        else if ($piece == 'q'){return true;}
        else if ($piece == 'r'){return true;}
    } else if ($piece != '0' and ord($piece) < 96 and $yourColor == 'black'){
        if($piece == 'K'){exit("SOFTWARE-ERROR: How can a king face a king?d");}
        else if ($piece == 'Q'){return true;}
        else if ($piece == 'R'){return true;}
    }

    $tmp_y = $king_y;
    # danger from right?
    for($tmp_x = $king_x + 1; $tmp_x < 8; $tmp_x++){
        if(  substr($newBoard,getIndex($tmp_x, $tmp_y),1) != '0'  ) {;break;}
    }
    $piece = substr($newBoard,getIndex($tmp_x, $tmp_y),1);
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
    for($tmp_x = $king_x - 1; $tmp_x > 2; $tmp_x--){
        if(  substr($newBoard,getIndex($king_x, $tmp_y),1) != '0'  ) {break;}
    }
    if($tmp_x<1){$tmp_x=1;}
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
        else if ($piece == 'p' and abs($tmp_x-$king_x)==1){return true;}
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
        else if ($piece == 'p' and abs($tmp_x-$king_x)==1){return true;}
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
        else if ($piece == 'P' and abs($tmp_x-$king_x)==1){return true;}
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
        else if ($piece == 'p' and abs($tmp_x-$king_x)==1){return true;}
    } else if ($piece != '0' and ord($piece) < 96 and $yourColor == 'black'){
        if($piece == 'K'){exit("SOFTWARE-ERROR: How can a king face a king?p");}
        else if ($piece == 'Q'){return true;}
        else if ($piece == 'B'){return true;}
        else if ($piece == 'P' and abs($tmp_x-$king_x)==1){return true;}
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
        exit("ERROR: From ($from_x | $from_y) to ($to_x | $to_y) is no valid ".
                    "move for a knight.");
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
        # Everything is ok, standard king move
    } else if (abs($from_x - $to_x) == 2 and ($from_y - $to_y) == 0) {
        # castling?
        if( ($yourColor == 'white' and $from_x ==  4) or
            ($yourColor == 'black' and $from_x == 60)    ){
            $table = "chess_currentGames";
            $rows  = array('whiteCastlingKingsidePossible', 
                           'whiteCastlingQueensidePossible',
                           'blackCastlingKingsidePossible',
                           'blackCastlingQueensidePossible');
            $cond  = "WHERE `id` = ".CURRENT_GAME_ID;

            $query = "SELECT `whiteCastlingKingsidePossible`, ";
            $query.= "`whiteCastlingQueensidePossible`, ";
            $query.= "`blackCastlingKingsidePossible`, ";
            $query.= "`blackCastlingQueensidePossible` ";
            $query.= "FROM `$table` $cond";
            $result= selectFromDatabase($query, $rows, $table, $cond);
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
                    $piece = substr($currentBoard, $index, 1);
                    if($piece != '0'){
                        exit("ERROR: $piece is between your King and your ".
                                    "rook. Castling is not possible.");
                    }
                }
            } else {
                for($x_tmp = $from_x-1; $x_tmp > $to_x; $x_tmp--){
                    $index = getIndex($x_tmp, $from_y);
                    $piece = substr($currentBoard, $index, 1);
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
                $index = getIndex($x_tmp, $y_tmp);
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
        exit("ERROR: From ($from_x | $from_y) to ($to_x | $to_y) is no valid ".
                    "move for a rook.");
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
                                                         $yourColor, $moveList){
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
                exit("ERROR: Pawns may only move two if they are on their home".
                            " row.");
        }
    } else if(abs($from_x-$to_x) == 1 and abs($from_y-$to_y) == 1){
        # pawns capturing move
        $index = getIndex($to_x, $to_y);
        $piece_target = substr($currentBoard, $index, 1);

        # For en passant:
        $moveArray = explode("\n", trim($moveList));
        $lastMove = $moveArray[-1];
        $lastFromX= substr($lastMove, 0, 1);
        $lastFromY= substr($lastMove, 1, 1);
        $lastToX  = substr($lastMove, 2, 1);
        $lastToY  = substr($lastMove, 3, 1);


        if($yourColor == 'white'){
            if($from_y > $to_y) {
                exit("ERROR: White may only move up with pawns.");
            }
            if($piece_target == '0' or ord($piece_target) < 96) {
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
            if($piece_target == '0' or ord($piece_target) > 96) {
                $shouldBePawn = substr($currentBoard, 
                                       getIndex($to_x, $to_y+1), 1);
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
    $piece = substr($currentBoard, $index, 1);
    if ($piece != '0') {
        if( ($yourColor == 'white' and ord($piece) < 96) or 
            ($yourColor == 'black' and ord($piece) > 96)) {
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

function makeMove($from_index, $to_index, $currentBoard, $move, $color){
    /* This function submits the move to the database. All checks if this move
       is possible have been done before. 
       All database-changes of the current game are done in this function.
       Nothing else. */

    $piece        = substr($currentBoard, $from_index, 1);
    $capturedPiece= substr($currentBoard, $to_index, 1);
    $to_coord     = getCoordinates($to_index);
    $from_coord   = getCoordinates($from_index);
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
        $table = "chess_currentGames";
        $cond  = "WHERE  `chess_currentGames`.`id` =".CURRENT_GAME_ID;
        if($color == 'white'){
            if($to_index == 6){
                // Kingside Castling for white
                $currentBoard = substr($currentBoard, 0, 7).'0'
                                          .substr($currentBoard, $from_index+1);
                $currentBoard = substr($currentBoard, 0, 5).'R'
                                          .substr($currentBoard, $to_index+1);

                $query = "UPDATE  `$table` SET  ";
                $query.= "`currentBoard` =  '$currentBoard', ";
                $query.= "`moveList` = CONCAT(`moveList`,'$move\n'), ";
                $query.= "`whoseTurnIsIt` =  ((`whoseTurnIsIt` + 1)%2), ";
                $query.= "`lastMove` = CURRENT_TIMESTAMP ";
                $query.= $cond;
                updateDataInDatabase($query, $table);
            } else {
                // Queenside Castling for white
                $currentBoard = substr($currentBoard, 0, 0).'0'
                                          .substr($currentBoard, $from_index+1);
                $currentBoard = substr($currentBoard, 0, 3).'R'
                                          .substr($currentBoard, $to_index+1);

                $query = "UPDATE  `$table` SET  ";
                $query.= "`currentBoard` =  '$currentBoard', ";
                $query.= "`moveList` = CONCAT(`moveList`,'$move\n'), ";
                $query.= "`whoseTurnIsIt` =  ((`whoseTurnIsIt` + 1)%2), ";
                $query.= "`lastMove` = CURRENT_TIMESTAMP ";
                $query.= $cond;
                updateDataInDatabase($query, $table);
            }
        } else {
            if($to_index == 62){
                // Kingside Castling for black
                $currentBoard = substr($currentBoard, 0, 63).'0'
                                          .substr($currentBoard, $from_index+1);
                $currentBoard = substr($currentBoard, 0, 61).'r'
                                          .substr($currentBoard, $to_index+1);

                $query = "UPDATE  `$table` SET  ";
                $query.= "`currentBoard` =  '$currentBoard', ";
                $query.= "`moveList` = CONCAT(`moveList`,'$move\n'), ";
                $query.= "`whoseTurnIsIt` =  ((`whoseTurnIsIt` + 1)%2), ";
                $query.= "`lastMove` = CURRENT_TIMESTAMP ";
                $query.= $cond;
                updateDataInDatabase($query, $table);
            } else {
                // Queenside Castling for black
                $currentBoard = substr($currentBoard, 0, 56).'0'
                                          .substr($currentBoard, $from_index+1);
                $currentBoard = substr($currentBoard, 0, 59).'r'
                                          .substr($currentBoard, $to_index+1);

                $query = "UPDATE  `$table` SET  ";
                $query.= "`currentBoard` =  '$currentBoard', ";
                $query.= "`moveList` = CONCAT(`moveList`,'$move\n'), ";
                $query.= "`whoseTurnIsIt` =  ((`whoseTurnIsIt` + 1)%2), ";
                $query.= "`lastMove` = CURRENT_TIMESTAMP ";
                $query.= $cond;
                updateDataInDatabase($query, $table);
            }
        }
    }

    // Is this piece relevant for castling?
    // White - Kingside Castling
    if ($piece == 'K' or ($piece == 'R' and $from_index == 7)){
        $table = "chess_currentGames";
        $cond  = " WHERE  `chess_currentGames`.`id` =".CURRENT_GAME_ID;
        $query = "UPDATE  `$table` SET  `whiteCastlingKingsidePossible` = '0'";
        $query.= " $cond";
        updateDataInDatabase($query, $table);
    }
    // White - Queenside Castling
    if ($piece == 'K' or ($piece == 'R' and $from_index == 0)){
        $table = "chess_currentGames";
        $cond  = " WHERE  `chess_currentGames`.`id` =".CURRENT_GAME_ID;
        $query = "UPDATE  `$table` SET  `whiteCastlingQueensidePossible` = '0'";
        $query.= " $cond";
        updateDataInDatabase($query, $table);
    }
    // Black - Kingside Castling
    if ($piece == 'K' or ($piece == 'R' and $from_index == 63)){
        $table = "chess_currentGames";
        $cond  = " WHERE  `chess_currentGames`.`id` =".CURRENT_GAME_ID;
        $query = "UPDATE  `$table` SET  `blackCastlingKingsidePossible` = '0'";
        $query.= " $cond";
        updateDataInDatabase($query, $table);
    }
    // Black - Queenside Castling
    if ($piece == 'K' or ($piece == 'R' and $from_index == 56)){
        $table = "chess_currentGames";
        $cond  = " WHERE  `chess_currentGames`.`id` =".CURRENT_GAME_ID;
        $query = "UPDATE  `$table` SET  `blackCastlingQueensidePossible` = '0'";
        $query.= " $cond";
        updateDataInDatabase($query, $table);
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
    $table = "chess_currentGames";
    $cond  = "WHERE  `chess_currentGames`.`id` =".CURRENT_GAME_ID;

    $query = "UPDATE  `$table` SET  ";
    $query.= "`currentBoard` =  '$currentBoard', ";
    $query.= "`moveList` = CONCAT(`moveList`,'$move\n'), ";
    $query.= "`whoseTurnIsIt` =  ((`whoseTurnIsIt` + 1)%2), ";
    $query.= "`lastMove` = CURRENT_TIMESTAMP, ";


    if($pawnMoved == false and $captureMade == false) {    
        $query.= "`noCaptureAndPawnMoves` = `noCaptureAndPawnMoves` + 1 ";
    } else {
        $query.= "`noCaptureAndPawnMoves` = 0 ";
    }
    $query.= $cond;

    updateDataInDatabase($query, $table);
    return $currentBoard;
}
################################################################################
# End of function definitions. Start of the script                             # 
################################################################################

if(isset($_GET['gameID'])){
    $gameID = intval($_GET['gameID']);
    $table = "chess_currentGames";
    $row   = array("currentBoard","whoseTurnIsIt", "whitePlayerID", 
                   "blackPlayerID", "moveList", "noCaptureAndPawnMoves");
    $cond  = "WHERE (`whitePlayerID` = ".USER_ID." OR `blackPlayerID` = ";
    $cond .= USER_ID.") AND `id` = ".$gameID;
    $query = "SELECT `currentBoard`, `whoseTurnIsIt`, `blackPlayerID`, ";
    $query.= "`whitePlayerID`, `moveList`, `noCaptureAndPawnMoves` ";
    $query.= "FROM `$table` $cond";
    $result = selectFromDatabase($query, $row, $table, $condition);

    if($result !== false){
        $currentBoard  = $result['currentBoard'];
        $whoseTurnIsIt = $result['whoseTurnIsIt'];
        $moveList      = $result['moveList'];
        $noCaptureAndPawnMoves = $result['noCaptureAndPawnMoves'];
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
    $piece = substr($currentBoard,$from_index,1);
    if($piece == '0'){
        exit("ERROR: No chess piece on field ($from_x | $from_y).");
    }
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
    $currentBoard = makeMove($from_index, $to_index, $currentBoard, $move);
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

            $table = "chess_currentGames";
            $row = array('moveList', 'whitePlayerID', 'blackPlayerID', 
                         'whitePlayerSoftwareID', 'blackPlayerSoftwareID', 
                         'whoseTurnIsIt', 'startTime', 'lastMove');
            $condition = "WHERE `id` = ".CURRENT_GAME_ID;
            $query = "SELECT `moveList`, `whitePlayerID`, `blackPlayerID`, 
                      `whitePlayerSoftwareID`, `blackPlayerSoftwareID`, 
                      `whoseTurnIsIt`, `startTime`, `lastMove` 
                      FROM `$table` $condition";
            $result = selectFromDatabase($query, $row, $table, $condition);
            $moves = $result['moves'];
            $whitePlayerID = $result['whitePlayerID'];
            $blackPlayerID = $result['blackPlayerID'];
            $whitePlayerSoftwareID = $result['whitePlayerSoftwareID'];
            $blackPlayerSoftwareID = $result['blackPlayerSoftwareID'];
            $startTime = $result['startTime'];
            $endTime   = $result['endTime'];

            deleteFromDatabase($table, $id);

            $table = "chess_pastGames";
            $keyValue   = array();
            $keyValue['moveList'] = $moves;
            $keyValue['whitePlayerID'] = $whitePlayerID;
            $keyValue['blackPlayerID'] = $blackPlayerID;
            $keyValue['whitePlayerSoftwareID'] = $whitePlayerSoftwareID;
            $keyValue['blackPlayerSoftwareID'] = $blackPlayerSoftwareID;
            $keyValue['outcome']   = $outcome;
            $keyValue['startTime'] = $startTime;
            $keyValue['endTime']   = $endTime;

            $query = "INSERT INTO `$table` ";
            $query.= "(`moveList` ,`whitePlayerID` ,`blackPlayerID` ,";
            $query.= "`whitePlayerSoftwareID` ,`blackPlayerSoftwareID` , ";
            $query.= "`outcome` ,`startTime` ,`endTime`) ";
            $query.= "VALUES ('$moves',  '$whitePlayerID',  ";
            $query.= "'$blackPlayerID',  '$whitePlayerSoftwareID',  ";
            $query.= "'$blackPlayerSoftwareID',  '$outcome',  ";
            $query.= "'$startTime',  '$endTime');";
            insertIntoDatabase($query,$keyValue, $table);
            exit("Game finished.");
        }
        # Threefold repetition: http://en.wikipedia.org/wiki/Threefold_repetition
        # Stalemate: http://en.wikipedia.org/wiki/Stalemate
        # 50-move rule: http://en.wikipedia.org/wiki/Fifty-move_rule
}

if(isset($_GET['claim50MoveRule'])){
    if($noCaptureAndPawnMoves >= 100){
        $table = "chess_currentGames";
        $row = array('moveList', 'whitePlayerID', 'blackPlayerID', 
                     'whitePlayerSoftwareID', 'blackPlayerSoftwareID', 
                     'whoseTurnIsIt', 'startTime', 'lastMove');
        $condition = "WHERE `id` = ".CURRENT_GAME_ID;
        $query = "SELECT `moveList`, `whitePlayerID`, `blackPlayerID`, 
                  `whitePlayerSoftwareID`, `blackPlayerSoftwareID`, 
                  `whoseTurnIsIt`, `startTime`, `lastMove` 
                  FROM `$table` $condition";
        $result = selectFromDatabase($query, $row, $table, $condition);
        $moves  = $result['moves'];
        $whitePlayerID = $result['whitePlayerID'];
        $blackPlayerID = $result['blackPlayerID'];
        $whitePlayerSoftwareID = $result['whitePlayerSoftwareID'];
        $blackPlayerSoftwareID = $result['blackPlayerSoftwareID'];
        $startTime = $result['startTime'];
        $endTime   = $result['endTime'];

        deleteFromDatabase($table, $id);

        $table = "chess_pastGames";
        $keyValue   = array();
        $keyValue['moveList'] = $moves;
        $keyValue['whitePlayerID'] = $whitePlayerID;
        $keyValue['blackPlayerID'] = $blackPlayerID;
        $keyValue['whitePlayerSoftwareID'] = $whitePlayerSoftwareID;
        $keyValue['blackPlayerSoftwareID'] = $blackPlayerSoftwareID;
        $keyValue['outcome']   = $outcome;
        $keyValue['startTime'] = $startTime;
        $keyValue['endTime']   = $endTime;

        $query = "INSERT INTO `$table` ";
        $query.= "(`moveList` ,`whitePlayerID` ,`blackPlayerID` ,";
        $query.= "`whitePlayerSoftwareID` ,`blackPlayerSoftwareID` , ";
        $query.= "`outcome` ,`startTime` ,`endTime`) ";
        $query.= "VALUES ('$moves',  '$whitePlayerID',  ";
        $query.= "'$blackPlayerID',  '$whitePlayerSoftwareID',  ";
        $query.= "'$blackPlayerSoftwareID',  '2',  ";
        $query.= "'$startTime',  '$endTime');";
        insertIntoDatabase($query,$keyValue, $table);
        exit("Game finished. Draw. You claimed draw by the fifty-move rule.");
    } else {
        exit("ERROR: The last $noCaptureAndPawnMoves were no capture or pawn ".
                    "moves. At least 100 have to be made before you can claim ".
                    "draw by fifty-move rule.");
    }
}

$table = "chess_currentGames";
$row   = array("currentBoard","whoseTurnIsIt", "whitePlayerID", 
               "blackPlayerID");
$cond  = "WHERE (`whitePlayerID` = ".USER_ID." OR `blackPlayerID` = ";
$cond .= USER_ID.") AND `id` = ".CURRENT_GAME_ID;
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

?>
