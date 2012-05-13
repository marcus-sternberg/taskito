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

echo '<span class="box">Neuen Check anlegen:</span><br><br>';

echo '<form action="verwaltung_check_speichern.php?toggle=1" method="post">';

echo '<table border=0 width=300>';

echo '<tr>';

echo '<td>&nbsp;&nbsp;';

echo '</td>';

echo '<td valign="top">Name des Checks</td>';

echo '<td>&nbsp;&nbsp;</td>';

echo '<td>';

echo '<input type="text" name="hck_name" style="width:400px;">';

echo '</td></tr>';

echo '<tr>';

echo '<td>&nbsp;&nbsp;';

echo '</td>';

echo '<td valign="top">URL</td>';

echo '<td>&nbsp;&nbsp;</td>';

echo '<td>';

echo '<input type="text" name="hck_url" style="width:400px;">';

echo '</td></tr>';

echo '<tr>';

echo '<td>&nbsp;&nbsp;';

echo '</td>';

echo '<td valign="top">Beschreibung</td>';

echo '<td>&nbsp;&nbsp;</td>';

echo '<td>';

echo '<textarea name="hck_beschreibung" cols="50" rows="5"></textarea>';

echo '</td></tr>';

echo '<tr>';

echo '<td>&nbsp;&nbsp;';

echo '</td>';

echo '<td valign="top">Ziel</td>';

echo '<td>&nbsp;&nbsp;</td>';

echo '<td>';

echo '<textarea name="hck_ziel" cols="50" rows="5"></textarea>';

echo '</td></tr>';

echo
    '<tr><td colspan="4" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Sichere Check" class="formularbutton" /></td></tr>';

echo '</table>';

echo '</form>';

include('segment_fuss.php');
?>