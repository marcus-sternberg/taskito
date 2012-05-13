<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (isset($_GET['uto_id']))
    {
    $uto_id=$_GET['uto_id'];
    }

// Look up the quote data

$sql_todo='SELECT * FROM todo ' .
    'WHERE uto_id = ' . $uto_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_todo=mysql_query($sql_todo, $verbindung))
    {
    fehler();
    }

while ($Daten=mysql_fetch_array($ergebnis_todo))
    {

    # First create a task for this TODO

    if ($Daten['uto_enddatum'] == '9999-01-01')
        {
        $Daten['uto_enddatum']='9999-01-01';
        $Datumstyp=1;
        }
    else
        {
        $Datumstyp=2;
        }

    // Speichere den Datensatz

    $sql='INSERT INTO aufgaben (' .
        'hau_titel, ' .
        'hau_beschreibung, ' .
        'hau_anlage, ' .
        'hau_inhaber, ' .
        'hau_prio, ' .
        'hau_pende, ' .
        'hau_zeitstempel, ' .
        'hau_aktiv, ' .
        'hau_terminaendern, ' .
        'hau_teamleiter, ' .
        'hau_datumstyp, ' .
        'hau_hprid, ' .
        'hau_typ, ' .
        'hau_tl_status) ' .
        'VALUES ( ' .
        '"' . mysql_real_escape_string($Daten['uto_text']) . '", ' .
        '"' . mysql_real_escape_string($Daten['uto_text']) . '", ' .
        'NOW(), ' .
        '"' . $_SESSION['hma_id'] . '", ' .
        '"' . $Daten['uto_prio'] . '", ' .
        '"' . $Daten['uto_enddatum'] . '", ' .
        'NOW(), ' .
        '"1", ' .
        '"0", ' .
        '"999", ' .
        '"' . $Datumstyp . '", ' .
        '"3", ' .
        '"5", ' .
        '"1")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $hau_id=mysql_insert_id();

    $sql='INSERT INTO aufgaben_mitarbeiter (' .
        'uau_hmaid, ' .
        'uau_hauid, ' .
        'uau_status, ' .
        'uau_prio, ' .
        'uau_stopp, ' .
        'uau_tende, ' .
        'uau_zeitstempel, ' .
        'uau_ma_status) ' .
        'VALUES ( ' .
        '"' . $_SESSION['hma_id'] . '", ' .
        '"' . $hau_id . '", ' .
        '"0", ' .
        '"99", ' .
        '"0", ' .
        '"' . $Daten['uto_enddatum'] . '", ' .
        'NOW(), ' .
        '"1")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $sql='INSERT INTO aufgaben_zuordnung
                (uaz_hauid, uaz_pg, uaz_pba) ' .
        'VALUES ("' . $hau_id . '", "' . $_SESSION['hma_level'] . '", "' . $_SESSION['hma_id'] . '" )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
        . '", "Todo changed in Task", NOW() )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $sql='DELETE FROM todo WHERE uto_id = ' . $uto_id;

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }

// Zurueck zur Liste

header('Location: schreibtisch_aufgabe_aendern.php?hau_id=' . $hau_id);
exit;
?>