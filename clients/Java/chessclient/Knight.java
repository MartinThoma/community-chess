package chessclient;
/**
 * The {@code Knight} class represents the knight chess piece.
 * @author Martin Thoma
 */
public class Knight extends ChessPiece {

    /** The long part of a knights move. */
    private static final int A = 2;

    /** The short part of a knights move. */
    private static final int B = 1;

    /**
     * The constructor for a rook.
     * @param x the x-coordinate of the new piece on the chess board
     * @param y the y-coordinate of the new piece on the chess board
     * @param isWhite is the piece white
     */
    public Knight(final int x, final int y, final boolean isWhite) {
        super(x, y, "Knight", isWhite);

        int[][] normalMoveTmp = {{A, B}, {-A, B}, {A, -B}, {-A, -B},
                                 {B, A}, {-B, A}, {B, -A}, {-B, -A}};

        setNormalMove(normalMoveTmp);
    }
}
