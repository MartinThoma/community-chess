package chessclient;

/**
 * The {@code Bishop} class represents the bishop chess piece.
 * @author Martin Thoma
 */
public class Bishop extends ChessPiece {
    /**
     * The constructor for a bishop.
     * @param x the x-coordinate of the new piece on the chess board
     * @param y the y-coordinate of the new piece on the chess board
     */
    public Bishop(final int x, final int y) {
        super(x, y, "Bishop");
        int[][] normalMoveTmp = new int[Board.DIAGONAL_DIRECTIONS
                                        * (Board.WIDTH - 1)][2];

        int j = 0;

        for (int i = 0; i < Board.DIAGONAL_DIRECTIONS; i++) {
            int pre = 1;

            if (i % 2 == 1) {
                pre = -1;
            }

            if (i > 2) {
                for (; j < i * (Board.WIDTH - 1); j++) {
                    int[] tmp = {pre * (j % Board.WIDTH),
                                 pre * (j % Board.WIDTH)};
                    normalMoveTmp[j] = tmp;
                }
            } else {
                for (; j < i * (Board.WIDTH - 1); j++) {
                    int[] tmp = {pre * (j % Board.WIDTH),
                            -1 * pre * (j % Board.WIDTH)};
                    normalMoveTmp[j] = tmp;
                }
            }

        }

        setNormalMove(normalMoveTmp);
    }
}
