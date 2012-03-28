import java.util.Arrays;

/**
 * The {@code Board} class stores the current chess board.
 *
 * @author Martin Thoma
 */

public class Board {

    /** A chess board has 64 fields. */
    public static final int AREA = 64;

    /** Board-width. */
    public static final int WIDTH = 8;

    /** Diagonal directions: top-left, top-right, bottom-left, bottom-right. */
    public static final int DIAGONAL_DIRECTIONS = 4;

    /** straight directions: top, bottom, left, right. */
    public static final int STRAIGHT_DIRECTIONS = 4;

    /** The chess pieces. */
    private final ChessPiece[] board;

    /**
     * Constructor.
     */
    public Board() {
        this.board = new ChessPiece[AREA];
    }

    /**
     * Get the current games board and store it in this.board.
     */
    public final void setBoard() {
        String currentBoard = ChessClient.getBoard();
        int x, y;
        for (int i = 0; i < AREA; i++) {
            x = i % WIDTH;
            y = (i - x) / WIDTH;
            if (Character.toLowerCase(currentBoard.charAt(i)) == 'p') {
                boolean isWhite = Character.isUpperCase(currentBoard.charAt(i));
                board[i] = new Pawn(x, y, isWhite);
            } else if (Character.toLowerCase(currentBoard.charAt(i)) == 'r') {
                board[i] = new Rook(x, y);
            } else if (Character.toLowerCase(currentBoard.charAt(i)) == 'k') {
                board[i] = new King(x, y);
            } else if (Character.toLowerCase(currentBoard.charAt(i)) == 'n') {
                board[i] = new Knight(x, y);
            } else if (currentBoard.charAt(i) == '0') {
                board[i] = new EmptyPiece(x, y);
            } else {
                System.out.println("Not implemented yet: "
                        + currentBoard.charAt(i));
                board[i] = new EmptyPiece(x, y);
            }
            // System.out.println(currentBoard.charAt(i));
        }
    }

    /**
     * Get a chess piece.
     * @param x The x-coodinate in a Cartesian Coordinate System. [0..7]
     * @param y The y-coodinate in a Cartesian Coordinate System. [0..7]
     * @return The chess piece on (x|y)
     */
    public final ChessPiece get(final int x, final int y) {
        return board[WIDTH * y + x];
    }

    /**
     * Get a chess piece.
     * @param pos The position of the piece [0..63]
     * @return The chess piece on pos
     */
    public final ChessPiece get(final int pos) {
        return board[pos];
    }

    /**
     * Display the Chess board in a nice way.
     */
    public final void printBoard() {
        int i = 0;
        for (ChessPiece piece : board) {
            System.out.print(piece);
            i++;
            if (i == WIDTH) {
                System.out.println("");
                i = 0;
            }
        }
    }

    /* (non-Javadoc)
     * @see java.lang.Object#toString()
     */
    @Override
    public final String toString() {
        return "Board [board=" + Arrays.toString(board) + "]";
    }
}
