<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');

$hba_id=$_GET['hba_id'];
$xGruppe = $_GET['xGruppe'];
$xProjekt = $_GET['xProjekt'];

// Look up the quote data

$sql_todo='SELECT * FROM backlog WHERE hba_id = ' . $hba_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_todo=mysql_query($sql_todo, $verbindung))
    {
    fehler();
    }

while ($Daten=mysql_fetch_array($ergebnis_todo))
    {
    # First create a task for this TODO

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
        '"' . mysql_real_escape_string($Daten['hba_titel']) . '", ' .
        '"' . mysql_real_escape_string($Daten['hba_titel']) . '", ' .
        'NOW(), ' .
        '"' . $Daten['hba_hmaid'] . '", ' .
        '"' . $Daten['hba_uprid'] . '", ' .
        '"9999-01-01", ' .
        'NOW(), ' .
        '"1", ' .
        '"0", ' .
        '"0", ' .
        '"1", ' .
        '"' . $Daten['hba_hprid'] . '", ' .
        '"18", ' .
        '"1")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $hau_id=mysql_insert_id();

    $sql='INSERT INTO aufgaben_zuordnung
                (uaz_hauid, uaz_pg) ' .
        'VALUES ("' . $hau_id . '", "' . $Daten['hba_gruppe'] . '")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }

    $sql='DELETE FROM backlog WHERE hba_id = '.$hba_id;

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    
    
// Zurueck zur Liste

header('Location: backlog_liste.php?xGruppe='.$xGruppe.'&xProjekt='.$xProjekt);
exit;
?>