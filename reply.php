<?php
###### Editnotes #####
#$LastChangedDate: 2012-04-23 17:30:50 +0200 (Mo, 23 Apr 2012) $
#$Author: bpetersen $ 
#####################

### Lese die Konfiguration ein ###

require_once('konfiguration.php');

### Werte übergebene Parameter aus ###

$task_id=$_GET['hau_id'];          // Ticketnummer
$aktions_id=$_GET['aktions_id'];   // gewünschte Aktion (1=> Neuanlage, 2=> Kommentar)
if(isset($_GET['sender'])) {$sender=$_GET['sender'];} else {$sender='';}           // Absender des Kommentars
if(isset($_GET['flag_closed'])) {$flag_closed=$_GET['flag_closed'];} else {$flag_closed=0;}           // Absender des Kommentars     

$sender = strtolower(trim($sender));
$sender = preg_replace("/\\\/","",$sender);
     
### Initialisiere eMail-Felder ###

$sende_eMail_an = array();          // Liste der anzuschreibenden Mailadressen
$eMail_blacklist = array();         // Liste der gesperrten Mailadressen
$feld_index = 0;                   // Index für das eMail-Feld 
$otrs_gefunden = 0;                // Erkenne Mails an OTRS
$mail_to_html = array();            // Sammle hier alle Mailadressen [TO] mit HTML-Versand
$mail_to_ascii = array();           // Sammle hier alle Mailadressen [TO] mit ASCII-Versand
$mail_cc_html = array();            // Sammle hier alle Mailadressen [CC] mit HTML-Versand
$mail_cc_ascii = array();           // Sammle hier alle Mailadressen [CC] mit ASCII-Versand

### Lese blacklist für den Mailversand ###

$sql_blacklist= 'SELECT hbl_mail, hbl_aktion FROM blacklist WHERE hbl_aktiv = 1 AND (hbl_aktion = '.$aktions_id.' OR hbl_aktion = 0)';         

if (!$ergebnis_blacklist=mysql_query($sql_blacklist, $verbindung))
    {
    fehler();
    }

while ($zeile_blacklist=mysql_fetch_array($ergebnis_blacklist))
    {
      $feld_index++;
      $eMail_blacklist[$feld_index]['mail']=$zeile_blacklist['hbl_mail'];
      $eMail_blacklist[$feld_index]['aktion']=$zeile_blacklist['hbl_aktion'];  
    }
  

$schwarze_liste = array();
   
foreach($eMail_blacklist AS $mail)
{
    $schwarze_liste[] = $mail['mail'];
}

if($aktions_id==2)
{
$schwarze_liste[] = $sender;  // Setze den Sender der Mail bei Kommentaren auch auf die schwarze Liste temporär, keine Mail senden
}

### Baue die Liste der Mailadressen zusammen, die ins AN-Feld gehören

## Erst die externen - die sind alle HTML

$sql_mail_to = 'SELECT * FROM ticket_info WHERE uti_hauid = ' . $task_id.' AND uti_aktiv = 1 AND uti_status=1';
         
if (!($ergebnis_mail_to=mysql_query($sql_mail_to, $verbindung)))
{
   fehler();
}

while ($zeile_mail_to=mysql_fetch_array($ergebnis_mail_to))
{
   if($zeile_mail_to['uti_mail']=='ticket@otrs.cc.is24.loc')
        {$otrs_gefunden = 1;} 
   else
        {$mail_to_html[] = $zeile_mail_to['uti_mail'];}
}

array_diff_ORG_NEW($mail_to_html, $schwarze_liste, 'VALUES');        
  
$mail_to_html = array_unique($mail_to_html);



## Damit haben wir alle externen Adressen im mail_to_feld, die HTML-Mails kriegen

## Jetzt addiere die internen Empfänger dazu

## Zunächst die HTML-Empfänger

$feld_index = 0;

