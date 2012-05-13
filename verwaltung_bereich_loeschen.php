<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$ule_id=$_GET['ule_id'];

$sql='DELETE FROM bereich WHERE ule_id = ' . $ule_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }


// Zurueck zur Liste

header('Location: verwaltung_bereich.php');
exit;
?>