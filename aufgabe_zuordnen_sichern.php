<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$Daten=array();

foreach ($_POST as $varname => $value)
    {
    $Daten[$varname]=$value;
    }

if ((!isset($Daten['uau_gruppe']) OR $Daten['uau_gruppe'] == 0) AND !isset($Daten['uau_ma_id']) AND $Daten['deleg']
    != "Reject")
    {

    include('segment_kopf.php');

    echo '<br><br>Bitte mindestens einen Bearbeiter oder eine Gruppe zuweisen!';

    echo '<form action="aufgabe_zuordnen.php?hau_id=' . $Daten['hau_id'] . '" method="post">';

    echo '&nbsp;&nbsp;<input type="submit" name="fehler" value="OK" class="formularbutton" />';

    echo '</form>';

    exit;
    }

if ($Daten['hau_pende'] != '')
    {
    if ($Daten['hau_pende'] == 'open')
        {
        $Daten['hau_pende']='9999-01-01';
        }

    list($anzahl_fehler, $fehlermeldung['hau_pende'])=datum_check($Daten['hau_pende'], 'hau_pende', 0);

    if ($anzahl_fehler > 0)
        {

        include('segment_kopf.php');

        echo '<br><br>Bitte ein korrektes Datum eingeben. Sie haben eingegeben == ' . $Daten['hau_pende'] . ' ==<br>';

        echo '<form action="aufgabe_zuordnen.php?hau_id=' . $Daten['hau_id'] . '" method="post">';

        echo '&nbsp;&nbsp;<input type="submit" name="fehler" value="OK" class="formularbutton" />';

        echo '</form>';

        exit;
        }
    }

if ($Daten['hau_pende'] != '9999-01-01')
    {
    $Daten['hau_pende']=datum_wandeln_euus($Daten['hau_pende']);
    }

$sql='SELECT * FROM aufgaben WHERE hau_id = ' . $Daten['hau_id'];

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    $sql_update = 'UPDATE aufgaben SET hau_dauer = "' . $Daten['hau_dauer'] . '" WHERE hau_id = ' . $Daten['hau_id'];

    if (!($ergebnis_update=mysql_query($sql_update, $verbindung)))
        {
        fehler();
        }

    $sql_update='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $Daten['hau_id'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
        . '", "Aufgabendauer wurde verändert auf ' . $Daten['hau_dauer'] . '.", NOW() )';

    if (!($ergebnis_update=mysql_query($sql_update, $verbindung)))
        {
        fehler();
        }

    if ($Daten['hau_pende'] != $zeile['hau_pende'])
        {
        $sql_update='UPDATE aufgaben SET hau_pende = "' . $Daten['hau_pende'] . '" WHERE hau_id = ' . $Daten['hau_id'];

        if (!($ergebnis_update=mysql_query($sql_update, $verbindung)))
            {
            fehler();
            }

        $sql_update='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
            'VALUES ("' . $Daten['hau_id'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
            . '", "Aufgabendauer wurde verändert auf' . datum_anzeigen($Daten['hau_pende']) . '.", NOW() )';

        if (!($ergebnis_update=mysql_query($sql_update, $verbindung)))
            {
            fehler();
            }

        ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

        $sql_check='SELECT hau_inhaber FROM aufgaben WHERE hau_id = ' . $Daten['hau_id'];

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

            $hauid=$Daten['hau_id'];
            $initiator=$_SESSION['hma_id'];
            $empfaenger=$inhaber;
            $info='Aufgabendauer wurde verändert auf ' . datum_anzeigen($Daten['hau_pende']) . ' '
                . $_SESSION['hma_login'];

            include('segment_news.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

            $mailtag='ume_aufgabestatus';
            $mail_hma_id=$inhaber;
            $mail_hau_id=$Daten['hau_id'];
            $text="\nAufgabendauer wurde verändert auf " . datum_anzeigen($Daten['hau_pende']) . "."
                . $_SESSION['hma_login'] . "\n";
            $mail_info='Aufgabendauer verändert';
            $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];

            include('segment_mail_senden.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
            }
        }
    }

if ($Daten['deleg'] == "Reject")
    {
    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $Daten['hau_id'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
        . '", "Aufgabe abgelehnt", NOW() )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    if ($Daten['uko_kommentar'] != '')
        {
        $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
            'VALUES ("' . $Daten['hau_id'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login'] . '", "'
            . $Daten['uko_kommentar'] . '", NOW() )';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }
        }

    $sql='UPDATE aufgaben SET hau_tl_status = "2", hau_teamleiter = "' . $_SESSION['hma_id'] . '" WHERE hau_id = "'
        . $Daten['hau_id'] . '"';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }


    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

    $sql_check='SELECT hau_inhaber FROM aufgaben WHERE hau_id = ' . $Daten['hau_id'];

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

        $hauid=$Daten['hau_id'];
        $initiator=$_SESSION['hma_id'];
        $empfaenger=$inhaber;
        $info='Aufgabe wurde abgelehnt ' . $_SESSION['hma_login'] . '.<br>Grund: ' . $Daten['uko_kommentar'];

        include('segment_news.php');


        ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

        $mailtag='ume_aufgabestatus';
        $mail_hma_id=$inhaber;
        $mail_hau_id=$Daten['hau_id'];
        $text="\nAufgabe wurde abgelehnt durch " . $_SESSION['hma_login'] . ".<br>Grund: " . $Daten['uko_kommentar']
            . "\n";
        $mail_info='Aufgabe abgelehnt';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];
        
        include('segment_mail_senden.php');

        ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

        }

    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////


    // Zurueck zur Liste

    header('Location: team_uebersicht.php');
    exit;
    }
