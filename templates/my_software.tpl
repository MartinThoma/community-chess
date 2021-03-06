<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8" />
  <meta name="description" content="Set up your current software." />
  <title>Software - Community Chess</title> 
  <link rel="stylesheet" type="text/css" href="styling/default.css" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
</head>
<body>
    {{navigation}}
    <div>
        <p style="color:#a67000;font-size:38px;margin:0;padding:0;margin-left:180px">Community Chess</p>
    </div>
    <div id="content">

    <h1>Add a new one</h1>
    <form method="post" action="my_software.php">
    <fieldset>
    <legend>Add a new software / version</legend>

    <p>
    <label for="newSoftwareName">Name of your Software</label>
    <input type="text" name="newSoftwareName" id="newSoftwareName" 
           placeholder="Chessmaster "/>
    </p>

    <p>
    <label for="version">Version</label>
    <input type="text" name="version" id="version" placeholder="0.1.0"/>
    </p>


    {if:isset(softwareArray)}
        <p>
        <label for="lastVersionID">lastVersionID</label>
        <select name="lastVersionID" id="lastVersionID">
            <option value="0">New software</option>
            {foreach:softwareArray,i,software}
                <option value="{software['id']}">{software['name']} {software['version']}
                </option>
            {end}
        </select>
        </p>
    {end}

    <p>
    <label for="changelog">Changelog</label>
    <textarea name="changelog" id="changelog"></textarea>
    </p>

    <input type="submit" value="Insert new software entry into database"/>
    </fieldset>
    </form>

    <h1>List</h1>
    <table>
    <tr>
    <th>Name of your Software</th>
    <th>Version</th>
    <th>action</th>
    <th>team</th>
    <th>add user by username</th>
    <th>languages</th>
    <th>add language</th>
    </tr>
    {if:($this->data->currentSoftwareID == 0)}
      <tr class="current">
    {else}
      <tr>
    {end}
    <td>Human player</td>
    <td><a href="http://en.wikipedia.org/wiki/Human">Homo sapiens</a></td>
    <td><a href="my_software.php?setCurrent=0">set to current</a></td>
    <td>You</td>
    <td>-</td>
    <td>English</td>
    <td>-</td>
    </tr>
    {if:isset(softwareArray)}
        {foreach:softwareArray,i,software}
            {if:software['id']==$this->data->currentSoftwareID}
                <tr class="current">
            {else}
                <tr>
            {end}
            <td>{software['name']}</td><td>{software['version']}</td>
            <td><a href="my_software.php?setCurrent={software['id']}">set to current</a></td>
            <td><ul>
            {foreach:software['players'],i,player}
                <li>{player['username']} ({player['task']})
                {if:player['user_id'] != USER_ID}
                    <a href="my_software.php?deleteTeammate={player['user_id']}&softwareID={software['id']}">
                    <img src="styling/delete.png" />
                    </a>
                {end}
                </li>
            {end}
            </ul></td>
            <td>
            <form method="get" action="my_software.php">
                <input type="text" name="addTeammate"/>
                <input type="text" name="task" value="" placeholder="developer">
                <input type="hidden" name="softwareID" value="{software['id']}">
                <input type="submit" value="add"/>
            </form>
            </td>
            <td>
            {foreach:software['languages'],$i,lang}
                {lang['name']}
                <a href="my_software.php?deleteLang={lang['id']}&softwareID={software['id']}"><img src="styling/delete.png" /></a>
            {end}
            </td>
            <td>
            <form method="get" action="my_software.php">
                <input type="text" name="addLanguage"/>
                <input type="hidden" name="softwareID" value="{software['id']}">
                <input type="submit" value="add"/>
            </form>
            </td>
            </tr>
        {end}
    {end}
    </div>
</body>
</html>
