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

echo '<table border=0 width=300>';

echo '<tr>';

echo '<td valign="top"></td>';

echo '<td>&nbsp;&nbsp;</td>';

echo '<td>';

echo '<span class="box">Folgende Statusmeldungen sind hinterlegt:</span>';

echo '<br><br>';
    
$sql='SELECT * FROM defcon_texte ORDER BY udm_bezug';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

// Beginne mit Tabellenausgabe
echo '<table class="matrix" cellspacing="1" cellpadding="3" width="350">';

echo '</tr>';

echo '<tr><td class="text_mitte_normal" colspan="3">';

echo 'Neuen Datensatz anlegen</td><td align="center"><a href="defcon_text_neu.php"><img src="bilder/icon_neu.gif" border="0" alt="Neuen Datensatz anlegen" title="Neuen Datensatz anlegen"></a>';

echo '</tr>';

echo '<tr>';

echo '<td class="text_mitte_normal" colspan="2" style="text-align:center;">';   
echo '<span class="xnormal_sort">Defcon-Status ändern</span></td>';
echo '<td class="text_mitte_normal" rowspan="2" colspan="2">&nbsp;</td>';   

echo '</tr>';

echo '<tr>';
echo '<td class="text_mitte_normal" style="text-align:center;">';   
echo '<span class="xnormal_sort">von</span></td>';
echo '<td class="text_mitte_normal" style="text-align:center;">'; 
echo '<span class="xnormal_sort">nach</span></td>';   

echo '</tr>';

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {

    // Beginne Datenausgabe
    echo '<tr>';
    echo '<td class="text_normal" style="text-align:center;">' . substr($zeile['udm_bezug'],0,1) . '</td>';         
    echo '<td class="text_normal" style="text-align:center;">' . substr($zeile['udm_bezug'],-1,1) . '</td>'; 

    echo '<td align="center" ><a href="defcon_text_aendern.php?udm_id=' . $zeile['udm_id'] . '"><img src="bilder/icon_aendern.gif" border="0" alt="Text ändern" title="Text ändern"></a></td>';
    echo '<td align="center" ><a href="defcon_text_loeschen.php?udm_id=' . $zeile['udm_id'] . '" onclick="return window.confirm(\'Text löschen?\');"><img src="bilder/icon_loeschen.gif" border="0" alt="Text löschen" title="Text löschen"></a></td>';    
    echo '</tr>';
   }

echo '</table>';
   
include('segment_fuss.php');
?>