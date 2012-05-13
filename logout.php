<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

error_reporting(E_ALL);
session_start();

$sql_log='INSERT INTO eventlog (' .
    'hel_area, ' .
    'hel_type, ' .
    'hel_referer, ' .
    'hel_text) ' .
    'VALUES ( ' .
    '"Account", ' .
    '"Logout", ' .
    '"' . $_SESSION['hma_login'] . '" ,' .
    '"has logged out")';

if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
    {
    fehler();
    }

unset($_SESSION['hma_id']);
unset($_SESSION['hma_login']);

session_destroy();
header("Location: index.php");
?>