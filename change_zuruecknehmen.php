<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');

if (isset($_REQUEST['hau_id']))
    {
    $task_id=$_REQUEST['hau_id'];
    }

///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

$sql_check='SELECT uau_hmaid
FROM aufgaben_mitarbeiter
WHERE uau_hauid = '. $task_id .'
UNION 
SELECT urm_hmaid
FROM rollen_matrix
WHERE urm_uroid =1';

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
        $info='Change wurde zurückgenommen und geschlossen.';

        include('segment_news.php');

        ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

        $mailtag='ume_aufgabestatus';
        $mail_hma_id=$bearbeiter;
        $mail_hau_id=$task_id;
        $text="\nChange wurde zurückgenommen und geschlossen:\n";
        $mail_info='Change wurde zurückgenommen und geschlossen';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];
        include('segment_mail_senden.php');

        ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

        }
    }
///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

$sql='UPDATE aufgaben SET hau_abschluss = 1, hau_abschlussdatum = NOW() WHERE hau_id = ' . $task_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$sql='DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $task_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$sql='DELETE FROM rollen_status WHERE urs_hauid = ' . $task_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }
    
$sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
    'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
    . '", "Change wurde zurückgenommen und geschlossen", NOW() )';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }


// Zurueck zur Liste

header('Location: aufgabe_ansehen.php?hau_id='. $task_id);
exit;
?>