if($aktions_id > 1) // 1 = Neuanlage, da muss man keine internen berücksichtigen
{
$sql_intern = 'SELECT ume_format, hma_mail, hma_level, hma_id FROM aufgaben 
              LEFT JOIN mitarbeiter ON hma_id = hau_inhaber 
              LEFT JOIN maileinstellungen on hau_inhaber = ume_hmaid
              WHERE hau_aktiv = 1 AND ume_format = 1 AND ume_kommentar_erhalten = 1 AND hau_id = ' . $task_id .'
UNION
              SELECT ume_format, hma_mail, hma_level, hma_id FROM aufgaben 
              LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid
              LEFT JOIN mitarbeiter ON hma_id = uau_hmaid 
              LEFT JOIN maileinstellungen on uau_hmaid = ume_hmaid
              WHERE hau_aktiv = 1 AND ume_format = 1 AND ume_kommentar_erhalten = 1 AND hau_id =' . $task_id; 

if (!$ergebnis_intern = mysql_query($sql_intern, $verbindung))
    {
    fehler();
    }

while ($zeile_intern = mysql_fetch_array($ergebnis_intern))
    {
      $mail_to_html[] = $zeile_intern['hma_mail'];
      
      $feld_index++;
      $sende_eMail_an[$feld_index]['hma_id'] = $zeile_intern['hma_id'];  
      }
  
## Jetzt die ASCII Empfänger

$sql_intern = 'SELECT ume_format, hma_mail, hma_level, hma_id FROM aufgaben 
              LEFT JOIN mitarbeiter ON hma_id = hau_inhaber 
              LEFT JOIN maileinstellungen on hau_inhaber = ume_hmaid
              WHERE hau_aktiv = 1 AND ume_format = 0 AND ume_kommentar_erhalten = 1 AND hau_id = ' . $task_id .'
UNION
              SELECT ume_format, hma_mail, hma_level, hma_id FROM aufgaben 
              LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid
              LEFT JOIN mitarbeiter ON hma_id = uau_hmaid 
              LEFT JOIN maileinstellungen on uau_hmaid = ume_hmaid
              WHERE hau_aktiv = 1 AND ume_format = 0 AND ume_kommentar_erhalten = 1 AND hau_id =' . $task_id; 

if (!$ergebnis_intern = mysql_query($sql_intern, $verbindung))
    {
    fehler();
    }

while ($zeile_intern = mysql_fetch_array($ergebnis_intern))
    {
      $mail_to_ascii[] = $zeile_intern['hma_mail'];
      
      $feld_index++;
      $sende_eMail_an[$feld_index]['hma_id'] = $zeile_intern['hma_id'];  

      }

## intern sollte keiner auf der Blacklist stehen, die schwarze Liste entfällt    
      
## Alle Adressen aus dem HTML-Array in String umwandeln

   } // Ende aktion > 1 (Kommentar)

## Umwandeln in Strings

$mail_to_html = implode(",", $mail_to_html);   
$mail_to_ascii = implode(",", $mail_to_ascii);     

## Jetzt noch die Empfänger auf CC ermitteln, die sind immer HTML

## Prüfe wieder die Blacklist

$schwarze_liste = array();
   
foreach($eMail_blacklist AS $mail)
{
    $schwarze_liste[] = $mail['mail'];
}

if($aktions_id==2)
{
$schwarze_liste[] = $sender;  // Setze den Sender der Mail bei Kommentaren auch auf die schwarze Liste temporär, keine Mail senden
}

$sql_mail_cc = 'SELECT * FROM ticket_info WHERE uti_hauid = ' . $task_id.' AND uti_aktiv = 1 AND uti_status=0';
         
if (!($ergebnis_mail_cc=mysql_query($sql_mail_cc, $verbindung)))
{
   fehler();
}

while ($zeile_mail_cc=mysql_fetch_array($ergebnis_mail_cc))
{
   if($zeile_mail_cc['uti_mail']=='ticket@otrs.cc.is24.loc')
        {$otrs_gefunden = 1;} 
   else
        {$mail_cc_html[] = $zeile_mail_cc['uti_mail'];}
}
    
array_diff_ORG_NEW($mail_cc_html, $schwarze_liste, 'VALUES');        

$mail_cc_html = array_unique($mail_cc_html);
$mail_cc_html = implode(",", $mail_cc_html); 


  
### Holen wir uns die benötigten Texte
## Zuerst holen wir uns die Textbausteine je nach übergebener Aktion

# Aktion

switch ($aktions_id)
{
    case 1:
    $aktions_text = htmlspecialchars('Für die Anfrage wurde ein neues Ticket angelegt.');
    $aktion_betreff = 'Neues Ticket';
    break;
    
    case 2:
    $aktions_text = htmlspecialchars('Für dieses Ticket wurde ein neuer Kommentar per Mail hinterlegt');
    $aktion_betreff = 'Neuer Kommentar';   
    break;             
}
                         
