<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

$ID=$_REQUEST['ID'];

$sql='SELECT * FROM tipps ' .
    'WHERE uti_id = ' . $ID;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<br><br><br>';

    echo
        '<span class="box">Tipp anschauen:</span><br><a href="tipps_uebersicht.php">&nbsp;&nbsp;&nbsp;zurück zur Liste</a><br><br>';

    echo '<table border=0 width=500>';

    echo '<tr>';

    echo '<td>&nbsp;&nbsp;';

    echo '</td>';

    echo '<td valign="top">Tipp</td>';

    echo '<td>&nbsp;&nbsp;</td>';

    echo '<td>' . $zeile['uti_tipp'] . '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td>&nbsp;&nbsp;';

    echo '</td>';

    echo '<td valign="top">Beschreibung</td>';

    echo '<td>&nbsp;&nbsp;</td>';

    echo '<td>' . $zeile['uti_beschreibung'] . '</td>';

    echo '</tr>';

    echo '</table>';
    }
?>