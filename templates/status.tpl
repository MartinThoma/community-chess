<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8" />
  <meta name="description" content="See all current and all past matches." />
  <title>Current and past matches - Community Chess</title> 
  <link rel="stylesheet" type="text/css" href="styling/default.css" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
</head>
<body>
    {{navigation}}
    <div>
        <p style="color:#a67000;font-size:38px;margin:0;padding:0;margin-left:180px">Community Chess</p>
    </div>
    <div id="content">

    {if:count(currentGames)>0}
        <p>Current game-IDs:</p>
        <ul>
        {foreach:currentGames,i,gameId}
	        <li>Play game with <a href="playChess.php?gameID={gameId['id']}">ID {gameId['id']}</a></li>
        {end}
        </ul>
    {else}
        <p>No current games.</p>
    {end}

    {if:count(pastGames)>0}
        <p>Past game-IDs:</p>
        <ul>
        {foreach:pastGames,i,gameId}
	        <li>{gameId['id']} </li>
        {end}
        </ul>
    {else}
        <p>No past games.</p>
    {end}
    </div>
</body>
</html>
