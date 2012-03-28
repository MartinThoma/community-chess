/**
 * The {@code Knight} class represents the knight chess piece.
 * @author Martin Thoma
 */
public class Knight extends ChessPiece {
    /** An array of the standard moves of a ChessPiece. */
    private int[][] normalMove;

    /** The long part of a knights move. */
    private static final int A = 2;

    /** The short part of a knights move. */
    private static final int B = 1;

    /**
     * The constructor for a rook.
     * @param x the x-coordinate of the new piece on the chess board
     * @param y the y-coordinate of the new piece on the chess board
     */
    public Knight(final int x, final int y) {
        super(x, y, "Knight");

        int[][] normalMoveTmp = {{A, B}, {-A, B}, {A, -B}, {-A, -B},
                                 {B, A}, {-B, A}, {B, -A}, {-B, -A}};

        this.normalMove = normalMoveTmp;
    }

    /** Make any move which is possible, no matter which one. */
    @Override
    public final void move() {
        int[] position = this.getPosition();
        int x = position[0];
        int y = position[1];
        int toX;
        int toY;
        for (int[] move : normalMove) {
            toX =  x + move[0];
            toY =  y + move[1];
            ChessClient.getWebSite("?gameID=1&move="
                + x + y
                + toX
                + toY);
        }
    }

}
