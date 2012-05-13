<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

echo '<br><table class="element" cellpadding = "5">';

echo '<tr>';

echo '<td class="text_mitte">';

echo '<img src="bilder/block.gif">&nbsp;Changes';

echo '</td>';

echo '<td class="text_mitte">';

echo ' | ';

echo '</td>';

$sql='SELECT * FROM version_log ' .
    'WHERE xStatus=3 ' .
    'ORDER BY xDate DESC';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<td class="text_mitte">';

echo '<a href="control_uebersicht.php">done (' . $anzahl . ')</a>';

echo '</td>';

echo '<td class="text_mitte">';

echo ' | ';

echo '</td>';

$sql='SELECT * FROM version_log ' .
    'WHERE xStatus=2 ' .
    'ORDER BY xDate DESC';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<td class="text_mitte">';

echo '<a href="control_uebersicht_scheduled.php">scheduled (' . $anzahl . ')</a>';

echo '</td>';

echo '<td class="text_mitte">';

echo ' | ';

echo '</td>';

$sql='SELECT * FROM version_log ' .
    'WHERE xStatus=1 ' .
    'ORDER BY xDate DESC';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<td class="text_mitte">';

echo '<a href="control_uebersicht_requested.php">requested (' . $anzahl . ')</a>';

echo '</td>';

echo '<td class="text_mitte">';

echo ' | ';

echo '</td>';

$sql='SELECT * FROM version_log ' .
    'WHERE xStatus=4 ' .
    'ORDER BY xDate DESC';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<td class="text_mitte">';

echo '<a href="control_uebersicht_rejected.php">rejected (' . $anzahl . ')</a>';

echo '</td>';

echo '</tr>';

echo '</table>';

echo '<br><br><br>';

echo '<table border=0 width=700>';

echo '<tr>';

echo '<td>&nbsp;&nbsp;</td>';

echo '<td colspan="5">';

echo '<span class="box">The following changes are filed:</span></td>';

echo '<br><br></tr><tr>';

echo '<td>&nbsp;&nbsp;</td><td>';

$sql='SELECT * FROM version_log ' .
    'WHERE xStatus=4 ' .
    'ORDER BY xDate DESC';

$anzeigefelder=array
    (
    'Date' => 'xDate',
    'Name' => 'xTitle',
    'Status' => 'xStatus'
    );

if ($_SESSION['hma_level'] == 1)
    {
    $iconzahl=1;
    $icons=array(array
        (
        "inhalt" => "change",
        "bild" => "icon_aendern.gif",
        "link" => "control_aendern.php"
        ));

    $link_id='ID';
    $link_neu='control_neuer_aufwand.php';
    }
else
    {
    $link_id='ID';
    $iconzahl=1;
    $icons=array(array
        (
        "inhalt" => "change",
        "bild" => "icon_anschauen.gif",
        "link" => "control_anzeigen.php"
        ));
    }

$link_neu='control_neuer_aufwand.php';

include('segment_liste_verwaltung.php');

echo '</td></tr></table>';

include('segment_fuss.php');
?>