<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_kopf.php');

if(isset($_GET['coserv_id'])) {$coserv_id = $_GET['coserv_id'];}

$kategorie = 2; // Für das Seitenmenü (1=Organisation)

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
echo 'Details für diesen Server';
echo '</caption>';

echo '<colgroup>';
echo '<col class="is24-first" />';
echo '</colgroup>';

echo '<tbody>';

$sql='  SELECT * FROM cmdb_obj_server
        LEFT JOIN cmdb_kl_plattform ON coserv_ckplatid=ckplat_id
        LEFT JOIN cmdb_sys_servertyp ON coserv_ckstid = ckst_id
        LEFT JOIN cmdb_sys_status ON cmst_objektid = coserv_id
        LEFT JOIN cmdb_sys_statusname ON cmst_status = cmstnam_id
        LEFT JOIN kl_lizenzen2obj_server ON klise_coservid = coserv_id
        LEFT JOIN cmdb_obj_lizenzen ON coliz_id = klise_cklizid
        LEFT JOIN cmdb_obj_dokument ON codoku_id = coliz_codokuid 
        LEFT JOIN kl_lun2server ON kluse_coservid = coserv_id
        LEFT JOIN cmdb_kl_lun ON klun_id = kluse_klunid
        LEFT JOIN cmdb_kl_array ON ckarr_id = klun_ckarrid
        LEFT JOIN kl_server2rack ON klsera_coservid = coserv_id
        LEFT JOIN cmdb_obj_rack ON corack_id = klsera_corackid
        LEFT JOIN cmdb_obj_standort ON costand_id = corack_costandid
        LEFT JOIN kl_nic2server ON klnise_coservid = coserv_id
        LEFT JOIN cmdb_kl_nic ON klnise_cklnicid = cklnic_id
        WHERE coserv_id = '.$coserv_id .'
        ORDER BY ckplat_name, coserv_name';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
    
while ($zeile=mysql_fetch_array($ergebnis))
    {

    $uid = $zeile['coserv_uid'];
    echo '<tr>';
    echo '<td valign="top" width="15%">Name: </td><td class="text" align="left">'
    . $zeile['coserv_name'] . '</td></tr>';
    
    switch ($zeile['ckst_id'])
    {
        case 2:
        
        $sql_host = 'SELECT * FROM cmdb_obj_server LEFT JOIN kl_vm2physik ON klvmph_coservid_host = coserv_id
                     WHERE klvmph_coservid_vm = '.$zeile['coserv_id'];
        
        if (!$ergebnis_host=mysql_query($sql_host, $verbindung))
        {
            fehler();
        }
    
        while ($zeile_host=mysql_fetch_array($ergebnis_host))
        {
            $host_server = $zeile_host['coserv_name'];
            $host_server_id =  $zeile_host['coserv_id'];
        }             
    
        echo '<td valign="top" width="15%">Typ: </td><td class="text" align="left">'
        . $zeile['ckst_name'] . ' [HOST: <a href="cmdb_server_detail.php?coserv_id='.$host_server_id.'">'.$host_server.']</td></tr>';
        
        break;
        
        case 4:
        
        $sql_vm = 'SELECT * FROM cmdb_obj_server LEFT JOIN kl_vm2physik ON klvmph_coservid_vm = coserv_id
                     WHERE klvmph_coservid_host = '.$zeile['coserv_id'];
        
        if (!$ergebnis_vm=mysql_query($sql_vm, $verbindung))
        {
            fehler();
        }
    
        echo '<td valign="top" width="15%">Typ: </td><td class="text" align="left">'
        . $zeile['ckst_name'] . ' [VM: ';

        while ($zeile_vm=mysql_fetch_array($ergebnis_vm))
        {
            echo '<a href="cmdb_server_detail.php?coserv_id='.$zeile_vm['coserv_id'].'">'.$zeile_vm['coserv_name'].' ';
        }             
    
        echo ' ]</td></tr>';
        
        break;        
        
        default:
        
        echo '<td valign="top" width="15%">Typ: </td><td class="text" align="left">'
        . $zeile['ckst_name'] . '</td></tr>';
    }


    echo '<td valign="top" width="15%">Seriennummer: </td><td class="text" align="left">'
    . $zeile['coserv_seriennummer'] . '</td></tr>';        
    echo '<td valign="top" width="15%">Plattform: </td><td class="text" align="left">'
    . datum_anzeigen($zeile['ckplat_name']) . '</td></tr>';        
    echo '<td valign="top" width="15%">angelegt am: </td><td class="text" align="left">'
    . datum_anzeigen($zeile['coserv_anlage']) . '</td></tr>';   
    echo '<td valign="top" width="15%">Status: </td><td class="text" align="left">'
    . $zeile['cmstatnam_name'] . '</td></tr>'; 
    echo '<td valign="top" width="15%">Lizenz: </td><td class="text" align="left"><a href="cmdb_lizenzvertrag.php?coliz_id='.$zeile['coliz_id'].'">'
    . $zeile['codoku_name'] . '</a></td></tr>';          
    echo '<td valign="top" width="15%">LUN: </td><td class="text" align="left"><a href="cmdb_lun_detail.php?klun_id='.$zeile['klun_id'].'">'.$zeile['klun_name'].' [<a href="cmdb_storage_detail.php?ckarr_id='.$zeile['ckarr_id'].'">'.$zeile['ckarr_name'].'</a>]</td></tr>';          
    echo '<td valign="top" width="15%">Rack: </td><td class="text" align="left">'.$zeile['corack_name'].'</td></tr>';
    echo '<td valign="top" width="15%">Standort: </td><td class="text" align="left">'.$zeile['costand_name'].'</td></tr>';   
    echo '<td valign="top" width="15%">IP: </td><td class="text" align="left">'.$zeile['cklnic_ip'].'</td></tr>'; 
    echo '<td valign="top" width="15%">Gateway: </td><td class="text" align="left">'.$zeile['cklnic_gw'].'</td></tr>'; 
    echo '<td valign="top" width="15%">Subnet: </td><td class="text" align="left">'.$zeile['cklnic_subnet'].'</td></tr>'; 
    }
    
    echo '</tbody';
    echo '</table>';
    
  
