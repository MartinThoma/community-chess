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
 * @param character $figure the chess piece
 * @param int       $x      the x-coordinate in [1;8]
 * @param int       $y      the y-coordinate in [1;8]
 *
 * @return string the new board
 */
function displayField($figure, $x, $y){
    if(strtoupper($figure)!=$figure){
        // Discern black from white pieces for windows servers, as
        // FAT is case-insensitive.
        $figure .= "b";
    }
    
    /* x and y are in [1;8]*/
    if (($x+($y-1)*8 + $y)%2==0){
        $chessfieldColor = 'black';
    } else {
        $chessfieldColor = 'white';
    }
    $return  = '<td class="'.$chessfieldColor.'Field">';
    $return .= '<img src="figures/'.$figure.'.png" alt="'.$figure.'"/>';
    $return .= '</td>';
    return $return;
}
?>
