<html>
    <head>
        <link rel="stylesheet" type="text/css" href="./default.css" />
    </head>
    <body>
        <?php
            /******************************************************************
             * Adjust                                                         * 
             ******************************************************************/
            $server="http://127.0.0.1/chess";
            $PHPSESSID="ighv1l2am2j0glgeajbhvnb8l5";
            $gameID=2;
            /******************************************************************
             * Functions                                                      *
             ******************************************************************/
            function displayField($figure, $x, $y){
                if(strtoupper($figure)!=$figure){
                    // Discern black from white pieces for windows servers, as
                    // FAT is case-insensitive.
                    $figure .= "b";
                }
                
                /* x and y are in [1;8]*/
                if (($x+($y-1)*8 + $y)%2==0){
                    $chessfieldColor = 'black';
                } else {
                    $chessfieldColor = 'white';
                }
                echo '<td class="'.$chessfieldColor.'Field">';
                echo '<img src="../../figures/'.$figure.'.png" alt="'.$figure.'"/>';
                echo '</td>';
            }
            /******************************************************************
             * Submit move                                                    *
             ******************************************************************/
            if (isset($_GET['from'])){
                $from      = $_GET['from'];
                $to        = $_GET['to'];
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
            /******************************************************************
             * Get Information                                                *
             ******************************************************************/
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
            if ($data == 'Please <a href="login.wrapper.php">login</a>'){
                exit("Wrong Password / Username / PHPSESSID?<br/>".
                     "If you use Chrome, you can find the PHPSESSID in your ".
                     '<a href="chrome://settings/cookies">cookies</a>.');
            }
            $data_array = explode("<br/>", $data);

            
            /******************************************************************
             * Display Information                                            *
             ******************************************************************/
            $debugmode = isset($_GET['debug']);
            $letters   = implode("</th><th>",array('&nbsp;','a','b','c','d','e','f','g','h','&nbsp;'));
            echo '<table><tr><td>';
            echo '<h2>Current Board</h2>';
            echo "<table><tr><th>$letters</th></tr>\r\n";
            for($y=8;$y>=1;$y--){
                echo "<tr><th>$y</th>";
                for($x=1;$x<=8;$x++){
                    $figure = substr($data_array[9-$y], $x-1, 1);
                    displayField($figure, $x, $y);
                }
                echo "<th>$y</th></tr>\r\n";
            }
            echo "<tr><th>$letters</th></tr>\r\n";
            echo '</table>';

            echo '</td><td>';
            echo '<h2>Move</h2>';
            echo '<form method="get" action="chess-client.php">';
            if ($debugmode) {echo('<input type="hidden" name="debug" value="1" />');}
            echo '<label for="from">From</label>';
            echo '<input type="text" name="from" id="from" placeholder="a2" size="2" />';
            echo '<label for="to">To</label>';
            echo '<input type="text" name="to" id="to" placeholder="a4" size="2" />';
            echo '<label for="promotion">Promotion (leave blank if you don\'t promote)</label>';
            echo '<input type="text" name="promotion" id="promotion" placeholder="Q" size="1" />';
            echo '<input type="submit" />';
            echo '</form>';
            echo '</tr></table>';

            echo implode("<br />",array_slice($data_array,10,13)).'<br/>';
            echo '<a href="./chess-client.php">Refresh</a>';
            if($debugmode){
                echo '<h1>Debug Information:</h1>';
                foreach($data_array as $x){
                    echo($x."<br />");
                }
            }
        ?>
    </body>
</html>
