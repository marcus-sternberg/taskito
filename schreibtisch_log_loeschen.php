<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (isset($_REQUEST['ulok_id']))
    {
    $ulok_id=$_REQUEST['ulok_id'];
    }

$sql_aufgabe='SELECT * FROM log_kunde WHERE ulok_id = ' . $ulok_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_aufgabe=mysql_query($sql_aufgabe, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis_aufgabe))
    {

    $Daten=($zeile['ulok_text']);
    }

$sql_log='INSERT INTO eventlog (' .
    'hel_area, ' .
    'hel_type, ' .
    'hel_referer, ' .
    'hel_text) ' .
    'VALUES ( ' .
    '"Whiteboard", ' .
    '"Edit", ' .
    '"' . $_SESSION['hma_login'] . '" ,' .
    '"hat folgenden Eintrag geloescht: ' . mysql_real_escape_string($Daten) . '")';

if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
    {
    fehler();
    }

$sql='DELETE FROM log_kunde WHERE ulok_id = ' . $ulok_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

header('Location: kunden_logfile.php');
exit;
?>