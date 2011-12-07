<?php
/**
 * The PGN Converter should convert the PGN notation to my notation. Doesn't
 * work at the moment.
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

$subjects     = "e4 c5
Nf3 d6
Bb5+ Bd7
Bxd7+ Qxd7
c4 Nc6
Nc3 Nf6
0-0 g6
d4 cxd4
Nxd4 Bg7
Nde2 Qe6
Nd5 Qxe4
Nc7+ Kd7
Nxa8 Qxc4
Nb6+ axb6
Nc3 Ra8
a4 Ne4
Nxe4 Qxe4
Qb3 f5
Bg5 Qb4
Qf7 Be5
h3 Rxa4
Rxa4 Qxa4
Qxh7 Bxb2
Qxg6 Qe4
Qf7 Bd4
Qb3 f4
Qf7 Be5
h4 b5
h5 Qc4
Qf5+ Qe6
Qxe6+ Kxe6
g3 fxg3
fxg3 b4
Bf4 Bd4+
Kh1 b3
g4 Kd5
g5 e6
h6 Ne7
Rd1 e5
Be3 Kc4
Bxd4 exd4
Kg2 b2
Kf3 Kc3
h7 Ng6
Ke4 Kc2
Rh1 d3
Kf5 b1=Q
Rxb1 Kxb1
Kxg6 d2
h8=Q d1=Q
Qh7 b5
Kf6+ Kb2
Qh2+ Ka1
Qf4 b4
Qxb4 Qf3+
Kg7 d5
Qd4+ Kb1
g6 Qe4
Qg1+ Kb2
Qf2+ Kc1
Kf6 d4
g7 1-0";
$subjects     = explode("\n", $subjects);
$pattern      = "/^";
$pattern     .= "(";
    $pattern .= "(";                  // Match whole line
    $pattern .= "([KQRBN]?)";         // which figur is moved
                                     // - if it'ss empty, it's a pawn
    $pattern .= "([a-h])?";           // if a pawn makes a capture move: where
                                     // did he come from?
    $pattern .= "(x?)";               // x means capture
    $pattern .= "([a-h]{1}[1-8]{1})"; // to-field
    $pattern .= "([+]{0,2})";         // + means this move sets the opponent check
                                     // ++means this move sets the opponent 
                                     // checkmate
    $pattern .= ")";
$pattern     .= "|";
    $pattern .= "0?(-0){0,2}";        // Castling: 0-0 is kingside and 
                                     //           0-0-0 is queenside
$pattern .= ")";
$pattern .= "$/";
foreach ($subjects as $subject) {
    $array = explode(" ", $subject);
    preg_match($pattern, $array[0], $matches);
    echo $array[0];
    print_r($matches);
    preg_match($pattern, $array[1], $matches);
    echo $array[1];
    print_r($matches);
}
?>
