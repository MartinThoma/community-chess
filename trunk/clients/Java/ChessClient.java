import java.io.BufferedInputStream;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;

public class ChessClient {
    private static final String BASE_URL = "http://community-chess.com/xhrframework.php";
    private static final String USER_NAME = "abc";
    private static final String USER_PASSWORD = "abc";
    private static String my_cookie = "";
    private static String gameID = "1";
    private static ChessPiece[] board = new ChessPiece[64];

    /**
     * Reads a web page into a StringBuilder object
     */
    private static String getWebSite(String parametersURL) {
        String strReturn = "";
        
        try {
            URL url = new URL(BASE_URL + parametersURL);
            URLConnection urlc = url.openConnection();
            urlc.setRequestProperty("Cookie", my_cookie);
            BufferedInputStream buffer = new BufferedInputStream(urlc.getInputStream());
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
            ex.printStackTrace();
        }
        return strReturn;
    }

    /* Getter *****************************************************************/
    public static String getBoard() {
        return getWebSite("?action=getBoard&gameID=" + gameID);
    }

    public static String getCurrentGamesIdList() {
        return getWebSite("?action=listCurrentGames");
    }

    public static String getPastGamesIdList() {
        return getWebSite("?action=listPastGames");
    }
    public static void printBoard() {
        //System.out.println(board);
        
        for(ChessPiece piece: board) {
            System.out.println(piece.name);
        }
    }

    /* Setter *****************************************************************/
    public static void setBoard() {
        String currentBoard = getBoard();
        for (int i=0; i < 64; i++) {
            if ( currentBoard.charAt(i) == 'P')  {
                board[i] = new Pawn();
                //board[i].name = "abc";
            } else {
                board[i] = new ChessPiece();
            }
            //System.out.println(currentBoard.charAt(i));
        }
    }
    /* Actions ****************************************************************/
    public static boolean login() {
        String returnVal = getWebSite("?action=login&username=" + USER_NAME + "&password=" + USER_PASSWORD);
        if (returnVal == "ERROR:You are not logged in.") {
            return false;
        } else {
            my_cookie = "PHPSESSID=" + returnVal;
            return true;
        }
    }

    public static void challengePlayer(String playerID) {
        getWebSite("?action=challengeUser&userID=" + playerID);
    }

    public static void submitMove(String move) {
        getWebSite("?gameID="+gameID+"&move=" + move);
    }

    /* Main *******************************************************************/
    public static void main(String[] args) {
        if ( login() ) {
            setBoard();
            //System.out.println(getCurrentGamesIdList());
            //System.out.println(getPastGamesIdList());
            //challengePlayer("1");
            submitMove("1214");
            //System.out.println(getBoard());
            printBoard();
        }
    }
}
