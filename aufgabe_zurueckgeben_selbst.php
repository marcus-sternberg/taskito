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
else
    {
    $task_id=$_POST['hau_id'];
    }


# Liegt ein Kommentar vor?
if (isset($_POST['button']) AND $_POST['button'] == 'save')
    {

    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
        . '", "Aufgabe wurde zurückgegeben", NOW() )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login'] . '", "'
        . mysql_real_escape_string($_POST['uko_kommentar']) . '", NOW() )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    
    $sql_check = 'SELECT COUNT(uau_id) FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $task_id.' AND uau_hmaid = '.$_SESSION['hma_id'];
    
    if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
        {
        fehler();
        }    
    
    if(mysql_num_rows($ergebnis_check)==1)
    {   
    
    $sql=
        'UPDATE aufgaben SET hau_terminaendern = "0", hau_tl_status="0", hau_teamleiter="0" WHERE hau_id = "' . $task_id
        . '"';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }
    
    # Lösche aus der Zuordnungstabelle raus

    # Pruefe die Zuordnungen

    # 1. Ermittle, ob es ein Primärbearbeiter ist:

    $sql_pba='SELECT * FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $task_id . ' AND uaz_pba = ' . $_SESSION['hma_id'];

    if (!($ergebnis_pba=mysql_query($sql_pba, $verbindung)))
        {
        fehler();
        }

    if (mysql_num_rows($ergebnis_pba) > 0) // Wir haben einen PBA
        {
        while ($zeile_pba=mysql_fetch_array($ergebnis_pba))
            {
            if ($zeile_pba['uaz_sba'] != 0)
                {
                # 1. Lösche die Aufgabe des SBA's

                $sql='DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid =' . $task_id . ' AND uau_hmaid = '
                    . $zeile_pba['uaz_sba'];

                if (!($ergebnis=mysql_query($sql, $verbindung)))
                    {
                    fehler();
                    }
                }

            // Pruefe, ob es noch weitere Bearbeiter ausser Dir gibt

            $sql_check='SELECT uau_id FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $task_id;

            if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
                {
                fehler();
                }

            if (mysql_num_rows($ergebnis_check) > 1)
                {
                // Es gibt noch andere, daher lösche Deinen Eintrag

                $sql_uaz='DELETE FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $task_id . ' AND uaz_pba = '
                    . $_SESSION['hma_id'];
                }
            else
                {
                // Du bist allein, werfe die Aufgabe zurück in die Primärgruppe

                $sql_uaz='UPDATE aufgaben_zuordnung SET 
           uaz_sg = 0, 
           uaz_sba = 0,
           uaz_pba = 0
           WHERE uaz_hauid = ' . $task_id
                    . ' AND uaz_pba = ' . $_SESSION['hma_id'];
                }

            if (!($ergebnis_uaz=mysql_query($sql_uaz, $verbindung)))
                {
                fehler();
                }

            $sql='DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid =' . $task_id . ' AND uau_hmaid = '
                . $_SESSION['hma_id'];

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
            }
        }
    else
        {

        # Wir haben wohl einen SBA

        # Lies den PBA aus:

        $sql_pba=
            'SELECT * FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $task_id . ' AND uaz_sba = ' . $_SESSION['hma_id'];

        if (!($ergebnis_pba=mysql_query($sql_pba, $verbindung)))
            {
            fehler();
            }

        while ($zeile_pba=mysql_fetch_array($ergebnis_pba))
            {
            ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

            $hauid = $task_id;
            $initiator=$_SESSION['hma_id'];
            $empfaenger=$zeile_pba['uaz_pba'];
            $info='Die delegierte Aufgabe wurde zurückgegeben durch ' . $_SESSION['hma_login'] . '.';

            include('segment_news.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            $mail_hma_id=$empfaenger;
            $mail_hau_id=$task_id;
            $text="Die delegierte Aufgabe wurde zurückgegeben durch " . $_SESSION['hma_login'] . ".<br>";
            $mail_info='Aufgabe zurückgegeben';
            $mailtag='ume_aufgabestatus';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];

            include('segment_mail_senden.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            $sql='DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid =' . $task_id . ' AND uau_hmaid = '
                . $_SESSION['hma_id'];

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            # Ändere Zuordnung

            $sql_uaz='UPDATE aufgaben_zuordnung SET 
                    uaz_sg = 0, 
                    uaz_sba = 0
                    WHERE uaz_hauid = ' . $task_id
                . ' AND uaz_sba = ' . $_SESSION['hma_id'];

            if (!($ergebnis_uaz=mysql_query($sql_uaz, $verbindung)))
                {
                fehler();
                }

            # Setze Flag zurück

            $sql_flag='UPDATE aufgaben_mitarbeiter SET uau_stopp = 0
                     WHERE uau_hauid = ' . $task_id . ' AND uau_hmaid = ' . $zeile_pba['uaz_pba'];

            if (!($ergebnis_flag=mysql_query($sql_flag, $verbindung)))
                {
                fehler();
                }
            }
        }


    // Zurueck zur Liste

    header('Location: schreibtisch_meine_aufgaben.php');
    exit;
    }
require_once('segment_kopf.php');

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Please enter a comment: <br><br>';

echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST">';

echo '<textarea cols="40" rows="5" name="uko_kommentar"></textarea><br><br>';

echo '<input type="submit" name="button" value="save" class="formularbutton" />';

echo '<input type="hidden" name="hau_id" value="' . $task_id . '">';

echo '</form>'
?>