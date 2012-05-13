<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_kopf.php');

$kategorie = 1; // Für das Seitenmenü (1=Organisation)

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

echo 'Verfügbare Gruppen</span>';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Kurzname</th>';

echo '<th class="is24">Name</th>';

echo '<th class="is24">Teamleiter</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';

$sql='  SELECT * FROM cmdb_obj_gruppe ORDER BY cogrup_name';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
    
while ($zeile=mysql_fetch_array($ergebnis))
    {


    echo '<tr>';
    echo '<td class="is24" nowrap>' . $zeile['cogrup_kurzname'] . '&nbsp;</td>';
    echo '<td class="is24"><a href="cmdb_gruppe_detail.php?cogrup_id='.$zeile['cogrup_id'].'">'. $zeile['cogrup_name'] . '</a></td>';
    
    $sql_tl = 'SELECT copers_name FROM cmdb_obj_person
              LEFT JOIN obj_person2obj_gruppe ON obpege_copersid = copers_id
              WHERE obpege_cogrupid = '.$zeile['cogrup_id'] .' AND obpege_corollid = 1';
              
            
              if (!$ergebnis_tl=mysql_query($sql_tl, $verbindung))
              {
              fehler();
              }
    echo '<td class="is24">';
    
        while ($zeile_tl=mysql_fetch_array($ergebnis_tl))
        {              
            echo $zeile_tl['copers_name'];             
        }
    echo '&nbsp;</td>'; 
    
    echo '</tr>';
    
    }
    echo '</tbody';
    echo '</table>';

    echo '</td></tr></table>';
   
    
include('segment_fuss.php');
?>