<?php
###### Editnotes ######
#$LastChangedDate: 2012-01-30 10:20:29 +0100 (Mo, 30 Jan 2012) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

$hau_id=$_GET['hau_id'];
$nagios_id=$_GET['n_probid'];
$nagios_id2=$_GET['n_lprobid'];   

$sql = 'SELECT hau_beschreibung FROM aufgaben WHERE hau_id = '.$hau_id;         

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
        $bis_zum_gesuchten_string = explode("Service:", $zeile['hau_beschreibung']);
        $ab_service = explode("Host", $bis_zum_gesuchten_string[1]);
        $service = trim($ab_service[0]);
        
        $bis_zum_gesuchten_string = explode("Host:", $zeile['hau_beschreibung']);
        $ab_string = explode("Address", $bis_zum_gesuchten_string[1]);
        $host = trim($ab_string[0]);
        
        $bis_zum_gesuchten_string = explode("State:", $zeile['hau_beschreibung']);
        $ab_string = explode("Date", $bis_zum_gesuchten_string[1]);
        $state = trim($ab_string[0]);
        
        $bis_zum_gesuchten_string = explode("Info:", $zeile['hau_beschreibung']);
        $infopart = trim($bis_zum_gesuchten_string[1]);
    }

if($nagios_id != 0)
{    

    # Ermittle, ob der Alarm schon in der Datenbank vorliegt
    
    $sql_check = 'SELECT hal_nagiosid FROM alarme WHERE hal_nagiosid = "'.$nagios_id.'"';
    
    if (!$ergebnis_check=mysql_query($sql_check, $verbindung)) 
        {
        fehler();
        }    
    
      if(mysql_num_rows($ergebnis_check)>0)
      {
      
          # Update auf Alarm
          
          $sql = 'UPDATE alarme SET
                hal_meldung = "'.$infopart.'",
                hal_status = "'.$state.'",
                hal_service = "'.$service.'"
                WHERE hal_nagiosid = "'.$nagios_id.'"';
                
        if (!$ergebnis=mysql_query($sql, $verbindung)) 
        {
        fehler();
        }
       
      }
      else 
      {
        # Neuen Alarm einfügen

        $sql = 'INSERT INTO alarme (hal_nagiosid, hal_hauid, hal_meldung, hal_status, hal_cciid, hal_service)
                VALUES ("'.$nagios_id.'", "'.$hau_id.'","'.$infopart.'","'.$state.'", "'.$host.'", "'.$service.'")';         
                 
                   
        if (!$ergebnis=mysql_query($sql, $verbindung)) 
            {
            fehler();
            }
      }
} else
{
    
        #  Alarm in Historie verschieben 
        
        $sql=   'SELECT * FROM alarme 
                WHERE hal_nagiosid = "'.$nagios_id2.'"';
                

        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }

        while ($zeile=mysql_fetch_array($ergebnis))
        {
        
        $sql_insert = 'INSERT INTO alarme_historie (hal_nagiosid, hal_hauid, hal_meldung, hal_status, hal_cciid, hal_service)
                VALUES ("'.$nagios_id2.'", "'.$zeile['hal_hauid'].'","'.$zeile['hal_meldung'].'","'.$zeile['hal_status'].'", "'.$zeile['hal_cciid'].'", "'.$zeile['hal_service'].'")';         
                 
                   
        if (!$ergebnis_insert=mysql_query($sql_insert, $verbindung)) 
            {
            fehler();
            }    
    
        $sql_delete = 'DELETE FROM alarme WHERE hal_nagiosid = "'.$nagios_id2.'"';         

        if (!$ergebnis_delete=mysql_query($sql_delete, $verbindung))
            {
            fehler();
            }    
        }
}
?>