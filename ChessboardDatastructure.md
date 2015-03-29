# Requirements #
The chess board representation is used to give the players the current board and to check if the move they want to do is valid. These two operations have to be fast.

# First idea #
The chessboard is represented in the database as a 64-character string. Each character represents one field.

The starting field is represented by this string:
RNBQKBNRPPPPPPPP00000000000000000000000000000000pppppppprnbqkbnr

## Field numbering ##
The first character is the field on the left from the white players view (field 11 in ICCF numeric notation). The second character represents field 21 in  ICCF notation. This goes from left to right and away from the white players first line.

## Chess pieces ##
  * 0 means no chess piece is on this field
  * See ChessPieces for the other names

# Links #
  * [Chess program board representations](http://www.cis.uab.edu/hyatt/boardrep.html)