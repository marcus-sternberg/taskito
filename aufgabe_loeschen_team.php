<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (isset($_REQUEST['hau_id']))
    {
    $task_id=$_REQUEST['hau_id'];
    }

$sql='UPDATE aufgaben SET hau_aktiv=0 WHERE hau_id = ' . $task_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$sql='DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $task_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
    'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
    . '", "Aufgabe wurde gelöscht vom Teamleiter", NOW() )';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }


// Zurueck zur Liste

header('Location: team_uebersicht.php');
exit;
?>