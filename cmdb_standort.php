<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_kopf.php');

$kategorie = 1; // Für das Seitenmenü (Organisation)

echo '<table>'; // Layout

echo '<tr><td width="100" valign="top">'; // Randnavigation

echo '<table width="100">';

$sql = 'SELECT * FROM menu_cmdb WHERE kategorie = '.$kategorie.' ORDER BY name';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
echo '<tr><td><a href="'.$zeile['link'].'">'.$zeile['name'].'</a></td></tr>';
    }
    
echo '</table>';
    
echo '</td><td>'; // Eingabebereich

echo '<table class="is24">';

echo '<caption class="is24">';

echo 'Verfügbare Standortinformationen</span>';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Name</th>';

echo '<th class="is24">Adresse</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';

$sql='SELECT * FROM cmdb_obj_standort WHERE costand_aktiv = 1 ORDER BY costand_name';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
    
while ($zeile=mysql_fetch_array($ergebnis))
    {


    echo '<tr>';
    echo '<td class="is24" nowrap>' . $zeile['costand_name'] . '&nbsp;</td>';
    echo '<td class="is24">' . $zeile['costand_adresse'] . '&nbsp;</td>';
    echo '</tr>';
    
    }
    echo '</tbody';
    echo '</table>';

    echo '</td></tr></table>';
   
    
include('segment_fuss.php');
?>