/**
 *  The class {@code ChessPiece} is the superclass for all chess pieces and
 *  the empty field.
 *  @author Martin Thoma
 */

public abstract class ChessPiece {

    /** The position is represented as two Integers. */
    private int[] position = new int[2];

    /** The color is either 'black' or 'white'. */
    private String color;

    /** Normal moves. Each move is an array of two Integers. */
    private int[][] normalMove;

    /** capture moves. Like normal moves. */
    private int[][] captureMove;

    /** The name is something like 'Pawn' or 'Empty'. */
    private String name;

    /** Constructor.
     * @param x the x-coordinate of the new piece on the chess board
     * @param y the y-coordinate of the new piece on the chess board
     * @param pieceName the name of the type of the chess piece
     **/
    public ChessPiece(final int x, final int y, final String pieceName) {
        this.name = pieceName;
        this.position[0] = x;
        this.position[1] = y;
    }

    /**
      * A string representation of the object.
      * @return A String which represents the object
      */
    @Override
    public final String toString() {
        return "ChessPiece - " + color + " - " + name;
    }

    /**
     * Get the position of the current chess piece.
     * @return the position as an int[2] array.
     */
    public final int[] getPosition() {
        return position;
    }

    /**
     * Get all normal moves of a chess piece.
     * @return the normal moves
     */
    public final int[][] getNormalMoves() {
        return normalMove;
    }

    /**
     * Get all capture moves of a chess piece.
     * @return the capture moves
     */
    public final int[][] getCaptureMoves() {
        return captureMove;
    }

    /**
     * Get the Name of the Chess Piece, e.g. Pawn or King.
      * @return The name of the Object.
      */
    public final String getName() {
        return this.name;
    }

    /** Make any move which is possible, no matter which one. */
    public abstract void move();
}
