<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (isset($_GET['hau_id']))
    {
    $task_id=$_GET['hau_id'];
    }

if (isset($_GET['toggle']))
    {
    $toggle=$_GET['toggle'];
    }

if ($toggle == 1)
    { // Aufgabe komplett zurücknehmen

    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
        . '", "Task was dropped in Pool again by Teamlead", NOW() )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $sql='UPDATE aufgaben SET hau_teamleiter = "0", hau_terminaendern = "0", hau_tl_status = "0" WHERE hau_id = "'
        . $task_id . '"';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $sql='SELECT * FROM aufgaben_mitarbeiter WHERE uau_hauid =' . $task_id;

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

        if ($zeile['uau_hmaid'] != $_SESSION['hma_id'])
            {

            $hauid=$task_id;
            $initiator=$_SESSION['hma_id'];
            $empfaenger=$zeile['uau_hmaid'];
            $info='This task was dropped again in Pool by Teamlead. No further action required.';

            include('segment_news.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            $mail_hma_id=$empfaenger;
            $mail_hau_id=$hauid;
            $text="\nThis task was dropped again in Pool by Teamlead. No further action required.\n";
            $mail_info='Task revoked';
            $mailtag='ume_ping';
            include('segment_mail_senden.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            }

        ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////
        }
    $sql='DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid =' . $task_id;

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }
else
    { // Aufgabe bleibt, nur MA mit STOPP löschen

    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
        . '", "Teamlead removed Staff Member", NOW() )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $sql='SELECT * FROM aufgaben_mitarbeiter WHERE uau_hauid =' . $task_id . ' AND uau_ma_status=2';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

        if ($zeile['uau_hmaid'] != $_SESSION['hma_id'])
            {

            $hauid=$task_id;
            $initiator=$_SESSION['hma_id'];
            $empfaenger=$zeile['uau_hmaid'];
            $info='This task was dropped again in Pool by Teamlead. No further action required.';

            include('segment_news.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            $mail_hma_id=$empfaenger;
            $mail_hau_id=$hauid;
            $text="\nThis task was dropped again in Pool by Teamlead. No further action required.\n";
            $mail_info='Task revoked';
            $mailtag='ume_ping';
            include('segment_mail_senden.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            }

        ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////
        }

    $sql='DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid =' . $task_id . ' AND uau_ma_status=2';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }


// Zurueck zur Liste

header('Location: team_uebersicht.php');
exit;
?>