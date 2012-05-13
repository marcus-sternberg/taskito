<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$sql='SELECT * FROM projekt_info ' .
    'INNER JOIN mitarbeiter ON upj_ma = hma_id ' .
    'WHERE upj_pid = ' . $hpr_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

echo '<table style="border: solid, 1px, black;" class="sample" cellspacing="1" cellpadding="3" width="750">';

echo
    '<tr><td class="tabellen_titel">Date</td><td class="tabellen_titel">Staff</td><td class="tabellen_titel">Text</td><td class="tabellen_titel">Effort [min]</td><tr>';

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<tr>';

    echo '<td class="text_klein" valign="top">' . $zeile['upj_datum'] . '</td>';

    echo '<td class="text_klein" valign="top">' . $zeile['hma_login'] . '</td>';

    echo '<td class="text_klein" valign="top">' . $zeile['upj_text'] . '</td>';

    echo '<td class="text_klein" valign="top">' . $zeile['upj_aufwand'] . '</td>';

    echo '</tr>';
    }

echo '</table>';
?>