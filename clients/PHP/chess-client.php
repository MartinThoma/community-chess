<html>
<head>
<link rel="stylesheet" type="text/css" href="default.css" />
</head>
<body>

<?php
/******************************************************************************
 * Adjust                                                                     * 
 ******************************************************************************/
$server="http://localhost/chess";
$PHPSESSID="ks76al1l5t479t3bdfid0p2mj7";
$gameID=1;
/******************************************************************************
 * Functions                                                                  *
 ******************************************************************************/
function displayField($figure, $x, $y, $server){
    /* x and y are in [1;8]*/
    if (($x+($y-1)*8 + $y)%2==0){
        $chessfieldColor = 'black';
    } else {
        $chessfieldColor = 'white';
    }
    echo '<td class="'.$chessfieldColor.'Field">';
    echo '<img src="'.$server.'/figures/'.$figure.'.png" />';
    echo '</td>';
}
/******************************************************************************
 * Submit move                                                                *
 ******************************************************************************/
if (isset($_GET['from'])){
    $from = $_GET['from'];
    $to = $_GET['to'];
    $promotion = $_GET['promotion'];
    $context = stream_context_create(
                array('http'=>array(
                                'method'=> "GET", 
                                'header'=> "Cookie: PHPSESSID=$PHPSESSID\r\n"
                              )
                      )
                );
    $fp = fopen("$server/playChess.php?gameID=$gameID".
                "&iccfalpha=$from$to$promotion", 'r', false, $context);
    $data=stream_get_contents($fp);
    fclose($fp);
    if (substr($data, 0, 5) == 'ERROR'){
        echo "Response to move:";
        echo $data;
    }
}

/******************************************************************************
 * Get Information                                                            *
 ******************************************************************************/
$context = stream_context_create(
            array('http'=>array(
                            'method'=> "GET", 
                            'header'=> "Cookie: PHPSESSID=$PHPSESSID\r\n"
                          )
                  )
            );
$fp = fopen("$server/playChess.php?gameID=$gameID", 'r', false, $context);
$data=stream_get_contents($fp);
fclose($fp);
if ($data[0] == 'Please <a href="login.wrapper.php">login</a>'){
    echo "Wrong Password / Username / PHPSESSID?";
}
$data_array = explode("<br/>", $data);

/******************************************************************************
 * Display Information                                                        *
 ******************************************************************************/
echo '<table><tr><td>';
echo '<h2>Current Board</h2>';
echo '<table>';
echo '<tr>';
echo '<th>&nbsp;</th>';
echo '<th>a</th>';
echo '<th>b</th>';
echo '<th>c</th>';
echo '<th>d</th>';
echo '<th>e</th>';
echo '<th>f</th>';
echo '<th>g</th>';
echo '<th>h</th>';
echo '<th>&nbsp;</th>';
echo '</tr>';
for($y=8;$y>=1;$y--){
    echo '<tr>';
    echo "<th>$y</th>";
    for($x=1;$x<=8;$x++){
        $figure = substr ( $data_array[9-$y] , $x-1 , 1 );
        displayField($figure, $x, $y, $server);
    }
    echo "<th>$y</th>";
    echo '</tr>';
}
echo '<tr>';
echo '<th>&nbsp;</th>';
echo '<th>a</th>';
echo '<th>b</th>';
echo '<th>c</th>';
echo '<th>d</th>';
echo '<th>e</th>';
echo '<th>f</th>';
echo '<th>g</th>';
echo '<th>h</th>';
echo '<th>&nbsp;</th>';
echo '</tr>';
echo '</table>';

echo '</td><td>';
echo '<h2>Move</h2>';
echo '<form method="get" action="chess-client.php">';
echo '<label for="from">From</label>';
echo '<input type="text" name="from" id="from" placeholder="a2" size="2"/>';
echo '<label for="to">To</label>';
echo '<input type="text" name="to" id="to" placeholder="a4" size="2"/>';
echo '<label for="promotion">Promotion (leave blank if you don\'t promote)';
echo '</label>';
echo '<input type="text" name="promotion" id="promotion" placeholder="Q" ';
echo 'size="1"/>';
echo '<input type="submit" />';
echo '</form>';
echo '</tr></table>';

echo $data_array[10].'<br/>';
echo $data_array[11].'<br/>';
echo $data_array[12].'<br/>';
echo $data_array[13].'<br/>';
echo '<a href="chess-client.php">Refresh</a>';
echo '<h1>All Information:</h1>';
print_r($data_array);

?>