<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################



function pruefung($benutzerrechte, $benoetigt, $max) {
    $rechte = array();
        for($i = $max; $i >= 0; $i--) {
            $wert = pow(2, $i);
            if($benutzerrechte >= $wert) {
                $rechte[] = $wert;
                #$benutzerrechte -= $wert;            }
        }
        }
        if(in_array($benoetigt, $rechte)) {
            return true;
        }
        else {
            return false;
        }
    }

echo '<div id="menu_container">';

echo '<ul id="pmenu">';

$sql='SELECT * FROM level';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
    
$max = mysql_num_rows($ergebnis);

$sql='SELECT * FROM menu_main ' .
    'ORDER BY xSort';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
    
while ($zeile=mysql_fetch_array($ergebnis))
    {
       
    if(pruefung($level, $zeile['xKey'], $max)) 
#    if (($zeile['xKey'] & (1 << $level)) > 0)   faellt weg
        {
        $sql_sub1='SELECT * FROM menu_sub1 ' .
            'WHERE xKey_main = ' . $zeile['ID'] .' ORDER BY xSort';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_sub1=mysql_query($sql_sub1, $verbindung))
            {
            fehler();
            }

        if (mysql_num_rows($ergebnis_sub1) == 0)
            {
            echo '<li><a href="' . $zeile['xLink'] . '">&nbsp;' . ($zeile['xTitle']) . '</a></li>';
            }
        else
            {
            echo '<li class="drop"><a href="' . $zeile['xLink'] . '">&nbsp;' . ($zeile['xTitle'])
                . '<!--[if IE 7]><!--></a><!--<![endif]--><!--[if lte IE 6]><table><tr><td><![endif]--> ';

            echo '<ul>';

            while ($zeile_sub1=mysql_fetch_array($ergebnis_sub1))
                {

                // Pruefe, ob Submenu angezeigt werden soll
                   if(pruefung($level, $zeile_sub1['xKey'], $max))    
               # if (($zeile_sub1['xKey'] & (1 << $level)) > 0)
                    {
                    $sql_sub2='SELECT * FROM menu_sub2 ' .
                        'WHERE xKey_main = ' . $zeile_sub1['ID_SUB1'] .' ORDER BY xSort';

                    // Frage Datenbank nach Suchbegriff
                    if (!$ergebnis_sub2=mysql_query($sql_sub2, $verbindung))
                        {
                        fehler();
                        }

                    if (mysql_num_rows($ergebnis_sub2) == 0)
                        {
                        echo '<li><a href="' . $zeile_sub1['xLink'] . '" class="enclose">' . $zeile_sub1['xTitle']
                            . '</a></li>';
                        }
                    else
                        {
                        echo '<li class="fly"><a href="' . $zeile_sub1['xLink'] . '">' . $zeile_sub1['xTitle']
                            . '<!--[if IE 7]><!--></a><!--<![endif]-->';

                        echo '<!--[if lte IE 6]><table><tr><td><![endif]-->';

                        echo '<ul>';

                        while ($zeile_sub2=mysql_fetch_array($ergebnis_sub2))
                        if(pruefung($level, $zeile_sub2['xKey'], $max)) 
                           # if (($zeile_sub2['xKey'] & (1 << $level)) > 0)
                                {
                                {
                                echo '<li><a href="' . $zeile_sub2['xLink'] . '" class="enclose">'
                                    . $zeile_sub2['xTitle'] . '</a></li>';
                                    }
                                }

                        echo '</ul>';

                        echo '<!--[if lte IE 6]></td></tr></table></a><![endif]-->';

                        echo '</li>';
                        }
                    }
                }

            echo '</ul>';

            echo '<!--[if lte IE 6]></td></tr></table></a><![endif]-->';

            echo '</li>';
            }
        }
    }

echo '</ul>';

echo '</div> ';
?>