## Jetzt holen wir uns die passenden Texte und Inhalte aus dem Ticket  

# Tickettitel, Ticketkommentar

$sql_ticket = ' SELECT hau_titel, hau_beschreibung, ulo_text, uti_md5, hau_prio, hau_otrsnr FROM aufgaben 
                LEFT JOIN log ON ulo_aufgabe = hau_id 
                LEFT JOIN ticket_info ON hau_id = uti_hauid 
                WHERE hau_aktiv = 1 AND hau_id = ' . $task_id;
   
if (!$ergebnis_ticket = mysql_query($sql_ticket, $verbindung))
   {
     fehler();
   }

   
while ($zeile_ticket = mysql_fetch_array($ergebnis_ticket))
   {
      $ticket_titel = htmlspecialchars($zeile_ticket['hau_titel']);
      if($aktions_id>1)
      {   
        $ticket_beschreibung_html = nl2br(htmlspecialchars(substr($zeile_ticket['hau_beschreibung'],0,500))).'  [...]';  
        $ticket_beschreibung_ascii = Preg_Replace('/<br(\s+)?\/?>/i', "\n",htmlspecialchars(substr($zeile_ticket['hau_beschreibung'],0,500))).'  [...]';
      } else
      {
        $ticket_beschreibung_html = nl2br(htmlspecialchars($zeile_ticket['hau_beschreibung']));
        $ticket_beschreibung_ascii = Preg_Replace('/<br(\s+)?\/?>/i', "\n",htmlspecialchars($zeile_ticket['hau_beschreibung']));
      }
      $md5_string = $zeile_ticket['uti_md5'];    
      $hau_otrsnr = $zeile_ticket['hau_otrsnr'];
      $ticket_prio = $zeile_ticket['hau_prio']; 
   }

# URI zum Ticket

    $url_mail_intern = "http://taskscout24.rz.is24.loc/aufgabe_ansehen.php?hau_id=". $task_id ;         
    $url_mail_extern = "http://taskscout24.prod/ticket_anzeigen.php?ticket_nr=". $md5_string;
   
# Kommentar auslesen

if($aktions_id>1)   // Bei Neuanlage können wir den Teil überspringen
{
$sql_kommentar = 'SELECT ulo_text, hma_name, hma_vorname, hma_telefon FROM log LEFT JOIN mitarbeiter ON hma_id = ulo_ma WHERE ulo_aufgabe = ' . $task_id . ' ORDER BY ulo_datum DESC LIMIT 1';

if (!$ergebnis_kommentar = mysql_query($sql_kommentar, $verbindung))
{
   fehler();
}

while ($zeile_kommentar=mysql_fetch_array($ergebnis_kommentar))
{
    $ticket_kommentar_html = nl2br(htmlspecialchars($zeile_kommentar['ulo_text']));
            
    $ersetze='\r\n';
    $suche='<br />';
    $ticket_kommentar_ascii=str_replace($suche, $ersetze, $zeile_kommentar['ulo_text']);
    $ticket_kommentar_ascii = strip_tags($ticket_kommentar_ascii);  
}
} else
{  
    $ticket_kommentar_html = '';
    $ticket_kommentar_ascii = ''; 
} 

   
### Bevor die Mail rausgeht, noch die Info ins Newsfach für die internen schreiben (Bei neuen Tickets gibts keine internen in der Liste)

foreach($sende_eMail_an AS $Adressat)
{
            $hauid=$task_id;
            $initiator= 1;
            $empfaenger=$Adressat['hma_id'];
            $info=$aktions_text;
            include('segment_news.php');
}

### Prüfe, ob das Ticket geschlossen werden soll

