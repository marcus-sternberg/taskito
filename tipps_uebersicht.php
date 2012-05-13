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

echo '<img src="bilder/block.gif">&nbsp;Tipps';

echo '</td>';

echo '</tr>';

echo '</table>';

echo '<br><br>';

$sql='SELECT uti_thema FROM tipps ' .
    'ORDER BY uti_thema';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<table border=0 class="element" width=700>';

    echo '<tr><td>' . $zeile['uti_thema'] . '</td></tr>';

    $sql_tipp='SELECT * FROM tipps WHERE uti_thema = "' . $zeile['uti_thema'] . '" ORDER BY uti_tipp';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_tipp=mysql_query($sql_tipp, $verbindung))
        {
        fehler();
        }

    while ($zeile_tipp=mysql_fetch_array($ergebnis_tipp))
        {
        echo '<tr><td><a href="tipp_anzeigen.php?ID=' . $zeile_tipp['uti_id'] . '">' . $zeile_tipp['uti_tipp']
            . '</a></td></tr>';
        }

    echo '</table>';
    }

echo '</td></tr></table>';

include('segment_fuss.php');
?>