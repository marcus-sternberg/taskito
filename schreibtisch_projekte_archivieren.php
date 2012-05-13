<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (isset($_GET['hpr_id']))
    {
    $hpr_id=$_GET['hpr_id'];
    }

if (isset($_GET['toggle']))
    {
    $toggle=$_GET['toggle'];
    }
else
    {
    $toggle='';
    }


// Prüfe, ob noch offene Aufgaben vorliegen

$sql='SELECT DISTINCT hau_id, hau_titel FROM aufgaben ' .
    'LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid ' .
    'WHERE hau_hprid = ' . $_GET['hpr_id'] . ' AND hau_aktiv = 1 ' .
    'AND hau_abschluss =0';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis) > 0)
    {
    require_once('segment_kopf.php');

    echo '<br><br>This Project cant be filed as we have still the following open tasks:<br><br>';

    $sql=$sql_standard .
        'WHERE hau_abschluss = 0 AND (uau_ma_status IS NULL OR uau_ma_status <2) AND hau_aktiv = 1 '
        . $_SESSION['filterstring'] . ' ' .
        'AND hau_hprid = ' . $_GET['hpr_id'] . ' GROUP BY hau_id ' .
        'ORDER BY hau_anlage DESC, hau_zeitstempel DESC';

    $anzeigefelder=$anzeige_pool;
    $aktionenzahl=1;
    $infozahl=1;
    $aktionen[]=array
        (
        "inhalt" => "view task",
        "bild" => "icon_anschauen.gif",
        "link" => "aufgabe_ansehen.php"
        );

    $infos=array('date' => 'hau_datumstyp');

    include('segment_liste.php');

    exit;
    }

$sql='UPDATE projekte SET hpr_fertig = "1", hpr_schluss=NOW() WHERE hpr_id = "' . $hpr_id . '"';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

if ($toggle != 'liste')
    {

    // Zurueck zur Liste

    header('Location: schreibtisch_projekte.php');
    exit;
    }
else
    {

    header('Location: uebersicht_projekt_gesamt.php');
    exit;
    }
?>