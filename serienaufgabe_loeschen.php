<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (isset($_GET['utr_id']))
    {
    $utr_id=$_GET['utr_id'];
    }
else
    {
    echo "Kein Datensatz ausgewählt.";
    exit;
    }

$sql='DELETE FROM tracker WHERE utr_id = ' . $utr_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }


// Zurueck zur Liste

header('Location: schreibtisch_meine_auftraege.php');
exit;
?>