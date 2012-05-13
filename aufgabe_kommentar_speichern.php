<?php
###### Editnotes ######
#$LastChangedDate: 2012-04-23 17:30:50 +0200 (Mo, 23 Apr 2012) $
#$Author: bpetersen $ 
#####################
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$feld_index = 0; 
$sql_blacklist= 'SELECT hbl_mail, hbl_aktion FROM blacklist WHERE hbl_aktiv = 1 AND (hbl_aktion = 2 OR hbl_aktion = 0)';        

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

$Daten=array();
$level_gruppe = 0;
$Daten['uau_status']= 'off';
$change_flag = 0;

foreach ($_POST as $varname => $value)
    {

    $Daten[$varname]=$value;
    }

if (isset($Daten['ulo_extra']))
    {
    if ($Daten['ulo_extra'] == 'on')
        {
        $Daten['ulo_extra']=1;
        }
    }
else
    {
    $Daten['ulo_extra']=0;
    }

    
// Minuten des Aufwands berechnen

if (isset($Daten['zeit']) AND $Daten['ulo_aufwand_m'] == '' AND $Daten['ulo_aufwand_h']
    == '' AND $Daten['ulo_aufwand_d'] == '')
    {

    $zeit_beginn=substr($Daten['zeit'], 0, 2) * 60 + substr($Daten['zeit'], 3, 2);
    $zeit_ende=substr(date("H:i"), 0, 2) * 60 + substr(date("H:i"), 3, 2);
    $Daten['ulo_aufwand']=$zeit_ende - $zeit_beginn;
    }
else
    {
    if (!(isset($Daten['ulo_aufwand_m'])))
        {
        $Daten['ulo_aufwand_m']=0;
        }

    if (!(isset($Daten['ulo_aufwand_h'])))
        {
        $Daten['ulo_aufwand_h']=0;
        }

    if (!(isset($Daten['ulo_aufwand_d'])))
        {
        $Daten['ulo_aufwand_d']=0;
        }

    $Daten['ulo_aufwand']=($Daten['ulo_aufwand_m']) + ($Daten['ulo_aufwand_h'] * 60)
        + ($Daten['ulo_aufwand_d'] * 60 * 8);
    }

// Umwandlung des Datumsfeldes in DATETIME

$DatumZeit=explode(" ", $Daten['ulo_datum']);
$Datum=explode(".", $DatumZeit[0]);
$Zeit=explode(":", $DatumZeit[1]);

if (count($Zeit) < 2)
    {
    $Zeit[0]='12';
    $Zeit[1]='00';
    }
else if ($Zeit[1] == '' OR $Zeit[0] == '')
    {
    $Zeit[0]='12';
    $Zeit[1]='00';
    }

if (count($Datum) < 3)
    {

    $heute=date("d.m.Y");
    $Datum=explode(".", $heute);
    }
else if (!checkdate($Datum[1], $Datum[0], $Datum[2]))
    {

    $heute=date("d.m.Y");
    $Datum=explode(".", $heute);
    }

$Daten['ulo_datum']=date("Y-m-d H:i:s", mktime($Zeit[0], $Zeit[1], 0, $Datum[1], $Datum[0], $Datum[2]));

#echo $Daten['ulo_datum'].'<br>';
if (!isset($Daten['urs_freigabe_ok']))
    {
    if ($Daten['ulo_text']
        == '' AND (!isset($Daten['uau_stopp']) AND !isset($Daten['uau_status']) AND !isset($Daten['uau_start'])))
        {

        include('segment_kopf.php');

        echo '<br><br>Bitte geben Sie einen Kommentar für die Aktivität ein.';

        echo '<form action="aufgabe_ansehen.php?hau_id=' . $Daten['ulo_aufgabe'] . '" method="post">';

        echo '&nbsp;&nbsp;<input type="submit" name="fehler" value="OK" class="formularbutton" />';

        echo '</form>';

        exit;
        }
    }

if (isset($Daten['uau_stopp_i']) && $Daten['uau_stopp_i'] == 'on')
    {

    $sql='UPDATE aufgaben_mitarbeiter SET uau_stopp = "1" WHERE uau_hauid = "' . $Daten['ulo_aufgabe']
        . '" AND uau_hmaid=' . $_SESSION['hma_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $Daten['ulo_aufgabe'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
        . '", "Bearbeitung der Aufgabe gestoppt - siehe Kommentar", NOW() )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

    $sql_check='SELECT hau_inhaber FROM aufgaben WHERE hau_id = ' . $Daten['ulo_aufgabe'];

    if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
        {
        fehler();
        }

    while ($zeile_check=mysql_fetch_array($ergebnis_check))
        {
        $inhaber=$zeile_check['hau_inhaber'];
        }

    if ($inhaber != $_SESSION['hma_id'])
        {

        $hauid=$Daten['ulo_aufgabe'];
        $initiator=$_SESSION['hma_id'];
        $empfaenger=$inhaber;
        $info='Bearbeitung der Aufgabe ist gestoppt durch ' . $_SESSION['hma_login'] . ' - siehe Kommentar.';

        include('segment_news.php');

        ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
        $level_gruppe = 0;  
        $mail_hma_id=$inhaber;
        $mail_hau_id=$Daten['ulo_aufgabe'];
        $text="Die Bearbeitung der Aufgabe wurde gestoppt durch " . $_SESSION['hma_login'] . " - siehe Kommentar.<br>";
        $mail_info='Aufgabe gestoppt';
        $mailtag='ume_aufgabestatus';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];

        include('segment_mail_senden.php');

        ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

        }

    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

    }

if (isset($Daten['uau_stopp_c']) && $Daten['uau_stopp_c'] == 'on')
    {

    $sql='UPDATE aufgaben_mitarbeiter SET uau_stopp = "2" WHERE uau_hauid = "' . $Daten['ulo_aufgabe']
        . '" AND uau_hmaid=' . $_SESSION['hma_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $Daten['ulo_aufgabe'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
        . '", "Bearbeitung der Aufgabe wurde gestoppt - siehe Kommentar", NOW() )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

    $sql_check='SELECT hau_inhaber FROM aufgaben WHERE hau_id = ' . $Daten['ulo_aufgabe'];

    if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
        {
        fehler();
        }

    while ($zeile_check=mysql_fetch_array($ergebnis_check))
        {
        $inhaber=$zeile_check['hau_inhaber'];
        }

    if ($inhaber != $_SESSION['hma_id'])
        {

        $hauid=$Daten['ulo_aufgabe'];
        $initiator=$_SESSION['hma_id'];
        $empfaenger=$inhaber;
        $info='Die Bearbeitung der Aufgabe wurde gestoppt von ' . $_SESSION['hma_login'] . ' - siehe Kommentar.';
        
        include('segment_news.php');

        ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

        $level_gruppe = 0;  
        $mail_hma_id=$inhaber;
        $mail_hau_id=$Daten['ulo_aufgabe'];
        $text="Die Bearbeitung der Aufgabe wurde gestoppt von " . $_SESSION['hma_login'] . " - siehe Kommentar.<br>";
        $mail_info='Aufgabe gestoppt';
        $mailtag='ume_aufgabestatus';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];
        include('segment_mail_senden.php');

        ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

        }

    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

    }

