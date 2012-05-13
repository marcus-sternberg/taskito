<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################


echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';

echo '<html>';

echo '<head>';

echo '<title>TaskScout24 - Task Organisation Management</title>';

echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8">';

echo '<meta http-equiv="refresh" content="60">';

echo '<link rel="stylesheet" type="text/css" href="css/tom.css">';

echo '<link rel="shortcut icon" href="tom.ico" type="image/x-icon">';

echo '<link rel="icon" href="tom.ico" type="image/x-icon">';

echo '</head>';

$auto='on';
$_SESSION['filterstring']='';
$_SESSION['hma_id']=1;
$_SESSION['hma_level']=99;    
require_once('konfiguration.php');
include('segment_init.php');

$jahr=date("Y");
$month=date("m");
$day=date("d");
$ergebnis_check=array();

########################  Definiere Variablen ################################


#####################################################################################
############################ Ausgabe Werte ##########################################

// Starte Tabelle

echo '<table border="0"><tr><td width="10">&nbsp;</td><td>';

echo '<table class="is24">';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Aufgabe</th>';

echo '<th class="is24">fällig am</th>';

echo '<th class="is24">Bearbeiter</th>'; 

echo '</tr>';

echo '</thead>'; 

$sql=
    'SELECT hau_dauer, hau_id, hau_titel, hau_pende, hau_nonofficetime FROM aufgaben 
         WHERE hau_aktiv =1 AND hau_kalender = 1 AND hau_pende BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 45 DAY) AND hau_abschluss = 0 
         ORDER BY hau_pende';


// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
    
  $zaehler = 0;
    
while ($zeile=mysql_fetch_array($ergebnis))
    {
        
    if (fmod($zaehler, 2) == 1 && $zaehler > 0)
        {
        $tr_stil='<tr class="is24">';
        }
    else
        {
        $tr_stil='<tr class="is24_odd">';
        }

        
    if ($zeile['hau_nonofficetime'] == 0)
        {
        $td_stil='is24_termin';
        }
    else
        {
        $td_stil='is24_termin_rot';
        }

    echo $tr_stil; 

    echo '<td class="'.$td_stil.'">'.($zeile['hau_titel']) . '</td>';

    echo '<td class="' . $td_stil . '">' . datum_anzeigen($zeile['hau_pende']) . '&nbsp;</td>';

    echo '<td class="' . $td_stil . '" nowrap>';

       $sql_owner='SELECT hma_id, hma_name, hma_vorname FROM mitarbeiter
                    LEFT JOIN aufgaben_mitarbeiter ON hma_id = uau_hmaid
                    WHERE uau_hauid = ' . $zeile['hau_id'] . ' 
                    ORDER BY hma_name';

    if (!$ergebnis_owner=mysql_query($sql_owner, $verbindung))
        {
        fehler();
        }
   
    while ($zeile_owner=mysql_fetch_array($ergebnis_owner))
        {
        echo $zeile_owner['hma_vorname'] . ' ' . $zeile_owner['hma_name'] .'<br>' ;
          
        }

    echo '</td>';

    echo '</tr>';
    
    $zaehler++;
    
    }

echo '</tbody>';
    
echo '</table>';
echo '</td></tr>';

echo '<tr><td width="10">&nbsp;</td><td>'; 

echo '<br>Roter Text markiert Nachtarbeit z.B. Downtime bei MR.';

echo '</td></tr></table>';  
include('segment_fuss.php');
?>
