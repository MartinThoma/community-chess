<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8" />
  <meta name="description" content="See all current and all past matches." />
  <title>Tournaments - Community Chess</title> 
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
    {if:isset(cantExitStartedAlready)}
        <script>showStickyErrorToast("You can't exit the tournament as it started already.");</script>
        <noscript>You can't exit the tournament as it started already.</noscript>
    {end}
    {if:isset(noTournamentName)}
        <script>showStickyErrorToast("You have to set a tournament name.");</script>
        <noscript>You have to set a tournament name.</noscript>
    {end}

    {if:isset(alreadyChallengedPlayer)}
        <script>showStickyErrorToast("You've already challenged the player {alreadyChallengedPlayer}. The current game has the id {alreadyChallengedGameID}.");</script>
        <noscript>You've already challenged the player {alreadyChallengedPlayer}. The current game has the id {alreadyChallengedGameID}.</noscript>
    {end}

    {if:isset(startedGameUserID)}
        <script>showStickyNoticeToast("You have started a game against the player with the ID {startedGameUserID}. The Username of this Player is {startedGamePlayerUsername}.");</script>
        <noscript>You have started a game against the player with the ID {startedGameUserID}. The Username of this Player is {startedGamePlayerUsername}.</noscript>
    {end}

    {if:isset(incorrectID)}
        <script>showStickyWarningToast("You have selected an incorrect ID.");</script>
        <noscript>You have selected an incorrect ID.</noscript>
    {end}

    {if:isset(joinedTournamentID)}
        <script>showStickyNoticeToast("You joined the tournament with the ID {joinedTournamentID}.");</script>
        <noscript>You joined the tournament with the ID {joinedTournamentID}.</noscript>
    {end}

    {if:isset(joinTournamentFailed)}
        <script>showStickyErrorToast("I couldn't add you to the tournament. Do you already participate?");</script>
        <noscript>I couldn't add you to the tournament. Do you already participate?</noscript>
    {end}

    {if:isset(detailsPlayers)}
        <h1>Details of tournament</h1>
        <table border="1">
        <tr>
            <th>user_id</th>
            <th>joinedDate</th>
            <th>games won / played</th>
            <th>PageRank</th>
            <th>challenge</th>
        </tr>
        {foreach:detailsPlayers,i,player}
            <tr>
                <td>{player['user_id']}</td>
                <td>{player['joinedDate']}</td>
                <td>{player['gamesWon']} / {player['gamesPlayed']}</td>
                <td>{player['pageRank']}</td>
                <td>
                {if:(player['user_id'] != USER_ID)}
                    {if:isset($this->data->tournamentDidntBeginn)}
                        Tournament didn't begin.
                    {else}
                        <a href="tournaments.php?challengeUserID={player['user_id']}&tournamentID={detailsTournamentID}">challenge</a>
                    {end}
                {else}
                    -
                {end}
                </td>
            </tr>
        {end}
        </table>
    {end}

    <h1>New tournament</h1>
    <form method="post" action="tournaments.php">
    <fieldset>
    <legend>Initiate new tournament</legend>

    <p>
    <label for="tournamentName">Name</label>
    <input type="text" id="tournamentName" name="tournamentName" required="required"/>
    </p>

    <p>
    <label for="description">Description</label>
    <input type="text" id="description" name="description" />
    </p>

    <p>
    <label for="password">password (leave blank for no password)</label>
    <input type="password" id="password" name="password" autocomplete="off"/>
    </p>

    <p>
    <label for="closingDate">closingDate</label>
    <input type="datetime" id="closingDate" name="closingDate" value="{closingDate}" min="{closingDateMin}" max="{closingDateMax}"/>
    </p>

    <p>
    <label for="finishedDate">finishedDate</label>
    <input type="datetime" id="finishedDate" name="finishedDate" value="{finishedDate}" min="{finishedDateMin}" max="{finishedDateMax}"/>
    </p>

    <input type="submit" value="Create tournament"/>
    </fieldset>
    </form>

    <h1>All tournaments</h1>
    {if:(count($this->data->allTournaments) > 0)}
        <table border="1">
        <tr>
            <th>Name</th>
            <th>Password</th>
            <th>Description</th>
            <th>Initiation Date</th>
            <th>Closing Date</th>
            <th>Finished Date</th>
            <th>Status</th>
            <th>action</th>
        </tr>
        {foreach:allTournaments,i,tournament}
            {if:doIParticipate[i]}
                <tr class="current">
            {else}
                <tr>
            {end}
            <td>{tournament['name']}</td>
            <td>
            {if:isPasswordNeeded[i]}
                Yes
            {else}
                No
            {end}
            </td>
            <td>{tournament['description']}</td>
            <td>{tournament['initiationDate']}</td>
            <td>{tournament['closingDate']}</td>
            <td>{tournament['finishedDate']}</td>
            <td>{tournament['status']}</td>
            <td>
            {if:(tournament['status'] == 'openForInvitations')}
                {if:doIParticipate[i]}
                    <a href="tournaments.php?deleteParticipation={tournament['id']}"><img src="styling/disconnect.png" alt="don't participate" title="don't participate" border="0"/></a>
                {else}
                    {if:isPasswordNeeded[i]}
                       
                        <form method="get" action="tournaments.php">
                        <input type="hidden" name="enterID" value="{tournament['id']}"/>
                        <input type="password" name="password" />
                        <input type="submit" value="participate" />
                        </form>
                    {else}
                        <a href="tournaments.php?enterID={tournament['id']}"><img src="styling/connect.png" alt="participate" title="participate" border="0"/></a>
                    {end}
                {end}
            {end}
            <a href="tournaments.php?getDetails={tournament['id']}"><img src="styling/magnifier.png" alt="details" title="details"  border="0"/></a>
            </td>
          </tr>
        {end}
    {else}
        <p>Currently are no tournaments running</p>
    {end}
    </div>
</body>
</html>
