<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$Daten=array();

if (isset($_GET['toggle']))
    {
    $toggle=$_GET['toggle'];
    }
else
    {
    $toggle=1;
    }

if (isset($_POST['hpr_id']))
    {
    $hpr_id=$_POST['hpr_id'];
    }

foreach ($_POST as $varname => $value)
    {

    $Daten[$varname]=$value;
    }
$anzahl_fehler=0;

if (empty($Daten['hpr_start']))
    {
    $anzahl_fehler++;
    $fehlermeldung['hpr_start']='You need to enter a date!';
    }
else
    {

    list($anzahl_fehler, $fehlermeldung['hpr_start'])=datum_check($Daten['hpr_start'], 'hpr_start', $anzahl_fehler);
    }

if (empty($Daten['hpr_pende']))
    {
    $anzahl_fehler++;
    $fehlermeldung['hpr_pende']='You need to enter a date!';
    }
else
    {

    list($anzahl_fehler, $fehlermeldung['hpr_pende'])=datum_check($Daten['hpr_pende'], 'hpr_pende', $anzahl_fehler);
    }

if ($Daten['hpr_titel'] == '')
    {
    $anzahl_fehler++;
    $fehlermeldung['hpr_titel']='Please enter a title for this Project!';
    }
else
    {
    $fehlermeldung['hpr_titel']='';
    }

if ($anzahl_fehler > 0)
    {

    include('segment_kopf.php');

    echo '<br><br>Please repeat the input - there were errors:<br>';

    echo '<br>' . $fehlermeldung['hpr_titel'];

    echo '<br>' . $fehlermeldung['hpr_pende'] . '<br><br>';

    echo '<br>' . $fehlermeldung['hpr_start'] . '<br><br>';

    echo '<form action="schreibtisch_projekte_ansehen.php?hpr_id=' . $hpr_id . '" method="post">';

    echo '&nbsp;&nbsp;<input type="submit" name="fehler" value="OK" class="formularbutton" />';

    echo '</form>';

    exit;
    }

$Daten['hpr_start']=pruefe_datum($Daten['hpr_start']);
$Daten['hpr_pende']=pruefe_datum($Daten['hpr_pende']);

if ($toggle == 1)
    {

    $sql='INSERT INTO projekte (' .
        'hpr_id, ' .
        'hpr_titel, ' .
        'hpr_beschreibung, ' .
        'hpr_inhaber, ' .
        'hpr_start, ' .
        'hpr_pende, ' .
        'hpr_zeitstempel, ' .
        'hpr_aktiv) ' .
        'VALUES ( ' .
        'NULL, ' .
        '"' . mysql_real_escape_string($Daten['hpr_titel']) . '", ' .
        '"' . mysql_real_escape_string($Daten['hpr_beschreibung']) . '", ' .
        '"' . $_SESSION['hma_id'] . '", ' .
        '"' . $Daten['hpr_start'] . '", ' .
        '"' . $Daten['hpr_pende'] . '", ' .
        'NOW(), ' .
        '"1")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }
else
    {

    $sql='UPDATE projekte SET ' .
        'hpr_titel = "' . mysql_real_escape_string($Daten['hpr_titel']) . '", ' .
        'hpr_beschreibung = "' . mysql_real_escape_string($Daten['hpr_beschreibung']) . '", ' .
        'hpr_start = "' . $Daten['hpr_start'] . '", ' .
        'hpr_pende = "' . $Daten['hpr_pende'] . '", ' .
        'hpr_zeitstempel = NOW() ' .
        'WHERE hpr_id = ' . $Daten['hpr_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }

// Zurueck zur Liste

header('Location: schreibtisch_projekte.php');
exit;
?>