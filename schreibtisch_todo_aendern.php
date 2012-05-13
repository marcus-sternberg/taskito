<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

if (isset($_GET['uto_id']))
    {
    $uto_id=$_GET['uto_id'];
    }

$sql='SELECT * FROM todo WHERE uto_status=0 AND uto_id = ' . $uto_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

echo '<br><br><br>';

echo '<span class="box">Change a ToDo:</span><br><br>';

echo '<form action="schreibtisch_todo_speichern.php?toggle=2" method="post">';

echo '<table border=0 width=300>';


// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {

    $zeile['uto_enddatum'] = datum_wandeln_useu($zeile['uto_enddatum']);

    echo '<tr>';

    echo '<td>&nbsp;&nbsp;';

    echo '</td>';

    echo '<td class="text_klein">ToDo: </td>';

    echo '<td class="text"><textarea name="uto_text" cols="50" rows="2">' . $zeile['uto_text'] . '</textarea></td>';

    echo '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td>&nbsp;&nbsp;';

    echo '</td>';

    echo '<td class="text_klein">Due until: </td>';

    echo "<td><input type='text' name='uto_enddatum' style='width:100px;' value='" . $zeile['uto_enddatum']
        . "' id='uto_enddatum'><img src='bilder/date_go.gif' alt='Anklicken für Kalenderansicht' onclick='kalender(document.getElementById(\"uto_enddatum\"));'/>";

    echo '</tr>';

    echo '<tr>';

    echo '<td>&nbsp;&nbsp;';

    echo '</td>';

    echo '<td class="text_klein">Priority: </td><td>';

    echo '<select size="1" name="uto_prio">';

    $sql_info='SELECT upr_nummer, upr_name FROM prioritaet ' .
        'ORDER BY upr_sort';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_info=mysql_query($sql_info, $verbindung))
        {
        fehler();
        }

    while ($zeile_info=mysql_fetch_array($ergebnis_info))
        {
        if ($zeile['uto_prio'] == $zeile_info['upr_nummer'])
            {
            echo '<option value="' . $zeile_info['upr_nummer'] . '" selected><span class="text">'
                . $zeile_info['upr_name'] . '</span></option>';
            }
        else
            {
            echo '<option value="' . $zeile_info['upr_nummer'] . '"><span class="text">' . $zeile_info['upr_name']
                . '</span></option>';
            }
        }

    echo '</select>';

    echo '</td>';

    echo '</tr>';

    echo
        '<tr><td colspan="4" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Save Change" class="formularbutton" /></td></tr>';

    echo '</table>';

    echo '<input type="hidden" name="uto_id" value="' . $uto_id . '">';

    echo '</form>';
    }

include('segment_fuss.php');

echo
    '<div id="bn_frame" style="position:absolute; display:none; height:198px; width:205px; background-color:#ced7d6; overflow:hidden;">';

echo
    '<iframe src="bytecal.php" style="width:208px; margin-left:-1px; border:0px; height:202px; background-color:#ced7d6; overflow:hidden;" border="0"></iframe>';

echo '</div>';
?>