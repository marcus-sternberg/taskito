<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$session_frei=1;

require_once('konfiguration.php');
include('segment_session_pruefung.php');

$status=$_GET['status'];
$hir_id=$_GET['hir_id'];

$sql='UPDATE ir_stammdaten SET  hir_status = "' . $status . '" WHERE hir_id = "' . $hir_id . '"';

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
    '"IR", ' .
    '"Edit", ' .
    '"' . $_SESSION['hma_login'] . '" ,' .
    '"hat für IR Nummer: ' . $hir_id . ' den Status auf ' . $status . ' geändert.")';

if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
    {
    fehler();
    }

if ($status == 5)
    {
    $sql='DELETE FROM ir_sperre WHERE uisp_hirid = "' . $hir_id . '"';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }


    // Zurueck zur Liste

    header('Location: ir_liste.php');
    exit;
    }
else
    {

    // Zurueck zur Liste

    header('Location: ir_neu.php?hir_id=' . $hir_id);
    exit;
    }
?>