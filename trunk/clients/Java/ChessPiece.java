import java.util.ArrayList;

/**
 *  The ChessPiece class is the superclass for all chess pieces and
 *  the empty field.
 */
public class ChessPiece {
    /** The position is represented as two Integers. */
    private int[] position;
    /** The color is either 'black' or 'white'. */
    private String color;
    /** Normal moves. Each move is an array of two Integers. */
    protected ArrayList < int[] > normalMove  = new ArrayList < int[] > ();
    /** capture moves. Like normal moves. */
    protected ArrayList < int[] > captureMove = new ArrayList < int[] > ();
    /** The name is either 'Pawn' or 'Empty'. */
    protected String name;

    /** Constructor. */
    public ChessPiece() {
        this.name = "Empty";
    }

    /** Overriding toString.
      * @return A String which represents the object
      */
    public final String toString() {
        return "ChessPiece - " + color + " - " + name;
    }

    /** Getter and setter.
      * @return The name of the Object.
      */
    public String getName() {
        return this.name;
    }

    /** Move the chess piece. */
    public void move() {
        int x = this.position[0];
        int y = this.position[1];
        int toX, toY;
        for (int[] move : normalMove) {
            ChessClient.getWebSite("?gameID=1&move="
                + x + y
                + (x + move[0])
                + (y + move[1]));
        }
    }
}
