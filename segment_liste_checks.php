<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################

// Bestimme Anzahl der Spalten der Tabelle
$col=count($anzeigefelder) + $iconzahl;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }


// Beginne mit Tabellenausgabe
echo '<table style="border: solid, 1px, black;" class="sample" cellspacing="1" cellpadding="3" width="700">';

echo '</tr>';

echo '<tr><td class="text_mitte_normal" colspan="' . $col . '">';

echo 'Neuen Check anlegen</td><td align="center"><a href="' . $link_neu
    . '"><img src="bilder/icon_neu.gif" border="0" alt="Neuen Check anlegen" title="Neuen Check anlegen"></a>';

echo '</tr>';

foreach ($anzeigefelder as $bezeichner => $inhalt)
    {
    echo '<td class="tabellen_titel" valign="top"><span class="xnormal_sort">' . $bezeichner . '</span></td>';
    }

for ($count=1; $count < ($iconzahl + 1); $count++)
    {
    echo '<td class="tabellen_titel">&nbsp;</td>';
    }

echo '</tr>';

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {

    // Beginne Datenausgabe
    echo '<tr>';

    foreach ($anzeigefelder as $bezeichner => $inhalt)
        {
        echo '<td class="text_normal" align="top">' . $zeile[$inhalt] . '</td>';
        }

    foreach ($icons as $icon)
        {
        echo '<td align="center" ><a href="' . $icon['link'] . '?' . $link_id . '=' . $zeile[$link_id]
            . '"><img src="bilder/' . $icon['bild'] . '" border="0" alt="' . $icon['inhalt'] . '" title="'
            . $icon['inhalt'] . '"></a></td>';
        }

    echo '</tr>';
    }

echo '</table>';
?>