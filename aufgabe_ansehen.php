<?php
###### Editnotes ####
#$LastChangedDate: 2011-09-22 09:56:20 +0200 (Do, 22 Sep 2011) $
#$Author: msternberg $ 
#####################
################## Binde Module ein #######################################

require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

################### Lege Variablen fest #####################################
                                                                              
$session_frei=0;
$bin_da=0;
$task_id=$_GET['hau_id'];
$hma_kennung=array();
$bisheriger_aufwand=0;
$freigabe_status=0;
$freigabe_noetig=0;
$aufgabentyp = 0;
$change_status=0;
$hau_abschluss = 0;
 
### Lese blacklist für den Mailversand ###

$sql_blacklist= 'SELECT hbl_mail, hbl_aktion FROM blacklist WHERE hbl_aktiv = 1';         

if (!$ergebnis_blacklist=mysql_query($sql_blacklist, $verbindung))
    {
    fehler();
    }

while ($zeile_blacklist=mysql_fetch_array($ergebnis_blacklist))
    {
      $eMail_blacklist[]=$zeile_blacklist['hbl_mail'];
    }
  


###################### Prüfe, ob ein direkter Link zur Übernahme der Aufgabe genutzt wurde #####################

if (isset($_GET['bw']))
    {

    // Pruefe, ob ueberhaupt ein Bearbeiter vorliegt

    $sql_check='SELECT uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $task_id;

    if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
        {
        fehler();
        }

    if (mysql_num_rows($ergebnis_check) == 0)
        { // Es gibt keinen Bearbeiter

        $sql_aufgabe='SELECT hau_pende FROM aufgaben WHERE hau_id = ' . $task_id;

        if (!($ergebnis_aufgabe=mysql_query($sql_aufgabe, $verbindung)))
            {
            fehler();
            }

        while ($zeile_aufgabe=mysql_fetch_array($ergebnis_aufgabe))
            {
            $Planende=$zeile_aufgabe['hau_pende'];
            }

        $sql_tl='UPDATE aufgaben SET hau_tl_status = "1", hau_teamleiter = "999" WHERE hau_id = ' . $task_id;

        if (!($ergebnis_tl=mysql_query($sql_tl, $verbindung)))
            {
            fehler();
            }

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
            '"' . $_SESSION['hma_id'] . '", ' .
            '"' . $task_id . '", ' .
            '"0", ' .
            '"99", ' .
            '"0", ' .
            '"' . $Planende . '", ' .
            'NOW(), ' .
            '"1")';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        $sql=
            'UPDATE aufgaben_zuordnung SET uaz_pba = "' . $_SESSION['hma_id'] . '", uaz_pg = "' . $_SESSION['hma_level']
            . '" WHERE uaz_hauid = ' . $task_id;

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
            'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
            . '", "Aufgabe übernommen", NOW() )';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }


        # Pruefe, ob es sich um eine Aufgabe im MR handelt

        $sql_mr=
            'SELECT hau_pende, hpr_id, hau_nonofficetime FROM projekte LEFT JOIN aufgaben ON hau_hprid = hpr_id WHERE hau_id = '
            . $task_id;

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
                     "' . $_SESSION['hma_id'] . '",
                     "1")';

                    if (!($ergebnis_kal=mysql_query($sql_kal, $verbindung)))
                        {
                        fehler();
                        }
                    }
                }
            }
        }
    else
        { // Es gibt einen Bearbeiter

        $sql_check='SELECT uau_hmaid, uau_ma_status FROM aufgaben_mitarbeiter 
                WHERE uau_hmaid = ' . $_SESSION['hma_id'] . ' AND uau_hauid = ' . $task_id;

        if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
            {
            fehler();
            }

        if (mysql_num_rows($ergebnis_check) == 0)
            { // Der aktuelle Besucher ist noch nicht beteiligt

            $sql='UPDATE aufgaben_mitarbeiter SET uau_hmaid = ' . $_SESSION['hma_id']
                . ', uau_status = 0, uau_ma_status = 1 ' .
                'WHERE uau_hauid = ' . $task_id . ' AND uau_hmaid = ' . $_GET['bw'];


            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis=mysql_query($sql, $verbindung))
                {
                fehler();
                }


            # Prüfe, ob der alte Bearbeiter primärer oder sekundärer Bearbeiter war

            $sql_check=
                'SELECT uaz_id FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $task_id . ' AND uaz_sba = ' . $_GET['bw'];

            if (!$ergebnis_check=mysql_query($sql_check, $verbindung))
                {
                fehler();
                }

            if (mysql_num_rows($ergebnis_check) > 0) // Ja, er ist Sekundärbearbeiter
                {
                $sql='UPDATE aufgaben_zuordnung SET uaz_sba = "' . $_SESSION['hma_id'] . '", uaz_sg = "'
                    . $_SESSION['hma_level'] . '" WHERE uaz_hauid = ' . $task_id . ' AND uaz_sba=' . $_GET['bw'];
                }
            else // Nein, er ist primärer Bearbeiter
                {
                $sql='UPDATE aufgaben_zuordnung SET uaz_pba = "' . $_SESSION['hma_id'] . '", uaz_pg = "'
                    . $_SESSION['hma_level'] . '" WHERE uaz_hauid = ' . $task_id . ' AND uaz_pba=' . $_GET['bw'];
                }

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }


            //////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

            if ($_GET['bw'] != $_SESSION['hma_id'])
                {

                $sql_check='SELECT hma_login FROM mitarbeiter WHERE hma_id = ' . $_SESSION['hma_id'];

                if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
                    {
                    fehler();
                    }

                while ($zeile_check=mysql_fetch_array($ergebnis_check))
                    {
                    $neuerbearbeiter=$zeile_check['hma_login'];
                    }

                $sql_check='SELECT hma_login FROM mitarbeiter WHERE hma_id = ' . $_GET['bw'];

                if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
                    {
                    fehler();
                    }

                while ($zeile_check=mysql_fetch_array($ergebnis_check))
                    {
                    $alterbearbeiter=$zeile_check['hma_login'];
                    }

                $hauid=$task_id;
                $initiator=$_SESSION['hma_id'];
                $empfaenger=$_GET['bw'];
                $info='Aufgabe wurde übernommen von ' . $neuerbearbeiter . '.';

                include('segment_news.php');
                }

            ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                . '", "Die Aufgabe für ' . $alterbearbeiter . ' wurde von ' . $neuerbearbeiter
                . ' übernommen.", NOW() )';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
            }
        else
            { // er steht schon da, Aufgabe wird nur noch in Arbeit gesetzt
            while ($zeile_check=mysql_fetch_array($ergebnis_check))
                {
                if ($zeile_check['uau_ma_status'] == 0)
                    {

                    $sql_status=
                        'UPDATE aufgaben_mitarbeiter SET uau_hmaid = ' . $_SESSION['hma_id'] . ', uau_ma_status = 1 ' .
                        'WHERE uau_hauid = ' . $task_id . ' AND uau_hmaid = ' . $_GET['bw'];


                    // Frage Datenbank nach Suchbegriff
                    if (!$ergebnis_status=mysql_query($sql_status, $verbindung))
                        {
                        fehler();
                        }

                    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                        'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                        . '", "Aufgabe wurde übernommen.", NOW() )';

                    if (!($ergebnis=mysql_query($sql, $verbindung)))
                        {
                        fehler();
                        }
                    }
                }
            }
        } // Ende Es gibt keinen Bearbeiter
    }
 
