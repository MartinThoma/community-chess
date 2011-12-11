import java.io.BufferedInputStream;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;

/** The ChessClient class should handle the interaction with the server. It also
  * stores the basic game information.
  */

public class ChessClient {
    /** A chess board has 64 fields. */
    private static final int BOARD_SIZE = 64;
    /** Board-width. */
    public static final int BOARD_WIDTH = 8;
    /** The url the the server. */
    private static final String BASE_URL =
                    "http://community-chess.com/xhrframework.php";
    /** The username. */
    private static final String USER_NAME = "abc";
    /** The password of the username. */
    private static final String USER_PASSWORD = "abc";
    /** The cookie. This is a PHPSESSID. */
    private static String myCookie = "";
    /** The gameID of the game you would like to play. */
    private static String gameID = "1";
    /** The chess board. */
    private static ChessPiece[] board = new ChessPiece[BOARD_SIZE];

    /**
     * Utility classes should not have a public or default constructor.
     */
    protected ChessClient() {
        // prevents calls from subclass
        throw new UnsupportedOperationException();
    }

    /**
     * Reads a web page into a StringBuilder object.
     * @param  parametersURL The URL you would like to take a look at.
     * @return The Websites content as a String.
     */
    public static String getWebSite(final String parametersURL) {
        String strReturn = "";

        try {
            URL url = new URL(BASE_URL + parametersURL);
            URLConnection urlc = url.openConnection();
            urlc.setRequestProperty("Cookie", myCookie);
            BufferedInputStream buffer = new BufferedInputStream(
                                            urlc.getInputStream()
                                            );
            StringBuilder builder = new StringBuilder();
            int byteRead;
            while ((byteRead = buffer.read()) != -1) {
                builder.append((char) byteRead);
            }

            buffer.close();
            strReturn = builder.toString();
        } catch (MalformedURLException ex) {
            ex.printStackTrace();
        } catch (IOException ex) {
            // ex.printStackTrace();
            System.out.println("You've got an IOException. Do you have an "
                            + "internet connection?");
            System.exit(0);
        } catch (Throwable t) {
            System.out.println("Woooah. I don't know what you've made! Please "
                            + "try to reproduce this error and report it to "
                            + "code.google.com/p/community-chess !");
            System.out.println(t.toString());
            System.exit(0);
        }

        return strReturn;
    }

    /* Getter *****************************************************************/
    /** Get the current games board.
        @return The String of the current board. */
    public static String getBoard() {
        return getWebSite("?action=getBoard&gameID=" + gameID);
    }

    /** Get all IDs of the current games.
        @return String. */
    public static String getCurrentGamesIdList() {
        return getWebSite("?action=listCurrentGames");
    }

    /** Get all IDs of the past games.
      * @return String.
      */
    public static String getPastGamesIdList() {
        return getWebSite("?action=listPastGames");
    }

    /** Display the Chessboard. */
    public static void printBoard() {
        for (ChessPiece piece : board) {
            System.out.println(piece.name);
        }
    }

    /* Setter *****************************************************************/
    /** Get the current games board and store it in this.board. */
    public static void setBoard() {
        String currentBoard = getBoard();
        int x, y;
        for (int i = 0; i < BOARD_SIZE; i++) {
            x = i % BOARD_WIDTH;
            y = (i - x) / BOARD_WIDTH;
            if (currentBoard.charAt(i) == 'P')  {
                board[i] = new Pawn(x, y);
                //board[i].name = "abc";
            } else {
                System.out.println("Not implemented yet: "
                     + currentBoard.charAt(i));
                board[i] = new EmptyPiece(x, y);
            }
            //System.out.println(currentBoard.charAt(i));
        }
    }
    /* Actions ****************************************************************/
    /** Login. Save the PHPSESSID to myCookie.
      * @return Was the login-process sucessful?
      */
    public static boolean login() {
        String returnVal = getWebSite("?action=login&username="
                                        + USER_NAME
                                        + "&password="
                                        + USER_PASSWORD);
        if (returnVal == "ERROR:You are not logged in.") {
            return false;
        } else {
            myCookie = "PHPSESSID=" + returnVal;
            return true;
        }
    }

    /** Challenge a player.
      * @param playerID The ID of the player you want to challenge.
      */
    public static void challengePlayer(final String playerID) {
        getWebSite("?action=challengeUser&userID=" + playerID);
    }

    /** Submit a move to the current game.
      * @param move The move, specified in
      *            http://code.google.com/p/community-chess/wiki/NotationOfMoves
      */
    public static void submitMove(final String move) {
        getWebSite("?gameID=" + gameID + "&move=" + move);
    }

    /** Try to calculate the next move. */
    public static void makeMove() {
        for (ChessPiece piece : board) {
            if (piece.getName() == "Pawn") {
                piece.move();
            }
        }
    }

    /* Main *******************************************************************/
    /** The main method.
     * @param args Some String arguments. Isn't used at the moment.
     */
    public static void main(final String[] args) {
        System.out.println("Starting Java client.");

        if (login()) {
            System.out.println("You are logged in.");

            setBoard();
            System.out.println("Board was set");

            makeMove();
            System.out.println("I moved.");

            //System.out.println(getCurrentGamesIdList());
            //System.out.println(getPastGamesIdList());
            //challengePlayer("1");
            submitMove("1214");
            //System.out.println(getBoard());
            //printBoard();
        }
    }
}
