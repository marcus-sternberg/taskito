<?php
###### Editnotes ####
#$LastChangedDate: 2012-02-23 08:30:05 +0100 (Do, 23 Feb 2012) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (isset($_REQUEST['hau_id']))
    {
    $task_id=$_REQUEST['hau_id'];
    }

///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

$sql_check='SELECT uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $task_id;

if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
    {
    fehler();
    }

while ($zeile_check=mysql_fetch_array($ergebnis_check))
    {
    $bearbeiter = $zeile_check['uau_hmaid'];

    if ($bearbeiter != $_SESSION['hma_id'])
        {
        $hauid=$task_id;
        $initiator=$_SESSION['hma_id'];
        $empfaenger=$bearbeiter;
        $info='Aufgabe wurde gelöscht.';

        include('segment_news.php');

        ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

        $mailtag='ume_aufgabestatus';
        $mail_hma_id=$bearbeiter;
        $mail_hau_id=$task_id;
        $text="\nAufgabe wurde gelöscht:\n";
        $mail_info='Aufgabe wurde gelöscht';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];
        include('segment_mail_senden.php');

        ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

        }
    }
///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

$sql='UPDATE aufgaben SET hau_aktiv=0 WHERE hau_id = ' . $task_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$sql='DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $task_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
    'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
    . '", "Task was deleted", NOW() )';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }


            # Schließe ggf. offene Alarme
        
        $sql_alarm = 'SELECT COUNT(hal_id) FROM alarme WHERE hal_hauid = '.$task_id;
        
        if (!($ergebnis_alarm=mysql_query($sql_alarm, $verbindung)))
            {
            fehler();
            }        
        
        if(mysql_num_rows($ergebnis_alarm)>0)
        {
        
           $sql_alarm_info = 'SELECT * FROM alarme WHERE hal_hauid = '.$task_id;         

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
    
            $sql_delete = 'DELETE FROM alarme WHERE hal_hauid = "'.$task_id.'"';         

            if (!$ergebnis_delete=mysql_query($sql_delete, $verbindung))
            {
            fehler();
            }    
        }
        }
    
// Zurueck zur Liste

header('Location: schreibtisch_meine_auftraege.php');
exit;
?>
