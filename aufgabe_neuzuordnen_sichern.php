<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$Daten=array();
$alte_zuordnung=array();

foreach ($_POST as $varname => $value)
    {
    $Daten[$varname]=$value;
    }

if ((!isset($Daten['uau_gruppe']) OR $Daten['uau_gruppe'] == 0) AND !isset($Daten['uau_ma_id']) AND $Daten['deleg']
    != "Reject")
    {

    include('segment_kopf.php');

    echo '<br><br>Bitte mindestens einen Mitarbeiter oder eine Gruppe auswählen! Wiederholen Sie die Zuordnung.';

    echo '<form action="aufgabe_zuordnen.php?hau_id=' . $Daten['hau_id'] . '" method="post">';

    echo '&nbsp;&nbsp;<input type="submit" name="fehler" value="OK" class="formularbutton" />';

    echo '</form>';

    exit;
    }

$sql_alt='SELECT uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $Daten['hau_id'];

if (!($ergebnis_alt=mysql_query($sql_alt, $verbindung)))
    {
    fehler();
    }

while ($zeile_alt=mysql_fetch_array($ergebnis_alt))
    {
    $alte_zuordnung[]=$zeile_alt['uau_hmaid'];
    }

foreach ($Daten['uau_ma_id'] AS $hma_id)
    {
    if (!in_array($hma_id, $alte_zuordnung))
        {

        # Ermittle den alten Mitarbeiter, der der Aufgabe zugeordnet war

        $sql_alt='SELECT hma_login FROM mitarbeiter WHERE hma_id = ' . $hma_id;

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_alt=mysql_query($sql_alt, $verbindung))
            {
            fehler();
            }

        while ($zeile_alt=mysql_fetch_array($ergebnis_alt))
            {
            $alter_ma=$zeile_alt['hma_login'];
            }

        # Der Eintrag ist neu
        # Schreibe zuerst einen Kommentar

        $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
            'VALUES ("' . $Daten['hau_id'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
            . '", "Mitarbeiter hat die Aufgabe zugeordnet an ' . $alter_ma . '.", NOW() )';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        # Aktualisiere den Teamleiterstatus

        $sql='UPDATE aufgaben SET hau_tl_status = "1", hau_teamleiter = "' . $_SESSION['hma_id'] . '" WHERE hau_id = "'
            . $Daten['hau_id'] . '"';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        # Lege zuletzt die neue Aufgabe an

        $sql='INSERT INTO aufgaben_mitarbeiter (' .
            'uau_id, ' .
            'uau_hmaid, ' .
            'uau_hauid, ' .
            'uau_status, ' .
            'uau_prio, ' .
            'uau_stopp, ' .
            'uau_tende, ' .
            'uau_zeitstempel, ' .
            'uau_ma_status) ' .
            'VALUES ( ' .
            'NULL, ' .
            '"' . $hma_id . '", ' .
            '"' . $Daten['hau_id'] . '", ' .
            '"0", ' .
            '"99", ' .
            '"0", ' .
            '"9999-01-01", ' .
            'NOW(), ' .
            '"0")';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        # Pruefe, ob es sich um eine Aufgabe im MR handelt

        $sql_mr=
            'SELECT hau_pende, hpr_id, hau_nonofficetime FROM projekte LEFT JOIN aufgaben ON hau_hprid = hpr_id WHERE hau_id = '
            . $Daten['hau_id'];

        if (!($ergebnis_mr=mysql_query($sql_mr, $verbindung)))
            {
            fehler();
            }

        while ($zeile_mr=mysql_fetch_array($ergebnis_mr))
            {
            if ($zeile_mr['hpr_id'] == 5) // Es ist ein MR
                {
                $einsatzdauer=array();

                // Stelle fest, ob es ein Nachteinsatz ist
                if ($zeile_mr['hau_nonofficetime'] == 1)
                    {
                    $einsatzdauer[]=$zeile_mr['hau_pende'];
                    $einsatzdauer[]=date("Y-m-d", strtotime("-1 day", strtotime($zeile_mr['hau_pende'])));
                    }
                else
                    {
                    $einsatzdauer[]=$zeile_mr['hau_pende'];
                    }
                var_dump($einsatzdauer);

                foreach ($einsatzdauer AS $einsatztag)
                    {
                    $sql_kal = 'INSERT INTO kalender 
                    (hka_tag,
                    hka_hmaid,
                    hka_release) 
                    VALUES
                    ("' . $einsatztag . '", 
                     "' . $hma_id . '",
                     "1")';

                    if (!($ergebnis_kal=mysql_query($sql_kal, $verbindung)))
                        {
                        fehler();
                        }
                    }
                }
            }

        $sql_pg='SELECT hma_level FROM mitarbeiter WHERE hma_id =' . $hma_id;

        if (!($ergebnis_pg=mysql_query($sql_pg, $verbindung)))
            {
            fehler();
            }

        while ($zeile_pg=mysql_fetch_array($ergebnis_pg))
            {
            $pg=$zeile_pg['hma_level'];
            }

        # Pruefe, ob es für die Aufgabe bereits einen Eintrag gibt mit PBA

        $sql_pba=
            'SELECT uaz_id FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $Daten['hau_id'] . ' AND uaz_pba = ' . $hma_id;

        if (!($ergebnis_pba=mysql_query($sql_pba, $verbindung)))
            {
            fehler();
            }

        if (mysql_num_rows($ergebnis_pba) == 0) // Bee gibts noch nicht, also was tun
            {

            # Pruefe, ob es für die Aufgabe einen Eintrag gibt mit leerem PBA

            $sql_pg='SELECT uaz_id FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $Daten['hau_id'] . ' AND uaz_pba = 0';

            if (!($ergebnis_pg=mysql_query($sql_pg, $verbindung)))
                {
                fehler();
                }

            if (mysql_num_rows($ergebnis_pg) == 0) // Nein, es gibt keine verwaiste Gruppe, also neu anlegen
                {
                $sql='INSERT INTO aufgaben_zuordnung (uaz_pba,uaz_pg, uaz_hauid) VALUES ("' . $hma_id . '", "' . $pg
                    . '", "' . $Daten['hau_id'] . '")';
                }
            else
                {
                $sql='UPDATE aufgaben_zuordnung SET uaz_pba = "' . $hma_id . '", uaz_pg = "' . $pg
                    . '" WHERE uaz_hauid = "' . $Daten['hau_id'] . '" AND uaz_pba = 0';
                }

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
            }

        ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

        if ($hma_id != $_SESSION['hma_id'])
            {

            $hauid=$Daten['hau_id'];
            $initiator=$_SESSION['hma_id'];
            $empfaenger=$hma_id;
            $info='Diese Aufgabe wurde Dir zugewiesen.';

            include('segment_news.php');


            ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            $mailtag='ume_aufgabestatus';
            $mail_hma_id=$hma_id;
            $mail_hau_id=$Daten['hau_id'];
            $text="\nDu hast eine neue Aufgabe:\n";
            $mail_info='Neue Aufgabe';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];


            include('segment_mail_senden.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            }
        }
    }

foreach ($alte_zuordnung AS $hma_id_alt)
    {
    if (!in_array($hma_id_alt, $Daten['uau_ma_id']))
        {
        ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

        if ($hma_id_alt != $_SESSION['hma_id'])
            {

            $hauid=$Daten['hau_id'];
            $initiator=$_SESSION['hma_id'];
            $empfaenger=$hma_id_alt;
            $info='Die Aufgabe wurde zurückgenommen.';

            include('segment_news.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            $mailtag='ume_aufgabestatus';
            $mail_hma_id=$hma_id_alt;
            $mail_hau_id=$Daten['hau_id'];
            $text="\nDie Aufgabe wurde zurückgenommen:\n";
            $mail_info='Aufgabe zurückgenommen';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];


            include('segment_mail_senden.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            }

        $sql='DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $Daten['hau_id'] . ' AND uau_hmaid = '
            . $hma_id_alt;

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        $sql='DELETE FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $Daten['hau_id'] . ' AND uaz_pba = ' . $hma_id_alt;

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        # Ermittle den alten Mitarbeiter, der der Aufgabe zugeordnet war

        $sql_alt='SELECT hma_login FROM mitarbeiter WHERE hma_id = ' . $hma_id_alt;

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_alt=mysql_query($sql_alt, $verbindung))
            {
            fehler();
            }

        while ($zeile_alt=mysql_fetch_array($ergebnis_alt))
            {
            $alter_ma=$zeile_alt['hma_login'];
            }

        $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
            'VALUES ("' . $Daten['hau_id'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
            . '", "Die Aufgabe wurde zurückgenommen von ' . $alter_ma . '.", NOW() )';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }
        }
    }


// Zurueck zur Liste

header('Location: team_uebersicht.php');
exit;
?>