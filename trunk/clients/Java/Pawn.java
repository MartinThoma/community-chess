/** The Pawn class represents the pawn chess piece. */

public class Pawn extends ChessPiece {
    /** An array of the standard moves of a ChessPiece. */
    int[][] normalMoves = {{0, 1}, {0, -1}};

    /** Constructor.
     * @param x the x-coordinate of the new piece on the chess board
     * @param y the y-coordinate of the new piece on the chess board
     **/
    public Pawn(final int x, final int y) {
        super(x, y);
        this.name = "Pawn";
        this.position[0] = x;
        this.position[1] = y;
    }

    /** Move the chess piece. */
    public final void move() {
        int x = this.position[0];
        int y = this.position[1];
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

    //ArrayList<Integer> normalMove = new ArrayList<Integer>();
    //normalMove.add("1");

    //ArrayList arrayList = new ArrayList();
    //arrayList<Vector>.add(v);

    //normalMove  = arrayList;
    //ArrayList<Vector> captureMove = new ArrayList<Vector>();
    //String color;
}
