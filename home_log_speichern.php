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

// Umwandlung des Datumsfeldes in DATETIME

$DatumZeit=explode(" ", $Daten['ulo_datum']);
$Datum=explode(".", $DatumZeit[0]);
$Zeit=explode(":", $DatumZeit[1]);

if (count($Zeit) < 2)
    {
    $Zeit[0]='12';
    $Zeit[1]='00';
    }
else if ($Zeit[1] == '' OR $Zeit[0] == '')
    {
    $Zeit[0]='12';
    $Zeit[1]='00';
    }

if (count($Datum) < 3)
    {

    $heute=date("d.m.Y");
    $Datum=explode(".", $heute);
    }
else if (!checkdate($Datum[1], $Datum[0], $Datum[2]))
    {

    $heute=date("d.m.Y");
    $Datum=explode(".", $heute);
    }

$Daten['ulo_datum']=date("Y-m-d H:i:s", mktime($Zeit[0], $Zeit[1], 0, $Datum[1], $Datum[0], $Datum[2]));

$sql='INSERT INTO log (' .
    'ulo_aufgabe, ' .
    'ulo_text, ' .
    'ulo_ma, ' .
    'ulo_extra, ' .
    'ulo_datum) ' .
    'VALUES ( ' .
    '"' . $Daten['ulo_aufgabe'] . '", ' .
    '"' . mysql_real_escape_string($Daten['ulo_text']) . '", ' .
    '"' . $Daten['ulo_ma'] . '", ' .
    '"1", ' .
    '"' . $Daten['ulo_datum'] . '")';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

# define connection parameters for TOM

$sql_log='INSERT INTO eventlog (hel_area, hel_type, hel_referer, hel_text) ' .
    'VALUES ("EXTERN", "Logeintrag", "' . $_SESSION['hma_login'] . '", "Eintrag im Activitylog erzeugt.")';

if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
    {
    fehler();
    }

# Konnektiere Dich auf die ACTIVITY-LOG-Datenbank

$rechnername="bersql03";
$datenbankname="activitylog";
$benutzername="activitylog";
$passwort="activitylog";

// Verbindung zum Host oeffnen
if (!$verbindung=mysql_connect($rechnername, $benutzername, $passwort))
    die("Konnte keine Verbindung herstellen !</p>");

// Datenbank auswaehlen
if (!(mysql_select_db($datenbankname, $verbindung)))
    fehler();

# Frage ID des aktuellen Benutzers ab

$sql='SELECT id, firstname, lastname FROM users ' .
    'ORDER BY lastname';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    if (trim($zeile['firstname']) == trim($_SESSION['hma_vorname']) AND trim($zeile['lastname'])
        == trim($_SESSION['hma_name']))
        {
        $id_nutzer=$zeile['id'];
        }
    }

# Frage Plattform ab

$id_plattform=1;

# Frage Aktivität ab

$id_activity=7;

#Frage Bereich ab

$id_bereich=5;


// Datenbank auswaehlen
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
    '"' . $id_nutzer . '", ' .
    '"' . $id_plattform . '", ' .
    '"' . $id_bereich . '", ' .
    '"' . $id_activity . '", ' .
    '"' . mysql_real_escape_string($Daten['ulo_text']) . '", ' .
    'NOW())';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

mysql_close($verbindung);

header('Location: home.php');
exit;
?>