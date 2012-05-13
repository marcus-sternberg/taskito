<?php
###### Editnotes ######
#$LastChangedDate: 2012-04-23 17:30:50 +0200 (Mo, 23 Apr 2012) $
#$Author: bpetersen $ 
#####################

# Initilaisiere eMail-Adressfeld

$eMail = array();
$i = 0;

# Prüfe zuerst, ob die Mail an eine Gruppe oder an einen einzelnen Empfänger gehen soll

if(!isset($level_gruppe) OR $level_gruppe == 0)
{
$sql_mail='SELECT * FROM mitarbeiter INNER JOIN maileinstellungen ON ume_hmaid = hma_id ' .
    'WHERE hma_aktiv = 1 AND hma_id = ' . $mail_hma_id;
}
else
{
$sql_mail='SELECT * FROM mitarbeiter INNER JOIN maileinstellungen ON ume_hmaid = hma_id ' .
    'WHERE hma_aktiv = 1 AND hma_level = ' . $level_gruppe;
}    


// Frage Datenbank nach Suchbegriff
if (!$ergebnis_mail=mysql_query($sql_mail, $verbindung))
    {
    fehler();
    }

while ($zeile_mail=mysql_fetch_array($ergebnis_mail))
    {
      $i++;
      $eMail[$i]['mail']=$zeile_mail['hma_mail'];
      $eMail[$i]['flag']= $zeile_mail[$mailtag]; 
      $eMail[$i]['format']= $zeile_mail['ume_format']; 
    }

   
// Schick ne Mail an den Empfaenger

