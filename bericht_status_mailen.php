<?php
###### Editnotes ######
#$LastChangedDate: 2012-04-23 17:30:50 +0200 (Mo, 23 Apr 2012) $
#$Author: bpetersen $ 
#####################

### Lese die Konfiguration ein ###

require_once('konfiguration.php');

### Initialisiere Variablen ###

$defcon_feld = array();
$ir_feld = array();
$deployment_feld = array();
$ticket_feld = array();
$feld_index = 0;
$ticket_fertig_gesamt = 0;
$ticket_neu_gesamt = 0;
$ticket_offen_gesamt = 0;
$ticket_changes_gesamt = 0; 

### Lege Zeitraum des Berichts fest 

$xKw = $_REQUEST['xKw'];
$xJahr = $_REQUEST['xJahr'];  
$xMontag=date("Y-m-d 00:00:00", mondaykw($xKw, $xJahr));
$xSonntag = date ('Y-m-d 23:59:59' , strtotime("$xMontag +6 days"));

#$xMontag = '2011-08-22 00:00:00';
#$xSonntag = '2011-08-28 00:00:00';

###  Lese die DEFCON-Status des Zeitraumes aus

$sql = 'SELECT * FROM defcon
        LEFT JOIN mitarbeiter ON hma_id = ude_hmaid 
        WHERE (ude_zeitstempel BETWEEN "'.$xMontag.'" 
        AND "'.$xSonntag.'") 
        ORDER BY ude_zeitstempel DESC';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
      $defcon_feld[$feld_index]['zeit'] = $zeile['ude_zeitstempel'];
      $defcon_feld[$feld_index]['status'] = $zeile['ude_status'];
      $defcon_feld[$feld_index]['ir'] = $zeile['ude_irid'];  
      $feld_index++;     
    }
    
### Lese die Incident-Reports ein

$sql=  'SELECT * FROM ir_stammdaten 
        LEFT JOIN impact ON uia_id = hir_auswirkung 
        WHERE (hir_datum BETWEEN "'.$xMontag.'" 
        AND "'.$xSonntag.'") ORDER BY hir_auswirkung'; 

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
      $ir_feld[$feld_index]['datum'] = $zeile['hir_datum'];
      $ir_feld[$feld_index]['nummer'] = $zeile['hir_id'];
      $ir_feld[$feld_index]['incident'] = $zeile['hir_problem']; 

      switch($zeile['hir_auswirkung'])
      {
          case 1:
          $ir_feld[$feld_index]['impact'] = 'Massive Impact';
          break;           

          case 2:
          $ir_feld[$feld_index]['impact'] = 'Significant Impact';
          break;  
          
          case 3:
          $ir_feld[$feld_index]['impact'] = 'Low Impact';
          break;  
          
          case 4:
          $ir_feld[$feld_index]['impact'] = 'No Impact';
          break; 
          
          default:
          $ir_feld[$feld_index]['impact'] = 'Not defined';
          break;            
                    
      } 
       
      $ir_feld[$feld_index]['release'] = $zeile['hir_release'];  
      $feld_index++;     
    }

### Lese die Deployments aus (Typ = 11)

$sql=  'SELECT * FROM aufgaben 
        WHERE (hau_abschlussdatum BETWEEN "'.$xMontag.'" 
        AND "'.$xSonntag.'") AND hau_aktiv =1 AND hau_abschluss = 1 AND hau_typ = 11 ORDER BY hau_abschlussdatum DESC'; 

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
      $deployment_feld[$feld_index]['datum'] = $zeile['hau_abschlussdatum'];
      $deployment_feld[$feld_index]['inhalt'] = $zeile['hau_titel'];
      $deployment_feld[$feld_index]['ticket'] = $zeile['hau_id'];
      $feld_index++;     
    } 
  
### Lese die Tickets aus
## Lese dazu zunächst die Gruppen ein

