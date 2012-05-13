<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (isset($_GET['toggle']))
    {
    $toggle=$_GET['toggle'];
    }
else
    {
    $toggle=1;
    }

if (isset($_REQUEST['hau_id']))
    {
    $task_id=$_REQUEST['hau_id'];
    }

if ($toggle == 2)
    {

    $sql_tende='SELECT uau_tende, uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $task_id
        . ' ORDER BY uau_tende DESC LIMIT 1';

    if (!($ergebnis_tende=mysql_query($sql_tende, $verbindung)))
        {
        fehler();
        }

    while ($zeile_tende=mysql_fetch_array($ergebnis_tende))
        {

        $sql =
            'UPDATE aufgaben SET hau_terminaendern = 0, hau_pende = "' . $zeile_tende['uau_tende'] . '" WHERE hau_id = '
            . $task_id;

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        $sql='UPDATE aufgaben_mitarbeiter SET uau_tende = "' . $zeile_tende['uau_tende'] . '" WHERE uau_hauid = '
            . $task_id;

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
            'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
            . '", "Die Änderung des Enddatums wurde bestätigt und angepasst auf ' . datum_anzeigen($zeile_tende['uau_tende'])
            . '", NOW() )';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }


        ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

        if ($zeile_tende['uau_hmaid'] != $_SESSION['hma_id'])
            {
            $hauid=$task_id;
            $initiator=$_SESSION['hma_id'];
            $empfaenger=$zeile_tende['uau_hmaid'];
            $info='Änderung des Enddatums der Aufgabe wurde bestätigt.';

            include('segment_news.php');


            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            $mailtag='ume_termin';
            $mail_hma_id=$zeile_tende['uau_hmaid'];
            $mail_hau_id=$task_id;
            $text="\nÄnderung des Enddatums wurde bestätigt:\n";
            $mail_info='Änderung des Enddatums bestätigt';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];


            include('segment_mail_senden.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            }

        ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

        }

    // Zurueck zur Liste

    header('Location: schreibtisch_meine_auftraege.php');
    exit;
    }
else
    {

    $fehlermeldung=array();
    $anzahl_fehler=0;

    foreach ($_POST as $varname => $value)
        {

        $Daten[$varname]=$value;
        }

    if (empty($Daten['uau_tende']))
        {
        $anzahl_fehler++;
        $fehlermeldung['uau_tende']='You need to enter a Due Date!';
        }
    else
        {
        list($anzahl_fehler, $fehlermeldung['uau_tende'])=datum_check($Daten['uau_tende'], 'uau_tende', $anzahl_fehler);
        }

    if ($anzahl_fehler > 0)
        {

        include('segment_kopf.php');

        echo $fehlermeldung['uau_tende'];

        echo '<br><br>Das eingegebene Datum ist ungültig - bitte wiederholen Sie die Eingabe.';

        echo '<form action="aufgabe_ansehen.php?hau_id=' . $task_id . '" method="post">';

        echo '&nbsp;&nbsp;<input type="submit" name="fehler" value="OK" class="formularbutton" />';

        echo '</form>';

        exit;
        }

    $Daten['uau_tende']=pruefe_datum($Daten['uau_tende']);


    // Speichere den Datensatz

    $sql='UPDATE aufgaben_mitarbeiter SET uau_tende = "' . $Daten['uau_tende'] . '" WHERE uau_hauid = '
        . $Daten['hau_id'] . ' AND uau_hmaid=' . $_SESSION['hma_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $sql='UPDATE aufgaben SET hau_dauer = "' . $Daten['hau_dauer'] . '" WHERE hau_id = ' . $Daten['hau_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $sql_update='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
        . '", "Die Dauer der Bearbeitung wurde durch den Bearbeiter auf ' . $Daten['hau_dauer'] . ' geändert.", NOW() )';

    if (!($ergebnis_update=mysql_query($sql_update, $verbindung)))
        {
        fehler();
        }
    $sql='SELECT hau_inhaber FROM aufgaben WHERE hau_id = ' . $Daten['hau_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

        if ($zeile['hau_inhaber'] != $_SESSION['hma_id'])
            {
            $hauid=$task_id;
            $initiator=$_SESSION['hma_id'];
            $empfaenger=$zeile['hau_inhaber'];
            $info='Das geplante Enddatum wurde geändert durch den Bearbeiter - bitte bestätigen.';

            include('segment_news.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            $mailtag='ume_termin';
            $mail_hma_id=$zeile['hau_inhaber'];
            $mail_hau_id=$task_id;
            $text="\nDas geplante Enddatum wurde geändert durch den Bearbeiter - bitte bestätigen.\n\r\n";
            $mail_info='Achtung: Enddatum Aufgabe wurde geändert';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];

            

            include('segment_mail_senden.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            }
        }
    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

    $sql_check='SELECT hau_pende, hau_datumstyp FROM aufgaben WHERE hau_id = ' . $Daten['hau_id'];

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_check=mysql_query($sql_check, $verbindung))
        {
        fehler();
        }

    while ($zeile_check=mysql_fetch_array($ergebnis_check))
        {

        // echo $zeile_check['hau_pende'].'#'.$Daten['uau_tende'].'#'.$zeile_check['hau_datumstyp']; exit;

        if ($zeile_check['hau_pende'] < $Daten['uau_tende'] AND $zeile_check['hau_datumstyp'] != 1)
            {

            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $Daten['hau_id'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                . '", "Fälligkeitsdatum wurder verlängert auf ' . datum_anzeigen($Daten['uau_tende']) . '", NOW() )';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $sql='UPDATE aufgaben SET hau_terminaendern = 1 WHERE hau_id = ' . $Daten['hau_id'];

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
            }
        else if (($zeile_check['hau_pende'] < $Daten['uau_tende'] AND $zeile_check['hau_datumstyp']
            == 1) OR ($zeile_check['hau_pende'] > $Daten['uau_tende']))
            {
            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $Daten['hau_id'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                . '", "Fälligkeit der Aufgabe wurde geändert auf ' . datum_anzeigen($Daten['uau_tende']) . '", NOW() )';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $sql_termin='SELECT * FROM aufgaben_mitarbeiter INNER JOIN aufgaben ON hau_id = uau_hauid WHERE hau_id = '
                . $Daten['hau_id'] . ' AND uau_hmaid !=' . $_SESSION['hma_id'] . ' AND uau_tende>"'
                . $zeile_check['hau_pende'] . '"';

            if (!($ergebnis_termin=mysql_query($sql_termin, $verbindung)))
                {
                fehler();
                }

            if (mysql_num_rows($ergebnis_termin) == 0)
                {

                $sql='UPDATE aufgaben SET hau_terminaendern = 0 WHERE hau_id = ' . $Daten['hau_id'];

                if (!($ergebnis=mysql_query($sql, $verbindung)))
                    {
                    fehler();
                    }
                }
            }
        }

    // Zurueck zur Liste

    header('Location: schreibtisch_meine_aufgaben.php');
    exit;
    } // ende toggle
?>