<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$hau_id=$_REQUEST['hau_id'];

$sql_ende='UPDATE aufgaben SET hau_abschlussdatum = NOW(), hau_abschluss = 1 WHERE hau_id = "' . $hau_id . '"';

if (!($ergebnis_ende=mysql_query($sql_ende, $verbindung)))
    {
    fehler();
    }

$sql='UPDATE aufgaben_mitarbeiter SET uau_status = "1" WHERE uau_hauid = "' . $hau_id . '"';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$sql='UPDATE log SET ulo_ping = "0" WHERE ulo_aufgabe = ' . $hau_id;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
    'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
    . '", "Die Aufgabe wurde für alle Beteiligten geschlossen.", NOW() )';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }


///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

$sql_check='SELECT hau_inhaber FROM aufgaben WHERE hau_id = ' . $hau_id;

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

    $hauid=$hau_id;
    $initiator=$_SESSION['hma_id'];
    $empfaenger=$inhaber;
    $info='Die Aufgabe wurde abgeschlossen.';

    include('segment_news.php');

    $mail_hma_id=$inhaber;
    $mail_hau_id=$hau_id;
    $text="\nDie Aufgabe wurde abgeschlossen:\n";
    $mail_info='Bearbeitung abgeschlossen';
    $mailtag='ume_aufgabestatus';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];

    include('segment_mail_senden.php');
    }

///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////


// Zurueck zur Liste

header('Location: aufgabe_ansehen.php?hau_id=' . $hau_id);
exit;
?>