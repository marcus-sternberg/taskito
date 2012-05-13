<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$usb_id=$_GET['usb_id'];
$m=$_GET['m'];

$sql_log='INSERT INTO eventlog (' .
    'hel_area, ' .
    'hel_type, ' .
    'hel_referer, ' .
    'hel_text) ' .
    'VALUES ( ' .
    '"eMailblock", ' .
    '"Delete", ' .
    '"' . $_SESSION['hma_login'] . '" ,' .
    '"hat folgende eMail geloescht: ' . $m . '")';

if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
    {
    fehler();
    }

$sql='DELETE FROM spam_block WHERE usb_id = ' . $usb_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }


// Zurueck zur Liste

header('Location: email_block_uebersicht.php');
exit;
?>