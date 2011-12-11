/** The Pawn class represents the pawn chess piece. */

public class Pawn extends ChessPiece {
    /** An array of the standard moves of a ChessPiece. */
    private int[][] normalMove;

    /** Constructor.
     * @param x the x-coordinate of the new piece on the chess board
     * @param y the y-coordinate of the new piece on the chess board
     * @param isWhite if true, the current piece will be white. else black
     **/
    public Pawn(final int x, final int y, final boolean isWhite) {
        super(x, y, "Pawn");
        if (isWhite) {
            int[][] normalMoveTmp = {{0, 1}};
            this.normalMove = normalMoveTmp;
        } else {
            int[][] normalMoveTmp = {{0, -1}};
            this.normalMove = normalMoveTmp;
        }
    }

    /** Move the chess piece. */
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