foreach($eMail AS $Einzelmail)
{
     
    if ($Einzelmail['flag'] == 1)
        {

        $sql_aufgabe=   'SELECT * FROM aufgaben 
                        WHERE hau_aktiv = 1 AND hau_id = ' . $mail_hau_id;

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_aufgabe=mysql_query($sql_aufgabe, $verbindung))
            {
            fehler();
            }

        while ($zeile_aufgabe=mysql_fetch_array($ergebnis_aufgabe))
            {

if($Einzelmail['format']=='0') // 1 = HTML, 0 = ASCII
{            

                $sql_comment=   'SELECT * FROM log  
                                LEFT JOIN mitarbeiter ON hma_id = ulo_ma 
                                WHERE ulo_aufgabe = ' . $mail_hau_id . ' 
                                ORDER BY ulo_datum DESC LIMIT 1';


                // Frage Datenbank nach Suchbegriff
                if (!$ergebnis_comment=mysql_query($sql_comment, $verbindung))
                    {
                    fehler();
                    }

                while ($zeile_comment=mysql_fetch_array($ergebnis_comment))
                    {
                $kommentar_text = $zeile_comment['ulo_text']; 
                $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
                $telefon = $_SESSION['hma_telefon'];
                $logdatum = $zeile_comment['ulo_datum']; 
                    }
                    
// Entfernt HTML-Umbruch
            $Daten['ulo_text']=Preg_Replace('/<br(\s+)?\/?>/i', "\n",$Daten['ulo_text']);   

            // Lösche eventuellen alten Mailtext
            
            $mail_text = '';
            
            
     
                
      //Mail Body - Position, background, font color, font size...
               
            if($Einzelmail['mail']!='ticket@otrs.cc.is24.loc')
            {     
            if($Daten['uau_status']=='on')
            {
            $mail_text
                ="Ticket ". $mail_hau_id. " wurde geschlossen.\r\n";               
            } else
            {
            $mail_text
                ="Ticket ". $mail_hau_id." wurde aktualisiert.\r\n";
            }
            $mail_text
                .="Ticketbetreff: " . $zeile_aufgabe['hau_titel'] . "\r\n\r\n";
             $mail_text
                .="Ticketkommentar:\n ". htmlspecialchars($kommentar_text) . "\r\n\r\n";
            $mail_text
                .="Ticketinhalt:\n " . htmlspecialchars(substr($zeile_aufgabe['hau_beschreibung'],0,500)) . "\r\n\r\n";
            }
              $mail_text
                .="durch: " . $kommentator . "\r\n\r\n";
             $mail_text
                .="um: " . zeitstempel_anzeigen($logdatum) . "\r\n\r\n";                

       
            $xsubject=$zeile_aufgabe['hau_titel'];

            if ( (strlen($zeile_aufgabe['hau_otrsnr']) > 1 ) && ( strpos($xsubject,$zeile_aufgabe['hau_otrsnr']) < 1 ) ) { $xsubject=$xsubject."[Ticket#".$zeile_aufgabe['hau_otrsnr']."]"; }
          
            $betreff=htmlspecialchars(substr($xsubject,0,100)).'-'.$mail_info.': >Ticket ID ' . $mail_hau_id. '< ';  
                 
            $header = "From: Taskscout24 <taskscout24@immobilienscout24.de>\r\n"; 
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-type: text/plain; charset=utf-8\r\n";
            $header .= "Content-Transfer-Encoding: 8-bit\r\n";
            $header .= "Return-Path: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "Reply-To: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "Date: " . date('r')."\r\n";
            $temp_ary = explode(' ', (string) microtime());
            $header .= "Message-Id: <" . date('YmdHis') . "." . substr($temp_ary[0],2) . "@immobilienscout24.de>\r\n";   
            $header .= "X-TASKSCOUT-Priority: ".$Daten['hau_prio']."\r\n";        
} else
{
                
            //Mail Body - Position, background, font color, font size...
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

            $suche='\r\n';
            $ersetze='<br />';
            // Verarbeitet \r\n's zuerst, so dass sie nicht doppelt konvertiert werden

            $mail_text.="<body><table class='is24_mail' width='600'>";
        
            $mail_text.="<caption class='is24'>";
            $mail_text
                .="<img src='http://www.insolitus.de/img/tom_small.gif'></img>";
            $mail_text.="</caption>";
        
            $mail_text.="<thead class='is24'>";   
            $mail_text.="<tr class='is24'><th class='is24'>News-Center</th><th class='is24'>" . date('H:i d.m.Y') . "</th></tr>";
            $mail_text.="</thead>";   
            $mail_text.="<tbody class='is24'>";   
            $mail_text
                .="<tr class='is24'><td class='is24' nowrap valign='top'>Aktualisierung des Tickets :</td><td class='is24'> "
                . $zeile_aufgabe['hau_id'] . "</td></tr>";
                       
            
            # Prüfe, ob der Empfänger in der Produktion sitzt, dann nutze interne URL
            
            $sql_produktion = 'SELECT hma_level FROM mitarbeiter WHERE hma_mail= "'.$Einzelmail['mail'].'"';
            if (!$ergebnis_produktion=mysql_query($sql_produktion, $verbindung))
                    {
                    fehler();
                    }

            if(mysql_num_rows($ergebnis_produktion)== 0)
            
            {
                $url_mail = "http://taskscout24.prod/";
            } else
            {
            while ($zeile_produktion=mysql_fetch_array($ergebnis_produktion))
                    {
                    if(in_array($zeile_produktion['hma_level'], array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '13')))
                    {$url_mail = "http://taskscout24.rz.is24.loc/";                     
                    } else
                                {
                $url_mail = "http://taskscout24.prod/";
                                }
                    
                    }
            }
            
            $mail_text
                .="<tr class='is24'><td class='is24' nowrap valign='top'>Link zum Ticket :</td><td class='is24'><a href='".$url_mail."aufgabe_ansehen.php?hau_id="
                . $zeile_aufgabe['hau_id'] . "'>Infos zu Ticket " . $zeile_aufgabe['hau_id'] . " anzeigen</a></td></tr>";
            $mail_text
                .="<tr class='is24'><td class='is24' valign='top'>Ticketthema :</td><td class='is24'> "
                . $zeile_aufgabe['hau_titel'] . "</td></tr>";
            if (isset($mail_comment))
            {
            if ($mail_comment == 1)
                {
                $sql_comment=   'SELECT * FROM log  
                                LEFT JOIN mitarbeiter ON hma_id = ulo_ma 
                                WHERE ulo_aufgabe = ' . $mail_hau_id . ' 
                                ORDER BY ulo_datum DESC LIMIT 1';


                // Frage Datenbank nach Suchbegriff
                if (!$ergebnis_comment=mysql_query($sql_comment, $verbindung))
                    {
                    fehler();
                    }

                while ($zeile_comment=mysql_fetch_array($ergebnis_comment))
                    {
            $Daten['ulo_text']=str_replace($suche, $ersetze, $Daten['ulo_text']);
             $mail_text
                .="<tr class='is24'><td class='is24' valign='top' colspan='2'>Kommentar :<br><br>" . nl2br(htmlspecialchars($zeile_comment['ulo_text'])) . "</td></tr>";
            $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
                    $telefon = $zeile_comment['hma_telefon'];
                    }
                }
             
            }
            
            $mail_text
                .="<tr class='is24'><td class='is24' valign='top'>Inhalt :</td><td class='is24'> "
                . nl2br(htmlspecialchars(substr($zeile_aufgabe['hau_beschreibung'],0,500))) . " ...</td></tr>";
            $mail_text
                .="<tr class='is24'><td class='is24' valign='top'>Aktion :</td><td class='is24'> "
                . $text . "</td></tr>";
           
             $mail_text
                .="<tr class='is24'><td class='is24' valign='top'>Bearbeiter :</td><td class='is24'>"
                . $kommentator . "<br>Telefon: ".$telefon."</td></tr>";
            $mail_text.="</tbody></table>";                 

	    $xsubject=$zeile_aufgabe['hau_titel'] ;
            if ( (strlen($zeile_aufgabe['hau_otrsnr']) > 1 ) && ( strpos($xsubject,$zeile_aufgabe['hau_otrsnr']) < 1 ) ) { $xsubject=$xsubject."[Ticket#".$zeile_aufgabe['hau_otrsnr']."]"; }
            $betreff= htmlspecialchars(substr($xsubject,0,100)).'-'.$mail_info .': >Ticket ID ' . $zeile_aufgabe['hau_id'] . '< ';
         
            $header  = "MIME-Version: 1.0\r\n";
            $header .= "Content-type: text/html; charset=utf-8\r\n";
            $header .= "Content-Transfer-Encoding: 8-bit\r\n";
            $header .= "Return-Path: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "Reply-To: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "From: Taskscout24 <taskscout24@immobilienscout24.de>\r\n"; 
            $header .= "Date: " . date('r')."\r\n";
            $temp_ary = explode(' ', (string) microtime());
            $header .= "Message-Id: <" . date('YmdHis') . "." . substr($temp_ary[0],2) . "@immobilienscout24.de>\r\n"; 
           
          #  $mail_text = rtrim(chunk_split(base64_encode($mail_text))); 
                
}
            
           
            #echo $Einzelmail['mail'].'<br>'.$betreff.'<br>'.$mail_text.'<br>'.$header; exit;
            mail($Einzelmail['mail'], $betreff, $mail_text, $header, '-ftaskscout24@immobilienscout24.de');
           
            } // ende if-Abfrage Mailadresse vorhanden
        }
}
#exit;
?>
