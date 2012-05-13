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

if ($Daten['hck_name'] == '')
    {

    include('segment_kopf.php');

    echo '<br><br>Eine Probe muss mindestens einen Namen haben. Bitte geben Sie einen Namen an.';

    echo '<form action="verwaltung_check_neu.php" method="post">';

    echo '&nbsp;&nbsp;<input type="submit" name="fehler" value="OK" class="formularbutton" />';

    echo '</form>';

    exit;
    }

if ($_GET['toggle'] == 1)
    {

    // Speichere Gruppe

    $sql='INSERT INTO checks (hck_aktiv, hck_name, hck_url, hck_ziel, hck_beschreibung) 
        VALUES ("1", "' . $Daten['hck_name'] . '","' . $Daten['hck_url'] . '","' . $Daten['hck_ziel'] . '","'
        . $Daten['hck_beschreibung'] . '")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }
else
    {

    $sql='UPDATE checks SET 
        hck_name = "' . $Daten['hck_name'] . '",
        hck_url = "' . $Daten['hck_url'] . '",         
        hck_ziel = "' . $Daten['hck_ziel'] . '",
        hck_beschreibung = "' . $Daten['hck_beschreibung'] . '"        
        WHERE hck_id = ' . $Daten['hck_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }


// Zurueck zur Liste

header('Location: verwaltung_check.php');
exit;
?>