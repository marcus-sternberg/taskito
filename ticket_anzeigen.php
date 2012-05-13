<?php
###### Editnotes ####
#$LastChangedDate: 2011-10-25 09:59:36 +0200 (Di, 25 Okt 2011) $
#$Author: msternberg $ 
#####################
$session_frei = 1; // wegen Anzeige filtern - nur freigegeben Inhalte anzeigen   

require_once('konfiguration.php');
include('segment_session_pruefung.php'); 
include('segment_kopf.php'); 

# Lese gewünschtes Ticket aus

$uti_md5=mysql_real_escape_string($_REQUEST['ticket_nr']);

# Untermenü einblenden

$sql='SELECT * FROM ticket_info WHERE uti_md5 = "' . $uti_md5 . '"';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {
    $task_id=$zeile['uti_hauid'];
    }

    
########################### Zaehle vorhandene Kommentare fuer die Aufgabe ############

$sql_count='SELECT COUNT(*) AS anzahl FROM log 
                INNER JOIN mitarbeiter ON ulo_ma = hma_id 
                WHERE ulo_aufgabe = ' . $task_id;

if (!($ergebnis_count=mysql_query($sql_count, $verbindung)))
    {
    fehler();
    }

$Menge=mysql_fetch_array($ergebnis_count);


### Prüfe, ob Aufgabe wichtig oder kritisch in der Priorität ist

$sql_prio = 'SELECT hau_prio FROM aufgaben WHERE hau_id = ' . $task_id;

if (!($ergebnis_prio=mysql_query($sql_prio, $verbindung)))
    {
    fehler();
    }
    
while ($zeile_prio=mysql_fetch_array($ergebnis_prio))
        {
        $task_prio = $zeile_prio['hau_prio'];
        }
        
echo '<br><table class="matrix" cellpadding = "5">';

echo '<thead class="is24">';

echo '<tr class="is24">';

if (in_array($task_prio, array(2,3)))
{
  echo '<th style = "background-color:#ff000c">Hohe Priorität</th>';    
} else 
{
  echo '<th class = "is24">Standard Priorität</th>'; 
  
}



echo '</th>';

echo '<th class="is24">';

echo ' | ';

echo '</th>';

echo '<th class="is24">';

echo $Menge['anzahl'] . ' Kommentar(e)';

echo '</th>';
   
# Checke, ob die Aufgabe beendet ist

$sql_check='SELECT * FROM `aufgaben` WHERE hau_abschluss = 1 AND hau_id = '.$task_id;

if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis_check) == 0)
    {

            echo '<th class="is24">';

            echo ' | ';

            echo '</th>';

            echo '<th class="is24">';

            echo 'Aufgabe offen';

            echo '</th>';
            }
        else
            {
            echo '<th class="is24">';

            echo ' | ';

            echo '</th>';

            echo '<th style = "background-color:#E3E3E3">';

            echo 'Aufgabe abgeschlossen';

            echo '</th>';
            }

echo '</tr>';

echo '</thead>';

echo '</table>';

echo '<br><br>';

# Baue Layout-Tabelle
echo '<table width=100%><tr><td width="10">&nbsp;</td><td>';

if ($task_id)
    {
    include('segment_aufgabe_anzeigen.php');

    echo '<br><br>';
  
    include('segment_liste_aktiv.php');
    }
else
    {
    echo '<br><br>';

    echo 'Zu der angegebenen Ticketnummer finden sich leider keine Informationen im System.';
    }

# Schließe Layout-Tabelle
echo '</tr></table>';
?>
