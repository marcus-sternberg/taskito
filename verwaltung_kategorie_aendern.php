<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

$ulk_id=$_GET['ulk_id'];

$sql='SELECT * FROM lizenzkategorie ' .
    'WHERE ulk_id = ' . $ulk_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {
    $ulk_name=$zeile['ulk_name'];
    }

echo '<br><br><br>';

echo '<span class="box">Change Category:</span><br><br>';

echo '<form action="verwaltung_kategorie_speichern.php?toggle=2" method="post">';

echo '<table border=0 width=300>';

echo '<tr>';

echo '<td>&nbsp;&nbsp;';

echo '</td>';

echo '<td valign="top">Name</td>';

echo '<td>&nbsp;&nbsp;</td>';

echo '<td>';

echo '<input type="text" name="ulk_name" value="' . $ulk_name . '">';

echo '</td></tr>';

echo
    '<tr><td colspan="4" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Save Change" class="formularbutton" /></td></tr>';

echo '</table>';

echo '<input type="hidden" name="ulk_id" value="' . $ulk_id . '">';

echo '</form>';

include('segment_fuss.php');
?>