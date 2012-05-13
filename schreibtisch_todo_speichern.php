<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$Daten=array();

foreach ($_POST as $varname => $value)
    {

    $Daten[$varname]=$value;
    }

if (isset($Daten['uto_enddatum']))
    {
    $Daten['uto_enddatum']=pruefe_datum1($Daten['uto_enddatum']);
    }

switch ($_GET['toggle'])
    {
    case 1:

        // Speichere Gruppe

        $sql='INSERT INTO todo (uto_hmaid, uto_status, uto_text, uto_enddatum, uto_prio) ' .
            'VALUES ("' . $_SESSION['hma_id'] . '", "0", "' . HTMLSPECIALCHARS($Daten['uto_text']) . '", "'
            . $Daten['uto_enddatum'] . '","' . $Daten['uto_prio'] . '" )';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }
        break;

    case 2:
        $sql='UPDATE todo SET ' .
            'uto_text = "' . HTMLSPECIALCHARS($Daten['uto_text']) . '", ' .
            'uto_enddatum = "' . $Daten['uto_enddatum'] . '", ' .
            'uto_prio = "' . $Daten['uto_prio'] . '" ' .
            'WHERE uto_id = ' . $Daten['uto_id'];

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }
        break;

    case 3:
        foreach ($Daten['done'] AS $key => $content)
            {

            $sql = 'UPDATE todo SET ' .
                'uto_status = "1" ' .
                'WHERE uto_id = ' . $key;

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
            }
    }


// Zurueck zur Liste

header('Location: schreibtisch_todo.php');
exit;
?>