########################### Zaehle vorhandene Kommentare fuer die Aufgabe ############

$sql_count='SELECT COUNT(*) AS anzahl FROM log 
                INNER JOIN mitarbeiter ON ulo_ma = hma_id 
                WHERE ulo_aufgabe = ' . $task_id;

if (!($ergebnis_count=mysql_query($sql_count, $verbindung)))
    {
    fehler();
    }

$Menge=mysql_fetch_array($ergebnis_count);

### Prüfe, ob Aufgabe wichtig oder kritisch in der Priorität ist

$sql_prio = 'SELECT hau_prio FROM aufgaben WHERE hau_id = ' . $task_id;

if (!($ergebnis_prio=mysql_query($sql_prio, $verbindung)))
    {
    fehler();
    }
    
while ($zeile_prio=mysql_fetch_array($ergebnis_prio))
        {
        $task_prio = $zeile_prio['hau_prio'];
        }
        

########################## Baue Titelblock ##############################


// ***************************************************************
// *** Ajax-Aufruf zur happenings-Meldung					   ***
// *** Parameter fuer task_gethappenings: debug (0/1), hau_id, ***
// *** 1 fuer setzen des intervalls.						   ***
// *** wichtig: der auszublendende content muss in einem       ***
// *** Element liegen mit der id 'is24_content'				   ***
// ***************************************************************

echo '<script>pagecalltime="'.date("Y-m-d H:i:s").'";
			  hau_id='.$task_id.';
              window.onload=task_gethappenings(0,'.$task_id.',0);
              setInterval ("task_gethappenings(0,hau_id)", 30000 );	
      </script>';
echo '<div style="position:absolute;right:10px;top:2%;width:20%;padding:3px;border-style:solid;border-color:#aa0000;border-width:1px;display:none;" id="taskhappenings"></div>';

echo '<br><table class="matrix" cellpadding = "5">';

echo '<thead class="is24">';

echo '<tr class="is24">';

if (in_array($task_prio, array(2,3)))
{
  echo '<th style = "background-color:#ff000c">Hohe Priorität</th>';    
} else 
{
  echo '<th class = "is24">Standard Priorität</th>'; 
  
}



echo '</th>';

echo '<th class="is24">';

echo ' | ';

echo '</th>';

echo '<th class="is24">';

echo '<a href="#comment"> ' . $Menge['anzahl'] . ' Kommentar(e) </a>';

echo '</th>';

echo '<th class="is24">';

echo ' | ';

echo '</th>';

echo '<th class="is24">';

echo '<a href="aufgabenlog.php?task_id=' . $task_id . '" target="_blank">Aufgabenhistorie</a>';

echo '</th>';

# Checke, ob es einen neuen Kommentar gibt

$sql_check='SELECT uls_komm_an FROM log_status LEFT JOIN log ON ulo_id = uls_uloid ' .
    'WHERE uls_komm_an = ' . $_SESSION['hma_id'] . ' AND ulo_aufgabe = ' . $task_id;

if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis_check) > 0)
    {
    echo '<th class="is24">';

    echo ' | ';

    echo '</th>';

    echo '<th class="is24" bgcolor="#C1E2A5">';

    echo 'Neuer Kommentar';

    echo '</th>';
    }

# Checke, ob es einen PING gibt

$sql_check='SELECT uls_ping_an FROM log_status LEFT JOIN log ON ulo_id = uls_uloid ' .
    'WHERE uls_ping_an = ' . $_SESSION['hma_id'] . ' AND ulo_aufgabe = ' . $task_id;

if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis_check) > 0)
    {
    echo '<th class="is24">';

    echo ' | ';

    echo '</th>';

    echo '<th class="is24" bgcolor="#FFF8B3">';

    echo 'Support gewünscht';

    echo '</th>';
    }


# Checke, ob die Aufgabe eine Freigabe braucht

$sql_check='SELECT * FROM rollen_status WHERE urs_hauid = '.$task_id.' ORDER BY urs_zeit DESC limit 1';

if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis_check) > 0)
    {
    $freigabe_noetig=1;

    while ($zeile_check=mysql_fetch_array($ergebnis_check))
        {
        $freigabe_status = $zeile_check['urs_freigabe_ok'];

        if ($freigabe_status == 0)
            {
            echo '<th class="is24">';

            echo ' | ';

            echo '</th>';

            echo '<th class="is24" bgcolor="#FFBFA0">';

            echo 'Keine Freigabe';

            echo '</th>';
            }
        else
            {
            echo '<th class="is24">';

            echo ' | ';

            echo '</th>';

            echo '<th class="is24" bgcolor="#C1E2A5">';

            echo 'Change freigegeben';

            echo '</th>';
            }
        }
    }
    
