<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$session_frei=1;

require_once('konfiguration.php');
include('segment_session_pruefung.php');

$Daten=array();

foreach ($_POST as $varname => $value)
    {
    $Daten[$varname]=$value;
    }

if (!isset($Daten['uir_hirid']))
    {
    $Daten['uir_hirid']=$_REQUEST['hir_id'];
    }

if ($_REQUEST['toggle'] == 2)
    {
    $sql='UPDATE ir_todo SET uir_fertig = 1 WHERE uir_id = ' . $_REQUEST['uir_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }
else
    {


    // Speichere den Datensatz

    $sql='INSERT INTO ir_todo (' .
        'uir_hirid, ' .
        'uir_todo, ' .
        'uir_wer, ' .
        'uir_prio) ' .
        'VALUES ( ' .
        '"' . $Daten['uir_hirid'] . '", ' .
        '"' . mysql_real_escape_string($Daten['uir_todo']) . '", ' .
        '"' . $Daten['uir_wer'] . '", ' .
        '"' . $Daten['uir_prio'] . '")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

# Aktualisiere Zeitstempel Stammdaten

    // Speichere den Datensatz

    $sql='Update ir_stammdaten SET hir_zeitstempel = NOW() where hir_id = '.$Daten['uir_hirid'] ;
    
     if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    
    }


// Zurueck zur Liste

header('Location: ir_neu.php?hir_id=' . $Daten['uir_hirid']);
exit;
?>