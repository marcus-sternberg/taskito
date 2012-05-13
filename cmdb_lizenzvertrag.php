<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_kopf.php');

if(isset($_GET['coliz_id'])) {$coliz_id = $_GET['coliz_id'];}

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
echo 'Details für diesen Lizenzvertrag';
echo '</caption>';

echo '<colgroup>';
echo '<col class="is24-first" />';
echo '</colgroup>';

echo '<tbody>';

$sql='  SELECT * FROM cmdb_obj_lizenzen 
        LEFT JOIN cmdb_obj_provider ON coprov_id = coliz_coprovid
        LEFT JOIN cmdb_obj_dokument ON codoku_id = coliz_codokuid
        WHERE coliz_id = '.$coliz_id;

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
    . $zeile['coliz_beschreibung'] . '</td></tr>';        
    echo '<td valign="top" width="15%">Alaufdatum: </td><td class="text" align="left">'
    . datum_anzeigen($zeile['coliz_ablaufdatum']) . '</td></tr>';        
    echo '<td valign="top" width="15%">Provider: </td><td class="text" align="left">'
    . datum_anzeigen($zeile['coprov_name']) . '</td></tr>';   
    
    $sql_anzahl = 'SELECT COUNT(klise_id) AS anzahl FROM kl_lizenzen2obj_server
                  WHERE klise_cklizid = '.$coliz_id;
     if (!$ergebnis_anzahl=mysql_query($sql_anzahl, $verbindung))
    {
    fehler();
    }
    
while ($zeile_anzahl=mysql_fetch_array($ergebnis_anzahl))
    {
        $anzahl_lizenzen_im_einsatz = $zeile_anzahl['anzahl'];
    }             
    
    if($zeile['coliz_anzahl']!=0)
    {
    echo '<td valign="top" width="15%">Lizenzmenge: </td><td class="text" align="left">'
    . $zeile['coliz_anzahl'] . ' [davon noch frei: '.($zeile['coliz_anzahl']-$anzahl_lizenzen_im_einsatz).']</td></tr>';          
    } else
    {
    echo '<td valign="top" width="15%">Lizenzmenge: </td><td class="text" align="left">uneingeschränkt</td></tr>';         
    }   
   
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

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';

$sql='  SELECT * FROM cmdb_obj_server
        LEFT JOIN cmdb_kl_plattform ON coserv_ckplatid=ckplat_id
        LEFT JOIN cmdb_sys_servertyp ON coserv_ckstid = ckst_id
        LEFT JOIN cmdb_sys_status ON cmst_objektid = coserv_id
        LEFT JOIN cmdb_sys_statusname ON cmst_status = cmstnam_id
        LEFT JOIN kl_lizenzen2obj_server ON klise_coservid = coserv_id
        WHERE klise_cklizid = '.$coliz_id .'
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
    echo '</tr>';
    
    }
    echo '</tbody';
    echo '</table>';
    echo '<br><br>';
    echo '</td></tr></table>';
   
    
include('segment_fuss.php');
?>