# Checke, ob die Aufgabe beendet ist

$sql_check='SELECT * FROM `aufgaben` WHERE hau_abschluss = 1 AND hau_id = '.$task_id;

if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis_check) == 0)
    {

            echo '<th class="is24">';

            echo ' | ';

            echo '</th>';

            echo '<th class="is24">';

            echo 'Aufgabe offen';

            echo '</th>';
            }
        else
            {
            echo '<th class="is24">';

            echo ' | ';

            echo '</th>';

            echo '<th style = "background-color:#E3E3E3">';

            echo 'Aufgabe abgeschlossen';

            echo '</th>';
            }

echo '</tr>';

echo '</thead>';

echo '</table>';

echo '<br>';

########################## Deaktiviere Kommentar-Flagge ##############################

# Setze einen Eintrag im News-Center

$sql_check='SELECT DISTINCT(uls_komm_von), hau_inhaber FROM aufgaben ' .
    'LEFT JOIN log ON hau_id = ulo_aufgabe ' .
    'LEFT JOIN log_status ON uls_uloid = ulo_id ' .
    'WHERE uls_komm_an = ' . $_SESSION['hma_id'] . ' AND ulo_aufgabe = ' . $task_id;

if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
    {
    fehler();
    }

while ($zeile_check=mysql_fetch_array($ergebnis_check))
    {
    $Kommentator = $zeile_check['uls_komm_von'];
    $Inhaber=$zeile_check['hau_inhaber'];

    # Sende News nur, wenn ich nicht selbst Inhaber der Aufgabe oder der Kommentator bin

    if ($Kommentator != $_SESSION['hma_id'] AND $Kommentator != $Inhaber)
        {
        $hauid=$task_id;
        $initiator=$_SESSION['hma_id'];
        $empfaenger=$Kommentator;
        $info='Dein Kommentar zur Aufgabe wurde gelesen.';

        include('segment_news.php');

        # Mail senden

        $mail_hma_id=$Kommentator;
        $mail_hau_id=$task_id;
        $text="\nDein Kommentar zur Aufgabe wurde gelesen:\n";
        $mail_info='Kommentar gelesen';
        $mailtag='ume_kommentar_gelesen';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];


        include('segment_mail_senden.php');
        }
    }
# Loesche das Flag Neuer Kommentar

$sql='UPDATE log_status ' .
    'LEFT JOIN log ON ulo_id = uls_uloid ' .
    'SET uls_komm_an = "0", uls_komm_von = "0" ' .
    'WHERE ulo_aufgabe = ' . $task_id . ' AND uls_komm_an = ' . $_SESSION['hma_id'];

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

########################### Starte Layout Tabelle #######################
echo '<table id="is24_content" border=0><tr><td valign="top">';

include('segment_aufgabe_anzeigen.php');

// Zweite Spalte der Tabelle

echo '<td valign="top" rowspan="3">';

# Pruefe, ob es bereits Bearbeiter der Gruppe gibt - ansonsten werfe Gruppeneinordnung aus

$sql_check='SELECT uau_hauid FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $task_id;

if (!$ergebnis_check=mysql_query($sql_check, $verbindung))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis_check) != 0)
    { // Es gibt Bearbeiter

    $sql='SELECT * FROM aufgaben_mitarbeiter
        LEFT JOIN mitarbeiter ON uau_hmaid = hma_id 
        LEFT JOIN aufgaben ON hau_id = uau_hauid 
        LEFT JOIN level ON hma_level = ule_id  
        WHERE uau_hauid = '
        . $task_id . ' ORDER BY ule_kurz, hma_name';

    $es_gibt_bearbeiter=1;
    }
else
    {

    $sql='SELECT *
FROM aufgaben
LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid
LEFT JOIN mitarbeiter ON uau_hmaid = hma_id
LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id
LEFT JOIN level ON uaz_pg = ule_id 
WHERE hau_id =' . $task_id . '
ORDER BY ule_kurz, hma_name';

    $es_gibt_bearbeiter=0;
    }

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl_ma=mysql_num_rows($ergebnis);

// Tabellenkopf Bearbeiter

echo '<table id="is24_layout">';
echo '<tr><td>';

echo '<table id="is24_vertikal_az" width="450">';

echo '<caption class="is24">';

echo 'Aufgabenzuordnung';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24" colspan="2">&nbsp;</th>';

echo '<th class="is24">Gruppe</th>';

echo '<th class="is24">Bearbeiter</th>';

echo '<th class="is24" colspan="2">Status</th>';

echo '<th class="is24" nowrap>Aufwand [h]</th>';

echo '<th class="is24" nowrap>Fortschritt [%]</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody class="is24">';

// Inhalt Tabelle
// Pruefe, ob der Betrachter bereits die Aufgabe in der Queue hat

$bearbeiter_vorhanden=mysql_query('SELECT COUNT(*) FROM aufgaben_mitarbeiter ' .
    'LEFT JOIN mitarbeiter ON uau_hmaid = hma_id ' .
    'WHERE uau_hauid = ' . $task_id . ' AND uau_hmaid = ' . $_SESSION['hma_id']);

if (mysql_result($bearbeiter_vorhanden, 0) == 1)
    {
    $bin_da=1;
    }
else
    {
    $bin_da=0;
    }

