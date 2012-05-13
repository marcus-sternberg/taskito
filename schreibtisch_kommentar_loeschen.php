<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (isset($_REQUEST['ulo_id']))
    {
    $ulo_id=$_REQUEST['ulo_id'];
    }

$sql_check='SELECT ulo_aufgabe FROM log WHERE ulo_id = ' . $ulo_id;

if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
    {
    fehler();
    }

while ($zeile_check=mysql_fetch_array($ergebnis_check))
    {
    $ulo_aufgabe=$zeile_check['ulo_aufgabe'];
    }

$sql='DELETE FROM log WHERE ulo_id = ' . $ulo_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

header('Location: aufgabe_ansehen.php?hau_id=' . $ulo_aufgabe);
exit;
?>