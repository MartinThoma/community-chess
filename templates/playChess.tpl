<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8" />
  <meta name="description" content="Play a chess match." />
  <title>Play Chess - Community Chess</title> 
  <link rel="stylesheet" type="text/css" href="styling/default.css" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
</head>
<body>
    {{navigation}}
    <div>
        <p style="color:#a67000;font-size:38px;margin:0;padding:0;margin-left:180px">Community Chess</p>
    </div>
    <div id="content">


    <table>
    <tr>
    <td>

    <table style="text-align:center;">
        <tr><td colspan="10">{if:$this->data->yourColor=='white'}
                                {if:youCheck}<span class="stressed">You are check.</span>{end}
                            {else}
                                {if:opponentCheck}<span class="stressed">Opponent is check.</span>{end}
                            {end} (White)</td></tr>
        <tr><td colspan="10">{if:$this->data->yourColor=='white'}You{else}Your opponent{end} (White)</td></tr>
        <tr><th>&nbsp;</th><th>a</th><th>b</th><th>c</th><th>d</th><th>e</th><th>f</th><th>g</th><th>h</th><th>&nbsp;</th></tr>
    {foreach:board,y,line}
        <tr><th>{(y+1)}</th>
        {foreach:line,x,piece}
             {displayField($this->data->piece, $this->data->x+1, $this->data->y+1, $this->data->yourTurn, $this->data->from)}
        {end}
        <th>{(y+1)}</th></tr>
    {end}
        <tr><th>&nbsp;</th><th>a</th><th>b</th><th>c</th><th>d</th><th>e</th><th>f</th><th>g</th><th>h</th><th>&nbsp;</th></tr>
        <tr><td colspan="10">{if:$this->data->yourColor=='black'}You{else}Your opponent{end} (Black)</td></tr>
        <tr><td colspan="10">{if:$this->data->yourColor=='black'}
                                {if:youCheck}<span class="stressed">You are check.</span>{end}
                            {else}
                                {if:opponentCheck}<span class="stressed">Opponent is check.</span>{end}
                            {end} (Black)</td></tr>
    </table>

    </td>
    <td>
        {if:$this->data->whoseTurnIsItLanguage==$this->data->yourColor}
            <p>It's your turn.</p>
        {else}
            <p>It's your opponents turn.</p>
        {end}
        <p>Game-ID: {CURRENT_GAME_ID} (<a href="playChess.php?gameID={CURRENT_GAME_ID}">refresh</a>)</p>
        <br/>
        <br/>
        <br/>
        <br/>
        <p><a href="playChess.php?gameID={CURRENT_GAME_ID}&giveUp=true">Give up</a></p>
    </td>
    </tr>
    </table>
    </div>
</body>
</html>
