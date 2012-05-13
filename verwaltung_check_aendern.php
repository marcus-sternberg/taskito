<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

$hck_id=$_GET['hck_id'];

$sql='SELECT * FROM checks WHERE hck_id = ' . $hck_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {
    $hck_name = $zeile['hck_name'];
    $hck_url=$zeile['hck_url'];
    $hck_beschreibung=$zeile['hck_beschreibung'];
    $hck_ziel=$zeile['hck_ziel'];
    }

echo '<br><br><br>';

echo '<span class="box">Check ändern:</span><br><br>';

echo '<form action="verwaltung_check_speichern.php?toggle=2" method="post">';

echo '<table border=0 width=500>';

echo '<tr>';

echo '<td>&nbsp;&nbsp;';

echo '</td>';

echo '<td valign="top">Name</td>';

echo '<td>&nbsp;&nbsp;</td>';

echo '<td>';

echo '<input type="text" name="hck_name" value="' . $hck_name . '" style="width:400px;">';

echo '</td></tr>';

echo '<tr>';

echo '<td>&nbsp;&nbsp;';

echo '</td>';

echo '<td valign="top">URL</td>';

echo '<td>&nbsp;&nbsp;</td>';

echo '<td>';

echo '<input type="text" name="hck_url" value="' . $hck_url . '" style="width:400px;">';

echo '</td></tr>';

echo '<tr>';

echo '<td>&nbsp;&nbsp;';

echo '</td>';

echo '<td valign="top">Beschreibung</td>';

echo '<td>&nbsp;&nbsp;</td>';

echo '<td>';

echo '<textarea name="hck_beschreibung" cols="50" rows="5">' . $hck_beschreibung . '</textarea>';

echo '</td></tr>';

echo '<tr>';

echo '<td>&nbsp;&nbsp;';

echo '</td>';

echo '<td valign="top">Ziel</td>';

echo '<td>&nbsp;&nbsp;</td>';

echo '<td>';

echo '<textarea name="hck_ziel" cols="50" rows="5">' . $hck_ziel . '</textarea>';

echo '</td></tr>';

echo
    '<tr><td colspan="4" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Änderung sichern" class="formularbutton" /></td></tr>';

echo '</table>';

echo '<input type="hidden" name="hck_id" value="' . $hck_id . '">';

echo '</form>';

include('segment_fuss.php');
?>