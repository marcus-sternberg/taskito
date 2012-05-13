<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################

require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$task_id=$_GET['hau_id'];

$sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
    'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
    . '", "Die Aufgabe wurde erneut eröffnet", NOW() )';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$sql='UPDATE aufgaben SET hau_tl_status = "0", hau_teamleiter = "0", hau_terminaendern = "0", hau_anlage = "'
    . date("Y-m-d H:i") . '" WHERE hau_id = "' . $task_id . '"';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$sql='DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $task_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

// Zurueck zur Liste

header('Location: schreibtisch_meine_auftraege.php');
exit;
?>