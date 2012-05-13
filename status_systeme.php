<?php
###### Editnotes ####
#$LastChangedDate: 2012-04-23 17:30:50 +0200 (Mo, 23 Apr 2012) $
#$Author: bpetersen $ 
#####################

############## INCLUDES ################

require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

############# Variablen ################

$xSystem_typ = array();
$xSystem_check = array();
$xFormulardaten=array();
$aenderung = array(0=>'unbearbeitet', 1=>'in Arbeit', 2=>'erledigt', 3=>'nicht benötigt');
$status_aenderung = array();   
$status_bemerkung = array();

############# Ermittle Plattform ###################

if(!isset($_REQUEST['xSystem'])) {$xSystem = 1;} else {$xSystem = $_REQUEST['xSystem'];}

############# Hole Inhalte ############

foreach ($_POST as $varname => $value)
    {
    $xFormulardaten[$varname]=$value;
    }
   
###############################################

######## Prüfe, ob Formular abgeschickt wurde ###############
######## Falls ja, werte Daten aus, aktualisiere DB und starte eMail-Versand #############

if (isset($xFormulardaten['speichern']))
    {

# Ermittle User
   
    $sql = 'SELECT hma_name FROM mitarbeiter WHERE hma_id = '.$xFormulardaten['hpl_user'];

    if (!($ergebnis = mysql_query($sql, $verbindung)))
    {
        fehler();
    }        
            
    while ($zeile=mysql_fetch_array($ergebnis))
    {
        $mitarbeiter = $zeile['hma_name'];
    }   
   
# Ermittle Plattform
   
    $sql = 'SELECT hpl_name FROM system_plattformen WHERE hpl_id = '.$xFormulardaten['xSystem'];

    if (!($ergebnis = mysql_query($sql, $verbindung)))
    {
        fehler();
    }        
            
    while ($zeile=mysql_fetch_array($ergebnis))
    {
        $server = $zeile['hpl_name'];
    }
   

   if($xFormulardaten['speichern']=='Status ändern')
       {
             
        if($xFormulardaten['hpl_status']==0) {$xStatus='DOWN';} else {$xStatus='UP';} 
  

   # Folgendes nur, wenn sich der Status geändert hat
   
   $sql = 'SELECT hpl_status, hpl_bemerkung FROM system_plattformen WHERE hpl_id = '.$xFormulardaten['xSystem']; 
   
           if (!($ergebnis = mysql_query($sql, $verbindung)))
            {
            fehler();
            }        
            
            while ($zeile=mysql_fetch_array($ergebnis))
            {
            $server_status_alt  = $zeile['hpl_status'];
            $server_bemerkung = $zeile['hpl_bemerkung']; 
            }
            
            if($xFormulardaten['hpl_status']!= $server_status_alt)
            {
   
                     # Activitylog IS24
  
  $sql='INSERT INTO log (' .
    'ulo_aufgabe, ' .
    'ulo_text, ' .
    'ulo_ma, ' .
    'ulo_extra, ' .
    'ulo_datum) ' .
    'VALUES ( ' .
    '"1", ' .
    '"' . mysql_real_escape_string('Status von Plattform '.$server.' wurde geändert: '.$xStatus).'", ' .
    '"' . $xFormulardaten['hpl_user'] . '", ' .
    '"1", ' .
    'NOW())';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }
      
        # Vermerke Statusänderung im LOG

        $sql_log='INSERT INTO eventlog (' .
            'hel_area, ' .
            'hel_type, ' .
            'hel_referer, ' .
            'hel_text) ' .
            'VALUES ( ' .
            '"Plattform", ' .
            '"Status", ' .
            '"' . $mitarbeiter . '" ,' .
            '"hat den Status von Plattform '.$server.' geändert: '.$xStatus.'")';

        if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
            {
            fehler();
            }

                
            # Setze Checkliste zurück
   
            # Leere beim DOWN die Checkliste
        
            if($xFormulardaten['hpl_status']==0)
            {
            $sql = 'UPDATE system_checkliste SET
            hsc_status = 0,
            hsc_bemerkung = "",
            hsc_hmaid = '.$xFormulardaten['hpl_user'] .'  
             WHERE hsc_hplid = '.$xFormulardaten['xSystem'];

            if (!($ergebnis = mysql_query($sql, $verbindung)))
            {
            fehler();
            }       
            } 
            
                # Ermittle, wer ein Update bestellt hat
    
    $abonnenten = array();
    
    $sql_mail = 'SELECT hsm_mail FROM system_mail';
    
    if (!($ergebnis_mail = mysql_query($sql_mail, $verbindung)))
    {
        fehler();
    }
    
    while ($zeile_mail=mysql_fetch_array($ergebnis_mail))
    {
         $abonnenten[] = $zeile_mail['hsm_mail'];
    }  
    
    $abonnenten[] = 'is24sd@immobilienscout24.de'; 
    
    # Schicke eine Update-Mail an alle Abonnenten
    
    foreach($abonnenten AS $mail_adresse)
    {
             
             # Sende Mail
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
       /*
            $mail_text.="<caption class='is24'>";
            $mail_text
                .="<img src='http://www.insolitus.de/img/tom_small.gif'></img>\n";
            $mail_text.="</caption>";
       */
            $mail_text.="<thead class='is24'>";   
            $mail_text.="<tr class='is24'><th class='is24'>News-Center</th><th class='is24'>" . date('H:i d.m.Y') . "</th></tr>";
            $mail_text.="</thead>";   
            $mail_text.="<tbody class='is24'>";   

                 if ($xFormulardaten['hpl_status'] == 1)
                {
                $mail_text.="<tr class='is24'><td class='is24' nowrap valign='top' colspan='2'><strong>Die Plattform " . $server
                    . " wird gestartet.</strong></td></tr>\n";
                $betreff=$server . " hat neuen Status: Server UP";
                }
                else
                {
                $mail_text.="<tr class='is24'><td class='is24' nowrap valign='top' colspan='2'><strong>Die Plattform " . $server
                    . " wird runtergefahren.</strong></td></tr>\n";
                $betreff=$server . " hat neuen Status: Plattform DOWN";
                }
 
            $mail_text.="<tr class='is24'><td  class='is24' nowrap valign='top'>Version :</td><td class='is24'>" . $xFormulardaten['hpl_version']
                . "</td></tr>\n";
                
            if($server_bemerkung!='')
            {
            $mail_text.= "<tr class='is24'><td class='is24' nowrap valign='top' colspan='2'>Bemerkung:<br><br>"; 
            $mail_text.= $xFormulardaten['hpl_bemerkung']."<br>";  
            $mail_text.= "</td></tr>\n";    
            }
             $mail_text
                .="<tr class='is24'><td class='is24' nowrap valign='top'>Link zu den Statusinfos :</td><td class='is24'><a href='http://taskscout24.prod/status_plattform.php?xSystem=".$xFormulardaten['xSystem']."'>Status</a></td></tr>\n";
            $mail_text
                .="<tr class='is24'><td class='is24' valign='top'>Bearbeiter :</td><td class='is24'>"
                . $mitarbeiter . "</td></tr>\n";
            $mail_text.="</tbody></table>";   

 
            $xtra="From: taskscout24@immobilienscout24.de (Task Organisation Management)\n";
            $xtra.="MIME-Version: 1.0\n";
            $xtra.="Content-type: text/html; charset=utf-8\n";
            $xtra.="Content-Transfer-Encoding: 8-bit\n";

            #echo $mail_text; exit;
            mail($mail_adresse, $betreff, $mail_text, $xtra);
                }       
              } // Ende IF Status geändert 
  
          # Speichere die Statusänderung

        $sql = 'UPDATE system_plattformen SET
                hpl_status = "'.$xFormulardaten['hpl_status'].'",  
                hpl_bemerkung = "'.$xFormulardaten['hpl_bemerkung'].'",  
                hpl_version = "'. $xFormulardaten['hpl_version'].'"
                WHERE hpl_id = '.$xFormulardaten['xSystem'];

        if (!($ergebnis = mysql_query($sql, $verbindung)))
            {
            fehler();
            }
  
           
       } else 
       
       {
   
       foreach($xFormulardaten['check_input_status'] AS $ID=>$inhalt)
       {

           # 1. Prüfe, ob es eine Statusänderung gab:
           
                # Lese dazu den alten Status aus:
                
                    $sql_alt = 'SELECT hsc_status, hss_name FROM system_checkliste 
                                LEFT JOIN system_schritte ON hss_id = hsc_hssid
                                WHERE hsc_hssid = '.$ID.' AND hsc_hplid = '.$xFormulardaten['xSystem'];  
                     
                    if (!($ergebnis_alt = mysql_query($sql_alt, $verbindung)))
                    {
                        fehler();
                    }
                    
                    while ($zeile_alt=mysql_fetch_array($ergebnis_alt))
                    {
                        $schritt_status_alt = $zeile_alt['hsc_status'];
                        $schritt_name = $zeile_alt['hss_name']; 
                    }  
                
                
                # Vergleiche alten und übergebenen Status
        
       
                    if($schritt_status_alt!=$inhalt)
                    {               
                    $status_aenderung[] = 'Der Status für ['.$schritt_name.'] wurde geändert auf '.$aenderung[$inhalt]; 
             
                # Die Status sind unterschiedlich, aktualisiere den Datensatz
                
                         $sql = 'UPDATE system_checkliste SET
                                 hsc_status = "'.$inhalt.'",
                                 hsc_hmaid = '.$xFormulardaten['hpl_user'] .'   
                                 WHERE hsc_hssid = '.$ID.' AND hsc_hplid = '.$xFormulardaten['xSystem'];

                        if (!($ergebnis = mysql_query($sql, $verbindung)))
                        {
                            fehler();
                        }
                        
                    
                    }
                        
           
           
           
           # 2. Prüfe, ob es eine neue Bemerkung gab:
           
                    # Lese dazu die alte Bemerkung aus:
                
                    $sql_alt = 'SELECT hsc_bemerkung FROM system_checkliste 
                                WHERE hsc_hssid = '.$ID.' AND hsc_hplid = '.$xFormulardaten['xSystem'];  
           
                    if (!($ergebnis_alt = mysql_query($sql_alt, $verbindung)))
                    {
                        fehler();
                    }
                    
                    while ($zeile_alt=mysql_fetch_array($ergebnis_alt))
                    {
                        $schritt_bemerkung_alt = $zeile_alt['hsc_bemerkung'];
                    }  
                
                
                # Vergleiche alten und übergebenen Status

                                                                                            
                    if($schritt_bemerkung_alt!=$xFormulardaten['check_input_bemerkung'][$ID])
                    {               
                     $status_bemerkung[] = $xFormulardaten['check_input_bemerkung'][$ID]; 
                # Sind die Status unterschiedlich, aktualisiere den Datensatz
                
                         $sql = 'UPDATE system_checkliste SET
                                 hsc_bemerkung = "'.mysql_real_escape_string($xFormulardaten['check_input_bemerkung'][$ID]).'",
                                 hsc_hmaid = '.$xFormulardaten['hpl_user'] .'   
                                 WHERE hsc_hssid = '.$ID.' AND hsc_hplid = '.$xFormulardaten['xSystem'];
   
                        if (!($ergebnis = mysql_query($sql, $verbindung)))
                        {
                            fehler();
                        }
                    }
           
                           
            }
        
     
 
    # Ermittle, wer ein Update bestellt hat
    
    $abonnenten = array();
    
    $sql_mail = 'SELECT hsm_mail FROM system_mail';
    
    if (!($ergebnis_mail = mysql_query($sql_mail, $verbindung)))
    {
        fehler();
    }
    
    while ($zeile_mail=mysql_fetch_array($ergebnis_mail))
    {
         $abonnenten[] = $zeile_mail['hsm_mail'];
    }  
    
    # Schicke eine Update-Mail an alle Abonnenten
    
    foreach($abonnenten AS $mail_adresse)
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
            $mail_text.="<tr class='is24'><th class='is24'>News-Center</th><th class='is24'>" . date('H:i d.m.Y') . "</th></tr>";
            $mail_text.="</thead>";   
            $mail_text.="<tbody class='is24'>";   
            $mail_text
                .="<tr class='is24'><td class='is24' nowrap valign='top'>Es gibt neue Informationen für die Plattform :</td><td class='is24'> "
                . $server . "</td></tr>\n";
            if(COUNT($status_aenderung)>0)
            {
            $mail_text.= "<tr class='is24'><td class='is24' nowrap valign='top' colspan='2'>";
                foreach($status_aenderung AS $text)
                {
                $mail_text.="".$text."<br>";
                }
            $mail_text.= "</td></tr>\n";
            }
            if(COUNT($status_bemerkung) >0)
            {
            $mail_text.= "<tr class='is24'><td class='is24' nowrap valign='top' colspan='2'>"; 
                foreach($status_bemerkung AS $text)
                {
                  $mail_text.="".$text."<br>";  
                }
            $mail_text.= "</td></tr>\n";    
            }
            $mail_text
                .="<tr class='is24'><td class='is24' nowrap valign='top'>Link zu den Statusinfos :</td><td class='is24'><a href='http://taskscout24.prod/status_plattform.php?xSystem=".$xFormulardaten['xSystem']."'>Status</a></td></tr>\n";
            $mail_text
                .="<tr class='is24'><td class='is24' valign='top'>Bearbeiter :</td><td class='is24'>"
                . $mitarbeiter . "</td></tr>\n";
            $mail_text.="</tbody></table>";   
        
        


            $betreff= 'Für '.$server . " wurden neue Informationen hinterlegt."; 

            
            $eMail='is24sd@immobilienscout24.de';
            $xtra="From: taskscout24@immobilienscout24.de (Task Organisation Management)\n";
            $xtra.="MIME-Version: 1.0\n";
            $xtra.="Content-type: text/html; charset=utf-8\n";
            $xtra.="Content-Transfer-Encoding: 8-bit\n";

            #echo $mail_text; exit;   
            mail($mail_adresse, $betreff, $mail_text, $xtra);
    }
    
      } 
    
    # Benutzer informieren
    
    require_once('segment_kopf.php');

    echo '<br>';

    echo '<img src="bilder/block.gif">&nbsp;Statusänderung wurde gespeichert.</td>';

    echo '<meta http-equiv="refresh" content="1;url=status_systeme.php?xSystem=' . $xFormulardaten['xSystem']. '">';
    exit;
    break;
    
    
    }

