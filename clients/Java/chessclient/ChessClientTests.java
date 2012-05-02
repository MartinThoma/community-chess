/**
 *
 */
package chessclient;

import junit.framework.TestCase;

/**
 * @author Martin Thoma
 *
 */
public class ChessClientTests extends TestCase {

    /** A chess board. */
    private Board board;

    /**
     * Constructor of the test case.
     * @param name the name
     */
    public ChessClientTests(final String name) {
        super(name);
    }

    /*
     * (non-Javadoc)
     *
     * @see junit.framework.TestCase#setUp()
     */
    @Override
    protected final void setUp() throws Exception {
        board = new Board();
    }

    /*
     * (non-Javadoc)
     *
     * @see junit.framework.TestCase#tearDown()
     */
    @Override
    protected final void tearDown() throws Exception {
        super.tearDown();
    }

    /**
     * Test all functionalities of the board.
     */
    public final void testBoard() {
        board.setBoard("RNBQKBNRPPPPPPPP"
                + "00000000000000000000000000000000"
                + "pppppppprnbqkbnr");
        Bishop b1 = new Bishop(2, 0, true);
        Bishop b2 = new Bishop(2, 7, false);
        assertEquals("white Bishop at (2|0)", b1, board.get(2));
        assertEquals("black Bishop at (5|7)", b2, board.get(58));
    }

    /**
     * Test if the bishop can go all valid moves and
     * only valid moves.
     */
    public final void testBishop() {
        Bishop bishop = new Bishop(2, 3, true);
        board.setPiece(bishop, 2, 3);
    }
}
