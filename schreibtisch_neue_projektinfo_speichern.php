<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$Daten=array();

foreach ($_POST as $varname => $value)
    {

    $Daten[$varname]=$value;
    }

$Daten['upj_datum']=pruefe_datum($Daten['upj_datum']);


// Speichere den Datensatz

$sql='INSERT INTO projekt_info (' .
    'upj_id, ' .
    'upj_datum, ' .
    'upj_text, ' .
    'upj_zeitstempel, ' .
    'upj_aufwand, ' .
    'upj_ma, ' .
    'upj_pid) ' .
    'VALUES ( ' .
    'NULL, ' .
    '"' . $Daten['upj_datum'] . '", ' .
    '"' . mysql_real_escape_string($Daten['upj_text']) . '", ' .
    'NOW(), ' .
    '"' . $Daten['upj_aufwand'] . '", ' .
    '"' . $_SESSION['hma_id'] . '", ' .
    '"' . $Daten['upj_pid'] . '")';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }


// Zurueck zur Liste

header('Location: schreibtisch_projekte.php');
exit;
?>