$sql_gruppe = 'SELECT ule_id, ule_kurz FROM level WHERE ule_aktiv =1 AND ule_id < 10 ORDER BY ule_kurz';

if (!($ergebnis_gruppe=mysql_query($sql_gruppe, $verbindung)))
    {
    fehler();
    }

while ($zeile_gruppe=mysql_fetch_array($ergebnis_gruppe))
    {
     
$neue_tickets = 0;        
$fertige_tickets = 0;
$offene_tickets = 0;
$changes_ticket = 0;  
$offene_tickets_ohne_ma = 0;
$offene_tickets_mit_ma = 0;  

# Lese zunächst die neu hereingekommen Tickets

$sql=  'SELECT COUNT(DISTINCT hau_id) AS anzahl FROM aufgaben 
        LEFT JOIN aufgaben_zuordnung ON hau_id = uaz_hauid 
        LEFT JOIN level ON uaz_pg = ule_id 
        WHERE (hau_anlage BETWEEN "'.$xMontag.'" AND "'.$xSonntag.'")  
        AND hau_aktiv =1 
        AND uaz_pg = '.$zeile_gruppe['ule_id'].'
        GROUP BY ule_kurz'; 
        
if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
       $neue_tickets = $zeile['anzahl'];
    }       
    
# Lese nun die abgeschlossenen Tickets

$sql=  'SELECT COUNT(DISTINCT hau_id) AS anzahl FROM aufgaben 
        LEFT JOIN aufgaben_zuordnung ON hau_id = uaz_hauid 
        LEFT JOIN level ON uaz_pg = ule_id 
        WHERE (hau_abschlussdatum BETWEEN "'.$xMontag.'" AND "'.$xSonntag.'")  
        AND hau_aktiv =1 
        AND hau_abschluss = 1
        AND uaz_pg = '.$zeile_gruppe['ule_id'].'
        GROUP BY ule_kurz'; 
        
if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
       $fertige_tickets = $zeile['anzahl'];
    }       
    
# Lese nun die noch offenen Tickets
# Zunächst die ohne Bearbeiter

$sql='SELECT COUNT(DISTINCT hau_id) AS anzahl FROM aufgaben 
    LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
    LEFT JOIN level ON uaz_pg = ule_id 
    WHERE uaz_pba = 0 AND hau_aktiv = 1 AND hau_abschluss = 0 
    AND uaz_pg = '.$zeile_gruppe['ule_id'].'
    AND hau_anlage < "'.$xSonntag.'" 
    GROUP BY ule_kurz'; 
                
if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
       $offene_tickets_ohne_ma = $zeile['anzahl'];
    }   
 
# Jetzt die mit Bearbeiter 
 
$sql='SELECT COUNT(DISTINCT hau_id) AS anzahl FROM aufgaben 
    LEFT JOIN aufgaben_mitarbeiter ON uau_hauid = hau_id    
    LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
    LEFT JOIN level ON uaz_pg = ule_id      
    WHERE uau_status = 0 AND hau_aktiv = 1 AND hau_abschluss = 0 
    AND uaz_pg = '.$zeile_gruppe['ule_id'].'
    AND hau_anlage < "'.$xSonntag.'"  
    GROUP BY ule_kurz'; 
                         
if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
       $offene_tickets_mit_ma = $zeile['anzahl'];
    }   
    
$offene_tickets = $offene_tickets_mit_ma + $offene_tickets_ohne_ma;
      
    
# Lese nun die Changes ein

$sql=  'SELECT COUNT(DISTINCT hau_id) AS anzahl FROM aufgaben 
        LEFT JOIN mitarbeiter ON hma_id = hau_inhaber 
        LEFT JOIN level ON hma_level = ule_id 
        WHERE (hau_abschlussdatum BETWEEN "'.$xMontag.'" AND "'.$xSonntag.'")  
        AND hau_aktiv =1 
        AND ule_id = '.$zeile_gruppe['ule_id'].'
        AND hau_hprid = 6  
        GROUP BY ule_kurz'; 
        
          
