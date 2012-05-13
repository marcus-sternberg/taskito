<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_kopf.php');

if(isset($_GET['cowart_id'])) {$cowart_id = $_GET['cowart_id'];}

$kategorie = 1; // Für das Seitenmenü (1=Organisation)

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
echo 'Details für diesen Wartungsvertrag';
echo '</caption>';

echo '<colgroup>';
echo '<col class="is24-first" />';
echo '</colgroup>';

echo '<tbody>';

$sql='  SELECT * FROM cmdb_obj_wartung 
        LEFT JOIN cmdb_obj_provider ON coprov_id = cowart_coprovid
        LEFT JOIN cmdb_obj_dokument ON codoku_id = cowart_codokuid
        WHERE cowart_id = '.$cowart_id;

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
    
while ($zeile=mysql_fetch_array($ergebnis))
    {

    echo '<tr>';
    echo '<td valign="top" width="15%">Name: </td><td class="text" align="left">'
    . $zeile['codoku_name'] . '</td></tr>';
    echo '<td valign="top" width="15%">Beschreibung: </td><td class="text" align="left">'
    . $zeile['cowart_beschreibung'] . '</td></tr>';        
    echo '<td valign="top" width="15%">Provider: </td><td class="text" align="left">'
    . datum_anzeigen($zeile['coprov_name']) . '</td></tr>';   
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

echo '<th class="is24">Typ</th>';

echo '<th class="is24">Plattform</th>';

echo '<th class="is24">Status</th>';

echo '<th class="is24">Ablauf Wartung</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';

$sql='  SELECT * FROM cmdb_obj_server
        LEFT JOIN cmdb_kl_plattform ON coserv_ckplatid=ckplat_id
        LEFT JOIN cmdb_sys_servertyp ON coserv_ckstid = ckst_id
        LEFT JOIN cmdb_sys_status ON cmst_objektid = coserv_id
        LEFT JOIN cmdb_sys_statusname ON cmst_status = cmstnam_id
        LEFT JOIN kl_server2wartung ON klsewa_coservid = coserv_id
        WHERE klsewa_cowartid = '.$cowart_id .'
        ORDER BY ckplat_name, coserv_name';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
    
while ($zeile=mysql_fetch_array($ergebnis))
    {


    echo '<tr>';
    echo '<td class="is24" nowrap><a href="cmdb_server_detail.php?coserv_id='.$zeile['coserv_id'].'"> '. $zeile['coserv_name'] . '&nbsp;</td>';
    echo '<td class="is24">' . $zeile['ckst_name'] . '&nbsp;</td>';
    echo '<td class="is24">' . $zeile['ckplat_name'] . '&nbsp;</td>';
    echo '<td class="is24">' . $zeile['cmstatnam_name'] . '&nbsp;</td>';
    echo '<td class="is24">' . datum_anzeigen($zeile['klsewa_ablaufdatum']) . '&nbsp;</td>';
    echo '</tr>';
    
    }
    echo '</tbody';
    echo '</table>';
    echo '<br><br>';
    echo '</td></tr></table>';
   
    
include('segment_fuss.php');
?>