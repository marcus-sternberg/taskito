<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$name=$_GET['name'];
$task=$_GET['task_id'];
$pfad=$_GET['pfad'];

$sql = 'DELETE FROM anlagen WHERE uan_hauid = '.$task.' AND uan_name = "'.$name.'"';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

if ($handle=opendir($pfad))
    {
    if (is_file($pfad . $name))
        {
        unlink($pfad . $name);
        }
    }


    
// Zurueck zur Liste

header('Location: aufgabe_ansehen.php?hau_id=' . $task);
exit;
?>