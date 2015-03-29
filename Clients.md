As this goal is to create a software that allows a server to run a chess application on which programmers can compete, I don't want to create a very good A.I. So the clients will only get basic functionality:

# Functionality of the client #
## Actions ##
  * **boolean login()**: Login
  * **void submitMove(1234)**: Submit a move (see NotationOfMoves)
  * **boolean challengePlayer(int id)**: Challenge the player with the ID id.

## Game information ##
  * **int[.md](.md) getCurrentGamesIdList()**: List of all current games.
  * **int[.md](.md) getPastGamesIdList()**: List of all past games.
  * **int[.md](.md) getChallengePlayerIds()**: List of player-IDs who can be challenged.
  * **getBoard()**: Return the current board.

## Chess logic ##
  * **boolean isPlayerCheck(int color)**: Is the player with the color 'color' check?
  * **String[.md](.md) getAllPossibleMovesPlayer(int color)**: Get all possible moves of a player.
  * **String[.md](.md) getAllPossibleMovesFigure(int x, int y)**: Get all possible moves of a figure at position (x|y).