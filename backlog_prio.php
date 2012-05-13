<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

#include('segment_init.php');

$hba_id = $_GET['hba_id'];
$hba_prio = $_GET['hba_prio'];
$xGruppe = $_GET['xGruppe'];
$xProjekt = $_GET['xProjekt'];

$sql='UPDATE backlog SET hba_uprid = "'.$hba_prio.'" WHERE hba_id = ' . $hba_id;

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

// Zurueck zur Liste

header('Location: backlog_liste.php?xGruppe='.$xGruppe.'&xProjekt='.$xProjekt);
exit;

?>