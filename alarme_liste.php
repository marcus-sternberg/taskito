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

if ($_SESSION['z'] == 1) {$sort_richtung = 'DESC';} else {$sort_richtung = 'ASC';}

if(isset($_REQUEST['zustand'])) {$zustand = $_GET['zustand'];} else {$zustand=1;}

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
                
             default:
                $sort_reihe = 'hal_zeitstempel';
                break;             
        
        }
             
} else
{
                $sort_reihe = 'hal_zeitstempel';     
}

$sort_sql = 'ORDER BY '.$sort_reihe.' '.$sort_richtung;

$reiter_status = array("","","","","");

switch($zustand)
{
    case 1:
        $sql_tabelle = 'alarme';
        $sql_part = $sort_sql;
        $reiter_status[1] = "current";
        break;
        
    case 2:
        $sql_tabelle = 'alarme_historie';
        $sql_part = $sort_sql.' LIMIT 50';
        $reiter_status[2] = "current";    
        break;    
        
    case 3:
        $reiter_status[3] = "current";    
        break;           

    case 4:
        $reiter_status[4] = "current";    
        break;           
            
}
     
#####################################################################################
############################ Ausgabe Werte ##########################################

include('segment_kopf_reload.php');

if($zustand>2)
{
    echo '<form action="alarme_liste.php?zustand='.$zustand.'" method="post">';

# Baue Tabelle
 
echo '<table border=0';

echo '<tr>';

echo '<td>';

if (isset($_REQUEST['xKw']))
    {
    $xKw=$_REQUEST['xKw'];
    }
else
    {
    $xKw=date('W');
    }

echo '<select size="1" name="xKw">';

for ($i=1; $i < 54; $i++)
    {
    if ($xKw == $i)
        {
        echo '<option value="' . $i . '" selected><span class="text">' . $i . '. KW</span></option>';
        }
    else
        {
        echo '<option value="' . $i . '"><span class="text">' . $i . '. KW</span></option>';
        }
    }

echo '</select> ';

echo '</td>';

echo '<td>';

if (isset($_REQUEST['xJahr']))
    {
    $xJahr=$_REQUEST['xJahr'];
    }
else
    {
    $xJahr=date('Y');
    }

echo '<select size="1" name="xJahr">';

for ($i=2011; $i < 2014; $i++)
    {
    if ($xJahr == $i)
        {
        echo '<option value="' . $i . '" selected><span class="text">' . $i . '</span></option>';
        }
    else
        {
        echo '<option value="' . $i . '"><span class="text">' . $i . '</span></option>';
        }
    }

echo '</select> ';

echo '</td>';
 
echo '<td align="right">';

echo '<input type="submit" value="Zeige KW" class="formularbutton" name="range"/>';

echo '</td></tr>';

echo '</table>';  

echo '</form><br>';
}

// Gebe Ueberschrift aus

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Alarm-Überblick</span><br><br>';

echo '<div id="header">';
echo '<ul>';

############# aktuelle Alarme ######################

$sql=   'SELECT COUNT(hal_id) AS anzahl FROM alarme';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
{
    echo '<li id='.$reiter_status[1].'><a href="alarme_liste.php?zustand=1">Aktuelle Alarme ('.$zeile['anzahl'].')</a></li>';
}
############ Historie ######################

    echo '<li id='.$reiter_status[2].'><a href="alarme_liste.php?zustand=2">Historische Alarme (letzte 50)</a></li>';

    echo '<li id='.$reiter_status[3].'><a href="alarme_liste.php?zustand=3">Top Alarme / Host</a></li>'; 

    echo '<li id='.$reiter_status[4].'><a href="alarme_liste.php?zustand=4">Top Alarme / Typ</a></li>';   
    
echo '</ul>';

echo '</div>';

