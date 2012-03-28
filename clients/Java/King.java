/**
 * The {@code King} class represents the king chess piece.
 * @author Martin Thoma
 */
public class King extends ChessPiece {
    /** An array of the standard moves of a ChessPiece. */
    private int[][] normalMove;

    /**
     * The constructor for a rook.
     * @param x the x-coordinate of the new piece on the chess board
     * @param y the y-coordinate of the new piece on the chess board
     */
    public King(final int x, final int y) {
        super(x, y, "King");
        int[][] normalMoveTmp = {{-1, 1}, {0, 1}, {1, 1}, {-1, 0},
                {1, 0}, {-1, -1}, {0, -1}, {1, -1}};
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
            // check if the player gets in check.
            toX =  x + move[0];
            toY =  y + move[1];
            ChessClient.getWebSite("?gameID=1&move="
                + x + y
                + toX
                + toY);
        }
    }

}