if (mysql_num_rows($ergebnis) != 0)
    {
    while ($zeile=mysql_fetch_array($ergebnis))
        {
        // Pruefe, ob der aktuelle Benutzer eine Aufgabe uebernehmen darf vom Kollegen

        $hma_kennung[] = $zeile['hma_id'];

        echo '<tr>';

        if (($bin_da == 0 OR ($zeile['hma_id'] == $_SESSION['hma_id']
            && $zeile['uau_ma_status'] == 0)) AND $zeile['hau_abschluss'] == 0)
            {
            echo '<td class="is24"><a href="aufgabe_ansehen.php?bw=' . $zeile['hma_id'] . '&hau_id=' . $task_id
                . '"><img src="bilder/icon_uebernehmen.gif" border="0" alt="Aufgabe übernehmen" title="Aufgabe übernehmen"></a></td>';
            }
        else
            {
            echo '<td class="is24">&nbsp;</td>';
            }

        // Pruefe, ob Aufgabe beendet ist und aktueller Benutzer Aufgabe wieder oeffnen darf

        if ($zeile['hma_id'] == $_SESSION['hma_id'] AND $zeile['uau_status'] == 1)
            {
            echo '<td class="is24"><a href="aufgabe_reaktivieren_einzel.php?hau_id=' . $task_id . '&hma_id='
                . $_SESSION['hma_id']
                . '"><img src="bilder/icon_zuruecknehmen.gif" border=0 alt="Aufgabe wieder öffnen" title="Aufgabe wieder öffnen"></a></td>';
            }
        else if ($zeile['hma_id'] == $_SESSION['hma_id'] AND $zeile['uau_status'] == 0)
            {
            echo '<td class="is24"><a href="aufgabe_zurueckgeben_selbst.php?hau_id=' . $task_id
                . '"><img src="bilder/icon_abgelehnt.gif" border=0 alt="Aufgabe zurückgeben" title="Aufgabe zurückgeben"></a></td>';
            }
        else
            {
            echo '<td class="is24">&nbsp;</td>';
            }

        // Gruppe ausgeben
        echo '<td class="is24" align="center">' . $zeile['ule_kurz'] . '</td>';

        // Bearbeiter anzeigen
        echo '<td class="is24" align="center">' . $zeile['hma_login'] . '</td>';

        if ($es_gibt_bearbeiter == 0)
            {
            echo '<td class="is24" align="center">&nbsp;</td>';

            echo '<td class="is24" align="center">&nbsp;</td>';
            }
        else
            {

            // Status Aufgabe

            if ($zeile['uau_status'] == 0)
                {
                // Aufgabe aktiv?
                switch ($zeile['uau_stopp'])
                    {
                    case 0:
                        echo
                            '<td class="is24" align="center"><img src="bilder/icon_gruen.png" alt="Task Enabled" title="Task Enabled"></td>';

                        break;

                    case 1:
                        echo
                            '<td class="is24" align="center"><img src="bilder/icon_rot.png" alt="Stopped due to internal Problems" title="Stopped due to internal Problems"></td>';
                        break;

                    case 2:
                        echo
                            '<td class="is24" align="center"><img src="bilder/icon_gelb.png" alt="Waiting for CC" title="Waiting for CC"></td>';
                        break;

                    case 3:
                        echo
                            '<td class="text" align="center"><img src="bilder/icon_blau.png" alt="Task is delegated" title="Task is delegated"></td>';
                        break;
                    }

                if ($zeile['uau_ma_status'] == 0)
                    {
                    echo
                        '<td class="is24" align="center"><img src="bilder/icon_offen.gif" alt="Task Queued" title="Task Queued"></td>';
                    }
                else if ($zeile['uau_ma_status'] == 1)
                    {
                    echo
                        '<td class="is24" align="center"><img src="bilder/icon_akzeptiert.gif" alt="Task accepted" title="Task accepted"></td>';
                    }
                else
                    {
                    echo
                        '<td class="is24" align="center"><img src="bilder/icon_abgelehnt.gif" alt="Task Rejected" title="Task Rejected"></td>';
                    }
                }
            else
                {
                echo '<td class="is24" align="center">&nbsp;</td>';

                echo
                    '<td class="is24" align="center"><img src="bilder/icon_erledigt.gif" alt="Task closed" title="Task closed">';

                echo '</td>';
                }
            }
        // Aufwand

        $sql_menge='SELECT ulo_id,SUM(ulo_aufwand) as Menge FROM log ' .
            'WHERE ulo_aufgabe = ' . $task_id . ' AND ulo_ma = "' . $zeile['hma_id'] . '" ' .
            'GROUP BY ulo_aufgabe';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_menge=mysql_query($sql_menge, $verbindung))
            {
            fehler();
            }

        if (mysql_num_rows($ergebnis_menge) != 0)
            {
            while ($zeile_menge=mysql_fetch_array($ergebnis_menge))
                {
                echo '<td class="is24" align="center">' . round($zeile_menge['Menge'] / 60, "2") . '</td>';
                }
            }
        else
            {
            echo '<td class="is24" align="center">0</td>';
            }

        // Fertigstellungsgrad fuer eingeloggten Kollegen fuer Anzeige bei Eingabe Kommentar

        $sql_menge='SELECT ulo_id,SUM(ulo_fertig) as Menge FROM log ' .
            'WHERE ulo_aufgabe = ' . $task_id . ' AND ulo_ma = "' . $_SESSION['hma_id'] . '" ' .
            'GROUP BY ulo_aufgabe';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_menge=mysql_query($sql_menge, $verbindung))
            {
            fehler();
            }

        if (mysql_num_rows($ergebnis_menge) != 0)
            {
            while ($zeile_menge=mysql_fetch_array($ergebnis_menge))
                {
                $bisheriger_aufwand=$zeile_menge['Menge'];
                }
            }
        else
            {
            $bisheriger_aufwand=0;
            }


        // Fertigstellungsgrad

        $sql_menge='SELECT ulo_id,SUM(ulo_fertig) as Menge FROM log ' .
            'WHERE ulo_aufgabe = ' . $task_id . ' AND ulo_ma = "' . $zeile['hma_id'] . '" ' .
            'GROUP BY ulo_aufgabe';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_menge=mysql_query($sql_menge, $verbindung))
            {
            fehler();
            }

        if (mysql_num_rows($ergebnis_menge) != 0)
            {
            while ($zeile_menge=mysql_fetch_array($ergebnis_menge))
                {
                echo '<td class="is24" align="center">' . $zeile_menge['Menge'] . '</td>';
                }
            }
        else
            {
            echo '<td class="is24" align="center">0</td>';
            }
        } // Ende WHILE Bearbeitertabelle
    }
