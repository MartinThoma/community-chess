package chessclient;
/**
 * The {@code King} class represents the king chess piece.
 * @author Martin Thoma
 */
public class King extends ChessPiece {
    /**
     * The constructor for a rook.
     * @param x the x-coordinate of the new piece on the chess board
     * @param y the y-coordinate of the new piece on the chess board
     */
    public King(final int x, final int y) {
        super(x, y, "King");
        int[][] normalMoveTmp = {{-1, 1}, {0, 1}, {1, 1}, {-1, 0},
                {1, 0}, {-1, -1}, {0, -1}, {1, -1}};
        setNormalMove(normalMoveTmp);
    }
}
