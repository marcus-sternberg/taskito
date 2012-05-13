<?php
###### Editnotes ####
#$LastChangedDate: 2011-09-21 08:49:52 +0200 (Mi, 21 Sep 2011) $
#$Author: msternberg $ 
#####################

# Kommentar
$session_frei = 1;
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_kopf.php'); 

# Lese gewünschten IR aus

$hir_id=$_REQUEST['hir_id'];
                            
# Baue Layout-Tabelle
echo '<table width=100%><tr><td width="10">&nbsp;</td><td>';

$sql='SELECT hir_id,hir_status FROM ir_stammdaten WHERE hir_id = "' . $hir_id . '"';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {
    
echo '<br><table class="matrix">';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">';

echo 'Incident Report: ' . $zeile['hir_id'] . ' ';

echo ' | ';


    $sql_count='SELECT COUNT(uir_id) AS menge FROM ir_log WHERE uir_hirid = "' . $hir_id . '"';

    if (!($ergebnis_count=mysql_query($sql_count, $verbindung)))
        {
        fehler();
        }
    // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
    while ($zeile_count=mysql_fetch_array($ergebnis_count))
        {
        echo '<a href="#Kommentar">Logeinträge: ' . $zeile_count['menge'] . '</a>';
        }


    echo ' | ';


    $sql_count='SELECT COUNT(uir_id) AS menge FROM ir_todo WHERE uir_hirid = "' . $hir_id . '"';

    if (!($ergebnis_count=mysql_query($sql_count, $verbindung)))
        {
        fehler();
        }

    // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
    while ($zeile_count=mysql_fetch_array($ergebnis_count))
        {
        echo '<a href="#todo">ToDos: ' . $zeile_count['menge'] . '</a>';
        }



    echo ' | ';



    echo 'aktueller Status:  ';



    # Statusanzeige

    $status_array=array
        (
        "1" => "eröffnet",
        "2" => "Analyse",
        "3" => "Fixing",
        "4" => "Testen",
        "5" => "geschlossen"
        );


    echo $status_array[$zeile['hir_status']];

    echo '</th>';
    }



echo '</table>'; // Ende Statusanzeige IR

echo '<br><br>';

echo '<table width="1000">';

echo '<tr><td>'; // Große Datentabelle bauen

# Stammdaten des IR auslesen

$sql='SELECT * FROM ir_stammdaten 
        LEFT JOIN impact ON uia_id = hir_auswirkung 
        LEFT JOIN prioritaet ON hir_prio = upr_nummer
        LEFT JOIN mitarbeiter ON hma_id = hir_agent
        WHERE hir_id = "'
    . $hir_id . '"';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {

        $hir_zeitstempel = 0;
        $uir_zeitstempel = 0;
        # Suche letzte Zeitstempel für die Anzeige
        
        $sql_time= 'SELECT uir_zeitstempel FROM ir_log
        LEFT JOIN ir_stammdaten ON uir_hirid = hir_id 
        WHERE uir_hirid = '.$hir_id.' 
        ORDER BY uir_zeitstempel DESC LIMIT 1';

if (!($ergebnis_time =mysql_query($sql_time, $verbindung)))
    {
    fehler();
    }
    
    while ($zeile_time=mysql_fetch_array($ergebnis_time))
    {
      $uir_zeitstempel = $zeile_time['uir_zeitstempel'];  
    }
        
     
        $sql_time= 'SELECT hir_zeitstempel FROM ir_stammdaten  
        WHERE hir_id = '.$hir_id.'  
        ORDER BY hir_zeitstempel DESC LIMIT 1';

if (!($ergebnis_time =mysql_query($sql_time, $verbindung)))
    {
    fehler();
    }
    
    while ($zeile_time=mysql_fetch_array($ergebnis_time))
    {
      $hir_zeitstempel = $zeile_time['hir_zeitstempel'];  
    }
      
    if(strtotime($uir_zeitstempel)>strtotime($hir_zeitstempel)) {$letzte_aenderung = zeitstempel_anzeigen($uir_zeitstempel); } else {$letzte_aenderung = zeitstempel_anzeigen($hir_zeitstempel);}    

    $meeting = $zeile['hir_meeting'];
    $ood=$zeile['hir_ood'];
    $agent_id=$zeile['hir_agent'];
    $angelegt_am = zeitstempel_anzeigen($zeile['hir_datum']);
    $angelegt_durch = $zeile['hma_login'];
    
    echo '<table class="is24_ir">';

    echo '<caption class="is24">Daten des Incidents</caption>';

    echo '<tbody>';

    echo '<tr>';

    echo '<td  class="is24_ir_head">';

    echo 'Thema des Incidents:';

    echo '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="is24_ir">';

    echo nl2br($zeile['hir_problem']);

    echo '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td  class="is24_ir_head">';

    echo 'Problembeschreibung:';

    echo '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="is24_ir">';

    echo nl2br(htmlspecialchars($zeile['hir_beschreibung']));

    echo '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td>';

    echo '<table>';

    echo '<tr>';

    echo '<td  class="is24_ir_head">';

    echo 'Auswirkung:';

    echo '</td>';

    echo '<td  class="is24_ir_head">';

    echo 'Priorität:';

    echo '</td>';

    echo '<td  class="is24_ir_head">';

    echo 'Kategorie:';

    echo '</td>';

    echo '<td  class="is24_ir_head">';

    echo 'Release:';

    echo '</td>';
    
    echo '</tr>';

    echo '<tr>';

    echo '<td  class="is24_ir">';

    echo $zeile['uia_name'];

    echo '</td>';

    echo '<td class="is24_ir">';

    echo $zeile['upr_name'];

    echo '</td>';

    echo '<td class="is24_ir">';

    echo $zeile['hir_kategorie'];

    echo '</td>';

    echo '<td class="is24_ir">';

    echo $zeile['hir_release'];

    echo '</td>';
    
    echo '</tr>';

    echo '</table>';

    echo '</td></tr>';

    echo '<tr>';

    echo '<td  class="is24_ir_head">';

    echo 'Analyse / Ergebnisse:';

    echo '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="is24_ir">';

    echo nl2br($zeile['hir_analyse']);

    echo '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td  class="is24_ir_head">';

    echo 'Getroffene Maßnahmen / Anpassungen:';

    echo '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="is24_ir">';

    echo nl2br($zeile['hir_massnahme']);

    echo '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="is24_ir_head">';

    echo 'Lessons Learned:';

    echo '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="is24_ir">';

    echo nl2br($zeile['hir_lessons']);

    echo '</td>';

    echo '</tr>';

    echo '</table>';
    }

