<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (!isset($_POST['speichern']))
    {
    require_once('segment_kopf.php');

    echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;New Task<br><br>';

    echo '<form action="control_neuer_aufwand.php" method="post">';

    echo '<table border="0" cellspacing="5" cellpadding="0">';

    echo '<tr>';

    echo '<td class="text_klein">Title: </td><td><input type="text" name="xTitle" style="width:240px;"></td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein">Changeeffort [h]: </td><td><input type="text" name="xEffort" style="width:40px;"></td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein" valign="top">Changedescription:&nbsp;&nbsp;</td><td><textarea cols="80" rows="5" name="xChange"></textarea></td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein">Status: </td><td>';

    echo '<select size="1" name="xStatus">';

    echo '<option value="1"><span class="text">requested</span></option>';

    echo '<option value="2"><span class="text">scheduled</span></option>';

    echo '<option value="3"><span class="text">done</span></option>';

    echo '<option value="4"><span class="text">rejected</span></option>';

    echo '</select>';

    echo '</td></tr>';

    echo
        '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="save" class="formularbutton" />';

    echo '</td></tr>';

    echo '</table>';

    echo '</form>';
    }
else
    {

    // Speichere den Datensatz

    $sql='INSERT INTO version_log (' .
        'xEffort, ' .
        'xTitle, ' .
        'xStatus, ' .
        'xChange) ' .
        'VALUES ( ' .
        '"' . $_POST['xEffort'] . '", ' .
        '"' . $_POST['xTitle'] . '", ' .
        '"' . $_POST['xStatus'] . '", ' .
        '"' . $_POST['xChange'] . '")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    header('Location: control_uebersicht.php');
    exit;
    }
?>