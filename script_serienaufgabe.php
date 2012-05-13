#!/usr/bin/php
<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################

require_once('konfiguration.php');

$Tagesdatum=date("Y-m-d");

$sql_script=
    'SELECT * FROM tracker INNER JOIN mitarbeiter ON utr_inhaber = hma_id WHERE utr_next_date = "' . $Tagesdatum . '"';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_script=mysql_query($sql_script, $verbindung))
    {
    fehler();
    }

while ($Daten=mysql_fetch_array($ergebnis_script))
    {
    $Startdatum_neue_aufgabe = $Daten['utr_next_date'];
    $Planende_neue_aufgabe=$Daten['utr_pende'];

    #So, nun pruefen wir noch, ob es sich ums Wochenende handelt und addieren ggf. Tage
    # Dazu wandeln wir zunächst das Datum in ein Feld um mit den einzelnen Komponenten des Datum
    # Vorher heben wir das originale Startdatum auf

    $Startdatum_array=getdate(strtotime($Startdatum_neue_aufgabe));

    # Nun nehmen wir davon den Wochentag und prüfen auf Wochenende (6=Samstag, 0= Sonntag)

    while ($Startdatum_array['wday'] == 0 OR $Startdatum_array['wday'] == 6)
        {
        # Anscheinend haben wir ein Wochenende erwischt, addiere einen Tag dazu und prüfe nochmal

        $Startdatum_neue_aufgabe = strftime("%Y-%m-%d", strtotime($Startdatum_neue_aufgabe . '+1 day'));
        $Startdatum_array=getdate(strtotime($Startdatum_neue_aufgabe));
        }

    # Nun das Enddatum berechnen

    if ($Daten['utr_Planende'] == 1) // sofort beginnen
        {
        $Planende_neue_aufgabe=$Startdatum_neue_aufgabe;
        }
    else
        {
        $Planende_neue_aufgabe=strftime("%Y-%m-%d",
            strtotime($Startdatum_neue_aufgabe . '+' . $Daten['utr_pende_wert'] . ' day'));
        }

    #So, nun pruefen wir noch, ob es sich ums Wochenende handelt und addieren ggf. Tage
    # Dazu wandeln wir zunächst das Datum in ein Feld um mit den einzelnen Komponenten des Datum
    $Planende_array=getdate(strtotime($Planende_neue_aufgabe));

    # Nun nehmen wir davon den Wochentag und prüfen auf Wochenende (6=Samstag, 0= Sonntag)

    while ($Planende_array['wday'] == 0 OR $Planende_array['wday'] == 6)
        {
        # Anscheinend haben wir ein Wochenende erwischt, addiere einen Tag dazu und prüfe nochmal

        $Planende_neue_aufgabe = strftime("%Y-%m-%d", strtotime($Planende_neue_aufgabe . '+1 day'));
        $Planende_array=getdate(strtotime($Planende_neue_aufgabe));
        }

    $Daten['utr_next_date']=$Startdatum_neue_aufgabe;
    $Daten['utr_pende']=$Planende_neue_aufgabe;

    # Aha, eine zur Einstellung fällige Aufgabe gefunden
    # Dann man los

    switch ($Daten['utr_zuordnung'])
        {
        case 1: // Pool ablegen
            $sql='INSERT INTO aufgaben (' .
                'hau_titel, ' .
                'hau_beschreibung, ' .
                'hau_anlage, ' .
                'hau_inhaber, ' .
                'hau_prio, ' .
                'hau_pende, ' .
                'hau_zeitstempel, ' .
                'hau_aktiv, ' .
                'hau_terminaendern, ' .
                'hau_teamleiter, ' .
                'hau_datumstyp, ' .
                'hau_hprid, ' .
                'hau_typ, ' .
                'hau_tl_status, ' .
                'hau_ticketnr) ' .
                'VALUES ( ' .
                '"' . mysql_real_escape_string($Daten['utr_titel']) . '", ' .
                '"' . mysql_real_escape_string($Daten['utr_beschreibung']) . '", ' .
                'NOW(), ' .
                '"' . $Daten['utr_inhaber'] . '", ' .
                '"' . $Daten['utr_prio'] . '", ' .
                '"' . $Daten['utr_pende'] . '", ' .
                'NOW(), ' .
                '"1", ' .
                '"0", ' .
                '"0", ' .
                '"' . $Daten['utr_datumstyp'] . '", ' .
                '"' . $Daten['utr_sid'] . '", ' .
                '"' . $Daten['utr_typ'] . '", ' .
                '"0", ' .
                '"' . $Daten['utr_ticketnr'] . '")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $hau_id=mysql_insert_id();

            $sql='INSERT INTO aufgaben_zuordnung
                            (uaz_hauid, uaz_pg) ' .
                'VALUES ("' . $hau_id . '", "' . $Daten['utr_bereich'] . '" )';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $Daten['hma_login']
                . '", "Aufgabe automatisch erzeugt und im Pool abgelegt", NOW() )';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            # Now lets update the eventlog

            $sql_log='INSERT INTO eventlog (hel_area, hel_type, hel_referer, hel_text) ' .
                'VALUES ("Auto", "NEW", "' . $hau_id . '", "Aufgabe automatisch erzeugt und im Pool abgelegt für '
                . $Daten['hma_login'] . '.")';

            if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
                {
                fehler();
                }
                
                break;

        case 2: // selbst uebernehmen
            $sql='INSERT INTO aufgaben (' .
                'hau_titel, ' .
                'hau_beschreibung, ' .
                'hau_anlage, ' .
                'hau_inhaber, ' .
                'hau_prio, ' .
                'hau_pende, ' .
                'hau_zeitstempel, ' .
                'hau_aktiv, ' .
                'hau_terminaendern, ' .
                'hau_teamleiter, ' .
                'hau_datumstyp, ' .
                'hau_hprid, ' .
                'hau_typ, ' .
                'hau_tl_status, ' .
                'hau_ticketnr) ' .
                'VALUES ( ' .
                '"' . mysql_real_escape_string($Daten['utr_titel']) . '", ' .
                '"' . mysql_real_escape_string($Daten['utr_beschreibung']) . '", ' .
                'NOW(), ' .
                '"' . $Daten['utr_inhaber'] . '", ' .
                '"' . $Daten['utr_prio'] . '", ' .
                '"' . $Daten['utr_pende'] . '", ' .
                'NOW(), ' .
                '"1", ' .
                '"0", ' .
                '"999", ' .
                '"' . $Daten['utr_datumstyp'] . '", ' .
                '"' . $Daten['utr_sid'] . '", ' .
                '"' . $Daten['utr_typ'] . '", ' .
                '"1", ' .
                '"' . $Daten['utr_ticketnr'] . '")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $hau_id=mysql_insert_id();

            $sql='INSERT INTO aufgaben_zuordnung
                            (uaz_hauid, uaz_pg, uaz_pba) ' .
                'VALUES ("' . $hau_id . '", "' . $Daten['utr_bereich'] . '", "' . $Daten['utr_inhaber'] . '" )';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $sql_job='INSERT INTO aufgaben_mitarbeiter (' .
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
                '"' . $Daten['utr_inhaber'] . '", ' .
                '"' . $hau_id . '", ' .
                '"0", ' .
                '"99", ' .
                '"0", ' .
                '"' . $Daten['utr_pende'] . '", ' .
                'NOW(), ' .
                '"1")';

            if (!($ergebnis_job=mysql_query($sql_job, $verbindung)))
                {
                fehler();
                }

            $sql_komm='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $Daten['utr_inhaber']
                . '", "Aufgabe automatisch erzeugt und Ersteller zugewiesen: (' . $Daten['hma_login'] . ')", NOW() )';

            if (!($ergebnis_komm=mysql_query($sql_komm, $verbindung)))
                {
                fehler();
                }

            # Now lets update the eventlog

            $sql_log='INSERT INTO eventlog (hel_area, hel_type, hel_referer, hel_text) ' .
                'VALUES ("Auto", "NEW", "' . $hau_id . '", "Aufgabe automatisch erzeugt durch ' . $Daten['hma_login']
                . ' und dem Bearbeiter zugewiesen.")';

            if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
                {
                fehler();
                }
        
 
            break;
        } // SWITCH

    # OK, die Aufgabe ist eingestellt - nun muss die nächste Aktulaisierung im Tracker eingetragen werden
    # Es ändern sich nur Start- und Enddatum

    # Berechnung des neuen Startdatums auf Basis des alten Startdatums
    # Dazu rechnen wir das Intervall hinzu
    # Dazu beachten, welche Wahl getroffen wurde

    $xIntervalltyp=$Daten['utr_intervalltyp'];
    $xStarttag=$Daten['utr_next_date'];

    switch ($xIntervalltyp)
        {
        case 0: break;

        case 1:
            $xIntervallwert=$Daten['utr_intervallwert'];
            break;

        case 2:
            $xIntervallwert=$Daten['utr_intervallwert'];
            break;

        case 3:
            $xIntervallwert=1;
            $xIntervalltag=$Daten['utr_intervalltag'];
            break;

        case 4:
            $xIntervallwert=$Daten['utr_intervallwert'];
            $fDatum=explode("-", $Daten['utr_intervalltag']);
            $xIntervallmonat=$fDatum[1];
            $xIntervalltag=$fDatum[0];
            break;
        }

    include('seg_startdatum_berechnen.php');

    $Startdatum_neue_aufgabe=$xStarttag;

    $Daten['xPlanende_eingabe']=$Daten['utr_pende_wert'];
    $Daten['xPlanende']=$Daten['utr_planende'];
    include('seg_enddatum_berechnen.php');

    #Jetzt prüfen wir noch, ob die Aufgabe erneut eingestellt werden soll

    if ($Daten['utr_wiederholung'] == 2)
        {
        $Daten['utr_wiederholungwert']=$Daten['utr_wiederholungwert'] - 1;

        if ($Daten['utr_wiederholungwert'] <= 0)
            {
            $sql_del='DELETE FROM tracker  WHERE utr_id = ' . $Daten['utr_id'];

            if (!$ergebnis_del=mysql_query($sql_del, $verbindung))
                {
                fehler();
                }

            # Aufgabe wurde gelöscht, schicke dem Inhaber eine Nachricht

            $sql_news=
                'INSERT INTO news (
        una_id, 
        una_zeitstempel, 
        una_initiator, 
        una_empfaenger, 
        una_hauid, 
        una_info, 
        una_gelesen, 
        una_geloescht, 
        una_typ
        ) '
                .

            'VALUES (
        NULL,
        NOW(),
        "38",  
        "' . $Daten['utr_inhaber']
                    . '",    
        "1",
        "Die Serienaufgabe wurde zum letztenmal ausgeführt und nun gelöscht.",
        "0",
        "0",
        "0"
        )';

            if (!($ergebnis_news=mysql_query($sql_news, $verbindung)))
                {
                fehler();
                }
            }
        else
            {
            $sql_up=
                'UPDATE tracker SET utr_wiederholungwert = ' . $Daten['utr_wiederholungwert'] . ', utr_next_date = "'
                . $Startdatum_neue_aufgabe . '", utr_pende = "' . $Planende_neue_aufgabe . '" WHERE utr_id = '
                . $Daten['utr_id'];

            if (!$ergebnis_up=mysql_query($sql_up, $verbindung))
                {
                fehler();
                }
            }
        }
    else
        {

        $sql_up='UPDATE tracker SET utr_next_date = "' . $Startdatum_neue_aufgabe . '", utr_pende = "'
            . $Planende_neue_aufgabe . '" WHERE utr_id = ' . $Daten['utr_id'];

        if (!$ergebnis_up=mysql_query($sql_up, $verbindung))
            {
            fehler();
            }
        }
    } // WHILE


?>