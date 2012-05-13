<?php
###### Editnotes ####
#$LastChangedDate: 2011-11-14 09:17:20 +0100 (Mo, 14 Nov 2011) $
#$Author: msternberg $ 
#####################

######### Define Includes ##################

require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$auto='on'; 

$xMontag = $_REQUEST['von'];
$xSonntag = $_REQUEST['bis'];
$zustand = $_REQUEST['typ'];  
$suchstring = $_REQUEST['ref']; 
     
if ($_SESSION['z'] == 1) {$sort_richtung = 'DESC';} else {$sort_richtung = 'ASC';}
  
if(isset($_GET['sortierschluessel']))
{                                      
    switch($_GET['sortierschluessel'])
        {
            case 'Nagios-ID':
                $sort_reihe = 'hal_nagiosid';
                break;

             case 'Host':
                $sort_reihe = 'hal_cciid';
                break;       
                
             case 'Status':
                $sort_reihe = 'hal_status';
                break;
                
             case 'Alarmzeit':
                $sort_reihe = 'hal_zeitstempel';
                break;

             case 'Service':
                $sort_reihe = 'hal_meldung';
                break;
                
             default:
                $sort_reihe = 'hal_zeitstempel';
                break;             
        
        }
             
} else
{
                $sort_reihe = 'hal_zeitstempel';     
}

$sort_sql = 'ORDER BY '.$sort_reihe.' '.$sort_richtung;
     
#####################################################################################
############################ Ausgabe Werte ##########################################

include('segment_kopf_reload.php');

 // Gebe Ueberschrift aus

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Alarm-Details für '.$suchstring.' von '.zeitstempel_anzeigen($xMontag).' bis '.zeitstempel_anzeigen($xSonntag).'</span><br><br>';

switch($zustand)
{
     case 3:
        $sql=   'SELECT * FROM alarme_historie
                WHERE hal_cciid = "'.$suchstring.'" AND (hal_zeitstempel BETWEEN "'.$xMontag.'" 
                AND "'.$xSonntag.'") '.$sort_sql;
        break;
        
     case 4:
        $sql=   'SELECT * FROM alarme_historie
                WHERE hal_service = "'.$suchstring.'" AND (hal_zeitstempel BETWEEN "'.$xMontag.'" 
               AND "'.$xSonntag.'") '.$sort_sql;   
        break;        
}
        
        
$zaehler=1;        

echo '<table class="matrix" cellspacing="1" cellpadding="3" width="1200" border="0">';

echo '<tr>';
echo '<td>Lfd.</td>';
echo '<td><a href="alarme_liste_detail.php?typ='.$zustand.'&sortierschluessel=Nagios-ID&ref='.$suchstring.'&von='.$xMontag.'&bis='.$xSonntag.'">Nagios-ID</a></td>';
echo '<td><a href="alarme_liste_detail.php?typ='.$zustand.'&sortierschluessel=Host&ref='.$suchstring.'&von='.$xMontag.'&bis='.$xSonntag.'">Host</a></td>';
echo '<td><a href="alarme_liste_detail.php?typ='.$zustand.'&sortierschluessel=Status&ref='.$suchstring.'&von='.$xMontag.'&bis='.$xSonntag.'">Status</a></td>';
echo '<td><a href="alarme_liste_detail.php?typ='.$zustand.'&sortierschluessel=Alarmzeit&ref='.$suchstring.'&von='.$xMontag.'&bis='.$xSonntag.'">Alarmzeit</a></td>'; 
echo '<td><a href="alarme_liste_detail.php?typ='.$zustand.'&sortierschluessel=Service&ref='.$suchstring.'&von='.$xMontag.'&bis='.$xSonntag.'">Service</a></td>';   
echo '<td>Info</td>';
echo '<td>Ticket</td>'; 
echo '</tr>';

echo '<tbody class="is24">';  

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

   
while ($zeile=mysql_fetch_array($ergebnis))
    {
       
           if (fmod($zaehler, 2) == 1 && $zaehler > 0)
        {
        $tr_stil='<tr class="is24">';
        }
    else
        {
        $tr_stil='<tr class="is24_odd">';
        }
    
       
        echo $tr_stil;
        echo '<td>'.$zaehler.'</td>';
        echo '<td>'.$zeile['hal_nagiosid'].'</td>';  
        echo '<td>'.$zeile['hal_cciid'].'</td>';         
        echo '<td>'.$zeile['hal_status'].'</td>';  
        echo '<td>'.zeitstempel_anzeigen($zeile['hal_zeitstempel']).'</td>'; 
        echo '<td>'.$zeile['hal_service'].'</td>';         
        echo '<td>'.$zeile['hal_meldung'].'</td>';  
        echo '<td><a href="aufgabe_ansehen.php?hau_id='.$zeile['hal_hauid'].'" target="_blank">'.$zeile['hal_hauid'].'</a></td>';  
        echo '</tr>'; 

           $zaehler++;  
    }

echo '</table>';    


include('segment_fuss.php');
?>