if($flag_closed==1)
{
   
## Schließe die Aufgabe

## Beende die Aufgabe für die Bearbeiter

       // Setze Hauptaufgabe auf FERTIG
        $sql_ende=
            'UPDATE aufgaben SET hau_abschlussdatum = NOW(), hau_abschluss = 1 WHERE hau_id = "' . $task_id . '"';

        if (!($ergebnis_ende=mysql_query($sql_ende, $verbindung)))
            {
            fehler();
            }

    // Beende die Unteraufgabe des Mitarbeiter
    $sql='UPDATE aufgaben_mitarbeiter SET uau_status = "1", uau_stopp="0" WHERE uau_hauid = "' . $task_id . '"';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    // Loesche eventuell noch vorhandene PINGs

    $sql=
        'UPDATE log_status 
        LEFT JOIN log ON ulo_id = uls_uloid 
        SET uls_ping_an = "0", uls_ping_von = "0" WHERE ulo_aufgabe = ' . $task_id;

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }


    // Schreibe einen Kommentar ins Log für den Bearbeiter

    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $sender . '", "Aufgabe wurde abgeschlossen", NOW() )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    // Vermerke im Aktivitaetslog den Abschluss

    $sql='insert INTO log (ulo_aufgabe, ulo_text, ulo_ma, ulo_datum, ulo_requestor) values ( "'.$task_id.'", "Aufgabe wurde durch '.$sender.' geschlossen.", "1", NOW(), "1")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    
    
$ticket_kommentar_html = $ticket_kommentar_html . "\r\nDas Ticket wurde geschlossen.\r\n";
$ticket_kommentar_ascii = $ticket_kommentar_ascii . "\r\nDas Ticket wurde geschlossen.\r\n";     
                          
}                         
  
### Definiere die Mailrelevanten Strings

## HTML Mail

## Headerfile

    $mail_header_html  = "MIME-Version: 1.0\r\n";
    $mail_header_html .= "Content-type: text/html; charset=utf-8\r\n";
    $mail_header_html .= "Content-Transfer-Encoding: 8-bit\r\n";
    $mail_header_html .= "Return-Path: taskscout24@immobilienscout24.de\r\n"; 
    $mail_header_html .= "Reply-To: taskscout24@immobilienscout24.de\r\n"; 
    $mail_header_html .= "From: Taskscout24 <taskscout24@immobilienscout24.de>\r\n";
    if($mail_cc_html!='')
    {$mail_header_html .= "CC: ".$mail_cc_html."\r\n";}
    $mail_header_html .= "Date: " . date('r')."\r\n";
    $temp_ary = explode(' ', (string) microtime());
    $mail_header_html .= "Message-Id: <" . date('YmdHis') . "." . substr($temp_ary[0],2) . "@immobilienscout24.de>\r\n"; 
    $mail_header_html .= "X-TASKSCOUT-Priority: ".$ticket_prio."\r\n"; 

## Betreff
    if($hau_otrsnr!=''){
    if ( ( strpos($ticket_titel,$hau_otrsnr) < 1 ) && ( strlen($hau_otrsnr) > 0 ) ) { $ticket_titel = $ticket_titel."[Ticket#".$hau_otrsnr."]"; }}
    $mail_betreff_html= substr($ticket_titel,0,100).' - '.$aktion_betreff . ' >Ticket ID ' . $task_id . '< ';   
   
## Mailbody 

    $mail_text_html =
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

            $mail_text_html.="<body><table class='is24_mail' width='600'>\n";
                      
            $mail_text_html.="<caption class='is24'>";
            $mail_text_html
                .="<img src='http://www.insolitus.de/img/tom_small.gif'></img>\n";
            $mail_text_html.="</caption>";
            
            $mail_text_html.="<thead class='is24'>";   
            $mail_text_html.="<tr class='is24'><th class='is24'>News-Center</th><th class='is24'>" . date('H:i d.m.Y') . "</th></tr>";
            $mail_text_html.="</thead>";   
            $mail_text_html.="<tbody class='is24'>";   
            $mail_text_html
                .="<tr class='is24'><td class='is24' nowrap valign='top'>Ticket :</td><td class='is24'> "
                . $task_id . "</td></tr>\n";
            $mail_text_html
                .="<tr class='is24'><td class='is24' valign='top'>Ticketthema :</td><td class='is24'> "
                . $ticket_titel . "</td></tr>\n";
            if ($ticket_kommentar_html !='')
            {
             $mail_text_html
                .="<tr class='is24'><td class='is24' valign='top' colspan='2'>Kommentar :<br><br>" . $ticket_kommentar_html . "</td></tr>\n";
            }  
            $mail_text_html
                .="<tr class='is24'><td class='is24' valign='top'>Inhalt :</td><td class='is24'> "
                . $ticket_beschreibung_html . " </td></tr>\n";
            $mail_text_html
                .="<tr class='is24'><td class='is24' valign='top'>Aktion :</td><td class='is24'> "
                . htmlspecialchars($aktions_text) . "</td></tr>\n";
            $mail_text_html
                .="<tr class='is24'><td class='is24' valign='top'>durch :</td><td class='is24'> "
                . $sender . "</td></tr>\n";      
                           
            $mail_text_html 
                .="<tr class='is24'><td class='is24' nowrap valign='top'>Link zum Ticket :</td><td class='is24'><a href='".$url_mail_intern."'>interner Link zum Ticket " . $task_id . "</a><br><a href='".$url_mail_extern."'>externer Link zum Ticket " . $task_id . "</a></td></tr>\n";
            $mail_text_html .="</tbody></table>";  
            
            
