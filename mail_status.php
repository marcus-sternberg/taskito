<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$uti_id = $_GET['uti_id'];
$uti_status = $_GET['status'];
$hau_id = $_GET['hau_id'];

    $sql='UPDATE ticket_info SET uti_aktiv = '.$uti_status.' WHERE uti_id = '.$uti_id;

   
    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    header('Location: aufgabe_ansehen.php?hau_id=' . $hau_id);
    exit;

include('segment_fuss.php');
?>
