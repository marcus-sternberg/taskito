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

if ($Daten['ule_name'] == '')
    {

    include('segment_kopf.php');

    echo '<br><br>A Sector needs a name - Please enter a name.';

    echo '<form action="verwaltung_bereich_neu.php" method="post">';

    echo '&nbsp;&nbsp;<input type="submit" name="fehler" value="OK" class="formularbutton" />';

    echo '</form>';

    exit;
    }

if ($_GET['toggle'] == 1)
    {

    // Speichere Gruppe

    $sql='INSERT INTO bereich (ule_id, ule_name) VALUES (NULL, "' . $Daten['ule_name'] . '")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }
else
    {

    $sql='UPDATE bereich SET ule_name = "' . $Daten['ule_name'] . '" WHERE ule_id = ' . $Daten['ule_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }


// Zurueck zur Liste

header('Location: verwaltung_bereich.php');
exit;
?>