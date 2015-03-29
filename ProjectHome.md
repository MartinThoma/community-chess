## What is Community Chess? ##
Community Chess is a Server-Side software for chess games. It provides an API for developers to submit chess moves and play chess games. The software checks if the moves are valid and stores all game relevant information. Developers can program their artificial intelligences for chess games and compete.

The software has only very few requirements:
  * the webserver must be able to run PHP
  * the webserver must have MySQL

## What does the server store? ##
  * a list of all chess players with IDs and login credentials
  * a list of all running / past chess games
    * the current board of this game
    * who is white/black
    * whose turn is it
    * chess software id
    * end of the game (still running / white won / black won / draw)
    * All moves are stored in a [kind of ICCF notation](NotationOfMoves.md)
  * ranking
  * tournaments
    * number of turnament
    * running / finished
    * game-ID
  * chess software
    * id
    * name of the software
    * version
    * language used (Python, Ruby, PHP, C, ...)
    * programmer
    * rating of the strength of the software according to BT2450

## How does it work? ##
The players can call some URLs which give them the necessary information. All lists use :: as a seperator:
  * **xhrframework.php?action=getPlayerIDs**: a list of all players you can challenge
  * **xhrframework.php?action=challengeUser&user\_id=1**: play a game against the player with the ID 12. You are white. The Game-ID is returned.
  * **xhrframework.php?action=listCurrentGames**: a list of the IDs of all current games
  * **xhrframework.php?action=listPastGames**: a list of the IDs of all past games
  * **xhrframework.php?action=login&username=abc&password=abc**: login
  * In a game:
    * **xhrframework.php?gameID=XYZ&action=whoAmI**: returns either 'white', 'black' or 'None'
    * **xhrframework.php?gameID=XYZ&action=whoseTurnIsIt**: returns either 'white' or 'black'
    * **xhrframework.php?gameID=XYZ&move=1234**: the players submit their moves via GET. See NotationOfMoves

## Open questions ##
  * how many moves may a chess game take at maximum?
    * if both players try to avoid the 50-move rule or threfold repetition, but take it when it's possible, the longest game takes 5899 moves (Bonsdorff u.a.: Schach und Zahl. Unterhaltsame Schachmathematik. S. 11–13.)
    * Israel, 1980, Spepak vs. Mashian: 193 moves
    * 1971, Thomas Ristoja vs. Jan-Michael Nykopp: 300 moves
  * should predictions be allowed? That means, should a player be able to submit his next moves, dependent of the next move of the opponent?

## What is not part of this project? ##
This project is not about creating animations like in Battle Chess. The first goal is to give programmers an API for developing their chess programs and competing them. The GUI was developed to be able to test the software.

## Timeline ##
  * 2011-07-21: Started project on Google Code
  * 2011-07-22: First draft for the database layout
  * 2011-07-29: All rules are implemented
  * 2011-09-25: Started phpBB3 integration
  * 2011-10-04: Started tournament integration
  * 2011-10-14:
    * integration of ranking finished
    * changed goal of the project from "easy to host / integrate" to "a own challenge site"
    * bought http://community-chess.com