else
    { //Es gibt keinen Bearbeiter, blende Uebernehme-Symbol ein
    echo '<tr>';

    echo '<td class="is24"><a href="aufgabe_ansehen.php?bw=' . $zeile['hma_id'] . '&hau_id=' . $task_id
        . '"><img src="bilder/icon_uebernehmen.gif" border="0" alt="Take Task" title="Take Task"></a></td>';

    echo '<td class="is24">' . $zeile['hma_login'] . '</td>';
    }

echo '</tr>';

echo '</tbody>';
############### Baue Untermenue fuer erlaubte Aktionen ############################################

echo '<tr>';

echo '<td colspan="8">';

echo '<table class="matrix"><tr><td>Aktion: ';

                                            
# Aufgabentyp ermitteln

$sql_typ='SELECT hau_hprid, hau_abschluss, hau_typ FROM aufgaben WHERE hau_id = ' . $task_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_typ=mysql_query($sql_typ, $verbindung))
    {
    fehler();
    }

    while ($zeile_typ=mysql_fetch_array($ergebnis_typ))
        {
        $aufgabentyp = $zeile_typ['hau_hprid'];
        $hau_abschluss = $zeile_typ['hau_abschluss']; 
        $hau_typ = $zeile_typ['hau_typ'];
        }    

if($aufgabentyp==6)
{
# Changestatus ermitteln

$sql_typ='SELECT urs_freigabe_ok FROM rollen_status WHERE urs_hauid = ' . $task_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_typ=mysql_query($sql_typ, $verbindung))
    {
    fehler();
    }

    while ($zeile_typ=mysql_fetch_array($ergebnis_typ))
        {
        $change_status = $zeile_typ['urs_freigabe_ok'];
        } 
}       

// Biete Uebernahme an, wenn der aktuelle Benutzer nicht bereits zugeteilt ist

$sql_button='SELECT * FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $task_id . ' AND uau_status = 0';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_button=mysql_query($sql_button, $verbindung))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis_button) != 0)
    {
    if ($bin_da == 0)
        {
        echo '<a href="aufgabe_mee_too.php?&hau_id=' . $task_id
            . '"><img src="bilder/icon_mitarbeiter_loeschen.png" border="0" alt="Als Bearbeiter hinzufügen" title="Als Bearbeiter hinzufügen"></a>';
        }

    if ($anzahl_ma > 1 AND in_array($_SESSION['hma_id'], $hma_kennung))
        {
        {
        echo '&nbsp;<a href="aufgabe_schliessen.php?&hau_id=' . $task_id
            . '"><img src="bilder/icon_fixdatum.gif" border="0" alt="Aufgabe für alle schliessen" title="Aufgabe für alle schliessen"></a>';
            }
        }
    }

echo '&nbsp;<a href="schreibtisch_aufgabe_aendern.php?hau_id=' . $task_id
    . '&toggle=1&return_to_task=1"><img src="bilder/icon_aendern.gif" border=0 alt="Aufgabe ändern" title="Aufgabe ändern"></a>';

echo '&nbsp;<a href="schreibtisch_aufgabe_in_change.php?hau_id_ref=' . $task_id . '"><img src="bilder/icon_projectnote.png" border=0 alt="Aus der Aufgabe einen Change erstellen" title="Aus der Aufgabe einen Change erstellen"></a>';
    
echo '&nbsp;<a href="schreibtisch_aufgabe_killen.php?hau_id=' . $task_id . '"><img src="bilder/icon_termin.gif" border=0 alt="Für Notfälle: Aufgabe endgültig zumachen" title="Für Notfälle: Aufgabe endgültig zumachen"></a>';
    
echo '&nbsp;<a href="aufgabe_loeschen.php?hau_id=' . $task_id
    . '&toggle=1" onclick="return window.confirm(\'Aufgabe löschen?\');"><img src="bilder/icon_loeschen.gif" border=0 alt="Aufgabe löschen" title="Aufgabe löschen"></a>';
    
if($aufgabentyp==6 AND $change_status==0 AND $hau_abschluss==0)
{
echo '&nbsp;<a href="change_zuruecknehmen.php?hau_id=' . $task_id.'"><img src="bilder/icon_minus.png" border=0 alt="Change zurücknehmen" title="Change zurücknehmen"></a>';
        
}
 
    

echo '</td></tr></table>';

echo '<form action="aufgabe_weiterreichen.php" method="post">';

echo '<br><table class="matrix" >';

if ($es_gibt_bearbeiter == 0)
    {
    echo '<tr><td>';

    echo '<select size="1" name="auf_gruppe">';
    $sql='SELECT ule_id, ule_name FROM level WHERE ule_id > 1 ORDER BY ule_name';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    echo '<option value="0"><span class="text">Bitte Gruppe auswählen</span></option>';

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        echo '<option value="' . $zeile['ule_id'] . '"><span class="text">' . $zeile['ule_name'] . '</span></option>';
        }

    echo '</select>';

    echo '</td>';
    }

echo '<td>';

echo '<select size="1" name="auf_mitarbeiter">';
$sql=
    'SELECT hma_id, hma_login, ule_kurz FROM mitarbeiter LEFT JOIN level ON ule_id = hma_level WHERE hma_level > 1 ORDER BY hma_login';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

echo '<option value="0"><span class="text">Bitte Mitarbeiter auswählen</span></option>';

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<option value="' . $zeile['hma_id'] . '"><span class="text">' . $zeile['hma_login'] . ' ('
        . $zeile['ule_kurz'] . ')</span></option>';
    }

echo '</select>';

echo '</td>';

echo '<td><input type="radio" name="auf_typ" value="1" checked><span class="text_klein"> weiterleiten</span></td>';

echo '<td><input type="radio" name="auf_typ" value="2"><span class="text_klein"> delegieren</span></td>';

