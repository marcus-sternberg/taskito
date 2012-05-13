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

if (isset($_GET['sortierschluessel']))
    {
    $sortierschluessel=$_GET['sortierschluessel'];
    }
else
    {
    $sortierschluessel='uto_prio DESC, uto_enddatum DESC';
    }

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

echo
    "<td><input type='text' name='uto_enddatum' style='width:100px;' id='uto_enddatum'><img src='bilder/date_go.gif' alt='Anklicken für Kalenderansicht' onclick='kalender(document.getElementById(\"uto_enddatum\"));'/>";

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

echo 'current | <a href="schreibtisch_todo_finished.php">finished</a>';

echo '<br><br>';

echo '<form action="schreibtisch_todo_speichern.php?toggle=3" method="post">';

echo '<table class="element" width="900">';

echo '<tr>';

echo '<td class="text">done</td>';

echo '<td class="text"><a href="' . $_SERVER['PHP_SELF'] . '?sortierschluessel=uto_text">ToDo</a></td>';

echo '<td class="text"><a href="' . $_SERVER['PHP_SELF'] . '?sortierschluessel=uto_enddatum">due until</a></td>';

echo '<td class="text"><a href="' . $_SERVER['PHP_SELF'] . '?sortierschluessel=upr_sort">Prio</td>';

echo '</tr>';

$sql='SELECT * FROM todo ' .
    'LEFT JOIN prioritaet ON upr_nummer = uto_prio ' .
    'WHERE uto_hmaid = ' . $_SESSION['hma_id'] . ' AND uto_status = 0 ORDER BY ' . $sortierschluessel;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<tr>';

    echo '<td class="text" valign="top"><input type="checkbox" name="done[' . $zeile['uto_id'] . ']"></td>';

    echo '<td class="text" valign="top">' . ($zeile['uto_text']) . '</td>';

    echo '<td class="text" valign="top">' . datum_anzeigen($zeile['uto_enddatum']) . '</td>';

    echo '<td class="text" valign="top">' . ($zeile['upr_name']) . '</td>';

    echo '<td class="text_klein" valign="top"><a href="schreibtisch_todo_aendern.php?uto_id=' . $zeile['uto_id']
        . '"><img src="bilder/icon_aendern.gif" border="0" alt="change ToDo" title="change ToDo"></a></td>';

    echo '<td class="text_klein" valign="top"><a href="schreibtisch_todo_loeschen.php?uto_id=' . $zeile['uto_id']
        . '" onclick="return window.confirm(\'Delete ToDo?\');"><img src="bilder/icon_loeschen.gif" border="0" alt="delete ToDo" title="delete ToDo"></a></td>';

    echo '<td class="text_klein" valign="top"><a href="schreibtisch_todo_transfer.php?uto_id=' . $zeile['uto_id']
        . '"><img src="bilder/icon_todo_transfer.png" border="0" alt="Make Task from ToDo" title="Make Task from ToDo"></a></td>';

    echo '</tr>';
    }

echo '<tr>';

echo
    '<td colspan="7" style="text-align:left; padding-top:10px;"><input type="submit" name="speichern" value="Mark ToDo as Done" class="formularbutton" /></td></tr>';

echo '</table>';

echo '<input type="hidden" name="uto_id" value="' . $zeile['uto_id'] . '">';

echo '</form>';

include('segment_fuss.php');

echo
    '<div id="bn_frame" style="position:absolute; display:none; height:198px; width:205px; background-color:#ced7d6; overflow:hidden;">';

echo
    '<iframe src="bytecal.php" style="width:208px; margin-left:-1px; border:0px; height:202px; background-color:#ced7d6; overflow:hidden;" border="0"></iframe>';

echo '</div>';
?>