echo '<form action="status_systeme.php" method="post">';
    
echo '<table>';

echo '<tr><td>';

echo '<select size="1" name="xSystem">';

$sql_filter='SELECT * FROM system_plattformen ORDER BY hpl_name';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_filter=mysql_query($sql_filter, $verbindung))
    {
    fehler();
    }

while ($zeile_filter=mysql_fetch_array($ergebnis_filter))
    {
    if ($xSystem == $zeile_filter['hpl_id'])
        {
        echo '<option value="' . $zeile_filter['hpl_id']
            . '" selected style="background-color:#E28B78;"><span class="text">' . $zeile_filter['hpl_name']
            . '</span></option>';
        }
    else
        {
        echo '<option value="' . $zeile_filter['hpl_id'] . '"><span class="text">' . $zeile_filter['hpl_name']
            . '</span></option>';
        }
    }

echo '</select>';

echo '<span style="vertical-align:top;">&nbsp;&nbsp;<input type="submit" name="filter" value="Plattform wählen" class="formularbutton" />';

echo '</td></tr>';

echo '</table>';

echo '</form>';

echo '<br>';
    
############## Zeige die aktuellen Statusdaten an #########################
 
$sql = 'SELECT hpl_name, hpl_status, hpl_bemerkung, hpl_version FROM system_plattformen WHERE hpl_id = '.$xSystem;

