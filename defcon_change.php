<?php
###### Editnotes ####
#$LastChangedDate: 2012-04-23 17:30:50 +0200 (Mo, 23 Apr 2012) $
#$Author: bpetersen $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

# Definiere Variablen 
$mailtext = '';
$mails = '';
$neuer_status=$_REQUEST['status'];
$anzahl_mailadressen = 0;
$mail_zaehler = 0;
$mail_to= '';         
$mails_cc= '';

# Ermittle vorherigen DEFCON-Status

$sql='SELECT ude_status FROM defcon ORDER BY ude_id DESC LIMIT 1';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    $alter_status=$zeile['ude_status'];
    }

if (!isset($_POST['speichern']))
    {
    require_once('segment_kopf.php');
    
    # Lege einen IR nur dann an, wenn wir aus dem DEFCON-Status 4 gehen, ansonsten sollte bereits ein IR da sein
    # Prüfe also den alten Status
    
    $sql = 'SELECT ude_status FROM defcon ORDER BY ude_zeitstempel DESC LIMIT 1';
    $erg = mysql_query($sql);
    $zeile = mysql_fetch_assoc($erg); 
    
    if($zeile['ude_status']==4) // Wir gehen aus dem Friedensstatus raus, IR anlegen
    {
        
    # IR anlegen 
            
        $sql = 'INSERT INTO ir_stammdaten (hir_status, hir_agent, hir_datum) 
        VALUES ( 
        "1", "' . $_SESSION['hma_id'] . '", NOW())';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    # Aufgabe für OM erzeugen (IR verfolgen)
    
    $hir_id=mysql_insert_id();
    $enddatum=date("Y-m-d", (time() + 604800)); // 604800 = 7 Tage in Sekunden

    $sql='INSERT INTO aufgaben (' .
        'hau_titel, ' .
        'hau_beschreibung, ' .
        'hau_anlage, ' .
        'hau_inhaber, ' .
        'hau_prio, ' .
        'hau_pende, ' .
        'hau_kalender, ' .
        'hau_nonofficetime, ' .
        'hau_zeitstempel, ' .
        'hau_aktiv, ' .
        'hau_terminaendern, ' .
        'hau_teamleiter, ' .
        'hau_datumstyp, ' .
        'hau_hprid, ' .
        'hau_typ, ' .
        'hau_tl_status, ' .
        'hau_dauer, ' .
        'hau_links, ' .
        'hau_ticketnr) ' .
        'VALUES ( ' .
        '"IR Nummer ' . $hir_id . ' bearbeiten & abschliessen", ' .
        '"Der IR mit der Nummer ' . $hir_id
        . ' wurde angelegt und muss bearbeitet werden, damit er abgeschlossen werden kann.", ' . 'NOW(), ' .
        '"' . $_SESSION['hma_id'] . '", ' .
        '"2", ' .
        '"' . $enddatum . '", ' .
        '"0", ' .
        '"0", ' .
        'NOW(), ' .
        '"1", ' .
        '"0", ' .
        '"0", ' .
        '"2", ' .
        '"2", ' .
        '"4", ' .
        '"0", ' .
        '"1", ' .
        '"", ' .
        '"IR ' . $hir_id . '")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $hau_id=mysql_insert_id();

    $sql='INSERT INTO aufgaben_zuordnung
                (uaz_hauid, uaz_pg) ' .
        'VALUES ("' . $hau_id . '", "4")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    } // Ende IR erzeugen 
    else // es muss schon einen IR geben - suche IR Nummer
    {
      $sql_status='SELECT ude_irid FROM defcon ORDER BY ude_zeitstempel DESC LIMIT 1';

    if (!($ergebnis_status=mysql_query($sql_status, $verbindung)))
        {
        fehler();
        }
        while ($zeile_status=mysql_fetch_array($ergebnis_status))
        {    
           $hir_id=$zeile_status['ude_irid'];
        }
    }
    
        # Schreibe LOGFILE

    $sql_log='INSERT INTO eventlog (' .
        'hel_area, ' .
        'hel_type, ' .
        'hel_referer, ' .
        'hel_text) ' .
        'VALUES ( ' .
        '"DEFCON", ' .
        '"Edit", ' .
        '"' . $_SESSION['hma_login'] . '" ,' .
        '"hat DEFCON auf : ' . $neuer_status . ' geändert.")';

    if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
        {
        fehler();
        }


    # Ändere den Status in der DB

    $sql='INSERT INTO defcon (ude_hmaid, ude_status, ude_irid)
        VALUES ("' . $_SESSION['hma_id'] . '", "' . $neuer_status . '", "'.$hir_id.'")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    
    # Generiere Mail

    $betreff='DEFCON Status wurde geändert: ' . $alter_status . ' ==> ' . $neuer_status;

    # Lese den passenden Textbaustein aus:

    $sql_text='SELECT udm_text, udm_mail, udm_mails_cc FROM defcon_texte WHERE udm_bezug = "' . $alter_status . $neuer_status . '"';

    if (!($ergebnis_text=mysql_query($sql_text, $verbindung)))
        {
        fehler();
        }

    while ($zeile_text=mysql_fetch_array($ergebnis_text))
        {
        $mailtext = $zeile_text['udm_text'];
        $mail_to=$zeile_text['udm_mail'];         
        $mails_cc=$zeile_text['udm_mails_cc'];
        }
        
    echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;DEFCON Mail erzeugen<br><br>';

    echo '<table border="0" cellspacing="5" cellpadding="0">';    
    
    echo '<form action="defcon_change.php" method="post">';
   
    echo '<input type="hidden" name="hir_id" value="' . $hir_id . '">';

    echo '<input type="hidden" name="status" value="' . $neuer_status . '">';

    echo '<tr>';

    echo '<td class="text_klein">Betreff: </td><td><input type="text" name="def_betreff" value="' . $betreff
        . '" style="width:400px;"></td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein" valign="top">Text:&nbsp;&nbsp;</td><td><textarea cols="80" rows="15" name="def_text">'
        . $mailtext . '</textarea></td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein">OOD_Nummer: </td><td>';

    echo '<input type="text" name="def_ood" style="width:400px;" value="1515"></td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein">Incident-Report: </td><td>';

    echo
        '<input type="text" name="def_ir" style="width:400px;" value="http://taskscout24.prod/ir_ansicht.php?hir_id='
        . $hir_id . '"></td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein" valign="top">Versand an:&nbsp;&nbsp;</td><td><textarea cols="80" rows="10" name="def_mail_to">'
        . $mail_to . '</textarea></td>';

    echo '</tr>';
    
    echo '<tr>';

    echo
        '<td class="text_klein" valign="top">Versand cc:&nbsp;&nbsp;</td><td><textarea cols="80" rows="10" name="def_mails_cc">'
        . $mails_cc . '</textarea></td>';

    echo '</tr>';

    echo
        '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Schicke Mail" class="formularbutton" /></td></tr>';

    echo '</table>';

    echo '</form>';
    }
    else // Es wurde eine Mail abgeschickt
    {
    foreach ($_POST as $varname => $value)
        {
        $Daten[$varname]=$value;
        }
        
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


            $mail_text.="<body><table class='is24_mail' width='600'>\n";
            
            $mail_text.="<caption class='is24'>";
            $mail_text
                .="<img src='http://www.insolitus.de/img/tom_small.gif'></img>\n";
            $mail_text.="</caption>";
            
            $mail_text.="<thead class='is24'>";   
            $mail_text.="<tr class='is24'><th class='is24'>DEFCON Meldung</th><th class='is24'>" . date('H:i d.m.Y') . "</th></tr>";
            $mail_text.="</thead>";   
            $mail_text.="<tbody class='is24'>";   
            $mail_text
                .="<tr class='is24'><td class='is24' nowrap valign='top'>DEFCON Statusänderung :</td><td class='is24'> "
                . $Daten['def_betreff'] . "</td></tr>\n";
            $mail_text
                .="<tr class='is24'><td class='is24' nowrap valign='top'>OOD :</td><td class='is24'>" . $Daten['def_ood']
        . "</td></tr>\n";
            $mail_text
                .="<tr class='is24'><td class='is24' valign='top'>Incident Report:</td><td class='is24'><a href='"
        . $Daten['def_ir'] . "'>IR Nummer " . $Daten['hir_id'] . "</a></td></tr>\n";
             $mail_text
                .="<tr class='is24'><td class='is24' valign='top' colspan='2'>Kommentar :<br><br>" . nl2br(htmlspecialchars($Daten['def_text']))
        . "</td></tr>\n";
             $mail_text
                .="<tr class='is24'><td class='is24' valign='top' colspan='2'>Definition der DEFCON Status :<br><br><a href='http://is24-wiki.iscout.local/index.php/DefCons'>http://is24-wiki.iscout.local/index.php/DefCons</a><br>
                <br>Für den Aufruf des Incident Reports von außerhalb des Produktionsnetzes bitte die automatische Proxyerkennung im Browser aktivieren. </td></tr>\n";        
                    $mail_text.="</tbody></table>";                 

            $def_mails_cc=explode(';', $Daten['def_mails_cc']);
            $def_mail_to = $Daten['def_mail_to'];

            $header  = "MIME-Version: 1.0\r\n";
            $header .= "Content-type: text/html; charset=utf-8\r\n";
            $header .= "Content-Transfer-Encoding: 8-bit\r\n";
            $header .= "Return-Path: defcon@immobilienscout24.de\r\n"; 
            $header .= "Reply-To: defcon@immobilienscout24.de\r\n"; 
            $header .= "From: Taskscout24 <taskscout24@immobilienscout24.de>\r\n"; 
            $header .= "CC: ";
            
            $anzahl_mailadressen = COUNT($def_mails_cc);
            
    foreach ($def_mails_cc AS $email)
        {
            $mail_zaehler++;
            if($mail_zaehler == $anzahl_mailadressen) {$letze_cc_adresse = $email; break;}           
            $header .= $email.",";

        }     
            $header .= $letze_cc_adresse."\r\n"; 
            $header .= "Date: " . date('r')."\r\n";
            $temp_ary = explode(' ', (string) microtime());
            $header .= "Message-Id: <" . date('YmdHis') . "." . substr($temp_ary[0],2) . "@immobilienscout24.de>\r\n"; 
           
            #echo $def_mail_to.'<br>'.$Daten['def_betreff'].'<br>'.$mail_text.'<br>'.$header;
            mail($def_mail_to, $Daten['def_betreff'], $mail_text, $header, '-fdefcon@immobilienscout24.de');
                   
        # Update des IR
    
    // Speichere den Datensatz

$sql='INSERT INTO ir_log (' .
    'uir_hirid, ' .
    'uir_hmaid, ' .
    'uir_eintrag, ' .
    'uir_zeitstempel, ' .
    'uir_datum, ' .
    'uir_aktiv) ' .
    'VALUES ( ' .
    '"' . $Daten['hir_id'] . '", ' .
    '"1", ' .
    '"' . mysql_real_escape_string($Daten['def_text']) . '", ' .
    'NOW(), ' .
    'NOW(), ' .
    '"1")';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }
  
    // Zurueck zur Liste

    header('Location: defcon_log.php');
    exit;
    }
?>
