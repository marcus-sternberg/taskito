<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$session_frei=1;

require_once('konfiguration.php');
include('segment_session_pruefung.php');

if(!ISSET($_POST['speichern']))
{
include ('segment_kopf.php');
echo '<form action="ir_anlegen.php?" method="post">';

echo '<table border="0" width="700">';

echo '<tr><td style="text-align:center; padding-top:10px;"><input type="submit" name="speichern" value="Einen neuen IR anlegen?" class="formularbutton" /></td></tr>';

echo '</table>';

} else 
{

// Speichere den Datensatz

$sql = 'INSERT INTO ir_stammdaten (hir_status, hir_agent, hir_datum) 
        VALUES ( 
        "1", "' . $_SESSION['hma_id'] . '", NOW())';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$hir_id=mysql_insert_id();
$enddatum=date("Y-m-d", (time() + 604800)); // 604800 = 7 Tage in Sekunden


// Speichere den Datensatz

$sql='INSERT INTO aufgaben (' .
    'hau_titel, ' .
    'hau_beschreibung, ' .
    'hau_anlage, ' .
    'hau_inhaber, ' .
    'hau_prio, ' .
    'hau_pende, ' .
    'hau_kalender, ' .
    'hau_nonofficetime, ' .
    'hau_zeitstempel, ' .
    'hau_aktiv, ' .
    'hau_terminaendern, ' .
    'hau_teamleiter, ' .
    'hau_datumstyp, ' .
    'hau_hprid, ' .
    'hau_typ, ' .
    'hau_tl_status, ' .
    'hau_dauer, ' .
    'hau_links, ' .
    'hau_ticketnr) ' .
    'VALUES ( ' .
    '"IR Nummer ' . $hir_id . ' bearbeiten & abschliessen", ' .
    '"Der IR mit der Nummer ' . $hir_id
    . ' wurde angelegt und muss bearbeitet werden, damit er abgeschlossen werden kann.", ' . 
    'NOW(), ' .
    '"' . $_SESSION['hma_id'] . '", ' .
    '"2", ' .
    '"' . $enddatum . '", ' .
    '"0", ' .
    '"0", ' .
    'NOW(), ' .
    '"1", ' .
    '"0", ' .
    '"0", ' .
    '"2", ' .
    '"1", ' .
    '"4", ' .
    '"0", ' .
    '"1", ' .
    '"", ' .
    '"IR ' . $hir_id . '")';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }
    
$hau_id=mysql_insert_id();

$sql='INSERT INTO aufgaben_zuordnung
                (uaz_hauid, uaz_pg) ' .
    'VALUES ("' . $hau_id . '", "4")';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }
// Zurueck zur Liste

header('Location: ir_neu.php?hir_id=' . $hir_id);
exit;
}
echo '</body>';

echo '</html>';
?>