## ASCII Mailtext

    //Mail Body - Position, background, font color, font size...
   
            $mail_betreff_ascii= substr($ticket_titel,0,100).'-'.$aktion_betreff . ' >Ticket ID ' . $task_id . '< ';  
                 
            $mail_text_ascii
                ="Ticketnummer : ". $task_id . "\r\n\r\n";    
            $mail_text_ascii
                .="Ticket: " . $ticket_titel . "\r\n\r\n";
            $mail_text_ascii
                .="Ticketinhalt:\n ". $ticket_beschreibung_ascii . "\r\n\r\n";
            $mail_text_ascii
                .="Aktion : ". $aktions_text . "\r\n\r\n";   
             $mail_text_ascii
                .="bearbeitet von: " . $sender . "\r\n\r\n";
            if ($ticket_kommentar_ascii !='')
            {
             $mail_text_ascii
                .="Kommentar :\r\n\r\n" . $ticket_kommentar_ascii . "\r\n";
            }                 

            $mail_betreff_ascii= substr($ticket_titel,0,100).' - '.$aktion_betreff . ' >Ticket ID ' . $task_id . '< ';  
                 
            $mail_text_ascii
                ="Ticketnummer : ". $task_id . "\r\n\r\n";    
             $mail_text_ascii
                .="bearbeitet von: " . $sender . "\r\n\r\n";
            if ($ticket_kommentar_ascii !='')
            {
             $mail_text_ascii
                .="Kommentar :\r\n\r\n" . $ticket_kommentar_ascii . "\r\n";
            }   
             
             $mail_text_ascii  
            .= "interner Link zum Ticket : ".$url_mail_intern."\n\nexterner Link zum Ticket : ".$url_mail_extern."\r\n";
             
            $mail_header_ascii = "From: Taskscout24 <taskscout24@immobilienscout24.de>\r\n";
            $mail_header_ascii .= "MIME-Version: 1.0\r\n";
            $mail_header_ascii .= "Content-type: text/plain; charset=utf-8\r\n";
            $mail_header_ascii .= "Content-Transfer-Encoding: 8-bit\r\n";
            $mail_header_ascii .= "Return-Path: taskscout24@immobilienscout24.de\r\n"; 
            $mail_header_ascii .= "Reply-To: taskscout24@immobilienscout24.de\r\n"; 
            $mail_header_ascii .= "Date: " . date('r')."\r\n";
            $temp_ary = explode(' ', (string) microtime());
            $mail_header_ascii .= "Message-Id: <" . date('YmdHis') . "." . substr($temp_ary[0],2) . "@immobilienscout24.de>\r\n";
            $mail_header_ascii .= "X-TASKSCOUT-Priority: ".$ticket_prio."\r\n";  


## Versende Mail
     
# ASCII

            if($mail_to_ascii!='')
            {
            mail($mail_to_ascii, $mail_betreff_ascii, $mail_text_ascii, $mail_header_ascii, '-ftaskscout24@immobilienscout24.de');
            }
          
# HTML
            if($mail_to_html!='')
            {
            mail($mail_to_html, $mail_betreff_html, $mail_text_html, $mail_header_html, '-ftaskscout24@immobilienscout24.de');
            }

if($otrs_gefunden==1 AND $sender!='ticket@otrs.cc.is24.loc') 
{  
                         
## Setze Mailtext zurück wegen der individuellen Linkbehandlung pro Mail
    
$mail_inhalt ="Kommentar :\r\n\r\n" . $ticket_kommentar_ascii . "\r\n";
$mail_betreff = $mail_betreff_ascii;

        mail('ticket@otrs.cc.is24.loc', $mail_betreff, $mail_inhalt, $mail_header_ascii, '-ftaskscout24@immobilienscout24.de');
       
            
  } 

#exit;
?>