echo '</tr><tr>';

echo
    '<td colspan="3"><textarea name="auf_hinweis" cols="50" rows="3"></textarea></td></tr><tr><td align="right" colspan=3"><input type="submit" name="speichern" value="Aufgabe weitergeben" class="formularbutton" /></td></tr>';

echo '</table>';

echo '<input type="hidden" name="auf_id" value="' . $task_id . '">';

echo '</form>';

echo '</td></tr>';

echo '</table>';

echo '</td></tr>';

echo '<tr><td>';


// Tabellenkopf Links

# echo '<tr class="none"><td class="is24_vertikal_layout" colspan="8">';

echo '<br><table class="matrix" width="450">';

echo '<tr>';

echo '<td class="xnormal_sort">Relevante Links:</td>';

echo '</tr>';

$sql='SELECT hau_links FROM aufgaben ' .
    'WHERE hau_aktiv = 1 AND hau_id=' . $task_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<tr>';

    echo '<td class="is24_vertikal_layout"><a href="' . $zeile['hau_links'] . '" target="_blank">' . ($zeile['hau_links']) . '</a></td>';

    echo '</tr>';
    }

echo '</table>';

echo '</td></tr>';

echo '<tr><td>'; 

// Tabellenkopf Anlagen  

echo '<br><table class="matrix" width="450">';

echo '<tr>';

echo '<td class="xnormal_sort" colspan="4">Anlagen zum Ticket</td>';

echo '</tr>';

echo '<tr>';

echo '<td class="xnormal_sort"><img src="bilder/email_go.png"></td>';

echo '<td class="xnormal_sort">Datei</td>';

echo '<td class="xnormal_sort">Datum</td>';

echo '<td class="xnormal_sort">von</td>';

echo '<td class="xnormal_sort">&nbsp;</td>';

echo '</tr>';

### Lese die Anlagen für das Ticket

$target_path="anhang/" . $task_id . "/";

$sql_anhang = 'SELECT * FROM anlagen WHERE uan_hauid = '.$task_id;

if (!$ergebnis_anhang=mysql_query($sql_anhang, $verbindung))
    {
    fehler();
    }

while ($zeile_anhang=mysql_fetch_array($ergebnis_anhang))
    {
    
echo '<tr>';

    echo '<td class="xnormal_sort">';

    if($zeile_anhang['uan_senden']==1)
    {
    echo '<a href="mail_anlage.php?uan_id='.$zeile_anhang['uan_id'].'&status=0&hau_id='.$task_id.'"><img src="bilder/ja.gif" border="0"></a>'; 
    }else
    {
    echo '<a href="mail_anlage.php?uan_id='.$zeile_anhang['uan_id'].'&status=1&hau_id='.$task_id.'""><img src="bilder/nein.gif" border="0"></a>';          
    }
    
        echo '</td>'; 

echo '<td class="xnormal_sort"><a href="' . $target_path . $zeile_anhang['uan_name'].'" target="_blank">'.$zeile_anhang['uan_name'].'</a></td>';
echo '<td class="xnormal_sort">'.zeitstempel_anzeigen($zeile_anhang['uan_zeitstempel']).'</td>';
echo '<td class="xnormal_sort">'.$zeile_anhang['uan_besitzer'].'</td>';
echo '<td class="xnormal_sort"><a href="aufgabe_file_loeschen.php?name=' . $zeile_anhang['uan_name'] . '&pfad=' . $target_path . '&task_id='. $task_id . '" onclick="return window.confirm(\'Delete File?\');"><img src="bilder/icon_loeschen.gif" border=0></a></td>';

echo '</tr>';

    }     
/*


echo '<td class="xnormal_sort">';

if (is_dir($target_path))
    {
    if ($handle=opendir($target_path))
        {
        while (false !== ($file=readdir($handle)))
            if ($file != '.' AND $file != '..')
                {     
                echo '<a href="' . $target_path . $file . '" target="_blank">' . ($file)
                    . '</a>  <a href="aufgabe_file_loeschen.php?name=' . $file . '&pfad=' . $target_path . '&task_id='
                    . $task_id
                    . '" onclick="return window.confirm(\'Delete File?\');"><img src="bilder/icon_loeschen.gif" border=0></a><br>';
                }
        closedir($handle);
        }
    }

echo '</td></tr>';
*/

echo '</table>';

echo '</td></tr>';

echo '<tr><td>'; 

// Tabellenkopf Mail

echo '<br><table class="matrix" id="is24_mitarbeiteroptionen" width="450">';

echo '<tr>';

echo '<td class="xnormal_sort" colspan="3">Verteilerliste (neben den Bearbeitern):</td>';

echo '</tr>';

$sql='SELECT * FROM ticket_info WHERE uti_hauid = ' . $task_id.' ORDER BY uti_status DESC';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<tr>';

    echo '<td class="xnormal_sort">';
    
    if($zeile['uti_aktiv']==1)
    {
    echo '<a href="mail_status.php?uti_id='.$zeile['uti_id'].'&status=0&hau_id='.$task_id.'"><img src="bilder/ja.gif" border="0"></a>'; 
    }else
    {
    echo '<a href="mail_status.php?uti_id='.$zeile['uti_id'].'&status=1&hau_id='.$task_id.'""><img src="bilder/nein.gif" border="0"></a>';          
    }
    
    echo '</td>';   
   
    echo '<td class="xnormal_sort">';
    
    if(in_array($zeile['uti_mail'],$eMail_blacklist))
    {
        echo '<span style="text-decoration:line-through;">'. $zeile['uti_mail'] . '</span></td>';    
    }  else
    {
        echo $zeile['uti_mail'] . '</td>';    
    }
    
    if($zeile['uti_status']==1)
    {
    echo '<td class="xnormal_sort">[an]</td>';        
    }else
    {
    echo '<td class="xnormal_sort">[cc]</td>';          
    }

    echo '</tr>';
    }

echo '<tr>';

