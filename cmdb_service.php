<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_kopf.php');

$kategorie = 2; // Für das Seitenmenü (2=Infrastruktur)

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

echo 'Erfasste Services</span>';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Servicename</th>';

echo '<th class="is24">Kritikalität</th>';

echo '<th class="is24">Status</th>';

echo '<th class="is24">Zustand</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';

$sql='  SELECT * FROM cmdb_obj_service
        LEFT JOIN cmdb_sys_status ON cmst_objektid = coser_id
        LEFT JOIN cmdb_sys_statusname ON cmst_status = cmstnam_id
        LEFT JOIN cmdb_zustand ON czustand_uuid = coser_uid
        ORDER BY coser_name';

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

    echo '<tr>';
    echo '<td class="is24" nowrap><a href="cmdb_service_detail.php?coser_id='.$zeile['coser_id'].'">' . $zeile['coser_name'] . '</a></td>';
    echo '<td class="is24">'. $zeile['coser_prio'] . '</td>';
    echo '<td class="is24">'. $zeile['cmstatnam_name'] . '</td>';
    echo '<td class="is24" align="middle"><img src="bilder/'.$anzeige_zeichen.'"></td>';    
     
    
    echo '</tr>';
    
    }
    echo '</tbody';
    echo '</table>';

    echo '</td></tr></table>';
   
    
include('segment_fuss.php');
?>