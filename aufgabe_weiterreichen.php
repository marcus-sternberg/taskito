<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');

$Daten=array();
$hau_id=$_REQUEST['auf_id'];

foreach ($_POST as $varname => $value)
    {
    $Daten[$varname]=$value;
    }

if (!isset($Daten['auf_gruppe']))
    {
    $Daten['auf_gruppe']=0;
    }

if (!isset($Daten['auf_mitarbeiter']))
    {
    $Daten['auf_mitarbeiter']=0;
    }

if ($Daten['auf_gruppe'] == 0 AND $Daten['auf_mitarbeiter'] == 0)
    {

    include('segment_kopf_einfach.php');

    echo '<br><br>Für die Aufgabenweiterleitung muss entweder ein Mitarbeiter oder eine Gruppe ausgewählt sein.';

    echo '<form action="aufgabe_ansehen.php?hau_id=' . $hau_id . '" method="post">';

    echo '&nbsp;&nbsp;<br><br><input type="submit" name="fehler" value="OK" class="formularbutton" />';

    echo '</form>';

    exit;
    }

if ($Daten['auf_mitarbeiter'] == $_SESSION['hma_id'] AND $Daten['auf_typ'] == '2')
    {

    include('segment_kopf_einfach.php');

    echo '<br><br>Eine Delegation an sich selbst ist nicht möglich.';

    echo '<form action="aufgabe_ansehen.php?hau_id=' . $hau_id . '" method="post">';

    echo '&nbsp;&nbsp;<br><br><input type="submit" name="fehler" value="OK" class="formularbutton" />';

    echo '</form>';

    exit;
    }
    
if ($Daten['auf_gruppe'] != 0 AND $Daten['auf_typ'] == '2')
    {

    include('segment_kopf_einfach.php');

    echo '<br><br>An eine Gruppe kann nur weitergeleitet werden. Eine Delegation ist nur an Einzelpersonen möglich.';

    echo '<form action="aufgabe_ansehen.php?hau_id=' . $hau_id . '" method="post">';

    echo '&nbsp;&nbsp;<br><br><input type="submit" name="fehler" value="OK" class="formularbutton" />';

    echo '</form>';

    exit;
    }

# Stelle sicher, daß der Delegierende die Aufgabe nicht schon mal delegiert hat

$sql='SELECT uaz_sba FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $hau_id . ' AND uaz_pba = ' . $_SESSION['hma_id']
    . ' AND uaz_sba <>0';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis) > 0) // Ja, es gibt ihn schon als Bearbeiter in der Aufgabe
    {
    include('segment_kopf_einfach.php');

    echo '<br><br>Sie haben die Aufgabe schon einmal delegiert.';

    echo '<form action="aufgabe_ansehen.php?hau_id=' . $hau_id . '" method="post">';

    echo '&nbsp;&nbsp;<br><br><input type="submit" name="fehler" value="OK" class="formularbutton" />';

    echo '</form>';

    exit;
    }


# Pruefe, ob derjenige, an den delegiert / weitergereicht werden soll, die Aufgabe schon hat

$sql='SELECT uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $hau_id . ' AND uau_hmaid = '
    . $Daten['auf_mitarbeiter'];

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis) > 0) // Ja, es gibt ihn schon als Bearbeiter in der Aufgabe
    {
    include('segment_kopf_einfach.php');

    echo '<br><br>Der ausgewählte Bearbeiter ist bereits der Aufgabe zugeordnet.';

    echo '<form action="aufgabe_ansehen.php?hau_id=' . $hau_id . '" method="post">';

    echo '&nbsp;&nbsp;<br><br><input type="submit" name="fehler" value="OK" class="formularbutton" />';

    echo '</form>';

    exit;
    }

if ($Daten['auf_gruppe'] != '0' AND $Daten['auf_typ'] == '2')
    {

    include('segment_kopf_einfach.php');

    echo '<br><br>An eine Gruppe kann nur weitergeleitet werden. Eine Delegation ist nur an Einzelpersonen möglich.';

    echo '<form action="aufgabe_ansehen.php?hau_id=' . $hau_id . '" method="post">';

    echo '&nbsp;&nbsp;<br><br><input type="submit" name="fehler" value="OK" class="formularbutton" />';

    echo '</form>';

    exit;
    }


# Bestimme Gruppe und Login-Namen des Kollegen, an den die Aufgabe gehen soll

$sql='SELECT * FROM mitarbeiter WHERE hma_id = ' . $Daten['auf_mitarbeiter'];

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    $neuer_hma_level = $zeile['hma_level'];
    $neuer_hma_login=$zeile['hma_login'];
    $neuer_hma_id=$zeile['hma_id'];
    }

