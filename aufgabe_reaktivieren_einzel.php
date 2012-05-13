<?php
###### Editnotes ######
#$LastChangedDate: 2012-04-23 17:30:50 +0200 (Mo, 23 Apr 2012) $
#$Author: bpetersen $ 
#####################
require_once('konfiguration.php');

// Auskommentiert, da das Script auch durch den Mailparser aufrufbar sein muss
# include('segment_session_pruefung.php');
# include('segment_init.php');
$task_id=$_GET['hau_id'];
$hma_id=$_GET['hma_id'];
$betroffene = array(); 
$mailliste = array();

# Lege Blacklist an

$feld_index = 0; 
$sql_blacklist= 'SELECT hbl_mail, hbl_aktion FROM blacklist WHERE hbl_aktiv = 1 AND (hbl_aktion = 2 OR hbl_aktion = 0)';      

if (!$ergebnis_blacklist=mysql_query($sql_blacklist, $verbindung))
    {
    fehler();
    }

while ($zeile_blacklist=mysql_fetch_array($ergebnis_blacklist))
    {
      $feld_index++;
      $eMail_blacklist[]=$zeile_blacklist['hbl_mail'];
    }

// Lese alle Betroffenen aus
// 1. Den Inhaber der Aufgabe

$sql = 'SELECT hau_inhaber FROM aufgaben WHERE hau_id = '.$task_id;

  if(!($ergebnis = mysql_query($sql, $verbindung)))
        { fehler(); }

  while ($zeile = mysql_fetch_array($ergebnis)) 
  { 
   $betroffene[$zeile['hau_inhaber']] = $zeile['hau_inhaber'];     
  }
     
// 2. Die Bearbeiter der Aufgabe

