import java.util.ArrayList;
import java.util.Vector;

public class ChessPiece {
    int[] position[];
    ArrayList<Vector> normalMove  = new ArrayList<Vector>();
    ArrayList<Vector> captureMove = new ArrayList<Vector>();
    String color;
    String name;

    public ChessPiece() {
        this.name = "Empty";
    }

    // Overriding toString 
    public String toString( ) {
        return "ChessPiece - " + color + " - " + name;
    }
}
