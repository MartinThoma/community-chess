package chessclient;
import java.io.BufferedInputStream;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.util.Properties;

/**
 * The {@code ChessClient} class handles the interaction with the server.
 * It also stores the basic game information.
 *
 * @author Martin Thoma
 */

public class ChessClient {
    /** Load the configuration file. */
    private Properties config;

    /** The chess board. */
    private final Board board;

    /** The current player. */
    private Player currentPlayer;

    /**
     * The constructor.
     */
    public ChessClient() {
        board = new Board();

        System.out.println("Starting Java client.");
        System.out.println("Loading configuration file.");
        loadConfig();

        if (login()) {
            System.out.println("You are logged in.");

            board.setBoard(getBoard());
            System.out.println("Board was set.");

            if (makeMove()) {
                System.out.println("I moved.");
            } else {
                System.out.println("I couldn't move.");
            }

            // System.out.println(getCurrentGamesIdList());
            // System.out.println(getPastGamesIdList());
            // challengePlayer("1");
            submitMove("1214");
            // System.out.println(getBoard());
            // printBoard();
        }
    }


    /**
     * Load the configuration file.
     */
    private void loadConfig() {
        config = new Properties();
        try {
            config.load(new FileInputStream("config.properties"));
        } catch (IOException e) {
            System.err.println("Error reading config.");
        }

        // Write configuration file.
        try {
            config.store(new FileOutputStream("config.properties"), null);
        } catch (IOException e) {
            System.err.println("Error writing config.");
        }
    }

    /**
     * Reads a web page into a StringBuilder object.
     *
     * @param parametersURL
     *            The URL you would like to take a look at.
     * @return The Websites content as a String.
     */
    public final String getWebSite(final String parametersURL) {
        String strReturn = "";

        try {
            URL url = new URL(config.getProperty("BASE_URL") + parametersURL);
            URLConnection urlc = url.openConnection();
            urlc.setRequestProperty("Cookie", config.getProperty("myCookie"));
            BufferedInputStream buffer = new BufferedInputStream(urlc
                    .getInputStream());
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

        if (strReturn.startsWith("ERROR")) {
            System.err.println(strReturn);
            return null;
        }

        return strReturn;
    }

    /**
     * Get the current games board.
     *
     * @return The String of the current board.
     */
    public final String getBoard() {
        return getWebSite("?action=getBoard&gameID="
                + config.getProperty("gameID"));
    }

    /**
     * Get all IDs of the current games.
     *
     * @return String.
     */
    public final String getCurrentGamesIdList() {
        return getWebSite("?action=listCurrentGames");
    }

    /**
     * Get all IDs of the past games.
     *
     * @return String.
     */
    public final String getPastGamesIdList() {
        return getWebSite("?action=listPastGames");
    }

    /**
     * Login. Save the PHPSESSID to myCookie.
     *
     * @return Was the login-process successful?
     */
    public final boolean login() {
        String returnVal = getWebSite("?action=login&username="
                + config.getProperty("USER_NAME")
                + "&password="
                + config.getProperty("USER_PASSWORD"));
        if (returnVal == "ERROR:You are not logged in.") {
            return false;
        } else {
            config.setProperty("myCookie", returnVal);
            return true;
        }
    }

    /**
     * Challenge a player.
     *
     * @param playerID
     *            The ID of the player you want to challenge.
     */
    public final void challengePlayer(final String playerID) {
        getWebSite("?action=challengeUser&userID=" + playerID);
    }

    /**
     * Submit a move to the current game.
     *
     * @param move
     *            The move, specified in
     *            http://code.google.com/p/community-chess/wiki/NotationOfMoves
     */
    public final void submitMove(final String move) {
        getWebSite("?gameID="
                + config.getProperty("gameID")
                + "&move=" + move);
    }

    /**
     * Try to calculate the next move.
     * @return {@code true} if you could move, otherwise {@code false}
     */
    public final boolean makeMove() {
        for (int pos = 0; pos < Board.AREA; pos++) {
            ChessPiece piece = board.get(pos);
            if (piece == null) {
                continue;
            } else {
                piece.getNormalMoves();
                return true;
            }
        }
        return false;
    }


    /**
     * Getter for currentPlayer.
     * @return the currentPlayer
     */
    protected final Player getCurrentPlayer() {
        return currentPlayer;
    }
}