if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
       $changes_ticket = $zeile['anzahl'];
    }  
    
# Jetzt speichere die Werte für die Gruppe

if($offene_tickets > 0)
{
      $ticket_feld[$feld_index]['gruppe'] = $zeile_gruppe['ule_kurz'];
      $ticket_feld[$feld_index]['neu'] = $neue_tickets;
      $ticket_feld[$feld_index]['fertig'] = $fertige_tickets;
      $ticket_feld[$feld_index]['offen'] = $offene_tickets;
      $ticket_feld[$feld_index]['changes'] = $changes_ticket; 
      $feld_index++;   
}
} 
                                                        
### Definiere die Mailrelevanten Strings

## Headerfile

    $mail_header  = "MIME-Version: 1.0\r\n";
    $mail_header .= "Content-type: text/html; charset=UTF-8\r\n";
    $mail_header .= "Content-Transfer-Encoding: base64\r\n";
    $mail_header .= "Return-Path: taskscout24@immobilienscout24.de\r\n"; 
    $mail_header .= "Reply-To: taskscout24@immobilienscout24.de\r\n"; 
    $mail_header .= "From: Taskscout24 <taskscout24@immobilienscout24.de>\r\n"; 
    $mail_header .= "Date: " . date('r')."\r\n";
    $temp_ary = explode(' ', (string) microtime());
    $mail_header .= "Message-Id: <" . date('YmdHis') . "." . substr($temp_ary[0],2) . "@immobilienscout24.de>\r\n"; 

## Betreff

    $mail_betreff= 'Statusreport IT PRO for CW '.$xKw.' '.$xJahr;   
   