if (!$ergebnis=mysql_query($sql, $verbindung))
   {
   fehler();
   }

while ($zeile=mysql_fetch_array($ergebnis))
   {
   $xSystem_typ[$xSystem]['hpl_name']=$zeile['hpl_name'];
   $xSystem_typ[$xSystem]['hpl_status']=$zeile['hpl_status'];
   $xSystem_typ[$xSystem]['hpl_version']=$zeile['hpl_version'];
   $xSystem_typ[$xSystem]['hpl_bemerkung']=$zeile['hpl_bemerkung'];
   }
    
echo '<form action="status_systeme.php?xSystem='.$xSystem.'" method="post">';
    
echo '<input type="hidden" name="xSystem" value="'.$xSystem.'">';

echo '<input type="hidden" name="hpl_user" value="'.$_SESSION['hma_id'].'">'; 
    
echo '<table class="is24" cellpadding="5">';

echo '<caption class="is24">';

echo $xSystem_typ[$xSystem]['hpl_name'];

echo '</caption>';

echo '<tr><td>Version</td><td><input type="text" name="hpl_version" value="'.$xSystem_typ[$xSystem]['hpl_version'].'"></td><td rowspan="3" align="right"><input type="submit" name="speichern" value="Status ändern" class="formularbutton" /></td></tr>';

