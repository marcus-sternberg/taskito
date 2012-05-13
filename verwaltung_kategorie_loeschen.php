<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$ulk_id=$_GET['ulk_id'];

$sql='UPDATE lizenzkategorie SET ulk_aktiv = 0 WHERE ulk_id = ' . $ulk_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }


// Zurueck zur Liste

header('Location: verwaltung_kategorie.php');
exit;
?>