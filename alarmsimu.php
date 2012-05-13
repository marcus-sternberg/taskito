<?php
  require_once('konfiguration.php');
  
 # $uid_start = '20110902213301.81886700'; //BERTOM01
 # $uid_start = '20110903192635.36490200'; //HAMWEB01
 # $uid_start = '20110902213510.80637500'; //HOST01
  $uid_start = '20110903184704.94298700'; //Array1
  
  

  $alarm_id = '';
  $alarm_stufe = '5';
  $alarm_ticket = '222222';
  $zustand = $alarm_stufe;
  
  
  
  
  
  # Schreibe den Alarm in die Alarmtabelle

$sql = 'INSERT INTO cmdb_alarme (calarm_uuid, calarm_stufe, calarm_ticket) VALUES ("'.$uid_start.'", "'.$alarm_stufe.'", "'.$alarm_ticket.'")';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
 
  # Suche aus der Beziehungstabelle die Ketten raus, in denen das UID vorkommt
  
  $ketten=array();
  $index=0;
  
  $sql = 'SELECT cbezuid_kette, cbezuid_pos FROM cmdb_beziehung_uid WHERE cbezuid_uid = '.$uid_start;

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
        $index++;
        $ketten[$index]['kette'] = $zeile['cbezuid_kette'];
        $ketten[$index]['pos'] = $zeile['cbezuid_pos'];
    }
    
  #  var_dump($ketten);
   # exit;
    
  # Nimm dabei nur die, deren Position größer ist als das vom UID

  $betroffene_uid = array();
  
  foreach($ketten AS $ketten_id)
  {
  
  $sql = 'SELECT cbezuid_uid FROM cmdb_beziehung_uid WHERE cbezuid_kette = '.$ketten_id['kette'].' AND cbezuid_pos > '.$ketten_id['pos'];

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
        $betroffene_uid[] = $zeile['cbezuid_uid'];
    }
    $betroffene_uid[] = $uid_start;
  }
  
  #   var_dump($betroffene_uid);
  
  # Setze den Status der UID neu
  
    foreach($betroffene_uid AS $UUID)
  {
  
   $sql = 'UPDATE cmdb_zustand SET czustand = '.$zustand.' WHERE czustand_uuid = "'.$UUID.'"';


   
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
  
  }
?>