echo '<tr><td>Status</td><td>';

switch ($xSystem_typ[$xSystem]['hpl_status'])
{
    case 0:
        echo 'UP <input type="radio" name="hpl_status" value="1"> DOWN <input type="radio" name="hpl_status" value="0" checked>';
        break;

    case 1:
        echo 'UP <input type="radio" name="hpl_status" value="1" checked> DOWN <input type="radio" name="hpl_status" value="0">';
        break;
}

echo '</td></tr>';

echo '<tr><td>Bemerkung</td><td valign="top" width="500"><input type="text" name="hpl_bemerkung" style="width:500px;" value="'.$xSystem_typ[$xSystem]['hpl_bemerkung'].'"></td></tr>';

echo '</table>';

echo '</form>';

echo '<br>';

echo '<form action="status_systeme.php?xSystem='.$xSystem.'" method="post">';
    
echo '<input type="hidden" name="xSystem" value="'.$xSystem.'">';

echo '<input type="hidden" name="hpl_user" value="'.$_SESSION['hma_id'].'">';    

echo '<table class="matrix" >';

echo '<thead class="is24">';

echo '<tr class="is24">';   

echo '<th class="is24">Arbeitsschritt</th>';

echo '<th class="is24">unbearbeitet</th>';

echo '<th class="is24">in Arbeit</th>';

