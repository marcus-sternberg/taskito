<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$session_frei=1;

require_once('konfiguration.php');
include('segment_session_pruefung.php');

$hir_id=$_GET['hir_id'];

$sql='DELETE FROM ir_sperre WHERE uisp_hirid = "' . $hir_id . '"';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }


// Zurueck zur Liste

header('Location: ir_liste.php');
exit;
?>