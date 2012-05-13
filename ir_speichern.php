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

if ($_REQUEST['toggle'] == 1)
    {


    // Speichere den Datensatz

    $sql='UPDATE ir_stammdaten SET
            hir_problem = "' . mysql_real_escape_string($Daten['hir_problem']) . '", 
            hir_beschreibung = "' . mysql_real_escape_string($Daten['hir_beschreibung']) . '",    
            hir_auswirkung = "' . $Daten['hir_auswirkung'] . '",    
            hir_analyse = "' . mysql_real_escape_string($Daten['hir_analyse']) . '",    
            hir_massnahme = "' . mysql_real_escape_string($Daten['hir_massnahme']) . '",                                        
            hir_kategorie = "' . $Daten['hir_kategorie'] . '",  
            hir_release = "' . $Daten['hir_release'] . '",  
            hir_lessons = "' . mysql_real_escape_string($Daten['hir_lessons']) . '", 
            hir_prio = "' . $Daten['hir_prio'] . '"
            WHERE hir_id = "' . $Daten['hir_id'] . '"';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    
    $sql=   'UPDATE aufgaben SET
            hau_titel = "IR: '.mysql_real_escape_string($Daten['hir_problem']).'",
            hau_beschreibung = "IR nachverfolgen & schliessen zum Thema: '.mysql_real_escape_string($Daten['hir_beschreibung']).'"
            WHERE hau_ticketnr = "IR ' . $Daten['hir_id'] . '"';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }
    
    
    }
else if ($_REQUEST['toggle'] == 2)
    {

    $sql='UPDATE ir_stammdaten SET
            hir_meeting = "' . $Daten['hir_meeting'] . '", 
            hir_agent = "' . $Daten['hir_agent'] . '",  
            hir_ood = "' . $Daten['hir_ood'] . '" 
            WHERE hir_id = "' . $Daten['hir_id'] . '"';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }

// Zurueck zur Liste

header('Location: ir_neu.php?hir_id=' . $Daten['hir_id']);
exit;
?>