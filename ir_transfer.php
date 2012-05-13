<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$session_frei=1;

require_once('konfiguration.php');
include('segment_session_pruefung.php');

$uir_id=$_GET['uir_id'];

// Look up the quote data

$sql_todo='SELECT * FROM ir_todo LEFT JOIN ir_stammdaten ON hir_id = uir_hirid ' .
    'WHERE uir_id = ' . $uir_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_todo=mysql_query($sql_todo, $verbindung))
    {
    fehler();
    }

while ($Daten=mysql_fetch_array($ergebnis_todo))
    {

    $laenge = strlen($Daten['uir_hirid']) + 3;


    // Pruefe, ob bereits ein Projekt für den IR angelegt wurde:

    $sql_check=
        'SELECT hpr_titel, hpr_id FROM projekte WHERE LEFT(hpr_titel,' . $laenge . ') = "IR:' . $Daten['uir_hirid']
        . '"';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_check=mysql_query($sql_check, $verbindung))
        {
        fehler();
        }

    if (mysql_numrows($ergebnis_check) == 0) // Es gibt noch keinen IR Report als Projekt
        {
        $sql_pro='INSERT INTO projekte (
                    hpr_titel,
                    hpr_beschreibung,
                    hpr_inhaber,
                    hpr_start,
                    hpr_pende,
                    hpr_aktiv,
                    hpr_sort)
                    VALUES (
                    "IR:' . $Daten['uir_hirid'] . '",
                    "Aufgaben aus dem Incident Report ' . $Daten['uir_hirid'] . '",
                    "' . $Daten['hir_agent'] . '",
                    "' . date("Y-m-d") . '",
                    "' . date("Y-m-d", strtotime("+1 day", strtotime(date("Y-m-d")))) . '",
                    "1",
                    "99")';

        if (!$ergebnis_pro=mysql_query($sql_pro, $verbindung))
            {
            fehler();
            }

        $hpr_id=mysql_insert_id();
        }
    else // es gibt einen, ermittle die hpr-Nummer
        {
        while ($zeile_hpr=mysql_fetch_array($ergebnis_check))
            {
            $hpr_id=$zeile_hpr['hpr_id'];
            }
        }


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
        '"' . mysql_real_escape_string($Daten['uir_todo']) . '", ' .
        '"' . mysql_real_escape_string($Daten['uir_todo']) . '", ' .
        'NOW(), ' .
        '"' . $Daten['hir_agent'] . '", ' .
        '"' . $Daten['uir_prio'] . '", ' .
        '"9999-01-01", ' .
        'NOW(), ' .
        '"1", ' .
        '"0", ' .
        '"0", ' .
        '"1", ' .
        '"' . $hpr_id . '", ' .
        '"4", ' .
        '"1")';

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
    }

// Zurueck zur Liste

header('Location: schreibtisch_aufgabe_aendern.php?hau_id=' . $hau_id);
exit;
?>