echo '</td>'; # Datentabelle Teil eins

echo '<td valign="top">';

# Beginn der Statustabelle

echo '<table class="matrix">';

echo '<tr><td class="text_mitte" bgcolor="#FFCA5E" align="center">Details zum IR</td></tr>';

echo '<tr>';

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

    echo '<td class="text_mitte" align="center" bgcolor="' . $color . '" width="200">Aktueller DEFCON: <strong>'
        . $zeile['ude_status'] . '</strong>&nbsp;<br>(' . $status . ')</td>';
    }

echo '</tr>';

echo '<tr><td><hr class="is24_hr"></td></tr>';

echo '<tr>';

echo '<td class="text_klein_fett">';

echo 'angelegt am:';

echo '</td>';

echo '</tr>';

echo '<tr>';

echo '<td>';

echo $angelegt_am;

echo '</td>';

echo '</tr>';

echo '<tr>';

echo '<td class="text_klein_fett">';

echo 'letzte Änderung:';

echo '</td>';

echo '</tr>';

echo '<tr>';

echo '<td>';

echo $letzte_aenderung;

echo '</td>';

echo '</tr>';

echo '<tr>';

echo '<td class="text_klein_fett">';

echo 'OOD:';

echo '</td>';

echo '</tr>';

echo '<tr>';

echo '<td>';

echo $ood;

echo '</td>';

echo '</tr>';

echo '<td class="text_klein_fett">';

echo 'angelegt von:';

echo '</td></tr><tr>';

echo '<td>'.$angelegt_durch;

echo '</td>';

echo '<tr><td><hr class="is24_hr"></td></tr>';

echo '<tr>';

echo '<td class="text_klein_fett">';

echo 'Nächstes Treffen:';

echo '</td>';

echo '</tr>';

echo '<tr>';

echo '<td>';

echo $meeting;

echo '</td>';

echo '</tr>';

echo '<tr><td><hr class="is24_hr"></td></tr>';

echo '<tr>';

echo '<td class="xnormal_sort" colspan="3">Anlage:</td>';

echo '</tr>';

$target_path="ir/" . $hir_id . "/";

echo '<td class="xnormal_sort">';

if (is_dir($target_path))
    {
    if ($handle=opendir($target_path))
        {
        while (false !== ($file=readdir($handle)))
            if ($file != '.' AND $file != '..')
                {
                echo '<a href="' . $target_path . $file . '" target="_blank">' . ($file)
                    . '</a>  <a href="ir_file_loeschen.php?name=' . $file . '&pfad=' . $target_path . '&ir=' . $hir_id
                    . '" onclick="return window.confirm(\'Delete File?\');"><img src="bilder/icon_loeschen.gif" border=0></a><br>';
                }
        closedir($handle);
        }
    }

echo '</td></tr>';

echo '</table>';

echo '</td></tr>';

echo '</table>';              # Datentabelle Ende

echo '<table width="1000">';

echo '<tr><td valign="top">'; // Activity Tabelle bauen

$sql='SELECT * FROM ir_log ' .
    'INNER JOIN mitarbeiter ON uir_hmaid = hma_id ' .
    'WHERE uir_hirid = ' . $hir_id .
    ' ORDER BY uir_datum DESC';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
echo '<br><br>';
echo '<a name="Kommentar"><table class="is24">';

echo '<thead class="is24"><th class="is24">Datum</th><th class="is24">Kommentar</th><th class="is24">durch</th></thead>';

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<tr>';

    echo '<td class="text_klein" valign="top">' . zeitstempel_anzeigen($zeile['uir_datum']) . '</td>';
    echo '<td class="text_klein" valign="top">' . nl2br($zeile['uir_eintrag']) . '</td>';
    echo '<td class="text_klein" valign="top">'.$zeile['hma_login'].'</td>';
    echo '</tr>';
    }

echo '</table>';

echo '<td valign="top">'; // Zweite Spalte

$sql='SELECT * FROM ir_todo ' .
    'INNER JOIN prioritaet ON uir_prio = upr_nummer ' .
    'WHERE uir_hirid = ' . $hir_id .
    ' ORDER BY uir_fertig ASC';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

echo '<br><br>';
echo '<a name="todo"><table class="is24">';

echo '<thead class="is24"><th class="is24">Verantwortlich</th><th class="is24">Aufgabe</th><th class="is24">Priorität</th></thead>';

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<tr>';

    echo '<td class="text_klein" valign="top">' . $zeile['uir_wer'] . '</td>';

    echo '<td class="text_klein" valign="top">' . nl2br($zeile['uir_todo']) . '</td>';

    echo '<td class="text_klein" valign="top">' . $zeile['upr_name'] . '</td>';

    echo '</tr>';
    }

echo '</table>';

echo '</td></tr>';

echo '</table>'; # Datentabelle Ende

# Schliesse Layout-Tabelle
echo '</tr></table>';
?>