$sql='  SELECT * FROM cmdb_zustand
        WHERE czustand_uuid = '.$uid;


if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
 
        switch ($zeile['czustand'])
        {
            case 1:
            $anzeige_zeichen = 'icon_quad_gruen.gif';           
            break;
            case 2:
            $anzeige_zeichen = 'icon_quad_info.gif';           
            break;
            case 3:
            $anzeige_zeichen = 'icon_quad_gelb.gif';           
            break;
            case 4:
            $anzeige_zeichen = 'icon_quad_orange.gif';           
            break;            
            case 5:
            $anzeige_zeichen = 'icon_quad_rot.gif';           
            break; 
            case 6:
            $anzeige_zeichen = 'icon_quad_schwarz.gif';           
            break; 
            default:
            $anzeige_zeichen = 'icon_quad_gruen.gif';           
            break;         
            
        }
    
    
    }


echo '<table class="is24">';

echo '<caption class="is24">';

echo 'Aktuelle Alarme [Status: <img src="bilder/'.$anzeige_zeichen.'">]</span>';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Zeit</th>';

echo '<th class="is24">Stufe</th>';

echo '<th class="is24">Ticket</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';
$sql='  SELECT * FROM cmdb_alarme
        LEFT JOIN cmdb_sys_alarm ON csalarm_id = calarm_stufe
        WHERE calarm_uuid = '.$uid .'
        ORDER BY calarm_stufe DESC';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
    
while ($zeile=mysql_fetch_array($ergebnis))
    {


    echo '<tr>';
    echo '<td class="is24">' . zeitstempel_anzeigen($zeile['calarm_zeit']) . '</td>';
    echo '<td class="is24">' . $zeile['csalarm_name'] . '&nbsp;</td>';
    echo '<td class="is24">' . $zeile['calarm_ticket'] . '&nbsp;</td>';
    echo '</tr>';
    
    }
    echo '</tbody';
    echo '</table>';
    echo '<br><br>';
    echo '</td></tr></table>';
   
    
include('segment_fuss.php');
?>