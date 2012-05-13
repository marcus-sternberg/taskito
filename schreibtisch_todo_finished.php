<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

echo '<br><br><br>';

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Enter a new personal ToDO:<br><br>';

echo '<form action="schreibtisch_todo_speichern.php?toggle=1" method="post">';

echo '<table class="element">';

echo '<tr>';

echo '<td class="text">New ToDo:</td>';

echo '</tr>';

echo '<tr>';

echo '<td class="text">ToDo</td>';

echo '<td class="text">due until</td>';

echo '<td class="text">Prio</td>';

echo '</tr>';

echo '<tr>';

echo '<td class="text"><textarea name="uto_text" cols="50" rows="2"></textarea></td>';

echo '<td class="text"><input type="text" name="uto_enddatum" width="20px"></td>';

echo '<td class="text">';

echo '<select size="1" name="uto_prio">';
$sql='SELECT upr_nummer, upr_name FROM prioritaet ' .
    'ORDER BY upr_sort';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<option value="' . $zeile['upr_nummer'] . '"><span class="text">' . $zeile['upr_name'] . '</span></option>';
    }

echo '</select>';

echo '</td>';

echo '</tr>';

echo '<tr>';

echo
    '<td colspan="4" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Save ToDO" class="formularbutton" /></td></tr>';

echo '</form>';

echo '</table>';

echo '<br><br>';

echo '<a href="schreibtisch_todo.php">current</a> | finished (Last 50)';

echo '<br><br>';

echo '<form action="schreibtisch_todo_speichern.php?toggle=3" method="post">';

echo '<table class="element" width="900">';

echo '<tr>';

echo '<td class="text">ToDo</td>';

echo '<td class="text">due until</td>';

echo '<td class="text">Prio</td>';

echo '<td class="text">closed</td>';

echo '</tr>';

$sql='SELECT * FROM todo ' .
    'LEFT JOIN prioritaet ON upr_nummer = uto_prio ' .
    'WHERE uto_hmaid = ' . $_SESSION['hma_id'] . ' AND uto_status = 1 ORDER BY uto_zeitstempel DESC LIMIT 50';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<tr>';

    echo '<td class="text" valign="top">' . $zeile['uto_text'] . '</td>';

    echo '<td class="text" valign="top">' . datum_wandeln_useu($zeile['uto_enddatum']) . '</td>';

    echo '<td class="text" valign="top">' . $zeile['upr_name'] . '</td>';

    echo '<td class="text" valign="top">' . $zeile['uto_zeitstempel'] . '</td>';

    echo '<td class="text_klein" valign="top"><a href="schreibtisch_todo_loeschen.php?uto_id=' . $zeile['uto_id']
        . '" onclick="return window.confirm(\'Delete ToDo?\');"><img src="bilder/icon_loeschen.gif" border="0" alt="delete ToDo" title="delete ToDo"></a></td>';

    echo '</tr>';
    }

echo '<tr>';

echo '</table>';

echo '<input type="hidden" name="uto_id" value="' . $zeile['uto_id'] . '">';

echo '</form>';

include('segment_fuss.php');
?>