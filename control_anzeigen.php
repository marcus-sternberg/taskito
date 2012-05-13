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

    echo '<span class="box">View Changecontent:</span><br><br>';

    echo '<table border=0 width=500>';

    echo '<tr>';

    echo '<td>&nbsp;&nbsp;';

    echo '</td>';

    echo '<td valign="top">Title</td>';

    echo '<td>&nbsp;&nbsp;</td>';

    echo '<td>' . $zeile['xTitle'] . '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td>&nbsp;&nbsp;';

    echo '</td>';

    echo '<td valign="top">Description</td>';

    echo '<td>&nbsp;&nbsp;</td>';

    echo '<td>' . $zeile['xChange'] . '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td>&nbsp;&nbsp;';

    echo '</td>';

    echo '<td  valign="top">Status: </td>';

    echo '<td>&nbsp;&nbsp;</td><td>';

    if ($zeile['xStatus'] == 1)
        {
        echo '<span class="text">requested</span>';
        }

    if ($zeile['xStatus'] == 2)
        {
        echo '<span class="text">scheduled</span>';
        }

    if ($zeile['xStatus'] == 3)
        {
        echo '<span class="text">done</span>';
        }

    if ($zeile['xStatus'] == 4)
        {
        echo '<span class="text">rejected</span>';
        }

    echo '</td></tr>';

    echo '</table>';
    }
?>