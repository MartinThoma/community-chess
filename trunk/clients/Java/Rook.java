
/**
 * The {@code Rook} class represents the rook chess piece.
 * @author Martin Thoma
 */
public class Rook extends ChessPiece {
    /** An array of the standard moves of a ChessPiece. */
    private final int[][] normalMove;

    /**
     * The constructor for a rook.
     * @param x the x-coordinate of the new piece on the chess board
     * @param y the y-coordinate of the new piece on the chess board
     */
    public Rook(final int x, final int y) {
        super(x, y, "Rook");
        int[][] normalMoveTmp = new int[Board.STRAIGHT_DIRECTIONS
                                        * (Board.WIDTH - 1)][2];

        int j = 0;

        for (int i = 0; i < Board.STRAIGHT_DIRECTIONS; i++) {
            int pre = 1;

            if (i % 2 == 1) {
                pre = -1;
            }

            if (i > 2) {
                for (; j < i * (Board.WIDTH - 1); j++) {
                    int[] tmp = {0, pre * (j % Board.WIDTH)};
                    normalMoveTmp[j] = tmp;
                }
            } else {
                for (; j < i * (Board.WIDTH - 1); j++) {
                    int[] tmp = {pre * (j % Board.WIDTH), 0};
                    normalMoveTmp[j] = tmp;
                }
            }

        }

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
