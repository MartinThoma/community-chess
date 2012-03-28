
import java.util.Scanner;

import chessclient.ChessClient;


/**
 * The class {@code Shell} implements a simple shell to use the chess client.
 * @author Martin Thoma
 */
public final class Shell {

    /** The prompt of this shell. */
    private static final String PROMPT = "chess> ";

    /** Command to terminate the shell. */
    private static final String CMD_QUIT = "quit";

    /**
     * Private constructor. This is a Utility class that should not be
     * instantiated.
     */
    private Shell() {
    }

    /**
     * This realizes the shell.
     * @param args command line arguments - not used here!
     */
    public static void main(final String[] args) {
        boolean quit = false;
        ChessClient client = new ChessClient();
        client.login();

        while (!quit) {
            String input = askString(PROMPT);
            final String cmd;
            final String[] tokens;

            if (input == null) {
                /* This error is weird and might indicate that some testing
                 * program has a bug it means that the input was NOT
                 * terminated by \n or \r\n, but canceled see
                 * http://docs.oracle.com/javase/1.3/docs/api/java/io/
                 * BufferedReader.html#readLine()
                 */
                error("The input stream was cancelled.");
                tokens = "quit".split("\\s+");
                cmd = "quit";
            } else {
                tokens = input.trim().split("\\s+");
                cmd = tokens[0];
            }

            if (CMD_QUIT.equals(cmd)) {
                quit = true;
            } else {
                error("Unknown command: '" + cmd + "'");
            }
        }
    }

    /**
     * Prints an error message.
     *
     * @param msg error message to print
     */
    private static void error(final String msg) {
        println("Error! " + msg);
    }

    /**
     * Print the message.
     *
     * @param s the message
     */
    private static void println(final String s) {
        System.out.println(s);
    }

    /**
     * This allows you to ask for a string.
     * @param promt the "question".
     * @return the "answer"
     */
    private static String askString(final String promt) {
        Scanner in = new Scanner(System.in);
        String sResponse = null;
        do {
            System.out.println(promt);
            sResponse = in.nextLine();
        } while (sResponse.trim().length() == 0);
        return sResponse;
    }

}