$sql = 'SELECT uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid = '.$task_id;

  if(!($ergebnis = mysql_query($sql, $verbindung)))
        { fehler(); }

  while ($zeile = mysql_fetch_array($ergebnis)) 
  { 
   $betroffene[$zeile['uau_hmaid']] = $zeile['uau_hmaid'];     
  }  

    
  // Entferne doppelte ID wenn z.B. Inhaber und Bearbeiter gleich sind
  
  $betroffene = array_unique($betroffene);
                            
                                         
  // Entferne die ID der Wiederöffners, da dieser keine Info braucht
  
  foreach($betroffene AS $key => $inhalt)
  {
  
      if($_SESSION['hma_id']==$key)
      {
        unset($betroffene[$key]); 
        sort($betroffene);         
      }

   }
   

  // Informiere alle betroffenen MA per NEWS
  
  foreach($betroffene AS $key => $inhalt)
  {
    $hauid=$task_id;
    $initiator=$hma_id;
    $empfaenger=$inhalt;
    $info='Die Aufgabe '.$task_id.' wurde wieder geöffnet.';
            
    include('segment_news.php');
  }
  // Lese alle Mails der MA aus, auch den Mailverteiler der Aufgabe
  // Lese zunächst alle Mails der MA ein, die Mails haben wollen und in der Aufgabe beschäftigt sind
   
   $sql =   'SELECT hma_id, hma_mail FROM aufgaben_mitarbeiter
            INNER JOIN mitarbeiter ON hma_id = uau_hmaid 
            INNER JOIN maileinstellungen ON hma_id = ume_hmaid
            WHERE hma_aktiv = 1 AND ume_aufgabestatus = 1 AND uau_hauid = '.$task_id;
            
      if(!($ergebnis = mysql_query($sql, $verbindung)))
        { fehler(); }

  while ($zeile = mysql_fetch_array($ergebnis)) 
  { 
   $mailliste[$zeile['hma_id']] = $zeile['hma_mail'];     
  }          
   
 
  // Lese den Inhaber der Aufgabe aus, der Mails haben will und nicht der Taskscout selbst ist (hma_id = 1)
  // Ist der Inhaber schon erfasst, wird er hier überschrieben und nicht doppelt angelegt - ansonsten hinzugefügt
   
   $sql =   'SELECT hma_id, hma_mail FROM aufgaben
            INNER JOIN mitarbeiter ON hma_id = hau_inhaber 
            INNER JOIN maileinstellungen ON hma_id = ume_hmaid
            WHERE hma_aktiv = 1 AND ume_aufgabestatus = 1 AND hau_id = '.$task_id;
            
      if(!($ergebnis = mysql_query($sql, $verbindung)))
        { fehler(); }

  while ($zeile = mysql_fetch_array($ergebnis)) 
  { 
   $mailliste[$zeile['hma_id']] = $zeile['hma_mail'];     
  }          
                                        
  
  // Lese eventuell vorhandene Mailverteiler
  
   $sql =   'SELECT uti_mail FROM ticket_info
             WHERE uti_aktiv = 1 AND uti_hauid = '.$task_id;
            
      if(!($ergebnis = mysql_query($sql, $verbindung)))
        { fehler(); }

  while ($zeile = mysql_fetch_array($ergebnis)) 
  { 
      if(!(in_array($zeile['uti_mail'],$eMail_blacklist)))
    {
    $mailliste[] = $zeile['uti_mail'];      
    }  
  }          
   // Lese letzten Kommentar
  
    $sql =   'SELECT ulo_text FROM log
             WHERE ulo_aufgabe = '.$task_id.' ORDER BY ulo_datum DESC LIMIT 1';
            
      if(!($ergebnis = mysql_query($sql, $verbindung)))
        { fehler(); }

  while ($zeile = mysql_fetch_array($ergebnis)) 
  { 
   $kommentar = nl2br(htmlspecialchars($zeile['ulo_text']));   
  }
  
  // Lese Ticketdaten
  
   $sql =   'SELECT * FROM aufgaben
             WHERE hau_id = '.$task_id;
            
      if(!($ergebnis = mysql_query($sql, $verbindung)))
        { fehler(); }

  while ($zeile = mysql_fetch_array($ergebnis)) 
  { 
  
 
  // Sende an alle Info über Ticketwiederöffnung
   
   foreach($mailliste AS $key => $mail)
   {
             $mail_text =
                '
    <html>
    <head>
    <style>
    <!--
              table.is24_mail
                {
                border-collapse: collapse;
                border: 1px solid #FFCA5E;
                }

            caption.is24
                {
                font: 1.8em/ 1.8em Arial, Helvetica, sans-serif;
                text-align: left;
                text-indent: 10px;
                color: #FFAA00;
                }

            thead.is24 th.is24
                {
                font-family: Arial, Helvetica, sans-serif;  
                color: #2c2c2c;
                font-size: 1.2em;
                font-weight: bold;
                text-align: left;
                border-right: 1px solid #FCF1D4;
                }


            tbody.is24 tr.is24
                {
                background: #FFF8E8 ;
                } 

            tbody.is24 th.is24, td.is24
                {
                font-size: 12px;
                font-family: Arial, Helvetica, sans-serif;
                color: #514F4F;
                border-top: 1px solid #FFCA5E; 
                 padding: 10px 7px; 
                text-align: left;
                }
    -->
    </style>
    </head>';

              $mail_text.="<body><table class='is24_mail' width='600'>\n";
           $mail_text.="<caption class='is24'>";
            $mail_text
                .="<img src='http://www.insolitus.de/img/tom_small.gif'></img>\n";
            $mail_text.="</caption>";  

            $mail_text.="<thead class='is24'>";   
            $mail_text.="<tr class='is24'><th class='is24'>Ticket-Service</th><th class='is24'>" . date('H:i d.m.Y') . "</th></tr>";
            $mail_text.="</thead>";   
            $mail_text.="<tbody class='is24'>";   
            $mail_text
                .="<tr class='is24'><td class='is24' nowrap valign='top'>Das Ticket wurde wieder geöffnet :</td><td class='is24'> "
                . $task_id . "</td></tr>\n";                

            $mail_text
                .="<tr class='is24'><td class='is24' valign='top'>Ticketthema :</td><td class='is24'> "
                . $zeile['hau_titel'] . "</td></tr>\n";
            $mail_text
                .="<tr class='is24'><td class='is24' valign='top'>Inhalt :</td><td class='is24'> "
                . htmlspecialchars(substr($zeile['hau_beschreibung'],0,500)) . " ...</td></tr>\n";
            $mail_text
                .="<tr class='is24'><td class='is24' valign='top'>Kommentar :</td><td class='is24'> "
                . $kommentar . "</td></tr>\n";
              $mail_text.="</tbody></table>";                 
   
               $xsubject=$zeile['hau_titel'];

            if ( (strlen($zeile['hau_otrsnr']) > 1 ) && ( strpos($xsubject,$zeile['hau_otrsnr']) < 1 ) ) { $xsubject=$xsubject."[Ticket#".$zeile['hau_otrsnr']."]"; }
          
            $betreff=htmlspecialchars(substr($xsubject,0,100)).'-'.$mail_info.': Wiedereroeffnung >Ticket ID ' . $task_id. '< ';  
      
          
            $header  = "MIME-Version: 1.0\r\n";
            $header .= "Content-type: text/html; charset=utf-8\r\n";
            $header .= "Content-Transfer-Encoding: 8-bit\r\n";
            $header .= "Return-Path: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "Reply-To: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "From: Taskscout24 <taskscout24@immobilienscout24.de>\r\n"; 
            $header .= "Date: " . date('r')."\r\n";
            $temp_ary = explode(' ', (string) microtime());
            $header .= "Message-Id: <" . date('YmdHis') . "." . substr($temp_ary[0],2) . "@immobilienscout24.de>\r\n"; 
            

            #echo $zeile_mail['uti_mail'].'<br>'.$betreff.'<br>'.$mail_text.'<br>'.$header;
            mail($mail, $betreff, $mail_text, $header, '-ftaskscout24@immobilienscout24.de');
            } // ende if-Abfrage Mailadresse vorhanden
   }

$sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
    'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $hma_id
    . '", "Die Aufgabe wurde wieder geöffnet", NOW() )';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }
    
    
# Ermittle vorherige Bearbeiter
 
$sql = 'SELECT uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid = '.$task_id;

  if(!($ergebnis = mysql_query($sql, $verbindung)))
        { fehler(); }

  $bearbeiter = '';
  
  while ($zeile = mysql_fetch_array($ergebnis)) 
  { 
   $bearbeiter .= '+' . $zeile['uau_hmaid'];     
  }

$sql='INSERT INTO eventlog (hel_area, hel_type, hel_referer, hel_text) ' .
    'VALUES ("TASK", "REOPEN", "' . $task_id . '", "Aufgabe wurde durch Mailkommentar erneut geöffnet und in die Gruppenqueue verschoben. Vorherige Bearbeiter: '.$bearbeiter.'.")';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }
    
$sql='UPDATE aufgaben SET hau_abschluss = 0 WHERE hau_id = "' . $task_id . '"';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

/*
if ($hma_id != "1")
    {
    $sql='UPDATE aufgaben_mitarbeiter SET uau_status = 0 WHERE uau_hmaid = ' . $hma_id . ' AND uau_hauid = "' . $task_id
        . '"';
    }
else
    {
    $sql='UPDATE aufgaben_mitarbeiter SET uau_status = 0 WHERE uau_hauid = "' . $task_id . '"';
    }
    
*/

# Lösche alle vorhandenen Bearbeiter für die Aufgabe
    $sql='DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid = "' . $task_id . '"';

 
if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

# Lösche Zuordnung und erhalte nur die Gruppe für die Aufgabe
    $sql='UPDATE aufgaben_zuordnung SET uaz_pba =0, uaz_sba=0, uaz_sg=0 WHERE uaz_hauid = "' . $task_id . '"';

 
if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

    
    
// Zurueck zur Liste

header('Location: schreibtisch_meine_aufgaben.php');
exit;
?>
