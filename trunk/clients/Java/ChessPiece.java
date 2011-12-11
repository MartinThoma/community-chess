import java.util.ArrayList;

/**
 *  The ChessPiece class is the superclass for all chess pieces and
 *  the empty field.
 */
public abstract class ChessPiece {
    /** The position is represented as two Integers. */
    protected int[] position = new int[2];
    /** The color is either 'black' or 'white'. */
    private String color;
    /** Normal moves. Each move is an array of two Integers. */
    protected ArrayList < int[] > normalMove  = new ArrayList < int[] >();
    /** capture moves. Like normal moves. */
    protected ArrayList < int[] > captureMove = new ArrayList < int[] >();
    /** The name is either 'Pawn' or 'Empty'. */
    protected String name;

    /** Constructor.
     * @param x the x-coordinate of the new piece on the chess board
     * @param y the y-coordinate of the new piece on the chess board
     **/
    public ChessPiece(final int x, final int y) {
        this.name = "Empty";
        this.position[0] = x;
        this.position[1] = y;
    }

    /** Overriding toString.
      * @return A String which represents the object
      */
    public final String toString() {
        return "ChessPiece - " + color + " - " + name;
    }

    /** Get the position of the current chess piece.
     * @return the positon as an int array.
     */
    public final int[] getPosition() {
        return position;
    }

    /** Get all capture moves of a chess piece.
     * @return the capture moves
     */
    public final ArrayList < int[] > getCaptureMoves() {
        return captureMove;
    }

    /** Get the Name of the Chess Piece, e.g. Pawn or King.
      * @return The name of the Object.
      */
    public final String getName() {
        return this.name;
    }

    /** Move the chess piece. */
    public abstract void move();
}
