<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
error_reporting(E_ALL);

ini_set('display_errors', '1');

require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');

$sql_projekt=
    'SELECT hma_name, hpr_start, hpr_id, hpr_titel, hpr_pende FROM projekte LEFT JOIN mitarbeiter on hpr_inhaber = hma_id WHERE hpr_fertig = 0 AND hpr_id > 3 and hpr_aktiv="1" ORDER BY hpr_titel';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_name=mysql_query($sql_projekt, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis_name);

echo
    '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;GANTT-Diagramm für alle in Bearbeitung befindlichen Projekte [Anzahl: '
    . $anzahl
        . ']<br>&nbsp;&nbsp;&nbsp;<span class="text_klein">[<a href="uebersicht_projekt_gesamt.php">zurück zur Projektliste</a>]</span>';

echo '<img src="seg_timeline.php"/>';
?>