## Mailbody 

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
            
            $mail_text.= "<body><table class='is24_mail' width='600'>";
            $mail_text.= "<thead class='is24'>"; 
            $mail_text.= "<tr class='is24'><th class='is24'><img src='http://www.insolitus.de/img/tom_small.gif'></img></th>";
  
            $mail_text.= "<th class='is24'>Statusreport for CW " . $xKw . " (".datum_anzeigen($xMontag)." - ".datum_anzeigen($xSonntag).")</th></tr>";
            $mail_text.= "</thead></table>";   
            
            $mail_text.= "<br>";
            
            ### Gebe DEFCON-Status aus

            $mail_text.= "<table class='is24_mail' width='600'>";
            $mail_text.= "<thead class='is24'>"; 
            $mail_text.= "<tr class='is24'><th class='is24' colspan='3'>DEFCON Changes</th></tr>";
            $mail_text.= "</thead>";
            $mail_text.= "<tbody class='is24'>";
            $mail_text.= "<tr class='is24'>";
            $mail_text.= "<td class='is24'>Incidentreport</td>";
            $mail_text.= "<td class='is24'>Date of Incident</td>";
            $mail_text.= "<td class='is24'>Status</td>";
            $mail_text.= "</tr>";


            foreach($defcon_feld AS $defcon_detail)
            {
                $mail_text.= "<tr>";
                $mail_text.= "<td class='is24'><a href='http://taskscout24.prod/ir_ansicht.php?hir_id=".$defcon_detail['ir']."'>" . $defcon_detail['ir'] . "</a></td>";
                $mail_text.= "<td class='is24'>" . zeitstempel_anzeigen($defcon_detail['zeit']) . "</td>";
                $mail_text.= "<td class='is24'>" . $defcon_detail['status'] . "</td>";
                $mail_text.= "</tr>";
            }
            
            $mail_text.= "</tbody>"; 
            $mail_text.= "</table>";
  
            $mail_text.= "<br>";
            
            ### Gebe IR-Liste aus 

            $mail_text.= "<table class='is24_mail' width='600'>";
            $mail_text.= "<thead class='is24'>"; 
            $mail_text.= "<tr class='is24'><th class='is24' colspan='5'>Incidents this CW</th></tr>";
            $mail_text.= "</thead>";
            $mail_text.= "<tbody  class='is24'>";
            $mail_text.= "<tr class='is24'>";
            $mail_text.= "<td class='is24'>Incidentreport</td>";
            $mail_text.= "<td class='is24'>Date of Incident</td>";
            $mail_text.= "<td class='is24'>Problem</td>";
            $mail_text.= "<td class='is24'>Impact</td>"; 
            $mail_text.= "<td class='is24'>Release based</td>"; 
            $mail_text.= "</tr>";
            foreach($ir_feld AS $ir_detail)
            {
                $mail_text.= "<tr>";
                $mail_text.= "<td class='is24'><a href='http://taskscout24.prod/ir_ansicht.php?hir_id=".$ir_detail['nummer']."'>" . $ir_detail['nummer'] . "</a></td>";
                $mail_text.= "<td class='is24'>" . datum_anzeigen($ir_detail['datum']) . "</td>";
                $mail_text.= "<td class='is24'>" . $ir_detail['incident'] . "</td>";
                $mail_text.= "<td class='is24'>" . $ir_detail['impact'] . "</td>";    
                $mail_text.= "<td class='is24'>" . $ir_detail['release'] . "</td>"; 
                $mail_text.= "</tr>";
            }
            $mail_text.= "</tbody>"; 
            $mail_text.= "</table>";

            $mail_text.= "<br>";    
            
            ### Gebe Deployments aus   

            $mail_text.= "<table class='is24_mail' width='600'>";
            $mail_text.= "<thead class='is24'>"; 
            $mail_text.= "<tr class='is24'><th class='is24' colspan='5'>Deployments this CW</th></tr>";
            $mail_text.= "</thead>";
            $mail_text.= "<tbody  class='is24'>";
            $mail_text.= "<tr class='is24'>";
            $mail_text.= "<td class='is24'>Ticket</td>";
            $mail_text.= "<td class='is24'>deployed on</td>";
            $mail_text.= "<td class='is24'>Unit</td>";
            $mail_text.= "</tr>";
            
            foreach($deployment_feld AS $deploy_detail) 
            {
                $mail_text.= "<tr>";
                $mail_text.= "<td class='is24'><a href='http://taskscout24.prod/aufgabe_ansehen.php?hau_id=".$deploy_detail['ticket']."'>" . $deploy_detail['ticket'] . "</a></td>";
                $mail_text.= "<td class='is24'>" . datum_anzeigen($deploy_detail['datum']) . "</td>";
                $mail_text.= "<td class='is24'>" . $deploy_detail['inhalt'] . "</td>";
                $mail_text.= "</tr>";
            }
            $mail_text.= "</tbody>"; 
            $mail_text.= "</table>";
            
            $mail_text.= "<br>";                
            
            ### Gebe Tickets aus   

            $mail_text.= "<table class='is24_mail' width='600'>";
            $mail_text.= "<thead class='is24'>"; 
            $mail_text.= "<tr class='is24'><th class='is24' colspan='5'>Ticketbalance this CW</th></tr>";
            $mail_text.= "</thead>";
            $mail_text.= "<tbody class='is24'>";
            $mail_text.= "<tr class='is24'>";
            $mail_text.= "<td class='is24'>Team</td>";
            $mail_text.= "<td class='is24'>Open Tickets*</td>";
            $mail_text.= "<td class='is24'>New Tickets</td>";
            $mail_text.= "<td class='is24'>Closed Tickets</td>";
            $mail_text.= "<td class='is24'>Closed Changes</td>";   
            $mail_text.= "</tr>";
            foreach($ticket_feld AS $ticket_detail)
            {
                $mail_text.= "<tr>";

                $mail_text.= "<td class='is24'>" . $ticket_detail['gruppe'] . "</td>";
                $mail_text.= "<td class='is24'>" . $ticket_detail['offen'] . "</td>";
                $mail_text.= "<td class='is24'>" . $ticket_detail['neu'] . "</td>";
                $mail_text.= "<td class='is24'>" . $ticket_detail['fertig'] . "</td>";
                $mail_text.= "<td class='is24'>" . $ticket_detail['changes'] . "</td>";   
                $ticket_offen_gesamt = $ticket_offen_gesamt + $ticket_detail['offen'];     
                $ticket_neu_gesamt = $ticket_neu_gesamt + $ticket_detail['neu'];   
                $ticket_fertig_gesamt = $ticket_fertig_gesamt + $ticket_detail['fertig']; 
                $ticket_changes_gesamt = $ticket_changes_gesamt + $ticket_detail['changes'];  
                $mail_text.= "</tr>";
            }

            $mail_text.= "<tr class='is24'>";
            $mail_text.= "<td class='is24'>Summe</td>";
            $mail_text.= "<td class='is24'>" . $ticket_offen_gesamt . "</td>";
            $mail_text.= "<td class='is24'>" . $ticket_neu_gesamt . "</td>";
            $mail_text.= "<td class='is24'>" . $ticket_fertig_gesamt . "</td>";
            $mail_text.= "<td class='is24'>" . $ticket_changes_gesamt . "</td>";   
            $mail_text.= "</tr>";

            $mail_text.= "</thead>";
            $mail_text.= "</tbody>";
            $mail_text.= "</table>";

            $mail_text.= "* Stand ".datum_anzeigen($xSonntag);      

            $mail_text.= "<br><br>";
            
            $mail_text.= "<strong>Relevant Infos:</strong><br><br>";

            $mail_text.= "The Operations Management Team is accessible by dialling -1515 or sending a ticket to produktion@immobilienscout24.de.<br><br>";
            
            $mail_text.= "<u>Standby team of IT-Production</u><br>";
            $mail_text.= "- The standby team of the production system is accessible by dialling +491622497090.<br>";
            $mail_text.= "- The actual standby team plan is accessible at: http://taskscout24.prod/verwaltung_urlaub_gesamt.php (OnCall = Rufbereitschaft).<br><br>";
            
            $mail_text.= "<u>Current Status of our Test-Systems:</u><br>";
            $mail_text.= "Here you can find the latest status about the release of test equipment systems: http://taskscout24.prod/index.php.<br><br>";
            
            $mail_text.= "<u>Incident Reports</u><br>";
            $mail_text.= "Information regarding current Incident Reports can be seen here: http://taskscout24.prod/ir_liste.php.<br><br>";
            
            $mail_text.= "<u>Performance Reports:</u><br>";
            $mail_text.= "If you want to have a look at our monthly performance report please use this link: https://intranet.immobilienscout24.de/SiteDirectory/itb/Performancereports1/Forms/AllItems.aspx.<br><br>";
            
            $mail_text.= "<u>Relevant Schedules of IT PRO</u><br>";
            $mail_text.= "Information regarding important schedules can be found in the team schedule: http://taskscout24.prod/<br><br>";  
 
            $mail_text.= "<u>Absentees</u><br>";
            $mail_text.= "To manage our absentees, please have a look here: http://taskscout24.prod/verwaltung_urlaub_gesamt.php<br>";
            $mail_text.= "Please have a look at this site for an actual overview.";
                       
            $mail_text = rtrim(chunk_split(base64_encode($mail_text)));
                       
            #echo 'Los Gehts: <br>'.$mail_betreff.'<br>'.$mail_text.'<br>'.$mail_header.'<hr>'; 
            #mail($mailempfaenger['mail'], $mail_betreff, $mail_text_detail, $mail_header, '-ftaskscout24@immobilienscout24.de');
            mail('andreas.hankel@immobilienscout24.de', $mail_betreff, $mail_text, $mail_header, '-ftaskscout24@immobilienscout24.de');
            
// Zurueck zur Liste

header('Location: bericht_status_it_pro.php?xKw='.$xKw.'&xJahr='.$xJahr);
exit;

?>

