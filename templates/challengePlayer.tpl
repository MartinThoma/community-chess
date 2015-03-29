<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8" />
  <meta name="description" content="A page where you can challenge another player." />
  <title>Challenge another player - Community Chess</title> 
  <link rel="stylesheet" type="text/css" href="styling/default.css" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <link type="text/css" href="external/jquery-toastmessage/css/jquery.toastmessage.css" rel="stylesheet">
  <script type="text/javascript" src="external/jquery-1.6.4.min.js"></script>
  <script type="text/javascript" src="external/jquery-toastmessage/jquery.toastmessage-min.js"></script>
  <script type="text/javascript" src="external/jquery-toastmessage/toast-custom.js"></script>
</head>
<body>
    {{navigation}}
    <div>
        <p style="color:#a67000;font-size:38px;margin:0;padding:0;margin-left:180px">Community Chess</p>
    </div>
    <div id="content">

    {if:isset(alreadyChallengedPlayer)}
        <script>showStickyErrorToast("You've already challenged the player {alreadyChallengedPlayer}.<br/>The current game has the id {gameID}.");</script>
        <noscript>
            <p>You've already challenged the player {alreadyChallengedPlayer}.</p>
            <p>The current game has the id {gameID}.</p>
        </noscript>
    {end}

    {if:isset(startedGameUserID)}
        <script>showStickySuccessToast("You have started a game against the player with the ID {startedGameUserID}.<br/>The Username of this Player is {startedGamePlayerUsername}.<br/>The new gameID is {gameID}.")</script>
         <noscript>
            <p>You have started a game against the player with the ID {startedGameUserID}.</p>
            <p>The Username of this Player is {startedGamePlayerUsername}.</p>
            <p>The new gameID is {gameID}.</p>
        </noscript>
    {end}

    {if:isset(incorrectID)}
        <script>showStickyErrorToast("You have selected an incorrect ID.");</script>
        <noscript><p>You have selected an incorrect ID.</p></noscript>
    {end}

    {if:isset(possibleOpponents) and count(possibleOpponents) > 0}
        <p>You can challenge these players:
        <ul>
        {foreach:possibleOpponents,i,playerArray}
            <li>challenge <a href="challengePlayer.php?user_id={playerArray['user_id']}">{playerArray['username']}</a></li>
        {end}
        </ul>
    {else}
        <p>No opponents available.</p>
    {end}
    </div>
</body>
</html>
