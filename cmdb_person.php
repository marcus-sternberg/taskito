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

echo 'Eingetragene Personen</span>';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Name</th>';

echo '<th class="is24">Gruppe</th>';

echo '<th class="is24">Rolle</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';

$sql='  SELECT * FROM cmdb_obj_person ORDER BY copers_name';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
    
while ($zeile=mysql_fetch_array($ergebnis))
    {

    $sql_tl = ' SELECT cogrup_kurzname, coroll_name
                FROM cmdb_obj_person
                LEFT JOIN obj_person2obj_gruppe ON obpege_copersid = copers_id
                LEFT JOIN cmdb_obj_gruppe ON cogrup_id = obpege_cogrupid
                LEFT JOIN cmdb_sys_rollen ON obpege_corollid = coroll_id                
                WHERE obpege_copersid = '.$zeile['copers_id'];
              
           
              if (!$ergebnis_tl=mysql_query($sql_tl, $verbindung))
              {
              fehler();
              }
    
        while ($zeile_tl=mysql_fetch_array($ergebnis_tl))
        {              

    echo '<tr>';
    echo '<td class="is24" nowrap>' . $zeile['copers_name'] . '&nbsp;</td>';
    echo '<td class="is24">';
    echo $zeile_tl['cogrup_kurzname'];             
    echo '&nbsp;</td>'; 

    echo '<td class="is24">';
    echo $zeile_tl['coroll_name'];             
    echo '&nbsp;</td>'; 
 
    echo '</tr>';
        }    
    }
    echo '</tbody';
    echo '</table>';

    echo '</td></tr></table>';
   
    
include('segment_fuss.php');
?>