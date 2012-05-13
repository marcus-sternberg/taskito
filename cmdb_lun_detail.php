<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_kopf.php');

if(isset($_GET['klun_id'])) {$klun_id = $_GET['klun_id'];}

$kategorie = 2; // Für das Seitenmenü (2=Infrastruktur)

echo '<table border=0>'; // Layout

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

echo '<table id="is24_vertikal" width="815">';

echo '<caption class="is24">';
echo 'Details für LUN';
echo '</caption>';

echo '<colgroup>';
echo '<col class="is24-first" />';
echo '</colgroup>';

echo '<tbody>';

$sql='  SELECT * FROM cmdb_kl_lun
        LEFT JOIN cmdb_kl_array ON klun_ckarrid=ckarr_id
        LEFT JOIN cmdb_obj_rack ON corack_id = ckarr_corackid
        LEFT JOIN cmdb_obj_standort ON costand_id = corack_costandid
        LEFT JOIN cmdb_sys_status ON cmst_objektid = klun_id
        LEFT JOIN cmdb_sys_statusname ON cmst_status = cmstnam_id
        WHERE klun_id = '.$klun_id;

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
    
while ($zeile=mysql_fetch_array($ergebnis))
    {

    echo '<tr>';
    echo '<td valign="top" width="15%">Name: </td><td class="text" align="left">'
    . $zeile['klun_name'] . '</td></tr>';
    echo '<tr><td valign="top" width="15%">Array: </td><td class="text" align="left"><a href="cmdb_storage_detail.php?ckarr_id='.$zeile['ckarr_id'].'">'.$zeile['ckarr_name'] . '</a></td></tr>';        
    echo '<tr><td valign="top" width="15%">angelegt am: </td><td class="text" align="left">'
    . datum_anzeigen($zeile['klun_anlage']) . '</td></tr>';   
    echo '<tr><td valign="top" width="15%">Status: </td><td class="text" align="left">'
    . $zeile['cmstatnam_name'] . '</td></tr>'; 
    echo '<tr><td valign="top" width="15%">Rack: </td><td class="text" align="left">'
    . $zeile['corack_name'] . '</td></tr>'; 
    echo '<tr><td valign="top" width="15%">Standort: </td><td class="text" align="left">'
    . $zeile['costand_name'] . '</td></tr>';         
   }
    
    echo '</tbody';
    echo '</table>';
    
  
echo '<table class="is24">';

echo '<caption class="is24">';

echo 'Zugeordnete Server</span>';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Server</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';

  
        $sql_server = ' SELECT * FROM cmdb_obj_server
                        LEFT JOIN kl_lun2server ON kluse_coservid = coserv_id
                        WHERE kluse_klunid = '.$klun_id;

        if (!$ergebnis_server=mysql_query($sql_server, $verbindung))
        {
            fehler();
        }        

        while ($zeile_server=mysql_fetch_array($ergebnis_server))
        {
            echo '<tr><td>';
            echo '[<a href="cmdb_server_detail.php?coserv_id='.$zeile_server['coserv_id'].'">'.$zeile_server['coserv_name'].'</a>] ';
        echo '</td></tr>';
        }  





    echo '</tbody';
    echo '</table>';
    echo '<br><br>';
    echo '</td></tr></table>';
   
    
include('segment_fuss.php');
?>