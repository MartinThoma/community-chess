package chessclient;

import java.util.Arrays;

/**
 * The {@code ChessPiece} class is the superclass for all chess pieces and the
 * empty field.
 *
 * @author Martin Thoma
 */

public abstract class ChessPiece {

    /** The position is represented as two Integers. */
    private final int[] position = new int[2];

    /** The color is either 'black' or 'white'. */
    private String color;

    /** Is this piece white? */
    private final boolean isWhite;

    /** Normal moves. Each move is an array of two Integers. */
    private int[][] normalMove;

    /** capture moves. Like normal moves. */
    private int[][] captureMove;

    /** The name is something like 'Pawn' or 'Empty'. */
    private final String name;

    /**
     * Constructor.
     *
     * @param x
     *            the x-coordinate of the new piece on the chess board
     * @param y
     *            the y-coordinate of the new piece on the chess board
     * @param pieceName
     *            the name of the type of the chess piece
     * @param isColorWhite
     *            is the chess piece white
     **/
    public ChessPiece(final int x, final int y, final String pieceName,
            final boolean isColorWhite) {
        this.name = pieceName;
        this.position[0] = x;
        this.position[1] = y;
        this.isWhite = isColorWhite;
    }

    /**
     * A string representation of the object.
     *
     * @return A String which represents the object
     */
    @Override
    public final String toString() {
        return "ChessPiece(" + color + " - " + name + " (" + position[0] + "|"
                + position[1] + "))";
    }

    /**
     * Get the position of the current chess piece.
     *
     * @return the position as an int[2] array.
     */
    public final int[] getPosition() {
        return position;
    }

    /**
     * Get all normal moves of a chess piece.
     *
     * @return the normal moves
     */
    public final int[][] getNormalMoves() {
        return normalMove;
    }

    /**
     * Get all capture moves of a chess piece.
     *
     * @return the capture moves
     */
    public final int[][] getCaptureMoves() {
        return captureMove;
    }

    /**
     * Get the Name of the Chess Piece, e.g. Pawn or King.
     *
     * @return The name of the Object.
     */
    public final String getName() {
        return this.name;
    }

    /**
     * @return the color
     */
    protected final String getColor() {
        return color;
    }

    /**
     * @param c
     *            the color to set
     */
    protected final void setColor(final String c) {
        this.color = c;
    }

    /**
     * @return the normalMove
     */
    protected final int[][] getNormalMove() {
        return normalMove;
    }

    /**
     * @param moves
     *            the normalMove to set
     */
    protected final void setNormalMove(final int[][] moves) {
        this.normalMove = moves;
    }

    /**
     * @return the captureMove
     */
    protected final int[][] getCaptureMove() {
        return captureMove;
    }

    /**
     * @param moves
     *            the captureMove to set
     */
    protected final void setCaptureMove(final int[][] moves) {
        this.captureMove = moves;
    }

    /**
     * Is this chess piece white?
     *
     * @return {@code true} if the piece is white, {@code false} otherwise.
     */
    protected final boolean isWhite() {
        return isWhite;
    }

    /*
     * (non-Javadoc)
     *
     * @see java.lang.Object#hashCode()
     */
    @Override
    public final int hashCode() {
        final int prime = 31;
        int result = 1;
        result = prime * result + Arrays.hashCode(captureMove);
        result = prime * result + ((color == null) ? 0 : color.hashCode());
        result = prime * result + (isWhite ? 1231 : 1237);
        result = prime * result + ((name == null) ? 0 : name.hashCode());
        result = prime * result + Arrays.hashCode(normalMove);
        result = prime * result + Arrays.hashCode(position);
        return result;
    }

    /*
     * (non-Javadoc)
     *
     * @see java.lang.Object#equals(java.lang.Object)
     */
    @Override
    public final boolean equals(final Object obj) {
        if (this == obj) {
            return true;
        }

        if (obj == null) {
            return false;
        }

        if (getClass() != obj.getClass()) {
            return false;
        }
        ChessPiece other = (ChessPiece) obj;

        if (!Arrays.equals(captureMove, other.captureMove)) {
            return false;
        }

        if (color == null) {
            if (other.color != null) {
                return false;
            }
        } else if (!color.equals(other.color)) {
            return false;
        }

        if (isWhite != other.isWhite) {
            return false;
        }

        if (name == null) {
            if (other.name != null) {
                return false;
            }
        } else if (!name.equals(other.name)) {
            return false;
        }
        // if (!Arrays.equals(normalMove, other.normalMove))
        // return false;
        if (!Arrays.equals(position, other.position)) {
            return false;
        }

        return true;
    }

}
