<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (isset($_GET['hgr_id']))
    {
    $grabber_id=$_GET['hgr_id'];
    }

if (isset($_GET['ip']))
    {
    $grabber_ip=$_GET['ip'];
    }

$sql='UPDATE grabber SET hgr_datum_entsperrt = now(), hgr_hmaid_entsperren = "' . $_SESSION['hma_id']
    . '" WHERE hgr_id =' . $grabber_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$sql_log='INSERT INTO eventlog (' .
    'hel_area, ' .
    'hel_type, ' .
    'hel_referer, ' .
    'hel_text) ' .
    'VALUES ( ' .
    '"Grabberlist", ' .
    '"Edit", ' .
    '"' . $_SESSION['hma_login'] . '" ,' .
    '"hat folgende IP entsperrt: ' . $grabber_ip . '")';

if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
    {
    fehler();
    }

// Zurueck zur Liste

header('Location: grabber_uebersicht.php');
exit;
?>