else
    {

    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $Daten['hau_id'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
        . '", "Aufgabe wurde Dir zugewiesen.", NOW() )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $sql='UPDATE aufgaben SET hau_tl_status = "1", hau_teamleiter = "' . $_SESSION['hma_id'] . '" WHERE hau_id = "'
        . $Daten['hau_id'] . '"';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $MA=array();

    if ($Daten['uau_gruppe'] != 0)
        {


        # Pruefe, ob es für die Aufgabe einen Eintrag gibt mit leerem PBA

        $sql_pg='SELECT uaz_id FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $Daten['hau_id'] . ' AND uaz_pba = 0';

        if (!($ergebnis_pg=mysql_query($sql_pg, $verbindung)))
            {
            fehler();
            }

        if (mysql_num_rows($ergebnis_pg) == 0) // Nein, es gibt keine verwaiste Gruppe, also neu anlegen
            {
            $sql='INSERT INTO aufgaben_zuordnung (uaz_pg, uaz_hauid) VALUES ("' . $Daten['uau_gruppe'] . '", "'
                . $Daten['hau_id'] . '")';
            }
        else
            {
            $sql='UPDATE aufgaben_zuordnung SET uaz_pg = "' . $Daten['uau_gruppe'] . '" WHERE uaz_hauid = "'
                . $Daten['hau_id'] . '" AND uaz_pba = 0';
            }

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }
        }
    else if (isset($Daten['uau_ma_id']))
        {
        foreach ($Daten['uau_ma_id'] as $ma_id)
            {
            $MA[]=$ma_id;
            }

        $MA=array_unique($MA);

        foreach ($MA as $ma_id)
            {
            $sql = 'INSERT INTO aufgaben_mitarbeiter (' .
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
                '"' . $ma_id . '", ' .
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
                     "' . $ma_id . '",
                     "1")';

                        if (!($ergebnis_kal=mysql_query($sql_kal, $verbindung)))
                            {
                            fehler();
                            }
                        }
                    }
                }

            $sql_pg='SELECT hma_level FROM mitarbeiter WHERE hma_id =' . $ma_id;

            if (!($ergebnis_pg=mysql_query($sql_pg, $verbindung)))
                {
                fehler();
                }

            while ($zeile_pg=mysql_fetch_array($ergebnis_pg))
                {
                $pg=$zeile_pg['hma_level'];
                }

            # Pruefe, ob es für die Aufgabe einen Eintrag gibt mit leerem PBA

            $sql_pg='SELECT uaz_id FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $Daten['hau_id'] . ' AND uaz_pba = 0';

            if (!($ergebnis_pg=mysql_query($sql_pg, $verbindung)))
                {
                fehler();
                }

            if (mysql_num_rows($ergebnis_pg) == 0) // Nein, es gibt keine verwaiste Gruppe, also neu anlegen
                {
                $sql='INSERT INTO aufgaben_zuordnung (uaz_pba,uaz_pg, uaz_hauid) VALUES ("' . $ma_id . '", "' . $pg
                    . '", "' . $Daten['hau_id'] . '")';
                }
            else
                {
                $sql='UPDATE aufgaben_zuordnung SET uaz_pba = "' . $ma_id . '", uaz_pg = "' . $pg
                    . '" WHERE uaz_hauid = "' . $Daten['hau_id'] . '" AND uaz_pba = 0';
                }

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            if (isset($Daten['hau_tl_info']))
                {
                $sql='UPDATE aufgaben SET hau_tl_info = "' . $Daten['hau_tl_info'] . '" WHERE hau_id = "'
                    . $Daten['hau_id'] . '"';

                if (!($ergebnis=mysql_query($sql, $verbindung)))
                    {
                    fehler();
                    }
                }

            ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

            if ($ma_id != $_SESSION['hma_id'])
                {

                $hauid=$Daten['hau_id'];
                $initiator=$_SESSION['hma_id'];
                $empfaenger=$ma_id;
                $info='Diese Aufgabe wurde Dir zugewiesen.';

                include('segment_news.php');


                ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

                ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

                $mailtag='ume_aufgabestatus';
                $mail_hma_id=$ma_id;
                $mail_hau_id=$Daten['hau_id'];
                $text="\nEs gibt eine neue Aufgabe:\n";
                $mail_info='Neue Aufgabe';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];

                include('segment_mail_senden.php');

                ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
                }
            }
        }
    // Zurueck zur Liste

    header('Location: team_uebersicht.php');
    exit;
    }
?>