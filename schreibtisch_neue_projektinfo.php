<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');

$hpr_id=$_GET['hpr_id'];

$datum=date("d.m.Y");

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Enter new Project Info<br><br>';

echo '<form action="schreibtisch_neue_projektinfo_speichern.php" method="post">';

echo '<table border="0" cellspacing="5" cellpadding="0">';

echo '<tr>';

echo '<td class="text_klein">Date: </td><td><input type="text" name="upj_datum" value="' . $datum
    . '" style="width:340px;"></td>';

echo '</tr>';

echo '<tr>';

echo
    '<td class="text_klein" valign="top">Description:&nbsp;&nbsp;</td><td><textarea cols="70" rows="15" name="upj_text"></textarea></td>';

echo '</tr>';

echo '<tr>';

echo '<td class="text_klein">Effort [min]: </td><td><input type="text" name="upj_aufwand" style="width:340px;"></td>';

echo '</tr>';

echo '<input type="hidden" name="upj_pid" value ="' . $hpr_id . '">';

echo
    '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Save Info" class="formularbutton" /></td></tr>';

echo '</table>';

echo '</form>';
?>