echo '<th class="is24">erledigt</th>';

echo '<th class="is24">nicht benötigt</th>';

echo '<th class="is24">Bemerkung</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody class="is24">'; 

$sql = 'SELECT * FROM system_schritte 
        LEFT JOIN system_checkliste ON hsc_hssid = hss_id
        WHERE hsc_hplid = '.$xSystem.' ORDER BY hss_sort';

if (!$ergebnis=mysql_query($sql, $verbindung))
   {
   fehler();
   }

while ($zeile=mysql_fetch_array($ergebnis))
   {
   $xSystem_check[$zeile['hss_id']]['hsc_status']=$zeile['hsc_status'];
   $xSystem_check[$zeile['hss_id']]['hsc_bemerkung']=$zeile['hsc_bemerkung'];
   $xSystem_check[$zeile['hss_id']]['hss_name']=$zeile['hss_name'];
   $xSystem_check[$zeile['hss_id']]['hss_id']=$zeile['hss_id'];
   }
   
foreach($xSystem_check AS $schritte)
{
  
    echo '<tr><td>'.$schritte['hss_name'].'</td>';

        switch ($schritte['hsc_status'])
            {
            case 0:

                echo '<td bgcolor="#EE775F" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="0" checked="checked"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#FFF8B3" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="1"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#C1E2A5" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="2"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
 
                 echo '<td bgcolor="#c2c2c2" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="3"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
                    
                echo '<td align="center"><input type="text" size="42" name="check_input_bemerkung[' . $schritte['hss_id']
                    . ']" value="' . $schritte['hsc_bemerkung'] . '" /></td>';
                break;

            case 1:

                echo '<td bgcolor="#EE775F" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="0"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#FFF8B3" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="1" checked="checked"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#C1E2A5" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="2"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
 
                 echo '<td bgcolor="#c2c2c2" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="3"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
                    
                echo '<td align="center"><input type="text" size="42" name="check_input_bemerkung[' . $schritte['hss_id']
                    . ']" value="' . $schritte['hsc_bemerkung'] . '" /></td>';
                break;

             case 2:

                echo '<td bgcolor="#EE775F" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="0"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#FFF8B3" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="1"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#C1E2A5" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="2" checked="checked"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
 
                 echo '<td bgcolor="#c2c2c2" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="3"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
                    
                echo '<td align="center"><input type="text" size="42" name="check_input_bemerkung[' . $schritte['hss_id']
                    . ']" value="' . $schritte['hsc_bemerkung'] . '" /></td>';
                break;
                
              case 3:

                echo '<td bgcolor="#EE775F" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="0"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#FFF8B3" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="1"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#C1E2A5" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="2"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
 
                 echo '<td bgcolor="#c2c2c2" align="center"><input type="radio" name="check_input_status[' . $schritte['hss_id']
                    . ']" value="3" checked="checked"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
                    
                echo '<td align="center"><input type="text" size="42" name="check_input_bemerkung[' . $schritte['hss_id']
                    . ']" value="' . $schritte['hsc_bemerkung'] . '" /></td>';
                break;
            }
    
}
echo '</tr>';

echo '<tr><td colspan="6" align="right"><input type="submit" name="speichern" value="Status Arbeitsschritte ändern" class="formularbutton" /></td></tr>';

echo '</tbody>';

echo '</table>';

echo '</form>';


include('segment_fuss.php');
?>
