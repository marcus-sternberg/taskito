<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (isset($_REQUEST['hau_id']))
    {
    $hau_id=$_REQUEST['hau_id'];
    }

$sql='UPDATE aufgaben_mitarbeiter SET uau_ma_status = 0 WHERE uau_hauid = ' . $hau_id.' AND uau_hmaid = '.$_SESSION['hma_id'];

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

header('Location: schreibtisch_meine_aufgaben.php');
exit;
?>