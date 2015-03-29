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

    <h1>{username}'s Profile</h1>
    <h2>Games played</h2>
    <table>
        <tr>
            <th>Game ID</th>
            <th>Tournament ID</th>
            <th>Outcome</th>
        </tr>
    {foreach:games,i,game}
        <tr><td>{game['id']}</td><td>{game['tournamentID']}</td><td>{game['outcome']}</td></tr>
    {end}
    </table>

    <p>Change <a href="my_software.php">your software</a>.</p>
    </div>
</body>
</html>
