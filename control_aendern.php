<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$ID=$_REQUEST['ID'];

if (!isset($_POST['speichern']))
    {
    include('segment_kopf.php');
    $sql='SELECT * FROM version_log ' .
        'WHERE ID = ' . $ID;

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
    while ($zeile=mysql_fetch_array($ergebnis))
        {
        echo '<br><br><br>';

        echo '<span class="box">Adapt a Change:</span><br><br>';

        echo '<form action="control_aendern.php" method="post">';

        echo '<table border=0 width=700>';

        echo '<tr>';

        echo '<td>&nbsp;&nbsp;';

        echo '</td>';

        echo '<td valign="top">Title</td>';

        echo '<td>&nbsp;&nbsp;</td>';

        echo '<td>';

        echo '<input type="text" name="xTitle" value="' . $zeile['xTitle'] . '" style="width:340px;">';

        echo '</td></tr>';

        echo '<tr>';

        echo '<td>&nbsp;&nbsp;';

        echo '</td>';

        echo '<td valign="top">Effort [h]</td>';

        echo '<td>&nbsp;&nbsp;</td>';

        echo '<td>';

        echo '<input type="text" name="xEffort" value="' . $zeile['xEffort'] . '">';

        echo '</td></tr>';

        echo '<tr>';

        echo '<td>&nbsp;&nbsp;';

        echo '</td>';

        echo '<td valign="top">Description</td>';

        echo '<td>&nbsp;&nbsp;</td>';

        echo '<td>';

        echo '<textarea cols="80" rows="5" name="xChange">' . $zeile['xChange'] . '</textarea></td>';

        echo '</td></tr>';

        echo '<tr>';

        echo '<td>&nbsp;&nbsp;';

        echo '</td>';

        echo '<td>Status: </td>';

        echo '<td>&nbsp;&nbsp;</td><td>';

        echo '<select size="1" name="xStatus">';

        if ($zeile['xStatus'] == 1)
            {
            echo '<option value="1" selected><span class="text">requested</span></option>';
            }
        else
            {
            echo '<option value="1"><span class="text">requested</span></option>';
            }

        if ($zeile['xStatus'] == 2)
            {
            echo '<option value="2" selected><span class="text">scheduled</span></option>';
            }
        else
            {
            echo '<option value="2"><span class="text">scheduled</span></option>';
            }

        if ($zeile['xStatus'] == 3)
            {
            echo '<option value="3" selected><span class="text">done</span></option>';
            }
        else
            {
            echo '<option value="3"><span class="text">done</span></option>';
            }

        if ($zeile['xStatus'] == 4)
            {
            echo '<option value="4" selected><span class="text">rejected</span></option>';
            }
        else
            {
            echo '<option value="4"><span class="text">rejected</span></option>';
            }

        echo '</select>';

        echo '</td></tr>';

        echo
            '<tr><td colspan="4" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Save Changes" class="formularbutton" /></td></tr>';

        echo '<input type="hidden" name="ID" value="' . $ID . '">';

        echo '</table>';

        echo '</form>';
        }
    }
else
    {

    $sql='UPDATE version_log SET ' .
        'xEffort = "' . $_POST['xEffort'] . '", ' .
        'xTitle = "' . $_POST['xTitle'] . '", ' .
        'xStatus = "' . $_POST['xStatus'] . '", ' .
        'xChange  = "' . $_POST['xChange'] . '" ' .
        'WHERE ID = ' . $ID;

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    header('Location: control_uebersicht.php');
    exit;
    }
?>