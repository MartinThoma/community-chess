/**
 * This is an empty field on a chess board.
 * @author moose
 *
 */
public class EmptyPiece extends ChessPiece {

    /** Constructor.
     * @param x the x-coordinate of the new piece on the chess board
     * @param y the y-coordinate of the new piece on the chess board
     **/
    public EmptyPiece(final int x, final int y) {
        super(x, y, "Empty");
    }

    /** an empty field can't move. */
    @Override
    public final void move() { }
}
