package chessclient;
/**
 * The {@code Pawn} class represents the pawn chess piece.
 * @author Martin Thoma
 */

public class Pawn extends ChessPiece {

    /**
     * The constructor for a pawn.
     * @param x the x-coordinate of the new piece on the chess board
     * @param y the y-coordinate of the new piece on the chess board
     * @param isWhite {@code true} if the current piece is white,
     *        otherwise {code false}
     **/
    public Pawn(final int x, final int y, final boolean isWhite) {
        super(x, y, "Pawn", isWhite);
        if (isWhite) {
            int[][] normalMoveTmp = {{0, 1}};
            setNormalMove(normalMoveTmp);
        } else {
            int[][] normalMoveTmp = {{0, -1}};
            setNormalMove(normalMoveTmp);
        }
    }
}