If($zustand < 3)
{

echo '<table class="matrix" cellspacing="1" cellpadding="3" width="1200" border="0">';

echo '<tr>';
echo '<td>Lfd.</td>';
echo '<td><a href="alarme_liste.php?zustand='.$zustand.'&sortierschluessel=Nagios-ID">Nagios-ID</a></td>';
echo '<td><a href="alarme_liste.php?zustand='.$zustand.'&sortierschluessel=Host">Host</a></td>';
echo '<td><a href="alarme_liste.php?zustand='.$zustand.'&sortierschluessel=Status">Status</a></td>';
echo '<td><a href="alarme_liste.php?zustand='.$zustand.'&sortierschluessel=Alarmzeit">Alarmzeit</a></td>';   
echo '<td>Service</td>';
echo '<td>Info</td>';
echo '<td>Ticket</td>'; 
echo '</tr>';

$sql=   'SELECT * FROM '.$sql_tabelle.' '.$sql_part;

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$lfd_nr = 0;
    
while ($zeile=mysql_fetch_array($ergebnis))
    {

$lfd_nr++;
        
switch($zeile['hal_status'])
{
    case 'CRITICAL':
       $color='#FFBFA0';
       break;

    case 'WARNING':
       $color='#FFF8B3';
       break;       
       
    default:
       $color='#CED9E7';    
       break;
} 
       
        echo '<tr>';
        
        echo '<td bgcolor="' . $color . '">'.$lfd_nr.'</td>'; 
        echo '<td bgcolor="' . $color . '">'.$zeile['hal_nagiosid'].'</td>';
        
        echo '<td bgcolor="' . $color . '">'.$zeile['hal_cciid'].'</td>';

        echo '<td bgcolor="' . $color . '">'.$zeile['hal_status'].'</td>';

        echo '<td bgcolor="' . $color . '">'.zeitstempel_anzeigen($zeile['hal_zeitstempel']).'</td>';

        echo '<td bgcolor="' . $color . '">'.$zeile['hal_service'].'</td>';

        echo '<td bgcolor="' . $color . '">'.$zeile['hal_meldung'].'</td>';   
        
        echo '<td bgcolor="' . $color . '"><a href="aufgabe_ansehen.php?hau_id='.$zeile['hal_hauid'].'" target="_blank">'.$zeile['hal_hauid'].'</a></td>';
        echo '</tr>'; 
        
        
    }

echo '</table>'; 
} else
{





### Lege Zeitraum des Berichts fest 

$xMontag=date("Y-m-d 00:00:00", mondaykw($xKw, $xJahr));
$xSonntag = date ('Y-m-d 23:59:59' , strtotime("$xMontag +6 days"));
          
switch($zustand)
{
     case 3:
        $sql=   'SELECT *, COUNT(hal_id) AS menge FROM alarme_historie
                WHERE (hal_zeitstempel BETWEEN "'.$xMontag.'" 
                AND "'.$xSonntag.'")
                GROUP BY hal_cciid ORDER BY menge DESC';
        $anzeige = 'hal_cciid';
        $tabellen_titel = 'Host';
        break;
        
     case 4:
        $sql=   'SELECT *, COUNT(hal_id) AS menge FROM alarme_historie
                WHERE (hal_zeitstempel BETWEEN "'.$xMontag.'" 
                AND "'.$xSonntag.'")
                GROUP BY hal_service ORDER BY menge DESC';
        $anzeige = 'hal_service';  
        $tabellen_titel = 'Service'; 
        break;        
}

$zaehler=0;   
$gesamtmenge = 0;     

echo '<table class="matrix" cellspacing="1" cellpadding="3" width="1200" border="0">';

echo '<tr>';
echo '<td class="text_fett">'.$tabellen_titel.'</td>';
echo '<td class="text_fett">Anzahl der Alarme</td>';
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
        echo '<td>'.$zeile[$anzeige].'</td>';
        echo '<td><a href="alarme_liste_detail.php?typ='.$zustand.'&ref='.$zeile[$anzeige].'&von='.$xMontag.'&bis='.$xSonntag.'">'.$zeile['menge'].'</a></td>'; 
        echo '</tr>'; 
        
        $gesamtmenge = $gesamtmenge + $zeile['menge'];
        $zaehler++;  
    }

    echo '<tr>';
    echo '<td class="text_fett" align="right">Gesamtanzahl Alarme diese KW:</td><td>'.$gesamtmenge.'</td>';
    echo '</tr>';
    
    
echo '</table>';    
}

include('segment_fuss.php');
?>
