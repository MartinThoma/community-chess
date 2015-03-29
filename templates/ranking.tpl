<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8" />
  <meta name="description" content="See all current and all past matches." />
  <title>Ranking - Community Chess</title> 
  <link rel="stylesheet" type="text/css" href="styling/default.css" />
  <link type="text/css" href="external/jquery-toastmessage/css/jquery.toastmessage.css" rel="stylesheet">
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <script type="text/javascript" src="external/jquery-1.6.4.min.js"></script>
  <script type="text/javascript" src="external/jquery-toastmessage/jquery.toastmessage-min.js"></script>
</head>
<body>
    {{navigation}}
    <div>
        <p style="color:#a67000;font-size:38px;margin:0;padding:0;margin-left:180px">Community Chess</p>
    </div>
    <div id="content">


    <h1>Ranks {fromRank} to {toRank} (page {currentPage} of {maxPages} - {nrOfUsers} users ranked)</h1>
    <table style="width:100%">
        <tr>
            <th style="width:50px;">Rank</th>
            <th>User</th>
            <th>PageRank</th>    
        </tr>
    {foreach:ranking,i,rank}
        {if:($this->data->i%2 == 1)}
        <tr class="odd">
        {else}
        <tr>
        {end}
            <td style="text-align:center;">{rank['rank']}</td>
            <td><a href="profile.php?username={rank['username']}">{rank['username']}</td>
            <td>{rank['pageRank']}</td>    
        </tr>
    {end}
    </table>
    </div>
</body>
</html>
