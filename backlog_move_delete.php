<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

#include('segment_init.php');

$hba_id = $_GET['hba_id'];
$xGruppe = $_GET['xGruppe'];
$xProjekt = $_GET['xProjekt'];
$xStatus = $_GET['hba_status'];

$sql='DELETE FROM backlog WHERE hba_id = ' . $hba_id;

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

// Zurueck zur Liste

header('Location: backlog_liste.php?xGruppe='.$xGruppe.'&xProjekt='.$xProjekt);
exit;
?>