echo '<td class="xnormal_sort" align="right" colspan="3"><a href="mail_add.php?ticket=' . $task_id
    . '">Neue Mail eintragen</a></td>';

echo '</tr>';

   echo '</table>';

echo '</td></tr>';

echo '</table>';

// Aufgabenlog

echo '</td></tr>';


// Kommentareingabe

echo '<tr><td valign="top">';

echo '<br>';

echo '<form action="aufgabe_kommentar_speichern.php" method="post" enctype="multipart/form-data">';

echo '<table width="815" id="is24_kommentare">';

echo '<caption class="is24">';

echo 'Neuer Kommentar';

echo '</caption>';

    echo '<colgroup>';
    echo '<col class="is24-first" />';
    echo '</colgroup>';

echo '<tbody>';

if(isset($_POST['timer']))
    {
    echo '<input type="hidden" name="zeit" value="' . date("H:i") . '">';
    }

// Datum Kommentar

echo '<tr>';

echo '<td class="text_klein" width="115px">Datum: </td><td colspan="6"><input type="text" name="ulo_datum" value="'
    . date("d.m.Y H:i") . '" style="width:340px;"></td>';

echo '</tr>';

// ID ces Schreibenden

echo '<input type="hidden" name="ulo_ma" value="' . $_SESSION['hma_id'] . '">';

// Zuordnung zur Aufgabe

echo '<input type="hidden" name="ulo_aufgabe" value="' . $task_id . '">';

// Text des Kommentars

echo '<tr>';

echo
    '<td class="text_klein" valign="top" width="115px">Kommentar</td><td colspan="6"><textarea cols="80" rows="10" name="ulo_text"></textarea></td>';

echo '</tr>';

// Aufwand

echo '<tr>';

echo
    '<td class="text_klein" width="115px">Aufwand</td><td><input type="text" name="ulo_aufwand_m" style="width:100px;"> [min]</td>';

echo '<td class="text_klein"></td><td><input type="text" name="ulo_aufwand_h" style="width:100px;"> [h]</td>';

echo '<td class="text_klein"></td><td><input type="text" name="ulo_aufwand_d" style="width:100px;"> [Tage]</td>';

echo '<td class="text_klein">&nbsp;</td>';

echo '</tr>';

// Fertigstellung

echo '<tr>';

echo
    '<td class="text_klein" width="115px">% Fortschritt insgesamt: </td><td><input type="text" name="ulo_fertig" style="width:100px;"></td><td class="text_klein" colspan="5">Damit sind '
    . $bisheriger_aufwand . '% der Aufgabe abgeschlossen.</td>';

echo '</tr>';


// Fileupload

echo '<tr>';

echo
    '<td class="text_klein" valign="top" width="115px">Anlagen zur Aufgabe:&nbsp;&nbsp;</td><td colspan="6"><input type="file" name="hau_datei"></td>';

echo '</tr>';

echo '<tr>';

echo
    '<td class="text_klein" valign="top" width="115px">PING senden:&nbsp;&nbsp;</td><td colspan="6">';

    echo '<select size="1" name="ulo_ping" style="width:140px;">';

    echo '<option value="0"><span class="text">kein Ping</span></option>';

    $sql=   'SELECT hma_id, hma_vorname, hma_name FROM mitarbeiter 
            WHERE hma_aktiv = 1 
            ORDER BY hma_vorname';

            // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

    while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<option value="' . $zeile['hma_id'] . '"><span class="text">' . $zeile['hma_vorname'] . ' '
        . $zeile['hma_name'] . '</span></option>';
    }

    echo '</select>';

echo '</td></tr>';
    
echo '</tbody>';

echo '</table>';

echo '<br>';

###################################################  AKTIONEN ####################################

echo '<table width="815" id="is24_kommentare">';

echo '<caption class="is24">';

echo 'Optionen';

echo '</caption>';

    echo '<colgroup>';
    echo '<col class="is24-first" />';
    echo '</colgroup>';

echo '<tbody>';



### PING Behandlung

## Prüfe, ob ein PING vorliegt

$sql_ping = 'SELECT * FROM log_status 
            INNER JOIN log ON uls_uloid = ulo_id 
            WHERE uls_ping_an = "' . $_SESSION['hma_id'] . '" AND ulo_aufgabe = ' . $task_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_ping=mysql_query($sql_ping, $verbindung))
    {
    fehler();
    }

while ($zeile_ping=mysql_fetch_array($ergebnis_ping))
    {
    $ulo_ping_off=$zeile_ping['uls_uloid'];
    }

    
if (mysql_num_rows($ergebnis_ping) != 0)
    {

    echo '<tr>';
        
    echo '<td class="box" width="115px">PING</td>';
    
    echo '<td width="20px"><input type="checkbox" name="ping_off"></td>';
    
    echo '<td width="200" align="left">PING deaktivieren?</td>';

    echo '<td colspan="2" width="480px"></td>';
    
    echo '<input type="hidden" name="ulo_ping_off" value="' . $ulo_ping_off . '">';

    echo '</tr>';
    
    } 

    echo '<td width="115px">Dokumentation</td>';
    
    echo '<td width="20px"><input type="checkbox" name="ulo_extra"></td>';
    
    echo '<td width="200" align="left">Ins Aktivitätsprotokoll schreiben?</td>';

    if($hau_typ!=11)
    {
        echo '<td width="20px"><input type="checkbox" name="ulo_deploy"></td>';
        echo '<td width="460px" align="left">Tickettyp auf Deployment ändern?</td>'; 
    }
        else
    {  
        echo '<td colspan="2" width="480px"></td>';
     }

echo '</tr>';

// Stoppen und schliessen nur nach Akzeptanz der Aufgabe

$sql_generell='SELECT * FROM aufgaben_mitarbeiter ' .
    'INNER JOIN mitarbeiter ON uau_hmaid = hma_id ' .
    'WHERE uau_hmaid = "' . $_SESSION['hma_id'] . '" AND uau_hauid = ' . $task_id . ' AND uau_ma_status = 1';


