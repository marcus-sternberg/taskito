<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_kopf.php');

$kategorie = 2; // Für das Seitenmenü (1=Organisation)

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

echo 'Erfasste Storageeinheiten</span>';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Array / LUN</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';

$sql='  SELECT * FROM cmdb_kl_array 
        ORDER BY ckarr_name';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
    
while ($zeile=mysql_fetch_array($ergebnis))
    {

    echo '<tr class="is24_odd">';

    echo '<td class="is24"><a href="cmdb_storage_detail.php?ckarr_id='.$zeile['ckarr_id'].'">' . $zeile['ckarr_name'] . '</a></td></tr>';

    $sql_lun = 'SELECT * FROM cmdb_kl_lun
                WHERE klun_ckarrid = '.$zeile['ckarr_id'];
        
        if (!$ergebnis_lun=mysql_query($sql_lun, $verbindung))
        {
            fehler();
        }
    
    echo '<td valign="top">';
    
        while ($zeile_lun=mysql_fetch_array($ergebnis_lun))
        {
            echo '[<a href="cmdb_lun_detail.php?klun_id='.$zeile_lun['klun_id'].'">'.$zeile_lun['klun_name'].'</a>] ';
        }             
    echo '</td></tr>';
   
    }
    echo '</tbody';
    echo '</table>';

    echo '</td></tr></table>';
   
    
include('segment_fuss.php');
?>
