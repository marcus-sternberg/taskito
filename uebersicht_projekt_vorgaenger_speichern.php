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

foreach ($Daten['hau_reihe'] AS $key => $parent)
    {

    // if($parent != NULL)
    // {
    $sql = 'UPDATE aufgaben SET hau_reihe = "' . $parent . '" WHERE hau_id = ' . $key;

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    //   }

    }


// Zurueck zur Liste

header('Location: uebersicht_projekt_vorgaenger.php');
exit;
?>