// Frage Datenbank nach Suchbegriff
if (!$ergebnis_generell=mysql_query($sql_generell, $verbindung))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis_generell) != 0)
    {


    // Bearbeitung stoppen

    $sql_stopp='SELECT * FROM aufgaben_mitarbeiter ' .
        'INNER JOIN mitarbeiter ON uau_hmaid = hma_id ' .
        'WHERE uau_hmaid = "' . $_SESSION['hma_id'] . '" AND uau_hauid = ' . $task_id;

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_stopp=mysql_query($sql_stopp, $verbindung))
        {
        fehler();
        }

    if (mysql_num_rows($ergebnis_stopp) != 0)
        {
        while ($zeile_stopp=mysql_fetch_array($ergebnis_stopp))
            {
            if ($zeile_stopp['uau_status'] == 0)
                {
                if ($zeile_stopp['uau_stopp'] == 0 OR $zeile_stopp['uau_stopp'] == 3) // 3 = delegiert
                    {
                 
                        echo '<tr>';
        
                        echo '<td width="115px">Stop Aufgabe</td>';
    
                        echo '<td width="20px"><input type="checkbox" name="uau_stopp_c"></td>';
    
                        echo '<td width="200" align="left">Auf Externe warten?</td>';
                        
                        echo '<td width="20px"><input type="checkbox" name="uau_stopp_i"></td>';
        
                        echo '<td width="460px" align="left">Interne Verzögerung?</td>'; 

                        echo '</tr>';
                    }
                else
                    {
                    echo '<tr>';

                    echo
                        '<td class="text_klein">Aufgabe wieder starten? </td><td colspan="6"><input type="checkbox" name="uau_start"></td>';

                    echo '</tr>';
                    }
                }
            }
        }

    // Bearbeitung abschliessen

    $sql_ende='SELECT * FROM aufgaben_mitarbeiter 
               LEFT JOIN aufgaben ON hau_id = uau_hauid 
               INNER JOIN mitarbeiter ON uau_hmaid = hma_id ' .
        'LEFT JOIN rollen_status ON urs_hauid = uau_hauid ' .
        'WHERE uau_hmaid = "' . $_SESSION['hma_id'] . '" AND uau_hauid = ' . $task_id;

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_ende=mysql_query($sql_ende, $verbindung))
        {
        fehler();
        }

    if (mysql_num_rows($ergebnis_ende) != 0)
        {
        while ($zeile_ende=mysql_fetch_array($ergebnis_ende))
            {
                
            if($zeile_ende['hau_hprid']!=6)
            {
            if ($zeile_ende['uau_status'] == 0) // AND ($zeile_ende['urs_freigabe_ok']>0 AND $zeile_ende['hau_hprid'])
                {
                    echo '<tr>';
        
                    echo '<td width="115px">Abschluss</td>';
    
                    echo '<td width="20px"><input type="checkbox" name="uau_status"></td>';
    
                    echo '<td width="200" align="left">Aufgabe schließen?</td>';

                    echo '<td colspan="2" width="480px"></td>';
    
                    echo '<tr>';
                 }
            } else if ($zeile_ende['uau_status'] == 0 AND $zeile_ende['urs_freigabe_ok']>0)
            {
                    echo '<tr>';
        
                    echo '<td width="115px">Abschluss</td>';
    
                    echo '<td width="20px"><input type="checkbox" name="uau_status"></td>';
    
                    echo '<td width="200" align="left">Aufgabe schließen?</td>';

                    echo '<td colspan="2" width="480px"></td>';
    
                    echo '<tr>';               
            }
            }
        }
    } // Ende genereller Check

// Pruefe, ob Bearbeiter eine Freigabe erteilen muss

$sql_ok='SELECT * FROM rollen_matrix ' .
    'WHERE urm_hmaid = "' . $_SESSION['hma_id'] . '" AND urm_uroid = 1';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_ok=mysql_query($sql_ok, $verbindung))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis_ok) != 0 AND $freigabe_status == 0 AND $freigabe_noetig == 1)
    {
                    echo '<tr>';
        
                    echo '<td width="115px">Change</td>';
    
                    echo '<td width="20px"><input type="checkbox" name="urs_freigabe_ok"></td>';
    
                    echo '<td width="200" align="left">Change freigeben?</td>';

                    echo '<td colspan="2" width="480px"></td>';
    
                    echo '<tr>';

    }


// Formularbutton

echo '<tr><td style="text-align:left; padding-top:10px;">';

    $sql_check='SELECT * FROM ticket_info WHERE uti_hauid = ' . $task_id;

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_check=mysql_query($sql_check, $verbindung))
    {
        fehler();
    }

    if (mysql_num_rows($ergebnis_check) > 0)
    {
 
 echo
    '<td colspan="6" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Eintrag intern speichern" class="formularbutton_gruen" />&nbsp;&nbsp;<input type="submit" name="speichern" value="Eintrag speichern und mailen" class="formularbutton_rot" />';
     /*
    $otrs_ticket_mail = 0;
    while ($zeile_check=mysql_fetch_array($ergebnis_check))
    {
        if($zeile_check['uti_mail'] == 'ticket@otrs.cc.is24.loc')
        {
        $otrs_ticket_mail = 1;
        }
    }
    
    if($otrs_ticket_mail==0)
    {
       echo '&nbsp;&nbsp;<input type="submit" name="speichern" value="Mit Eintrag OTRS-Ticket starten" class="formularbutton_rot" />'; 
    }
    echo '</td>';
 */
 
    } else
    {
        echo
    '<td colspan="6" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Eintrag intern speichern" class="formularbutton_gruen" />&nbsp;&nbsp;</td>';
    }
     

 

echo '</td></tr>';


echo '</tbody>';

echo '</table>';

echo '</form>';

echo '<br>';
// Liste Aktivitaeten

echo '<a name="comment"></a>';

include('segment_liste_aktiv.php');


// Abschluß

echo '</td></tr></table>';
include('segment_fuss.php');
?>