if (isset($Daten['uau_start']) && $Daten['uau_start'] == 'on')
    {

    $sql='UPDATE aufgaben_mitarbeiter SET uau_stopp = "0" WHERE uau_hauid = "' . $Daten['ulo_aufgabe']
        . '" AND uau_hmaid=' . $_SESSION['hma_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $Daten['ulo_aufgabe'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
        . '", "Die Bearbeitung der Aufgabe wurde fortgesetzt", NOW() )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

    $sql_check='SELECT hau_inhaber FROM aufgaben WHERE hau_id = ' . $Daten['ulo_aufgabe'];

    if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
        {
        fehler();
        }

    while ($zeile_check=mysql_fetch_array($ergebnis_check))
        {
        $inhaber=$zeile_check['hau_inhaber'];
        }

    if ($inhaber != $_SESSION['hma_id'])
        {

        $hauid=$Daten['ulo_aufgabe'];
        $initiator=$_SESSION['hma_id'];
        $empfaenger=$inhaber;
        $info='Bearbeitung der Aufgabe wird fortgesetzt.';

        include('segment_news.php');

        $mail_hma_id=$inhaber;
        $mail_hau_id=$Daten['ulo_aufgabe'];
        $text="Die Bearbeitung der Aufgabe wird fortgesetzt:";
        $mail_info='Aufgabe wird fortgesetzt';
        $mailtag='ume_aufgabestatus';
        include('segment_mail_senden.php');
        }

    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

    }

// ######################### AUFGABE  #####################################
if (isset($Daten['uau_status']) && $Daten['uau_status'] == 'on')
    {

    // Pruefe, ob ich die Aufgabe delegiert hatte

    $sql='SELECT uaz_sba FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $Daten['ulo_aufgabe'] . ' AND uaz_pba = '
        . $_SESSION['hma_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        if ($zeile['uaz_sba'] != 0) // Ups, es gab eine delegierte Aufgabe
            {
            // Loesche die Aufgabe im Konto des SBA
            $sql_delete=
                'DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $Daten['ulo_aufgabe'] . ' AND uau_hmaid = '
                . $zeile['uaz_sba'];

            if (!($ergebnis_delete=mysql_query($sql_delete, $verbindung)))
                {
                fehler();
                }
            }
        }

    // Pruefe, ob ich ein Sekundärbearbeiter war

    $sql='SELECT uaz_sba FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $Daten['ulo_aufgabe'] . ' AND uaz_sba = '
        . $_SESSION['hma_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    if (mysql_num_rows($ergebnis) > 0) // Jupp, die Aufgabe wurde an mich delegiert
        {

        // Setze das Flag des Delegierenden wieder auf aktiv
        // Wer hat die Aufgabe delegiert?

        $sql_sba='SELECT uaz_pba FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $Daten['ulo_aufgabe'] . ' AND uaz_sba = '
            . $_SESSION['hma_id'];

        if (!($ergebnis_sba=mysql_query($sql_sba, $verbindung)))
            {
            fehler();
            }

        while ($zeile_sba=mysql_fetch_array($ergebnis_sba))
            {
            $Delegat=$zeile_sba['uaz_pba'];
            }

        $sql_sba='UPDATE aufgaben_mitarbeiter SET uau_stopp = 0  WHERE uau_hauid = ' . $Daten['ulo_aufgabe']
            . ' AND uau_hmaid = ' . $Delegat;

        if (!($ergebnis_sba=mysql_query($sql_sba, $verbindung)))
            {
            fehler();
            }

        // Lösche mich aus der Zuordnungstabelle
        $sql_sba='UPDATE aufgaben_zuordnung SET uaz_sg = 0, uaz_sba = 0 WHERE uaz_hauid = ' . $Daten['ulo_aufgabe']
            . ' AND uaz_sba = ' . $_SESSION['hma_id'];

        if (!($ergebnis_sba=mysql_query($sql_sba, $verbindung)))
            {
            fehler();
            }
        }
/*
    // Pruefe, ob ich ein Sekundärbearbeiter war

    $sql='SELECT uaz_pba FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $Daten['ulo_aufgabe'] . ' AND uaz_pba = '
        . $_SESSION['hma_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    if (mysql_num_rows($ergebnis) > 0)
        {
        // Loesche mich aus der Zuordnung raus
        $sql_delete='DELETE FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $Daten['ulo_aufgabe'] . ' AND uaz_pba = '
            . $_SESSION['hma_id'];

        if (!($ergebnis_delete=mysql_query($sql_delete, $verbindung)))
            {
            fehler();
            }
        }
*/
    // Pruefe, ob es nur einen Bearbeiter gab, wenn ja, schliesse die Hauptaufgabe

    $sql='SELECT * FROM aufgaben_mitarbeiter WHERE uau_status = "0" AND uau_hauid = "' . $Daten['ulo_aufgabe'] . '"';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    if (mysql_num_rows($ergebnis) == 1) // Ja, nur ein Bearbeiter
        {
        // Setze Hauptaufgabe auf FERTIG
        $sql_ende=
            'UPDATE aufgaben SET hau_abschlussdatum = NOW(), hau_abschluss = 1 WHERE hau_id = "' . $Daten['ulo_aufgabe']
            . '"';

        if (!($ergebnis_ende=mysql_query($sql_ende, $verbindung)))
            {
            fehler();
            }
        }

    // Beende die Unteraufgabe des Mitarbeiter
    $sql='UPDATE aufgaben_mitarbeiter SET uau_status = "1", uau_stopp="0" WHERE uau_hauid = "' . $Daten['ulo_aufgabe']
        . '" AND uau_hmaid=' . $_SESSION['hma_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    // Loesche eventuell noch vorhandene PINGs

    $sql=
        'UPDATE log_status LEFT JOIN log ON ulo_id = uls_uloid SET uls_ping_an = "0", uls_ping_von = "0" WHERE ulo_aufgabe = '
        . $Daten['ulo_aufgabe'] . ' AND (uls_ping_an=' . $_SESSION['hma_id'] . ' OR uls_ping_von = '
        . $_SESSION['hma_id'] . ')';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }


    // Schreibe einen Kommentar ins Log für den Bearbeiter

    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $Daten['ulo_aufgabe'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
        . '", "Aufgabe wurde abgeschlossen", NOW() )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }


    // Sende eine Info

    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

    $sql_check='SELECT hau_inhaber FROM aufgaben WHERE hau_id = ' . $Daten['ulo_aufgabe'];

    if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
        {
        fehler();
        }

    while ($zeile_check=mysql_fetch_array($ergebnis_check))
        {
        $inhaber=$zeile_check['hau_inhaber'];
        }

    if ($inhaber != $_SESSION['hma_id'])
        {
        $hauid=$Daten['ulo_aufgabe'];
        $initiator=$_SESSION['hma_id'];
        $empfaenger=$inhaber;
        $info='Auftrag wurde durch ' . $_SESSION['hma_login'] . ' abgeschlossen.';
        include('segment_news.php');
        $mail_hma_id=$inhaber;
        $mail_hau_id=$Daten['ulo_aufgabe'];
        $text="Ihr Auftrag wurde von " . $_SESSION['hma_login'] . " beendet:<br>";
        $mail_info='Auftrag abgeschlossen';
        $mailtag='ume_aufgabestatus';
        
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];
        
        include('segment_mail_senden.php');
        }
    
            # Schließe ggf. offene Alarme
        
        $sql_alarm = 'SELECT COUNT(hal_id) FROM alarme WHERE hal_hauid = '.$Daten['ulo_aufgabe'];
        
        if (!($ergebnis_alarm=mysql_query($sql_alarm, $verbindung)))
            {
            fehler();
            }        
        
        if(mysql_num_rows($ergebnis_alarm)>0)
        {
        
           $sql_alarm_info = 'SELECT * FROM alarme WHERE hal_hauid = '.$Daten['ulo_aufgabe'];         

           if (!$ergebnis_alarm_info=mysql_query($sql_alarm_info, $verbindung))
           {
           fehler();
           }

           while($zeile_alarm_info = mysql_fetch_assoc($ergebnis_alarm_info)) { 
    
            $sql_insert = 'INSERT INTO alarme_historie (hal_nagiosid, hal_hauid, hal_meldung, hal_status, hal_cciid, hal_service)
                VALUES ("'.$zeile_alarm_info['hal_nagiosid'].'", "'.$zeile_alarm_info['hal_hauid'].'","'.$zeile_alarm_info['hal_meldung'].'","'.$zeile_alarm_info['hal_status'].'", "'.$zeile_alarm_info['hal_cciid'].'", "'.$zeile_alarm_info['hal_service'].'")';         
                 
                   
            if (!$ergebnis_insert=mysql_query($sql_insert, $verbindung)) 
            {
            fehler();
            }    
    
            $sql_delete = 'DELETE FROM alarme WHERE hal_hauid = "'.$Daten['ulo_aufgabe'].'"';         

            if (!$ergebnis_delete=mysql_query($sql_delete, $verbindung))
            {
            fehler();
            }    
        }
        }
    
    
    }

if ($Daten['ulo_ping'] == $_SESSION['hma_id'])
    {
    $Daten['ulo_ping']=0;
    }

if (isset($Daten['ping_off']) && $Daten['ping_off'] == 'on')
    {

    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $Daten['ulo_aufgabe'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
        . '", "Ping deaktivert", NOW() )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $sql='UPDATE log_status LEFT JOIN log ON ulo_id = uls_uloid SET uls_ping_an ="0", uls_ping_von = "0" ' .
        ' WHERE ulo_aufgabe = "' . $Daten['ulo_aufgabe'] . '" AND uls_ping_an =' . $_SESSION['hma_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }


    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

    $sql_check='SELECT uls_ping_von, uls_ping_an FROM log_status WHERE uls_uloid = ' . $Daten['ulo_ping_off'];

    if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
        {
        fehler();
        }

    while ($zeile_check=mysql_fetch_array($ergebnis_check))
        {
        $pingsender = $zeile_check['uls_ping_von'];
        $pingling=$zeile_check['uls_ping_an'];
        }

    if ($pingling != $pingsender)
        {

        $hauid=$Daten['ulo_aufgabe'];
        $initiator=$_SESSION['hma_id'];
        $empfaenger=$pingsender;
        $info='Ihr PING wurde gelesen und deaktiviert durch ' . $_SESSION['hma_login'] . '.';

        include('segment_news.php');

        ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

        $level_gruppe = 0;  
        $mail_hma_id=$pingsender;
        $mail_hau_id=$Daten['ulo_aufgabe'];
        $text="Ihr PING wurde gelesen und deaktiviert durch " . $_SESSION['hma_login'] . ":<br>";
        $mail_info='PING deaktiviert';
        $mailtag='ume_ping';
        
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];
        include('segment_mail_senden.php');

        ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

        }

    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

    }


// Speichere den Datensatz

if (($Daten['ulo_text'] != '' || $Daten['ulo_fertig'] != '' || $Daten['ulo_aufwand'] != '' || isset($_FILES["hau_datei"]["tmp_name"])) OR $Daten['urs_freigabe_ok']
    == 'on')
    {
    if (isset($Daten['ulo_ping']) && $Daten['ulo_ping'] != 0)
        {

        $sql='SELECT hma_login, hma_id FROM mitarbeiter WHERE hma_id = ' . $Daten['ulo_ping'];

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        while ($zeile=mysql_fetch_array($ergebnis))
            {
            $pingling = $zeile['hma_login'];
            $ping_empfaenger=$zeile['hma_id'];
            }

        $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
            'VALUES ("' . $Daten['ulo_aufgabe'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
            . '", "Ping an ' . $pingling . '", NOW() )';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }


        ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

        if ($ping_empfaenger != $_SESSION['hma_id'])
            {

            $hauid=$Daten['ulo_aufgabe'];
            $initiator=$_SESSION['hma_id'];
            $empfaenger=$ping_empfaenger;
            $info=$_SESSION['hma_login'] . ': Hallo, Du hast ein PING, bitte schau mal in diese Aufgabe.';
            
            include('segment_news.php');

            ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////
             
            
            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            $level_gruppe = 0;  
            $mail_hma_id=$ping_empfaenger;
            $mail_hau_id=$Daten['ulo_aufgabe'];
            $text= "Hallo, Du hast ein PING, schau bitte mal in diese Aufgabe.<br>";
            $mail_info='PING';
            $mailtag='ume_ping';
            $mail_comment=1;  
            $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
            $telefon = $_SESSION['hma_telefon'];
            
            include('segment_mail_senden.php');
            
            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            }
        }

if($_POST['speichern']=='Eintrag speichern und mailen') // OR $_POST['speichern']=='Mit Eintrag OTRS-Ticket starten')
        {
        $Daten['ulo_requestor']=1;
        $Daten['ulo_mail']=1;   
        }
    else
        {
        $Daten['ulo_requestor']=0;
        $Daten['ulo_mail']=0;  
        }

    if (isset($Daten['urs_freigabe_ok']) AND $Daten['urs_freigabe_ok'] == 'on')
        {

################# CHANGE wurde freigegeben #############################
            
# 1. Setze das Flag zur Freigabe des Changes und definiere Textblock
            
          $sql_freigabe='UPDATE rollen_status 
                    SET urs_freigabe_durch = ' . $_SESSION['hma_id'] . ', 
                    urs_freigabe_ok = 1 
                    WHERE urs_hauid = ' . $Daten['ulo_aufgabe'];

        if (!($ergebnis_freigabe=mysql_query($sql_freigabe, $verbindung)))
            {
            fehler();
            }
            
    $sql_text = 'SELECT hau_titel, hau_prio FROM aufgaben WHERE hau_id = '.$Daten['ulo_aufgabe'];

    if (!($ergebnis_text=mysql_query($sql_text, $verbindung)))
        {
        fehler();
        }

    while ($zeile_text=mysql_fetch_array($ergebnis_text))
        {
           $text_freigabe = "Der Change [".$zeile_text['hau_titel']."] wurde durch ".$_SESSION['hma_vorname']." ".$_SESSION['hma_name']." freigegeben.";
        }     
        
        if($Daten['ulo_text']!='')
        {
           $text_freigabe .= "<br>Kommentar:<br><br>".$Daten['ulo_text']; 
        }
            
            
            
# 2. Schreibe ins Kommentarfeld die Freigabe

        $sql_freigabe='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
             'VALUES ("' . $Daten['ulo_aufgabe'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
             . '", "'.nl2br(mysql_real_escape_string($text_freigabe)).'", NOW() )';

        if (!($ergebnis_freigabe=mysql_query($sql_freigabe, $verbindung)))
          {
          fehler();
          }
          
# Definiere Eintrag für das Log

    $Daten['ulo_text'] = $text_freigabe;
          
          
/*
# 3. Change in die Queue von CC schieben und ggf. vorhandene Zuordnungen zu Mitarbeitern löschen

        $sql_uaz='UPDATE aufgaben_zuordnung SET 
            uaz_pg = 10, 
            uaz_pba = 0,
            uaz_sg = 0, 
            uaz_sba = 0
            WHERE uaz_hauid = ' . $Daten['ulo_aufgabe'];

        if (!($ergebnis_uaz=mysql_query($sql_uaz, $verbindung)))
            {
            fehler();
            }

        $sql_uaz='DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid = '.$Daten['ulo_aufgabe'];

        if (!($ergebnis_uaz=mysql_query($sql_uaz, $verbindung)))
            {
            fehler();
            }
*/
            
# 4. Informiere CC über den neu eingegangenen Change


$sql_change_inhalt = '  SELECT *, m1.hma_login AS inhaber, m2.hma_login AS freigeber FROM aufgaben 
                        LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id 
                        LEFT JOIN rollen_status ON hau_id = urs_hauid  
                        LEFT JOIN mitarbeiter m2 ON urs_freigabe_durch = m2.hma_id  
                        LEFT JOIN prioritaet ON upr_nummer = hau_prio
                        LEFT JOIN typ_change ON utc_id = hau_utcid 
                        WHERE hau_id = '.$Daten['ulo_aufgabe'];
                        
if (!($ergebnis_change_inhalt = mysql_query($sql_change_inhalt, $verbindung)))
            {
            fehler();
            }
        
        while ($zeile_change_inhalt = mysql_fetch_array($ergebnis_change_inhalt))
            {            


                  
           $mail_text
                = $zeile_change_inhalt['utc_name']."-Change: ". $zeile_change_inhalt['hau_id']." Prioritaet: ". $zeile_change_inhalt['upr_name']. ".\r\n\r\n";            
            $mail_text
                .="angelegt am: ". zeitstempel_anzeigen($zeile_change_inhalt['hau_anlage']). ".\r\n";
            $mail_text
                .="Eigner: ". $zeile_change_inhalt['inhaber']. ".\r\n";                
            $mail_text
                .="freigegeben am: ". zeitstempel_anzeigen($zeile_change_inhalt['urs_zeit']). ".\r\n";                   
            $mail_text
                .="durch: ". $zeile_change_inhalt['freigeber']. ".\r\n";
            $mail_text
                .="abschließen bis: ". datum_anzeigen($zeile_change_inhalt['hau_pende']). ".\r\n\r\n";   
             $mail_text
                .="Thema: \r\n". $zeile_change_inhalt['hau_titel']. ".\r\n\r\n";                                
            $mail_text
                .="Beschreibung:\r\n ". $zeile_change_inhalt['hau_beschreibung']. ".\r\n\r\n";    

            if($Daten['ulo_text']!='')
            {
            $mail_text
                .="Kommentar von ". $zeile_change_inhalt['freigeber'].":\r\n ". $Daten['ulo_text']. ".\r\n\r\n";
            }
               
           if($zeile_change_inhalt['hau_prio'] ==2 OR $zeile_change_inhalt['hau_prio'] ==3)
           {
           $mail_titel = "PRIO Changefreigabe >Ticket ID ".$zeile_change_inhalt['hau_id']."< ".htmlspecialchars(substr($zeile_change_inhalt['hau_titel'],0,100));                
           } else
           {
           $mail_titel = "Normale Changefreigabe >Ticket ID ".$zeile_change_inhalt['hau_id']."< ".htmlspecialchars(substr($zeile_change_inhalt['hau_titel'],0,100));                  
           }
     

             
            $header = "From: Taskscout24 <taskscout24@immobilienscout24.de>\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-type: text/plain; charset=utf-8\r\n";
            $header .= "Content-Transfer-Encoding: 8-bit\r\n";
            $header .= "Return-Path: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "Reply-To: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "Date: " . date('r')."\r\n";
            $temp_ary = explode(' ', (string) microtime());
            $header .= "Message-Id: <" . date('YmdHis') . "." . substr($temp_ary[0],2) . "@immobilienscout24.de>\r\n"; 
            $header .= "X-TASKSCOUT-Priority: ".$zeile_change_inhalt['hau_prio']."\r\n";      
            $header .= "X-TASKSCOUT-Type: ".$zeile_change_inhalt['utc_name']."\r\n";              
                                                                                                                                                                                              
            #echo $mail_titel.'(OTRS)<br>'.$mail_text.'<br>'.$header; exit;
            mail('ticket@otrs.cc.is24.loc', $mail_titel, $mail_text, $header, '-ftaskscout24@immobilienscout24.de');
     } 
          ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

          $sql_info = 'SELECT hma_id FROM mitarbeiter
                    WHERE hma_level = 10';
                 
                    
        if (!($ergebnis_info = mysql_query($sql_info, $verbindung)))
            {
            fehler();
            }
        
        while ($zeile_info = mysql_fetch_array($ergebnis_info))
            {
          
            $hauid=$Daten['ulo_aufgabe'];
            $initiator=$_SESSION['hma_id'];
            $empfaenger=$zeile_info['hma_id'];
            $info=$text_freigabe;
            include('segment_news.php');

            }
            ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////
       
    
# Schreibe das auch bei allen Gruppenmitgliedern ins Newsfach
       
        $sql_change = 'SELECT hma_id FROM mitarbeiter WHERE hma_level = 10';

       
        if (!($ergebnis_change = mysql_query($sql_change, $verbindung)))
            {
            fehler();
            }
        
        while ($zeile_change = mysql_fetch_array($ergebnis_change))
            {
       
        ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

            $hauid=$Daten['ulo_aufgabe'];
            $initiator=$_SESSION['hma_id'];
            $empfaenger=$zeile_change['hma_id'];
            $info=$text_freigabe;
            include('segment_news.php');

            ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////
            }           
 
 /* Nehme ich raus, da es zwei Mails gibt. Momentan ist Eigner = bearbeiter .
 
 # 5. Informiere den Changesteller über die Freigabe

    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////
    $sql_check='SELECT hau_inhaber FROM aufgaben WHERE hau_id = ' . $Daten['ulo_aufgabe'];

    if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
        {
        fehler();
        }

    while ($zeile_check=mysql_fetch_array($ergebnis_check))
        {
        $pingling = $zeile_check['hau_inhaber'];

        if ($pingling != $_SESSION['hma_id'])
            {
            $hauid=$Daten['ulo_aufgabe'];
            $initiator=$_SESSION['hma_id'];
            $empfaenger=$pingling;
            $info=$_SESSION['hma_login'] . ' hat Deinen Change freigegeben.';

            include('segment_news.php');


            ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
            $level_gruppe = 0;   
            $mailtag='ume_aufgabestatus';
            $mail_hma_id=$pingling;
            $mail_hau_id=$Daten['ulo_aufgabe'];
            $text=$text_freigabe;
            $mail_info='Changefreigabe '; 
            $mail_comment=1;
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];

            include('segment_mail_senden.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
            }
        
        }
      */
      
# 6. Definiere Werte fürs CC-Activitylog
        
     /*   if($Daten['ulo_text']=='') {$Daten['ulo_text']=$text_freigabe;}
        $Daten['ulo_requestor']=$_SESSION['hma_id'];
        $Daten['ulo_extra']=1;
      */ 
# 7. Setze Infoflag, daß keine weiteren News für den Changesteller eingetragen werden

       $change_flag = 1;

        }  // Changelauf ist abgeschlossen
        
    $sql='INSERT INTO log (' .
        'ulo_id, ' .
        'ulo_aufgabe, ' .
        'ulo_text, ' .
        'ulo_zeitstempel, ' .
        'ulo_ma, ' .
        'ulo_datum, ' .
        'ulo_aufwand, ' .
        'ulo_fertig, ' .
        'ulo_extra, ' .
        'ulo_requestor, ' .
        'ulo_mail, '.
        'ulo_ping) ' .
        'VALUES ( ' .
        'NULL, ' .
        '"' . $Daten['ulo_aufgabe'] . '", ' .
        '"' . nl2br(mysql_real_escape_string($Daten['ulo_text'])) . '", ' .
        'NOW(), ' .
        '"' . $_SESSION['hma_id'] . '", ' .
        '"' . $Daten['ulo_datum'] . '", ' .
        '"' . $Daten['ulo_aufwand'] . '", ' .
        '"' . $Daten['ulo_fertig'] . '", ' .
        '"' . $Daten['ulo_extra'] . '", ' .
        '"' . $Daten['ulo_requestor'] . '", ' .
        '"' . $Daten['ulo_mail'] . '", ' .  
        '"' . $Daten['ulo_ping'] . '")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $ulo_id=mysql_insert_id();

    if ($Daten['ulo_ping'] != 0)
        {
        $sql='INSERT INTO log_status (' .
            'uls_uloid, ' .
            'uls_ping_an, ' .
            'uls_ping_von) ' .
            'VALUES ( ' .
            '"' . $ulo_id . '", ' .
            '"' . $Daten['ulo_ping'] . '", ' .
            '"' . $_SESSION['hma_id'] . '")';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }
        }
    else
        {
        $empfaenger=array();
        $sql_wer='SELECT hau_inhaber FROM aufgaben WHERE hau_id = ' . $Daten['ulo_aufgabe'];

        if (!($ergebnis_wer=mysql_query($sql_wer, $verbindung)))
            {
            fehler();
            }

        while ($zeile_wer=mysql_fetch_array($ergebnis_wer))
            {
            if ($zeile_wer['hau_inhaber'] != $_SESSION['hma_id'])
                {
                $empfaenger[]=$zeile_wer['hau_inhaber'];
                }
            $inhaber=$zeile_wer['hau_inhaber'];
            }
        $sql_wer='SELECT uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $Daten['ulo_aufgabe'];

        if (!($ergebnis_wer=mysql_query($sql_wer, $verbindung)))
            {
            fehler();
            }

        while ($zeile_wer=mysql_fetch_array($ergebnis_wer))
            {
            if ($zeile_wer['uau_hmaid'] != $_SESSION['hma_id'] AND $zeile_wer['uau_hmaid'] != $inhaber)
                {
                $empfaenger[]=$zeile_wer['uau_hmaid'];
                }
            }

        foreach ($empfaenger AS $abonnement)
            {

            $sql = 'INSERT INTO log_status (' .
                'uls_uloid, ' .
                'uls_komm_an, ' .
                'uls_komm_von) ' .
                'VALUES ( ' .
                '"' . $ulo_id . '", ' .
                '"' . $abonnement . '", ' .
                '"' . $_SESSION['hma_id'] . '")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
            }
        } # Ende ELSE
        
if(isset($Daten['ulo_deploy']))
{
$sql='UPDATE aufgaben SET hau_typ = 11 WHERE hau_id = '.$Daten['ulo_aufgabe'];

if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
}
      
        
### Der Kommentar soll als Mail extern verschickt werden

#if (isset($Daten['ulo_mail']) AND $Daten['ulo_mail'] == '1')
if($_POST['speichern']=='Eintrag speichern und mailen')
{
$schwarze_liste = array();
$mail_to = array();
$mail_cc = array();
$otrs_gefunden = 0;
    
foreach($eMail_blacklist AS $mail)
{
    $schwarze_liste[] = $mail['mail'];
}

    $kommentar = htmlspecialchars(Preg_Replace('/<br(\s+)?\/?>/i', "\n",$Daten['ulo_text']));

    $sql_mail_to = 'SELECT * FROM ticket_info WHERE uti_hauid = ' . $Daten['ulo_aufgabe'].' AND uti_aktiv = 1 AND uti_status=1';
         
                      
    if (!($ergebnis_mail_to=mysql_query($sql_mail_to, $verbindung)))
    {
        fehler();
    }
            
    while ($zeile_mail_to=mysql_fetch_array($ergebnis_mail_to))
    {
        if($zeile_mail_to['uti_mail']=='ticket@otrs.cc.is24.loc')
            {$otrs_gefunden = 1;} 
        else
            {$mail_to[] = $zeile_mail_to['uti_mail'];}
    }
    
    array_diff_ORG_NEW($mail_to, $schwarze_liste, 'VALUES');        
 
    $mail_to = array_unique($mail_to);
    $mail_to = implode(",", $mail_to);
  
$schwarze_liste = array();
foreach($eMail_blacklist AS $mail)
{
    $schwarze_liste[] = $mail['mail'];
}
                  
    $sql_mail_cc = 'SELECT * FROM ticket_info WHERE uti_hauid = ' . $Daten['ulo_aufgabe'].' AND uti_aktiv = 1 AND uti_status=0';
                            
    if (!($ergebnis_mail_cc=mysql_query($sql_mail_cc, $verbindung)))
    {
        fehler();
    }
    
    while ($zeile_mail_cc=mysql_fetch_array($ergebnis_mail_cc))
    {
        if($zeile_mail_cc['uti_mail']=='ticket@otrs.cc.is24.loc')
            {$otrs_gefunden = 1;} 
        else
            {$mail_cc[] = $zeile_mail_cc['uti_mail'];}
    }
    
    array_diff_ORG_NEW($mail_cc, $schwarze_liste, 'VALUES');        
 
    $mail_cc = array_unique($mail_cc);
    $mail_cc = implode(",", $mail_cc);
           
        $sql_mail ='SELECT * FROM ticket_info 
                LEFT JOIN log ON ulo_aufgabe = uti_hauid 
                LEFT JOIN aufgaben ON hau_id = ulo_aufgabe 
                LEFT JOIN mitarbeiter ON hma_id = ulo_ma 
                WHERE ulo_id = '
            . $ulo_id . ' AND uti_aktiv = 1 AND uti_hauid = ' . $Daten['ulo_aufgabe'];

        if (!($ergebnis_mail=mysql_query($sql_mail, $verbindung)))
            {
            fehler();
            }
        
    while ($zeile_mail=mysql_fetch_array($ergebnis_mail))
    {    
        $ticket_titel = $zeile_mail['hau_titel'];
        $ticket_prio = $zeile_mail['hau_prio'];              
        $ticket_inhalt = nl2br(htmlspecialchars(substr($zeile_mail['hau_beschreibung'],0,500)));
        $mitarbeiter = $zeile_mail['hma_vorname'] . " " . $zeile_mail['hma_name'];
        $telefon = $zeile_mail['hma_telefon'];
        $md5 = $zeile_mail['uti_md5'];
        $datum =  zeitstempel_anzeigen($zeile_mail['ulo_datum']);
        $betreffzeile = $ticket_titel;
        if ( (strlen($zeile_mail['hau_otrsnr']) > 1 ) && ( strpos($betreffzeile,$zeile_mail['hau_otrsnr']) < 1 ) ) { $betreffzeile=$betreffzeile ." [Ticket#".$zeile_mail['hau_otrsnr']."]"; }
    }             
       
### Prüfe, ob Anlagen da sind und versandt werden sollen

## Lese dazu die Namen der ausgewählten Anlagen ein

#Lese alle zu versendenden Dateien ein

$zu_versendene_dateien = array();

$sql_anlage = 'SELECT uan_name FROM anlagen WHERE uan_hauid = '.$Daten['ulo_aufgabe'].' AND uan_senden = 1';

if (!($ergebnis_anlage=mysql_query($sql_anlage, $verbindung)))
{
    fehler();
}

while ($zeile_anlage=mysql_fetch_array($ergebnis_anlage))
{
    $zu_versendene_dateien[] = $zeile_anlage['uan_name'];    
}


# Pfad zu den Anlagen
$target_path="anhang/" . $Daten['ulo_aufgabe'] . "/";

# generate boundary... 
$boundary = strtoupper(md5(uniqid(time()))); 

if($otrs_gefunden==1) 
{  
                         
            $betreff=htmlspecialchars(substr($betreffzeile,0,100)).' >Ticket ID ' . $Daten['ulo_aufgabe']. '< ';  
            
            $header = "From: Taskscout24 <taskscout24@immobilienscout24.de>\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-Type: multipart/mixed; boundary=$boundary\r\n";  
            $header .= "Return-Path: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "Reply-To: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "Date: " . date('r')."\r\n";
            $temp_ary = explode(' ', (string) microtime());
            $header .= "Message-Id: <" . date('YmdHis') . "." . substr($temp_ary[0],2) . "@immobilienscout24.de>\r\n"; 
            $header .= "X-TASKSCOUT-Priority: ".$ticket_prio."\r\n"; 
            
        # commencement of mail-text part...  
            $mail_text = ''; 
            $mail_text = "--$boundary\r\n";  
            $mail_text .= "Content-type: text/plain; charset=utf-8\r\n";
            $mail_text .= "Content-Transfer-Encoding: 8-bit\r\n\r\n";
            $mail_text
                .="Ticketkommentar:\n ". $kommentar . "\r\n\r\n";
             $mail_text
                .="durch: " . $mitarbeiter . "\r\n\r\n";
             $mail_text
                .="um: " . $datum . "\r\n\r\n"; 


foreach($zu_versendene_dateien AS $datei)
{
        $file_content = fread(fopen($target_path.$datei,"r"),filesize($target_path.$datei)); 
        # encode file to BASE64... 
        $file_content = chunk_split(base64_encode($file_content)); 

        #get the MIME
        $file_mime = mime_content_type($datei);
            
        # commencement of attachement... 
        $mail_text .= "--$boundary\r\n"; 
        $mail_text .= "Content-Type: $file_mime; name=\"$datei\"\r\n";  

        # encode file to BASE64... 
        $mail_text .= "Content-Transfer-Encoding: base64\r\n"; 
        $mail_text .= "Content-Disposition: attachment; filename=\"$datei\"\r\n"; 
        $mail_text .= "$file_content\r\n"; 
}
        
        # print ending of email... 
        $mail_text .= "--$boundary--\r\n"; 
        
        #echo $header.mail_text;; exit;
        mail('ticket@otrs.cc.is24.loc', $betreff, $mail_text, $header, '-ftaskscout24@immobilienscout24.de');
                   
            # Flag für die versendung wieder auf 0 setzen, um erneutes Senden zu vermeiden
            
            $sql_flag = 'UPDATE anlagen SET uan_senden = 0 WHERE uan_hauid = '.$Daten['ulo_aufgabe'];
            
            if (!($ergebnis_flag=mysql_query($sql_flag, $verbindung)))
            {
                fehler();
            }
            
            
  } 


# Message
 
  $mail_text = '
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
            $Daten['ulo_text']=str_replace($suche, $ersetze, $Daten['ulo_text']);

            $mail_text.="<body><table class='is24_mail' width='600'>\n";
           $mail_text.="<caption class='is24'>";
            $mail_text
                .="<img src='http://www.insolitus.de/img/tom_small.gif'></img>\n";
            $mail_text.="</caption>"; 

            $mail_text.="<thead class='is24'>";   
            $mail_text.="<tr class='is24'><th class='is24'>Ticket-Service</th><th class='is24'>" . date('H:i d.m.Y') . "</th></tr>";
            $mail_text.="</thead>";   
            $mail_text.="<tbody class='is24'>";   
            if($Daten['uau_status']=='on')
            {
            $mail_text
                .="<tr class='is24'><td class='is24' nowrap valign='top'>Ihr Ticket wurde geschlossen :</td><td class='is24'> "
                . $Daten['ulo_aufgabe'] . "</td></tr>\n";                
            } else
            {
            $mail_text
                .="<tr class='is24'><td class='is24' nowrap valign='top'>Aktualisierung Ihres Tickets :</td><td class='is24'> "
                . $Daten['ulo_aufgabe'] . "</td></tr>\n";
            }
            $mail_text
                .="<tr class='is24'><td class='is24' nowrap valign='top'>Link zum Ticket :</td><td class='is24'><a href='http://taskscout24.prod/ticket_anzeigen.php?ticket_nr="
                . ($md5) . "'>Infos zu Ticket " . $Daten['ulo_aufgabe'] . " anzeigen.</td></tr>\n";
            $mail_text
                .="<tr class='is24'><td class='is24' valign='top'>Ticketthema :</td><td class='is24'> "
                . $ticket_titel . "</td></tr>\n";
              $mail_text
                .="<tr class='is24'><td class='is24' valign='top' colspan='2'>Kommentar :<br><br>" . $kommentar . "</td></tr>\n";
            $mail_text
                .="<tr class='is24'><td class='is24' valign='top'>Inhalt :</td><td class='is24'> "
                . $ticket_inhalt . " ...</td></tr>\n";
              $mail_text
                .="<tr class='is24'><td class='is24' valign='top'>Bearbeiter :</td><td class='is24'>"
                . $mitarbeiter . "<br>Telefon: ".$telefon."</td></tr>\n";
            $mail_text.="</tbody></table>";                 

            if($Daten['uau_status']=='on')
            {
            $betreff=htmlspecialchars(substr($betreffzeile,0,100)).' - Bearbeitung abgeschlossen >Ticket ID ' . $Daten['ulo_aufgabe']. '< ';  
            } else
            {
            $betreff= htmlspecialchars(substr($betreffzeile,0,100)).' - Aktualisierung >Ticket ID ' . $Daten['ulo_aufgabe'] . '< ';           
            }

            
      
            $header = "From: Taskscout24 <taskscout24@immobilienscout24.de>\r\n";   
            if($mail_cc!='')
            {$header .= "CC: ".$mail_cc."\r\n";}
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-Type: multipart/mixed; boundary=$boundary\r\n";   
            $header .= "Return-Path: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "Reply-To: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "Date: " . date('r')."\r\n";
            $temp_ary = explode(' ', (string) microtime());
            $header .= "Message-Id: <" . date('YmdHis') . "." . substr($temp_ary[0],2) . "@immobilienscout24.de>\r\n"; 
            $header .= "X-TASKSCOUT-Priority: ".$ticket_prio."\r\n";  


        # commencement of mail-text part...  
            $mail_inhalt = ''; 
            $mail_inhalt = "--$boundary\r\n";  
            $mail_inhalt .= "Content-type: text/html; charset=utf-8\r\n";
            $mail_inhalt .= "Content-Transfer-Encoding: 8-bit\r\n\r\n";
            $mail_inhalt .= "$mail_text\r\n"; 

 


foreach($zu_versendene_dateien AS $datei)
{
        $file_content = fread(fopen($target_path.$datei,"r"),filesize($target_path.$datei)); 
        # encode file to BASE64... 
        $file_content = chunk_split(base64_encode($file_content)); 

        #get the MIME
        $file_mime = mime_content_type($datei);
            
        # commencement of attachement... 
        $mail_inhalt .= "--$boundary\r\n"; 
        $mail_inhalt .= "Content-Type: $file_mime; name=\"$datei\"\r\n";  

        # encode file to BASE64... 
        $mail_inhalt .= "Content-Transfer-Encoding: base64\r\n"; 
        $mail_inhalt .= "Content-Disposition: attachment; filename=\"$datei\"\r\n"; 
        $mail_inhalt .= "$file_content\r\n"; 
}
        
        # print ending of email... 
        $mail_inhalt .= "--$boundary--\r\n"; 
        
            mail($mail_to, $betreff, $mail_inhalt, $header, '-ftaskscout24@immobilienscout24.de');
            
            # Flag für die versendung wieder auf 0 setzen, um erneutes Senden zu vermeiden
            
            $sql_flag = 'UPDATE anlagen SET uan_senden = 0 WHERE uan_hauid = '.$Daten['ulo_aufgabe'];
            
            if (!($ergebnis_flag=mysql_query($sql_flag, $verbindung)))
            {
                fehler();
            }

        } 
/*
if($_POST['speichern']=='Mit Eintrag OTRS-Ticket starten')
{

        $sql_mail='INSERT INTO ticket_info (uti_hauid, uti_mail, uti_status, uti_aktiv) VALUES ("'.$Daten['ulo_aufgabe'].'", "ticket@otrs.cc.is24.loc", "1", "1")';


        if (!($ergebnis_mail=mysql_query($sql_mail, $verbindung)))
            {
            fehler();
            }

        $sql_mail ='SELECT * FROM ticket_info 
                LEFT JOIN log ON ulo_aufgabe = uti_hauid 
                LEFT JOIN aufgaben ON hau_id = ulo_aufgabe 
                LEFT JOIN mitarbeiter ON hma_id = ulo_ma 
                WHERE ulo_id = '
            . $ulo_id . ' AND uti_aktiv = 1 AND uti_hauid = ' . $Daten['ulo_aufgabe'];

        if (!($ergebnis_mail=mysql_query($sql_mail, $verbindung)))
            {
            fehler();
            }
            
$kommentar = htmlspecialchars(Preg_Replace('/<br(\s+)?\/?>/i', "\n",$Daten['ulo_text']));
        
    while ($zeile_mail=mysql_fetch_array($ergebnis_mail))
    {    
        $ticket_titel = $zeile_mail['hau_titel'];
        $ticket_prio = $zeile_mail['hau_prio'];              
        $ticket_inhalt = nl2br(htmlspecialchars(substr($zeile_mail['hau_beschreibung'],0,500)));
        $mitarbeiter = $zeile_mail['hma_vorname'] . " " . $zeile_mail['hma_name'];
        $telefon = $zeile_mail['hma_telefon'];
        $md5 = $zeile_mail['uti_md5'];
        $datum =  zeitstempel_anzeigen($zeile_mail['ulo_datum']);
        $betreffzeile = $ticket_titel;
        if ( (strlen($zeile_mail['hau_otrsnr']) > 1 ) && ( strpos($betreffzeile,$zeile_mail['hau_otrsnr']) < 1 ) ) { $betreffzeile="[Ticket#".$zeile_mail['hau_otrsnr']."]".$betreffzeile ; }
    }
            
#Lese alle zu versendenden Dateien ein

$zu_versendene_dateien = array();

$sql_anlage = 'SELECT uan_name FROM anlagen WHERE uan_hauid = '.$Daten['ulo_aufgabe'].' AND uan_senden = 1';

if (!($ergebnis_anlage=mysql_query($sql_anlage, $verbindung)))
{
    fehler();
}

while ($zeile_anlage=mysql_fetch_array($ergebnis_anlage))
{
    $zu_versendene_dateien[] = $zeile_anlage['uan_name'];    
}


# Pfad zu den Anlagen
$target_path="anhang/" . $Daten['ulo_aufgabe'] . "/";

# generate boundary... 
$boundary = strtoupper(md5(uniqid(time()))); 

  
           // Lösche eventuellen alten Mailtext
            
            $mail_text
                .="Ticketbetreff: " . $ticket_titel . "\r\n\r\n";
            $mail_text
                .="Ticketinhalt: " . $ticket_inhalt . "\r\n\r\n";
            $mail_text
                .="Ticketkommentar:\n ". $kommentar . "\r\n\r\n";
            $mail_text
                .="durch: " . $mitarbeiter . "\r\n\r\n";
            $mail_text
                .="um: " . $datum . "\r\n\r\n";
          
            $betreff='>Ticket ID ' . $Daten['ulo_aufgabe']. '< '.htmlspecialchars(substr($betreffzeile,0,100));  
            
            $header = "From: taskscout24@immobilienscout24.de (TaskScout 24)\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Return-Path: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "Reply-To: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "Date: " . date('r')."\r\n";
            $temp_ary = explode(' ', (string) microtime());
            $header .= "Message-Id: <" . date('YmdHis') . "." . substr($temp_ary[0],2) . "@immobilienscout24.de>\r\n"; 
            $header .= "X-TASKSCOUT-Priority: ".$ticket_prio."\r\n"; 
            
        # commencement of mail-text part...  
            $header .= "--$boundary\r\n";  
            $header .= "Content-type: text/plain; charset=utf-8\r\n";
            $header .= "Content-Transfer-Encoding: 8-bit\r\n";
            $header .= "$mail_text\r\n"; 


foreach($zu_versendene_dateien AS $datei)
{
        $file_content = fread(fopen($target_path.$datei,"r"),filesize($target_path.$datei)); 
        # encode file to BASE64... 
        $file_content = chunk_split(base64_encode($file_content)); 

         #get the MIME
        $file_mime = mime_content_type($datei);
           
        # commencement of attachement... 
        $header .= "--$boundary\r\n"; 
        $header .= "Content-Type: $file_mime; name=\"$datei\"\r\n"; 

        # encode file to BASE64... 
        $header .= "Content-Transfer-Encoding: base64\r\n"; 
        $header .= "Content-Disposition: attachment; filename=\"$datei\"\r\n"; 
        $header .= "$file_content\r\n"; 
}
        
        # print ending of email... 
        $header .= "--$boundary--\r\n"; 
        
        mail('ticket@otrs.cc.is24.loc', $betreff, '', $header, '-ftaskscout24@immobilienscout24.de');
        
                    # Flag für die versendung wieder auf 0 setzen, um erneutes Senden zu vermeiden
            
            $sql_flag = 'UPDATE anlagen SET uan_senden = 0 WHERE uan_hauid = '.$Daten['ulo_aufgabe'];
            
            if (!($ergebnis_flag=mysql_query($sql_flag, $verbindung)))
            {
                fehler();
            }
            
  }
*/
           
        
        
       
    if(isset($_FILES["hau_datei"]["tmp_name"]))
    {
    if ($_FILES["hau_datei"]["tmp_name"] != '')
        {
        if (!is_dir("anhang/" . $Daten['ulo_aufgabe']))
            {
            $oldumask = umask(0); 
            mkdir("anhang/" . $Daten['ulo_aufgabe'], 0777);
            umask($oldumask); 
            }
               
        if (($_FILES["hau_datei"]["error"] == 3) OR ($_FILES["hau_datei"]["error"] == 4))
            {
            
            $sql = 'insert into eventlog ( hel_area,hel_type,hel_referer,hel_text) values ( "FILE", "Uploaderror", "'.$Daten['ulo_aufgabe'].'", "Fehler= '.$_FILES["hau_datei"]["error"].'")';   
        
            if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }

            }
        else
            {
            move_uploaded_file($_FILES["hau_datei"]["tmp_name"], "anhang/" . $Daten['ulo_aufgabe'] . "/" . $_FILES["hau_datei"]["name"]);
                      
            $sql = 'insert into eventlog ( hel_area,hel_type,hel_referer,hel_text) values ( "FILE", "Upload_OK", "'.$Daten['ulo_aufgabe'].'", "File= '.$_FILES["hau_datei"]["name"].' Fehler= '.$_FILES["hau_datei"]["error"].'")';   
        
            if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }
            
            $sql = 'insert INTO anlagen (uan_name, uan_besitzer, uan_hauid) values ( "'.$_FILES["hau_datei"]["name"].'", "'.$_SESSION['hma_login'].'", "'.$Daten['ulo_aufgabe'].'")'; 
            
            if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }
            
            }
        }
    }
                   /*
            echo '<hr>';
            echo dirname( __FILE__ );
            echo '<hr>';
            echo $_FILES["hau_datei"]["tmp_name"];
            echo '<hr>';
            echo "anhang/" . $Daten['ulo_aufgabe'];
            echo '<hr>';
            echo $Daten['ulo_aufgabe']; exit;  */
           
    # Informiere alle, die an der Aufgabe arbeiten

    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

    $sql_check='SELECT uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $Daten['ulo_aufgabe'];

    if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
        {
        fehler();
        }

    while ($zeile_check=mysql_fetch_array($ergebnis_check))
        {
        $pingling = $zeile_check['uau_hmaid'];

        if ($pingling != $_SESSION['hma_id'])   // Bitte dran denken, daß dies alle Kommentare ausschaltet, nicht nur den Changesteller
            {
            $hauid=$Daten['ulo_aufgabe'];
            $initiator=$_SESSION['hma_id'];
            $empfaenger=$pingling;
            $info=$_SESSION['hma_login'] . ' hat einen Kommentar in dieser Aufgabe hinterlassen.';

            include('segment_news.php');


            ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
            $level_gruppe = 0;   
            $mailtag='ume_kommentar_erhalten';
            $mail_hma_id=$pingling;
            $mail_hau_id=$Daten['ulo_aufgabe'];
            $text=$_SESSION['hma_login'] . " hat einen Kommentar hinterlassen";
            $mail_info='Neuer Kommentar';
            $mail_comment=1;

            include('segment_mail_senden.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
            }
        }

    # Informiere den Aufgabeninhaber

    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

    $sql_check='SELECT hau_inhaber FROM aufgaben WHERE hau_id = ' . $Daten['ulo_aufgabe'];

    if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
        {
        fehler();
        }

    while ($zeile_check=mysql_fetch_array($ergebnis_check))
        {
        $pingling = $zeile_check['hau_inhaber'];

        if ($pingling != $_SESSION['hma_id'])
            {
            $hauid=$Daten['ulo_aufgabe'];
            $initiator=$_SESSION['hma_id'];
            $empfaenger=$pingling;
            $info=$_SESSION['hma_login'] . ' hat einen Kommentar hinterlassen.';

            include('segment_news.php');


            ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
            $level_gruppe = 0;   
            $mailtag='ume_kommentar_erhalten';
            $mail_hma_id=$pingling;
            $mail_hau_id=$Daten['ulo_aufgabe'];
            $text=$_SESSION['hma_login'] . " hat einen Kommentar hinterlassen.";
            $mail_info='Neuer Kommentar';
            $mail_comment=1;

            include('segment_mail_senden.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
            } 
        }

}  // Ende Datensatz speichern    

    # Sichere den Eintrag ins Activitylog von IS24

    if ($Daten['ulo_extra'] == 1)
        {

        require_once('segment_kopf.php');

        echo '<form action="aufgabe_activity_log_speichern.php" method="post">';

        # Konnektiere Dich auf die ACTIVITY-LOG-Datenbank

        $rechnername="bersql03";
        $datenbankname="activitylog";
        $benutzername="activitylog";
        $passwort="activitylog";

        // Verbindung zum Host oeffnen
        if (!$verbindung=mysql_connect($rechnername, $benutzername, $passwort))
            die("Konnte keine Verbindung herstellen !</p>");

        // Datenbank auswaehlen
        if (!(mysql_select_db($datenbankname, $verbindung)))
            fehler();

        // Baue Tabelle

        echo '<table>';

        # Frage Name ab

        echo '<tr>';

        echo '<td class="text_klein">Eintragender: </td><td>';

        echo '<select size="1" name="ac_user">';
        $sql='SELECT id, firstname, lastname FROM users ' .
            'ORDER BY lastname';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }

        while ($zeile=mysql_fetch_array($ergebnis))
            {
            if ($zeile['id'] == $_SESSION['hma_al_id'])
                {
                echo '<option value="' . $zeile['id'] . '" selected="selected"><span class="text">' . $zeile['lastname']
                    . ', ' . $zeile['firstname'] . '</span></option>';
                }
            else
                {
                echo '<option value="' . $zeile['id'] . '"><span class="text">' . $zeile['lastname'] . ', '
                    . $zeile['firstname'] . '</span></option>';
                }
            }

        echo '</select>';

        echo '</td></tr>';


        # Frage Plattform ab

        echo '<tr>';

        echo '<td class="text_klein">Plattform: </td><td>';

        echo '<select size="1" name="ac_environment">';
        $sql='SELECT id, name, recipients FROM environments ' .
            'ORDER BY name';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }

        while ($zeile=mysql_fetch_array($ergebnis))
            {
            if ($zeile['id'] == 1)
                {
                echo '<option value="' . $zeile['id'] . '" selected><span class="text">' . $zeile['name']
                    . '</span></option>';
                }
            else
                {
                echo '<option value="' . $zeile['id'] . '"><span class="text">' . $zeile['name'] . '</span></option>';
                }
            }

        echo '</select>';

        echo '</td></tr>';

        # Frage Aktivität ab

        echo '<tr>';

        echo '<td class="text_klein">Aktivität: </td><td>';

        echo '<select size="1" name="ac_activity">';
        $sql='SELECT id, name FROM activities ' .
            'ORDER BY name';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }

        while ($zeile=mysql_fetch_array($ergebnis))
            {
            if ($zeile['id'] == 7)
                {
                echo '<option value="' . $zeile['id'] . '" selected><span class="text">' . $zeile['name']
                    . '</span></option>';
                }
            else
                {
                echo '<option value="' . $zeile['id'] . '"><span class="text">' . $zeile['name'] . '</span></option>';
                }
            }

        echo '</select>';

        echo '</td></tr>';

        #Frage Bereich ab

        echo '<tr>';

        echo '<td class="text_klein">Bereich: </td><td>';

        echo '<select size="1" name="ac_area">';
        $sql='SELECT id, name FROM areas ' .
            'ORDER BY name';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }

        while ($zeile=mysql_fetch_array($ergebnis))
            {
            if ($zeile['id'] == 5)
                {
                echo '<option value="' . $zeile['id'] . '" selected><span class="text">' . $zeile['name']
                    . '</span></option>';
                }
            else
                {
                echo '<option value="' . $zeile['id'] . '"><span class="text">' . $zeile['name'] . '</span></option>';
                }
            }

        echo '</select>';

        echo '</td></tr>';

        # Zeige Eintrag an

        echo '<tr>';

        echo '<td class="text_klein" valign="top">Eintrag:</td><td><textarea cols="80" rows="5" name="ac_eintrag">'
            . htmlspecialchars($Daten['ulo_text']) . '</textarea></td>';

        echo '</tr>';

        echo '<tr><td></td><td class="text_klein" valign="top" colspan="2"><a href="aufgabe_ansehen.php?hau_id='
            . $Daten['ulo_aufgabe']
                . '">Nicht ins IS24 Activity Log schreiben</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="activitylog" value="Schreibe ins IS24 Activity-Log" class="formularbutton" /></td></tr>';

        echo '<input type="hidden" name="hau_id" value="' . $Daten['ulo_aufgabe'] . '">';

        echo '</table>';

        echo '</form>';
        }
        else
        {


// Zurueck zur Liste

if (isset($Daten['uau_status']) AND $Daten['uau_status'] == 'on')
    {

    header('Location: schreibtisch_meine_aufgaben.php');
    exit;
    }
else
    {

    header('Location: aufgabe_ansehen.php?hau_id=' . $Daten['ulo_aufgabe']);
    exit;
    }
        }

?>