if ($Daten['auf_typ'] == 1)             // Weiterleitung von Aufgaben, keine Delegation
    {
    if ($Daten['auf_mitarbeiter'] != 0) // Es wurde ein Bearbeiter angegeben (hat Vorrang vor Gruppen)
        {

        # I. Pruefe, ob der aktuelle Besucher sich die Aufgabe selbst zuweist

        if ($neuer_hma_id != $_SESSION['hma_id']) // es liegt keine Selbstzuweisung vor
            {

            # II. Pruefe, ob der neue Kollege bereits der Aufgabe zugeordnet ist

            $sql_check='SELECT uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $hau_id . ' AND uau_hmaid = '
                . $Daten['auf_mitarbeiter'];

            if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
                {
                fehler();
                }

            if (mysql_num_rows($ergebnis_check) == 0) // Nein, ist er noch nicht, also zuordnen
                {

                # III. Pruefe, ob der aktuelle Verweiser auch alter Bearbeiter war

                $sql_alt='SELECT uau_id FROM aufgaben_mitarbeiter WHERE uau_hmaid = ' . $_SESSION['hma_id']
                    . ' AND uau_hauid = ' . $hau_id;

                if (!$ergebnis_alt=mysql_query($sql_alt, $verbindung))
                    {
                    fehler();
                    }

                if (mysql_num_rows($ergebnis_alt) > 0) // Ja, der Verweisende hatte auch die Aufgabe
                    {

                    # Lösche die Aufgabe des Verweisers

                    $sql='DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $hau_id . ' AND uau_hmaid = '
                        . $_SESSION['hma_id'];

                    if (!($ergebnis=mysql_query($sql, $verbindung)))
                        {
                        fehler();
                        }

                    # IV. Pruefe nun noch, ob er primärer oder sekundärer Bearbeiter war

                    $sql_klasse='SELECT uaz_id FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $hau_id . ' AND uaz_sba = '
                        . $_SESSION['hma_id'];

                    if (!$ergebnis_klasse=mysql_query($sql_klasse, $verbindung))
                        {
                        fehler();
                        }

                    if (mysql_num_rows($ergebnis_klasse) > 0) // Ja, er war Sekundärbearbeiter
                        {
                        $sql_update='UPDATE aufgaben_zuordnung SET uaz_sba = "' . $Daten['auf_mitarbeiter']
                            . '", uaz_sg = "' . $neuer_hma_level . '" WHERE uaz_hauid = ' . $hau_id . ' AND uaz_sba='
                            . $_SESSION['hma_id'];
                        }
                    else // Nein, er ist primärer Bearbeiter
                        {
                        $sql_update='UPDATE aufgaben_zuordnung SET uaz_pba = "' . $Daten['auf_mitarbeiter']
                            . '", uaz_pg = "' . $neuer_hma_level . '" WHERE uaz_hauid = ' . $hau_id . ' AND uaz_pba='
                            . $_SESSION['hma_id'];
                        } # Ende IV
                    }
                else      // Nein, der Verweisende hatte keine Aufgabe
                    {

                    # Pruefe, ob es für die Aufgabe einen Eintrag gibt mit leerem PBA

                    $sql_pg='SELECT uaz_id FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $hau_id . ' AND uaz_pba = 0';

                    if (!($ergebnis_pg=mysql_query($sql_pg, $verbindung)))
                        {
                        fehler();
                        }

                    if (mysql_num_rows($ergebnis_pg) == 0) // Nein, es gibt keine verwaiste Gruppe, also neu anlegen
                        {
                        $sql_update='INSERT INTO aufgaben_zuordnung (uaz_pba,uaz_pg, uaz_hauid) VALUES ("'
                            . $Daten['auf_mitarbeiter'] . '", "' . $neuer_hma_level . '", "' . $hau_id . '")';
                        }
                    else
                        {
                        $sql_update='UPDATE aufgaben_zuordnung SET uaz_pba = "' . $Daten['auf_mitarbeiter']
                            . '", uaz_pg = "' . $neuer_hma_level . '" WHERE uaz_hauid = "' . $hau_id
                            . '" AND uaz_pba = 0';
                        }
                    } # Ende III.

                if (!($ergebnis_update=mysql_query($sql_update, $verbindung)))
                    {
                    fehler();
                    }

                # Lege neue Aufgabe für den neuen Kollegen an

                $sql='INSERT INTO aufgaben_mitarbeiter (' .
                    'uau_hmaid, ' .
                    'uau_hauid, ' .
                    'uau_status, ' .
                    'uau_prio, ' .
                    'uau_stopp, ' .
                    'uau_ma_status) ' .
                    'VALUES ( ' .
                    '"' . $Daten['auf_mitarbeiter'] . '", ' .
                    '"' . $hau_id . '", ' .
                    '"0", ' .
                    '"99", ' .
                    '"0", ' .
                    '"0")';

                if (!($ergebnis=mysql_query($sql, $verbindung)))
                    {
                    fehler();
                    }

                # Pruefe, ob es sich um eine Aufgabe im MR handelt

                $sql_mr=
                    'SELECT hau_pende, hpr_id, hau_nonofficetime FROM projekte LEFT JOIN aufgaben ON hau_hprid = hpr_id WHERE hau_id = '
                    . $hau_id;

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
                     "' . $Daten['auf_mitarbeiter'] . '",
                     "1")';

                            if (!($ergebnis_kal=mysql_query($sql_kal, $verbindung)))
                                {
                                fehler();
                                }
                            }
                        }
                    }

                $sql='UPDATE aufgaben SET hau_tl_status = 1, hau_teamleiter = 999 WHERE hau_id = ' . $hau_id;

                if (!($ergebnis=mysql_query($sql, $verbindung)))
                    {
                    fehler();
                    }

                # Aufgabehistorie aktualisieren

                $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                    'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                    . '", "Die Aufgabe wurde von ' . $_SESSION['hma_login'] . ' an ' . $neuer_hma_login
                    . ' weitergereicht", NOW() )';

                if (!($ergebnis=mysql_query($sql, $verbindung)))
                    {
                    fehler();
                    }

                //////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

                $hauid=$hau_id;
                $initiator=$_SESSION['hma_id'];
                $empfaenger=$Daten['auf_mitarbeiter'];
                $info='Diese Aufgabe wurde von ' . $_SESSION['hma_login'] . ' an Dich weitergereicht.';
                include('segment_news.php');

                ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

                ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

                $mailtag='ume_aufgabestatus';
                $mail_hma_id=$empfaenger;
                $mail_hau_id=$hau_id;
                $text="\nDu hast eine neue Aufgabe:\n";
                $mail_info='Neue Aufgabe';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];
   

                include('segment_mail_senden.php');

                ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
                } # Ende II.  Der neue Kollege hat die Aufgabe schon, also nichts zu tun
            }     # Ende I.
        else      # Er weist sich den Job selbst zu
            {
            # V. Pruefe, ob der Selbstzuweiser bereits die Aufgabe hat

            $sql_check='SELECT uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $hau_id . ' AND uau_hmaid = '
                . $Daten['auf_mitarbeiter'];

            if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
                {
                fehler();
                }

            if (mysql_num_rows($ergebnis_check) == 0) // Nein, hat er noch nicht, also zuordnen
                {
                $sql='INSERT INTO aufgaben_mitarbeiter (' .
                    'uau_hmaid, ' .
                    'uau_hauid, ' .
                    'uau_status, ' .
                    'uau_prio, ' .
                    'uau_stopp, ' .
                    'uau_ma_status) ' .
                    'VALUES ( ' .
                    '"' . $Daten['auf_mitarbeiter'] . '", ' .
                    '"' . $hau_id . '", ' .
                    '"0", ' .
                    '"99", ' .
                    '"0", ' .
                    '"0")';

                if (!($ergebnis=mysql_query($sql, $verbindung)))
                    {
                    fehler();
                    }

                # Pruefe, ob es sich um eine Aufgabe im MR handelt

                $sql_mr=
                    'SELECT hau_pende, hpr_id, hau_nonofficetime FROM projekte LEFT JOIN aufgaben ON hau_hprid = hpr_id WHERE hau_id = '
                    . $hau_id;

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
                     "' . $Daten['auf_mitarbeiter'] . '",
                     "1")';

                            if (!($ergebnis_kal=mysql_query($sql_kal, $verbindung)))
                                {
                                fehler();
                                }
                            }
                        }
                    }

                # Pruefe, ob es für die Aufgabe einen Eintrag gibt mit leerem PBA

                $sql_pg='SELECT uaz_id FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $hau_id . ' AND uaz_pba = 0';

                if (!($ergebnis_pg=mysql_query($sql_pg, $verbindung)))
                    {
                    fehler();
                    }

                if (mysql_num_rows($ergebnis_pg) == 0) // Nein, es gibt keine verwaiste Gruppe, also neu anlegen
                    {
                    $sql='INSERT INTO aufgaben_zuordnung (uaz_pba,uaz_pg, uaz_hauid) VALUES ("'
                        . $Daten['auf_mitarbeiter'] . '", "' . $neuer_hma_level . '", "' . $hau_id . '")';
                    }
                else
                    {
                    $sql='UPDATE aufgaben_zuordnung SET uaz_pba = "' . $Daten['auf_mitarbeiter'] . '", uaz_pg = "'
                        . $neuer_hma_level . '" WHERE uaz_hauid = "' . $hau_id . '" AND uaz_pba = 0';
                    }

                if (!($ergebnis=mysql_query($sql, $verbindung)))
                    {
                    fehler();
                    }
                } # Ende V.
            }
        }
    else
        { // Es wurde kein Mitarbeiter ausgewählt, also Gruppe

        $sql_uaz='UPDATE aufgaben_zuordnung SET 
            uaz_pg = ' . $Daten['auf_gruppe'] . ', 
            uaz_pba = 0,
            uaz_sg = 0, 
            uaz_sba = 0
            WHERE uaz_hauid = ' . $hau_id;

        if (!($ergebnis_uaz=mysql_query($sql_uaz, $verbindung)))
            {
            fehler();
            }
            
        # Ermittle Gruppenname fürs Log
        
        $sql_name='SELECT ule_name FROM level WHERE ule_id = '.$Daten['auf_gruppe'];
        
        if (!($ergebnis_name=mysql_query($sql_name, $verbindung)))
        {
        fehler();
        }

        while ($zeile_name=mysql_fetch_array($ergebnis_name))
        {
        $gruppen_name = $zeile_name['ule_name'];
        }
                
      # Aufgabehistorie aktualisieren

    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
        . '", "Die Aufgabe wurde von ' . $_SESSION['hma_login'] . ' an ' . $gruppen_name . ' delegiert.", NOW() )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
        
        # Informiere betroffene Gruppe
        
        ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
        $level_gruppe = $Daten['auf_gruppe'];

        $sql_check = 'SELECT hau_titel FROM aufgaben WHERE hau_id = '.$hau_id;

    if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
        {
        fehler();
        }

    while ($zeile_check=mysql_fetch_array($ergebnis_check))
        {
           $text = "Die Aufgabe [".$zeile_check['hau_titel']."] wurde durch ".$_SESSION['hma_vorname']." ".$_SESSION['hma_name']." an Deine Gruppe weitergereicht.";
        }        
      
            $mail_hau_id = $hau_id;
            $mailtag='ume_gruppe';
            $mail_info='Neue Gruppenaufgabe';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];
            include('segment_mail_senden.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
        
        
        
        
        }
    }
