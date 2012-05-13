<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$session_frei=1;

require_once('konfiguration.php');
include('segment_session_pruefung.php');

if (isset($_REQUEST['uir_id']))
    {
    $uir_id=$_REQUEST['uir_id'];
    }

$sql_check='SELECT uir_hirid FROM ir_log WHERE uir_id = ' . $uir_id;

if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
    {
    fehler();
    }

while ($zeile_check=mysql_fetch_array($ergebnis_check))
    {
    $hir_id=$zeile_check['uir_hirid'];
    }

$sql='DELETE FROM ir_log WHERE uir_id = ' . $uir_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

header('Location: ir_neu.php?hir_id=' . $hir_id);
exit;
?>