<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

#include('segment_init.php');

$hba_id = $_GET['hba_id'];
$hba_hmaid = $_POST['hba_hmaid'];
$xGruppe = $_GET['xGruppe'];
$xProjekt = $_GET['xProjekt'];

$sql = 'SELECT hma_level FROM mitarbeiter WHERE hma_id = '.$hba_hmaid;

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
            while ($zeile=mysql_fetch_array($ergebnis))
            {
            $hma_level = $zeile['hma_level'];
            }

$sql='UPDATE backlog SET hba_hmaid = "'.$hba_hmaid.'", hba_gruppe = "'.$hma_level.'" WHERE hba_id = ' . $hba_id;

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

// Zurueck zur Liste

header('Location: backlog_liste.php?xGruppe='.$xGruppe.'&xProjekt='.$xProjekt);
exit;

?>