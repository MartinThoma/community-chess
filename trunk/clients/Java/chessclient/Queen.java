package chessclient;
/**
 * The {@code Queen} class represents the queen chess piece.
 * @author Martin Thoma
 */
public class Queen extends ChessPiece {
    /**
     * The constructor for a queen.
     * @param x the x-coordinate of the new piece on the chess board
     * @param y the y-coordinate of the new piece on the chess board
     * @param isWhite is the piece white
     */
    public Queen(final int x, final int y, final boolean isWhite) {
        super(x, y, "Queen", isWhite);
        int[][] normalMoveTmp = new int[Board.DIAGONAL_DIRECTIONS
                                        * (Board.WIDTH - 1)
                                        + Board.STRAIGHT_DIRECTIONS
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

        for (int i = Board.DIAGONAL_DIRECTIONS;
                 i < 2 * Board.STRAIGHT_DIRECTIONS; i++) {
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

        setNormalMove(normalMoveTmp);
    }
}
