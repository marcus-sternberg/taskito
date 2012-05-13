<?php
###### Editnotes ####
#$LastChangedDate: 2012-04-23 17:30:50 +0200 (Mo, 23 Apr 2012) $
#$Author: bpetersen $ 
#####################
$session_frei = 1;
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_init.php');

if (!isset($_GET['auto']))
    {
    $auto='on';
    }
else
    {
    $auto=$_GET['auto'];
    }
    
if ($auto == 'on')
    {

    $autolink='home.php?auto=off';
    $autobild='bilder/icon_refresh.png';
    $autotext='Page-Refresh: ON!';
    }
else
    {

    $autolink='home.php?auto=on';
    $autobild='bilder/icon_refresh_off.png';
    $autotext='Page-Refresh: OFF!';
    }

include('segment_kopf_reload.php');

$jahr=date("Y");
$month=date("m");
$day=date("d");
$ergebnis_check=array();


 #   echo '</table>';  // Kommt aus dem Kopf

    echo '<br><br>';  
    
############################ Status-Anzeige ######################################


echo '<tr><td width="20"></td><td>';

echo '<table class="is24">';

echo '<caption class="is24">';

echo 'Status PRODUKTION';

echo '</caption>';

echo '<thead class="is24">';

echo '<th class="is24">DEFCON</th>';

$sql='SELECT * FROM system_plattformen ORDER BY hpl_id';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<th class="is24" width="20" colspan="2">' . $zeile['hpl_name'] . '</th> ';
    }

echo '</thead>'; 


echo '<tbody class="is24">';

echo '<tr class="is24">';

$sql='SELECT ude_status, ude_zeitstempel FROM defcon
         ORDER BY ude_zeitstempel DESC LIMIT 1';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    switch ($zeile['ude_status'])
        {
        case 1:
            $color='#EE775F';
            $status='KRITISCH';
            break;

        case 2:
            $color='#F3C39B';
            $status='PROBLEM';
            break;

        case 3:
            $color='#FFF8B3';
            $status='WARNUNG';
            break;

        case 4:
            $color='#C1E2A5';
            $status='OK';
            break;
        }

    echo '<td class="is24_start" align="center" bgcolor="' . $color . '">';

    echo $zeile['ude_status'] . ' : ' . $status . '</td>';
    }



echo '</td>';

$sql='SELECT * FROM system_plattformen ORDER BY hpl_id';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<td class="is24" width="20" nowrap><a href="status_plattform.php?xSystem='.$zeile['hpl_id'].'">Version<br> (' . $zeile['hpl_version'] . ')</a></td> ';

    if ($zeile['hpl_status'] == 1)
        {
        $bild='<img src="bilder/icon_quad_gruen.gif" alt="Server up" title="Server up">';
        }
    else
        {
        $bild='<img src="bilder/icon_quad_rot.gif" alt="Server down" title="Server down">';
        }

    echo '<td class0"is24" align="left"><a href="status_plattform.php?xSystem='.$zeile['hpl_id'].'">'.$bild.'</a>';

    echo '</td>';
    }

echo '</tr>';

echo '<tr><td colspan="10" align="right"><a href="status_plattform.php">Detaillierte Statussicht der Plattformen</a></td></tr>';
    
echo '</table>';

echo '<br>';

#################### Kalender ######################

echo '<table class="is24">';

echo '<caption class="is24">';

echo 'Termine&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text_klein">[<a href="uebersicht_task_timeline.php">zeige GANTT-Diagram</a>]</span>'; 

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';   

echo '<th class="is24">Aufgabe</th>';

echo '<th class="is24">fällig am</th>';

echo '<th class="is24">Dauer [d]</th>';

echo '<th class="is24">Mitarbeiter / Fortschritt</th>';

echo '<th class="is24">Nachtschicht</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody class="is24">'; 

$sql=
    'SELECT hau_dauer, hau_id, hau_titel, hau_pende, hau_nonofficetime FROM aufgaben 
         WHERE hau_aktiv =1 AND hau_kalender = 1 AND hau_pende >= CURDATE() AND hau_abschluss = 0 
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
        $td_stil='is24';
        }
    else
        {
        $td_stil='is24_text_rot';
        }

    echo $tr_stil; 
       
    echo '<td class="' . $td_stil . '">' . ($zeile['hau_titel'])
        . '&nbsp;</td>';

    echo '<td class="' . $td_stil . '">' . datum_anzeigen($zeile['hau_pende']) . '&nbsp;</td>';

    echo '<td class="' . $td_stil . '">' . $zeile['hau_dauer'] . '&nbsp;</td>';

    $sql_owner='SELECT hma_id, hma_name, hma_vorname FROM mitarbeiter
                    LEFT JOIN aufgaben_mitarbeiter ON hma_id = uau_hmaid
                    WHERE uau_hauid = ' . $zeile['hau_id'] . ' 
                    ORDER BY hma_name';

    if (!$ergebnis_owner=mysql_query($sql_owner, $verbindung))
        {
        fehler();
        }

    echo '<td class="' . $td_stil . '" nowrap>';

    while ($zeile_owner=mysql_fetch_array($ergebnis_owner))
        {
        echo $zeile_owner['hma_vorname'] . ' ' . $zeile_owner['hma_name'];    #  . ' | ';
      /*
        $sql_menge='SELECT ulo_id,SUM(ulo_fertig) as Menge FROM log ' .
            'WHERE ulo_aufgabe = ' . $zeile['hau_id'] . ' AND ulo_ma = "' . $zeile_owner['hma_id'] . '" ' .
            'GROUP BY ulo_aufgabe';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_menge=mysql_query($sql_menge, $verbindung))
            {
            fehler();
            }

        if (mysql_num_rows($ergebnis_menge) != 0)
            {
            while ($zeile_menge=mysql_fetch_array($ergebnis_menge))
                {
                $bisheriger_aufwand=$zeile_menge['Menge'];
                }
            }
        else
            {
            $bisheriger_aufwand=0;
            }
        */  
        # echo $bisheriger_aufwand . ' %<br>';
        echo '<br>';
        
        }

    echo '</td>';

    echo '<td class="' . $td_stil . '">';

    if ($zeile['hau_nonofficetime'] == 1)
        {
        echo 'Ja';
        }
    else
        {
        echo 'Nein';
        }

    echo '</td>';

    echo '</tr>';
    
    $zaehler++;
    
    }

echo '</tbody>';
    
echo '</table>';

############################# Kalender abgeschlossen #############################    

/*
$sql = 'SELECT fan FROM fans';

        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }

            while ($zeile=mysql_fetch_array($ergebnis))
                {
                $fans=$zeile['fan'];
                }
*/

echo '</td><td valign="middle" align="center" width="300">&nbsp;</td></tr><tr><td width="20"></td><td valign="top">'; // <a href="fan_add.php"><img src="bilder/i_love_it.gif"></a><br><br>'.$fans.' Fans</td></tr><tr><td width="20"></td><td valign="top">';

echo
'<br><br>Probleme oder Erweiterungswünsche? <a href="mailto:sebastian.spoerer@immobilienscout24.de">Mail senden</a>.';


echo '</td><td></td></tr>';

############################# Patches abgeschlossen #############################



echo '</tbody>';

echo '</table>';



?>

</body>

</html>