else // Ende Erkennung Weiterleitung, jetzt kommt Delegation
    {


    # Check, ob der Delegeierende bereits Bearbeiter der Aufgabe ist

    $sql='SELECT uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $hau_id . ' AND uau_hmaid = '
        . $_SESSION['hma_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    if (mysql_num_rows($ergebnis) > 0) // Ja, er hat die Aufgabe schon, setze die Flagge auf DELEGATION
        {
        $sql_update='UPDATE aufgaben_mitarbeiter SET uau_stopp = 3 WHERE uau_hauid = ' . $hau_id . ' AND uau_hmaid = '
            . $_SESSION['hma_id'];

        if (!($ergebnis_update=mysql_query($sql_update, $verbindung)))
            {
            fehler();
            }
        }
    else // Nein, er hat die Aufgabe noch nicht - anlegen
        {

        $sql='INSERT INTO aufgaben_mitarbeiter (' .
            'uau_hmaid, ' .
            'uau_hauid, ' .
            'uau_status, ' .
            'uau_prio, ' .
            'uau_stopp, ' .
            'uau_ma_status) ' .
            'VALUES ( ' .
            '"' . $_SESSION['hma_id'] . '", ' .
            '"' . $hau_id . '", ' .
            '"0", ' .
            '"99", ' .
            '"3", ' .
            '"1")';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        # Aufgabehistorie aktualisieren

        $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
            'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
            . '", "Die Aufgabe wurde von ' . $_SESSION['hma_login'] . ' übernommen.", NOW() )';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        # Pruefe, ob es sich um eine Aufgabe im MR handelt

        $sql_mr=
            'SELECT hau_pende, hpr_id, hau_nonofficetime FROM projekte LEFT JOIN aufgaben ON hau_hprid = hpr_id WHERE hau_id = '
            . $hau_id;

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
                     "' . $Daten['auf_mitarbeiter'] . '",
                     "1")';

                    if (!($ergebnis_kal=mysql_query($sql_kal, $verbindung)))
                        {
                        fehler();
                        }
                    }
                }
            }

        # Pruefe, ob es für die Aufgabe einen Eintrag gibt mit leerem PBA

        $sql_pg='SELECT uaz_id FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $hau_id . ' AND uaz_pba = 0';

        if (!($ergebnis_pg=mysql_query($sql_pg, $verbindung)))
            {
            fehler();
            }

        if (mysql_num_rows($ergebnis_pg) == 0) // Nein, es gibt keine verwaiste Gruppe, also neu anlegen
            {
            $sql='INSERT INTO aufgaben_zuordnung (uaz_pba,uaz_pg, uaz_hauid) VALUES ("' . $_SESSION['hma_id'] . '", "'
                . $_SESSION['hma_level'] . '", "' . $hau_id . '")';
            }
        else
            {
            $sql='UPDATE aufgaben_zuordnung SET uaz_pba = "' . $_SESSION['hma_id'] . '", uaz_pg = "'
                . $_SESSION['hma_level'] . '" WHERE uaz_hauid = "' . $hau_id . '" AND uaz_pba = 0';
            }

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }
        }

    # Lege für den neuen Bearbeiter eine Aufgabe an

    $sql='INSERT INTO aufgaben_mitarbeiter (' .
        'uau_hmaid, ' .
        'uau_hauid, ' .
        'uau_status, ' .
        'uau_prio, ' .
        'uau_stopp, ' .
        'uau_ma_status) ' .
        'VALUES ( ' .
        '"' . $Daten['auf_mitarbeiter'] . '", ' .
        '"' . $hau_id . '", ' .
        '"0", ' .
        '"99", ' .
        '"0", ' .
        '"0")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    # Aufgabehistorie aktualisieren

    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
        'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
        . '", "Die Aufgabe wurde von ' . $_SESSION['hma_login'] . ' an ' . $neuer_hma_login . ' delegiert.", NOW() )';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    # Pruefe, ob es sich um eine Aufgabe im MR handelt

    $sql_mr=
        'SELECT hau_pende, hpr_id, hau_nonofficetime FROM projekte LEFT JOIN aufgaben ON hau_hprid = hpr_id WHERE hau_id = '
        . $hau_id;

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
                     "' . $Daten['auf_mitarbeiter'] . '",
                     "1")';

                if (!($ergebnis_kal=mysql_query($sql_kal, $verbindung)))
                    {
                    fehler();
                    }
                }
            }
        }
    # Ergänze die Zuordnung um SBA

    $sql='UPDATE aufgaben_zuordnung SET uaz_sba = "' . $Daten['auf_mitarbeiter'] . '", uaz_sg = "' . $neuer_hma_level
        . '" WHERE uaz_hauid = "' . $hau_id . '" AND uaz_pba = ' . $_SESSION['hma_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    //////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

    $hauid=$hau_id;
    $initiator=$_SESSION['hma_id'];
    $empfaenger=$Daten['auf_mitarbeiter'];
    $info='Diese Aufgabe wurde von ' . $_SESSION['hma_login'] . ' an Dich delegiert.';
    include('segment_news.php');

    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

    ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

    $mailtag='ume_aufgabestatus';
    $mail_hma_id=$empfaenger;
    $mail_hau_id=$hau_id;
    $text="\nDu hast eine neue Aufgabe:\n";
    $mail_info='Neue Aufgabe';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];

    include('segment_mail_senden.php');

    ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

    }

# Aufgabe wurde zugewiesen durch jemanden, setze den Status auf 1 für Zuweisung

$sql_tl_update=
    'UPDATE aufgaben SET hau_teamleiter = ' . $_SESSION['hma_id'] . ', hau_tl_status = 1 WHERE hau_id = ' . $hau_id;

if (!($ergebnis_tl_update=mysql_query($sql_tl_update, $verbindung)))
    {
    fehler();
    }

if ($Daten['auf_hinweis'] != '')
    {
    $sql='INSERT INTO log (' .
        'ulo_aufgabe, ' .
        'ulo_text, ' .
        'ulo_ma, ' .
        'ulo_datum) ' .
        'VALUES ( ' .
        '"' . $hau_id . '", ' .
        '"' . mysql_real_escape_string($Daten['auf_hinweis']) . '", ' .
        '"' . $_SESSION['hma_id'] . '", ' .
        '"' . date("Y-m-d H:i") . '")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }


// Zurueck zur Liste

header('Location: aufgabe_ansehen.php?hau_id=' . $hau_id);
exit;
?>