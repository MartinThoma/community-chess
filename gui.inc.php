<?php
/**
 * all gui-only functions
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

/** This function manages how a single cell should look like
 * 
 * @param character $figure   the chess piece
 * @param int       $x        the x-coordinate in [1;8]
 * @param int       $y        the y-coordinate in [1;8]
 * @param boolean   $yourTurn Is it your turn?
 * @param int       $from     Position of a chess piece. False if none was selected
 *
 * @return string the new board
 */
function displayField($figure, $x, $y, $yourTurn, $from)
{
    if (strtoupper($figure)!=$figure) {
        // Discern black from white pieces for windows servers, as
        // FAT is case-insensitive.
        $figure .= "b";
    }
    
    /* x and y are in [1;8]*/
    if (($x)*10+($y) == $from) {
        $chessfieldColor = 'highlight';
    } else if (($x+($y-1)*8 + $y)%2==0) {
        $chessfieldColor = 'black';
    } else {
        $chessfieldColor = 'white';
    }


    $return = '<td class="'.$chessfieldColor.'Field">';
    if ($yourTurn) {
        if ($from) {
            $return .= '<a href="playChess.php?gameID='.CURRENT_GAME_ID;
            $return .= '&from='.$from.'&to='.$x.$y.'">';
        } else {
            $return .= '<a href="playChess.php?gameID='.CURRENT_GAME_ID;
            $return .= '&from='.$x.$y.'">';
        }
    }
    $return .= '<img src="figures/'.$figure.'.png" alt="'.$figure.'" border="0"/>';

    if ($yourTurn) {
        $return .= '</a>';
    }
    $return .= '</td>';
    return $return;
}
?>
