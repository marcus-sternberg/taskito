<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
include('segment_session_pruefung.php');

include('segment_init.php');
include('konfiguration.php');

$Daten=array();

foreach ($_POST as $varname => $value)
    {

    $Daten[$varname]=$value;
    }

$rechnername="bersql03";
$datenbankname="taskscout24";
$benutzername="taskscout24";
$passwort="taskscout24";

// Verbindung zum Host öffnen
if (!$verbindung=mysql_connect($rechnername, $benutzername, $passwort))
    die("Konnte keine Verbindung herstellen !</p>\n");

// Datenbank auswaehlen
if (!(mysql_select_db($datenbankname, $verbindung)))
    fehler();

$sql_log='INSERT INTO eventlog (hel_area, hel_type, hel_referer, hel_text) ' .
    'VALUES ("EXTERN", "Logeintrag", "' . $_SESSION['hma_login'] . '", "Eintrag im Activitylog erzeugt.")';

if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
    {
    fehler();
    }

mysql_close($verbindung);

# Konnektiere Dich auf die ACTIVITY-LOG-Datenbank

$rechnername="bersql03";
$datenbankname="activitylog";
$benutzername="activitylog";
$passwort="activitylog";

// Verbindung zum Host öffnen
if (!$verbindung=mysql_connect($rechnername, $benutzername, $passwort))
    die("Konnte keine Verbindung herstellen !</p>\n");

// Datenbank auswählen
if (!(mysql_select_db($datenbankname, $verbindung)))
    fehler();

$sql='INSERT INTO executed_activities (' .
    'user_id, ' .
    'environment_id, ' .
    'area_id, ' .
    'activity_id, ' .
    'description, ' .
    'created_at) ' .
    'VALUES ( ' .
    '"' . $Daten['ac_user'] . '", ' .
    '"' . $Daten['ac_environment'] . '", ' .
    '"' . $Daten['ac_area'] . '", ' .
    '"' . $Daten['ac_activity'] . '", ' .
    '"' . htmlspecialchars($Daten['ac_eintrag']) . '", ' .
    'NOW())';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }



 // Zurueck zur Liste

# define connection parameters for TOM

$rechnername="bersql03";
$datenbankname="taskscout24";
$benutzername="taskscout24";
$passwort="taskscout24";

// Verbindung zum Host oeffnen
if (!$verbindung=mysql_connect($rechnername, $benutzername, $passwort))
    die("Konnte keine Verbindung herstellen !</p>\n");

// Datenbank auswaehlen
if (!(mysql_select_db($datenbankname, $verbindung)))
    fehler();
require_once('segment_kopf.php');

echo '<br>';

echo '<img src="bilder/block.gif">&nbsp;Activityeintrag wurde gespeichert.';

if(ISSET($DATEN['status']))
{
    header('Location: status_systeme.php?xSystem='.$Daten['xSystem']);
    exit;
}

echo '<meta http-equiv="refresh" content="1;url=aufgabe_ansehen.php?hau_id=' . $Daten['hau_id'] . '">';
exit;
break;
?>