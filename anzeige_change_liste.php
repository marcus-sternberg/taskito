<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php'); 

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';

echo '<html>';

echo '<head>';

echo '<title>TaskScout24 - Task Organisation Management</title>';

echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8">';

echo '<meta http-equiv="refresh" content="60" >';

echo '<link rel="stylesheet" type="text/css" href="css/tom.css">';

echo '<link rel="shortcut icon" href="tom.ico" type="image/x-icon">';

echo '<link rel="icon" href="tom.ico" type="image/x-icon">';

echo '<script type="text/javascript" src="scroll.js" ></script>';

echo '<style type="text/css">';

echo 'body { background-color:#000;}';

echo 'table.element { background-color:#000;}';

echo '</style>';

echo '</head>';
  

  $sql =  'SELECT hau_titel, hau_anlage FROM aufgaben
            LEFT JOIN rollen_status ON urs_hauid = hau_id 
          WHERE hau_hprid = 6 and hau_abschluss = 0 AND hau_aktiv = 1 AND urs_freigabe_ok = 1  
         ORDER BY hau_anlage DESC';
                                                                   

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
    
echo '<div id="datacontainer" style="position:absolute;left:1px;top:10px;width:100%">';

echo '<table border="1" class="element" width="900" cellpadding="5">';

    echo '<tr>';
 
    echo '<td align="left" class="text_anzeige" colspan="2">Freigegebene Changes</td>';

    echo '</tr>';
    
// Starte Tabelle

while ($zeile=mysql_fetch_array($ergebnis))
    {

    echo '<tr>';

    echo '<td align="left" class="text_anzeige">' . datum_anzeigen($zeile['hau_anlage']) . '&nbsp;</td>';

    echo '<td align="left" class="text_anzeige">' . $zeile['hau_titel'] . '&nbsp;</td>';

    echo '</tr>';
    }

echo '</table>';

echo '</div>';

echo '</body></html>';
?>
