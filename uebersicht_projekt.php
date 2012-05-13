<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');

echo '<form action="uebersicht_projekt.php" method="post">';

echo '<br><span class="text">Bitte ein Projekt zur Anzeige auswählen: </span>';

echo '<select size="1" name="hpr_id">';
$sql='SELECT hpr_id, hpr_titel FROM projekte 
            WHERE hpr_fertig = 0 and hpr_aktiv="1" AND hpr_id > 3 ' .
    'ORDER BY hpr_titel';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    if ($zeile['hpr_id'] > 2)
        {
        echo '<option value="' . $zeile['hpr_id'] . '"><span class="text">' . $zeile['hpr_titel'] . '</span></option>';
        }
    }

echo '</select> ';

echo '<input type="submit" value="Zeige Projektdaten" class="formularbutton" />';

echo '</form><br>';


////////////////////// Beginne Anzeige /////////////////////////////

if (isset($_REQUEST['hpr_id']))
    {
    $check=0;

    $sql_name=
        'SELECT *, m1.hma_name AS pinhabern, m1.hma_vorname AS pinhaberv, m2.hma_name AS techpmn, m2.hma_vorname AS techpmv FROM projekte '
        .
        'LEFT JOIN mitarbeiter m1 ON hpr_inhaber = m1.hma_id ' .
        'LEFT JOIN mitarbeiter m2 ON hpr_techpm = m2.hma_id 
                WHERE hpr_id= ' . $_REQUEST['hpr_id'];

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_name=mysql_query($sql_name, $verbindung))
        {
        fehler();
        }

    while ($zeile_name=mysql_fetch_array($ergebnis_name))
        {
        echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Projektdetails ['
            . $zeile_name['hpr_titel'] . ']<br><br>';

        echo '<table width=800>';

        echo '<tr>';

        echo '<td class="text">Projekteigner:</td><td>' . $zeile_name['pinhaberv'] . ' ' . $zeile_name['pinhabern']
            . '</td>';

        echo '</tr><tr>';

        echo '<td class="text">Start:</td><td>' . datum_anzeigen($zeile_name['hpr_start']) . '</td>';

        echo '</tr><tr>';

        echo '<td class="text">Ende:</td><td>' . datum_anzeigen($zeile_name['hpr_pende']) . '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text">Projekttitel:</td><td>' . ($zeile_name['hpr_titel']) . '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text" valign="top">Projektbeschreibung:</td><td>' . nl2br(htmlspecialchars(($zeile_name['hpr_beschreibung'])))
            . '</td>';

        echo '</tr>';

        $sql_effort='SELECT SUM(ulo_aufwand) AS effort FROM projekte ' .
            'INNER JOIN aufgaben ON hau_hprid = hpr_id ' .
            'INNER JOIN log ON hau_id = ulo_aufgabe ' .
            'WHERE hpr_id = ' . $_REQUEST['hpr_id'];

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_effort=mysql_query($sql_effort, $verbindung))
            {
            fehler();
            }

        while ($zeile_effort=mysql_fetch_array($ergebnis_effort))
            {
            echo '<tr>';

            echo '<td class="text" valign="top">Projektaufwand:</td><td>' . (round($zeile_effort['effort'] / 60, 2))
                . ' h</td>';

            echo '</tr>';
            }

        echo '</table>';

        echo
            '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Projektstatus<br>&nbsp;&nbsp;&nbsp;<span class="text_klein">[<a href="uebersicht_projektdetail_timeline.php?hpr_id='
            . $_REQUEST['hpr_id'] . '">zeige GANTT-Diagramm</a>]</span><br><br>';

        echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Aufgaben in Warteposition<br><br>';

        $sql='SELECT hau_id, hau_ticketnr FROM projekte ' .
            'LEFT JOIN aufgaben ON hpr_id = hau_hprid ' .
            'LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid ' .
            'WHERE NOT EXISTS (SELECT * FROM aufgaben_mitarbeiter WHERE hau_id = uau_hauid LIMIT 1) ' .
            ' AND hpr_id = ' . $_REQUEST['hpr_id'] . ' AND hau_aktiv = 1 ' .
            'GROUP BY hau_id ' .
            'ORDER BY hau_prio, hau_pende';

        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }

        echo '<table class="element" cellspacing="1" cellpadding="3" width="900">';

        echo '<tr>';

        echo '<td class="tabellen_titel">TNR</td>';

        echo '<td class="tabellen_titel">Aufgabe</td>';

        echo '<td class="tabellen_titel">Priorität</td>';

        echo '<td class="tabellen_titel">Eigner</td>';

        echo '<td class="tabellen_titel">angelegt</td>';

        echo '<td class="tabellen_titel">Plan-Enddatum</td>';

        echo '<td class="tabellen_titel">Gruppe</td>';

        echo '<td class="tabellen_titel">Typ</td>';

        echo '<td class="tabellen_titel">&nbsp;</td>';

        echo '<td class="tabellen_titel">Vorgänger</td>';

        echo '</tr>';

        echo '<tr><td colspan="5"> </td></tr>';

        while ($zeile=mysql_fetch_array($ergebnis))
            {
            if (!(is_null($zeile['hau_id'])))
                {

                $task_id=$zeile['hau_id'];
                include('segment_zeilenfarbe.php');

                $sql_daten='SELECT * FROM aufgaben ' .
                    'LEFT JOIN typ ON uty_id = hau_typ ' .
                    'LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id ' .
                    'LEFT JOIN level ON uaz_pg = ule_id ' .
                    'LEFT JOIN mitarbeiter ON hma_id = hau_inhaber ' .
                    'LEFT JOIN prioritaet ON upr_nummer = hau_prio ' .
                    'LEFT JOIN projekte ON hpr_id = hau_hprid    
            WHERE  hau_aktiv = 1 AND hau_id = ' . $zeile['hau_id'];

                if (!$ergebnis_daten=mysql_query($sql_daten, $verbindung))
                    {
                    fehler();
                    }

                while ($zeile_daten=mysql_fetch_array($ergebnis_daten))
                    {
                    $TNR = $zeile_daten['hau_id'];
                    $Titel=$zeile_daten['hau_titel'];
                    $P_ende=$zeile_daten['hau_pende'];
                    $Typ=$zeile_daten['uty_name'];
                    $Bereich=$zeile_daten['ule_name'];
                    $Inhaber=$zeile_daten['hma_login'];
                    $Angelegt=$zeile_daten['hau_anlage'];
                    $Prio=$zeile_daten['upr_name'];

                    echo '<tr>';

                    echo '<td bgcolor="' . $color . '" class="text">' . $TNR . '</td>';

                    if ($check != $zeile['hau_id'])
                        {
                        echo '<td bgcolor="' . $color . '" class="text"><a href="aufgabe_ansehen.php?hau_id='
                            . $zeile['hau_id'] . '">' . ($Titel) . '</a></td>';
                        }
                    else
                        {
                        echo '<td bgcolor="' . $color . '" class="text"> </td>';
                        }

                    echo '<td bgcolor="' . $color . '" class="text">' . $Prio . '</td>';

                    echo '<td bgcolor="' . $color . '" class="text">' . $Inhaber . '</td>';

                    echo '<td bgcolor="' . $color . '" class="text">' . datum_anzeigen($Angelegt) . '</td>';

                    echo '<td bgcolor="' . $color . '" class="text">' . datum_anzeigen($P_ende) . '</td>';

                    echo '<td bgcolor="' . $color . '" class="text">' . $Bereich . '</td>';

                    echo '<td bgcolor="' . $color . '" class="text">' . $Typ . '</td>';

                    $lock=0;

                    if ($zeile_daten['hau_reihe'] != '')
                        {
                        $Reihe=explode("#", $zeile_daten['hau_reihe']);
                        $lock=0;

                        # var_dump ($Reihe);
                        foreach ($Reihe AS $Vorgaenger)
                            {
                            $sql_reihe = 'SELECT hau_abschluss FROM aufgaben WHERE hau_id = ' . $Vorgaenger;

                            // Frage Datenbank nach Suchbegriff
                            if (!$ergebnis_reihe=mysql_query($sql_reihe, $verbindung))
                                {
                                fehler();
                                }

                            while ($zeile_reihe=mysql_fetch_array($ergebnis_reihe))
                                {
                                if ($zeile_reihe['hau_abschluss'] == 0)
                                    {
                                    $lock=1;
                                    $locked_task=$Vorgaenger;
                                    }
                                else
                                    {
                                    $unlocked_task=$Vorgaenger;
                                    }
                                }
                            }

                        if ($lock == 1)
                            {
                            echo '<td  bgcolor="' . $color
                                . '" class="text"><img src="bilder/icon_lock.gif" border=0 alt="parent task is not closed" title="parent task is not closed"></td>';
                            }
                        else
                            {
                            echo '<td  bgcolor="' . $color
                                . '" class="text"><img src="bilder/icon_unlock.gif"  border=0 alt="parent task is done" title="parent task is done"></td>';
                            }
                        }
                    else
                        {
                        echo '<td  bgcolor="' . $color . '" class="text">&nbsp;</td>';
                        }

                    echo '<td  bgcolor="' . $color . '" class="text">' . $zeile_daten['hau_reihe'] . '</td>';

                    $check=$zeile['hau_id'];

                    echo '</tr>';
                    }
                } // Ende IS_NULL
            }

        echo '</table>';

        echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Zugewiesene Aufgaben in Arbeit<br><br>';

        $sql='SELECT hau_id FROM projekte ' .
            'LEFT JOIN aufgaben ON hpr_id = hau_hprid 
            WHERE hau_abschluss = 0 AND hpr_id = ' . $_REQUEST['hpr_id'] . ' ' .
            'GROUP BY hau_id ' .
            'ORDER BY hau_prio, hau_pende';

        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }

        echo '<table class="element" cellspacing="10" cellpadding="3" width="900">';

        echo '<tr>';

        #  echo '<td class="tabellen_titel">SID</td>';
        echo '<td class="tabellen_titel">TNR</td>';

        echo '<td class="tabellen_titel">Aufgabe</td>';

        echo '<td class="tabellen_titel">Mitarbeiter</td>';

        echo '<td class="tabellen_titel">Plan-Enddatum</td>';

        echo '<td class="tabellen_titel">reales Enddatum</td>';

        echo '<td class="tabellen_titel">angenommen?</td>';

        echo '<td class="tabellen_titel">Status</td>';

        echo '<td class="tabellen_titel">Fortschritt [%]</td>';

        echo '<td class="tabellen_titel">&nbsp;</td>';

        echo '<td class="tabellen_titel">Vorgänger</td>';

        echo '</tr>';

        echo '<tr><td colspan="5"> </td></tr>';

        while ($zeile=mysql_fetch_array($ergebnis))
            {

            $task_id = $zeile['hau_id'];
            include('segment_zeilenfarbe.php');

            $sql_daten='SELECT * FROM aufgaben ' .
                'INNER JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid ' .
                'INNER JOIN mitarbeiter ON hma_id = uau_hmaid 
                    LEFT JOIN projekte ON hpr_id = hau_hprid 
                    WHERE  hau_aktiv = 1 AND hau_id = '
                . $zeile['hau_id'];

            if (!$ergebnis_daten=mysql_query($sql_daten, $verbindung))
                {
                fehler();
                }

            while ($zeile_daten=mysql_fetch_array($ergebnis_daten))
                {
                $TNR = $zeile_daten['hau_id'];
                $Titel=$zeile_daten['hau_titel'];
                $Bearbeiter=$zeile_daten['hma_login'];
                $Bearbeiter_id=$zeile_daten['uau_hmaid'];
                $P_ende=$zeile_daten['hau_pende'];
                $T_ende=$zeile_daten['uau_tende'];
                $Status_MA=$zeile_daten['uau_ma_status'];
                $Stopp=$zeile_daten['uau_stopp'];
                $Status=$zeile_daten['uau_status'];

                $Fertig=0;
                // Fertigstellungsgrad

                $sql_menge='SELECT ulo_id,SUM(ulo_fertig) as Menge FROM log ' .
                    'WHERE ulo_aufgabe = ' . $zeile['hau_id'] . ' AND ulo_ma = "' . $Bearbeiter_id . '" ' .
                    ' GROUP BY ulo_aufgabe';


                // Frage Datenbank nach Suchbegriff
                if (!$ergebnis_menge=mysql_query($sql_menge, $verbindung))
                    {
                    fehler();
                    }

                if (mysql_num_rows($ergebnis_menge) != 0)
                    {
                    while ($zeile_menge=mysql_fetch_array($ergebnis_menge))
                        {
                        $Fertig=$zeile_menge['Menge'];
                        }
                    }
                else
                    {
                    $Fertig=0;
                    }

                // akzeptiert ?

                switch ($Status_MA)
                    {
                    case '0':
                        $Bild_MA_Status=
                            '<img src="bilder/icon_offen.gif" alt="Task is mapped but queued" title="Task is mapped but queued">';
                        break;

                    case '1':
                        $Bild_MA_Status=
                            '<img src="bilder/icon_akzeptiert.gif" alt="Task in Progress" title="Task in Progress">';
                        break;

                    case '2':
                        $Bild_MA_Status=
                            '<img src="bilder/icon_abgelehnt.gif" alt="Task rejected" title="Task rejected">';
                        break;
                    }


                // Stopp der Aufgabe ?

                switch ($Stopp)
                    {
                    case '0': // Aufgabe in Arbeit
                        $Bild_Stopp='<img src="bilder/icon_gruen.png" alt="Task enabled" title="Task enabled">';
                        break;

                    case '1': // Aufgabe gestoppt
                        $Bild_Stopp=
                            '<img src="bilder/icon_rot.png" alt="Task stopped internally" title="Task stopped">';
                        break;

                    case '2': // Aufgabe gestoppt
                        $Bild_Stopp=
                            '<img src="bilder/icon_gelb.png" alt="Task stopped by customer" title="Task stopped">';
                        break;

                    case '3': // Aufgabe delegiert
                        $Bild_Stopp=
                            '<img src="bilder/icon_blau.png" alt="Task is delegated" title="Task is delegated">';
                        break;
                    }

                // Stopp der Aufgabe ?

                switch ($Status)
                    {
                    case '0': // Aufgabe offen
                        $Bild_Status='<img src="bilder/icon_arbeit.gif" alt="Task open" title="Task open">';
                        break;

                    case '1': // Aufgabe erledigt
                        $Bild_Status='<img src="bilder/icon_erledigt.gif" alt="Task done" title="Task done">';
                        break;
                    }

                echo '<tr>';

                #echo '<td bgcolor="'.$color.'" class="text">'.$SID.'</td>';
                echo '<td bgcolor="' . $color . '" class="text">' . $TNR . '</td>';

                if ($check != $zeile['hau_id'])
                    {
                    echo '<td bgcolor="' . $color . '" class="text"><a href="aufgabe_ansehen.php?hau_id='
                        . $zeile['hau_id'] . '">' . ($Titel) . '</a></td>';
                    }
                else
                    {
                    echo '<td bgcolor="' . $color . '" class="text"> </td>';
                    }

                echo '<td  bgcolor="' . $color . '" class="text">' . $Bearbeiter . '</td>';

                echo '<td  bgcolor="' . $color . '" class="text">' . datum_anzeigen($P_ende) . '</td>';

                echo '<td  bgcolor="' . $color . '" class="text">' . datum_anzeigen($T_ende) . '</td>';

                echo '<td  bgcolor="' . $color . '" class="text">' . $Bild_MA_Status . '</td>';

                echo '<td  bgcolor="' . $color . '" class="text">' . $Bild_Stopp . '</td>';

                echo '<td  bgcolor="' . $color . '" class="text">' . $Fertig . '</td>';

                $lock=0;

                if ($zeile_daten['hau_reihe'] != '')
                    {
                    $Reihe=explode("#", $zeile_daten['hau_reihe']);
                    $lock=0;

                    # var_dump ($Reihe);
                    foreach ($Reihe AS $Vorgaenger)
                        {
                        $sql_reihe = 'SELECT hau_abschluss FROM aufgaben WHERE hau_id = ' . $Vorgaenger;

                        // Frage Datenbank nach Suchbegriff
                        if (!$ergebnis_reihe=mysql_query($sql_reihe, $verbindung))
                            {
                            fehler();
                            }

                        while ($zeile_reihe=mysql_fetch_array($ergebnis_reihe))
                            {
                            if ($zeile_reihe['hau_abschluss'] == 0)
                                {
                                $lock=1;
                                $locked_task=$Vorgaenger;
                                }
                            else
                                {
                                $unlocked_task=$Vorgaenger;
                                }
                            }
                        }

                    if ($lock == 1)
                        {
                        echo '<td  bgcolor="' . $color
                            . '" class="text"><img src="bilder/icon_lock.gif" border=0 alt="parent task is not closed" title="parent task is not closed"></td>';
                        }
                    else
                        {
                        echo '<td  bgcolor="' . $color
                            . '" class="text"><img src="bilder/icon_unlock.gif"  border=0 alt="parent task is done" title="parent task is done"></td>';
                        }
                    }
                else
                    {
                    echo '<td  bgcolor="' . $color . '" class="text">&nbsp;</td>';
                    }

                echo '<td  bgcolor="' . $color . '" class="text">' . $zeile_daten['hau_reihe'] . '</td>';
                $check=$zeile['hau_id'];

                echo '</tr>';
                }
            }

        echo '</table>';

        echo
            '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Abgeschlossene Aufgaben des Projekts<br><br>';

        $check=0;

        $sql='SELECT hau_id FROM projekte ' .
            'LEFT JOIN aufgaben ON hpr_id = hau_hprid 
            WHERE hau_abschluss = 1 AND hpr_id = ' . $_REQUEST['hpr_id'] . ' ' .
            'GROUP BY hau_id ' .
            'ORDER BY hau_prio, hau_pende';

        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }

        echo '<table class="element" cellspacing="1" cellpadding="3" width="900">';

        echo '<tr>';

        # echo '<td class="tabellen_titel">SID</td>';
        echo '<td class="tabellen_titel">TNR</td>';

        echo '<td class="tabellen_titel">Aufgabe</td>';

        echo '<td class="tabellen_titel">Bearbeiter</td>';

        echo '<td class="tabellen_titel">Plan-Enddatum</td>';

        echo '<td class="tabellen_titel">reales Enddatum</td>';

        echo '</tr>';

        echo '<tr><td colspan="5"> </td></tr>';

        while ($zeile=mysql_fetch_array($ergebnis))
            {
            if ($zeile['hau_id'] != NULL)
                {

                $task_id=$zeile['hau_id'];
                include('segment_zeilenfarbe.php');

                $sql_daten='SELECT * FROM aufgaben ' .
                    'INNER JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid ' .
                    'INNER JOIN mitarbeiter ON hma_id = uau_hmaid ' .
                    'LEFT JOIN projekte ON hpr_id = hau_hprid 
                    WHERE  hau_aktiv = 1 AND hau_id = ' . $zeile['hau_id'];

                if (!$ergebnis_daten=mysql_query($sql_daten, $verbindung))
                    {
                    fehler();
                    }

                while ($zeile_daten=mysql_fetch_array($ergebnis_daten))
                    {
                    $TNR = $zeile_daten['hau_id'];
                    $Titel=$zeile_daten['hau_titel'];
                    $Bearbeiter=$zeile_daten['hma_login'];
                    $Bearbeiter_id=$zeile_daten['uau_hmaid'];
                    $P_ende=$zeile_daten['hau_pende'];
                    $T_ende=$zeile_daten['uau_tende'];
                    $Status_MA=$zeile_daten['uau_ma_status'];
                    $Stopp=$zeile_daten['uau_stopp'];
                    $Status=$zeile_daten['uau_status'];

                    echo '<tr>';

                    echo '<td bgcolor="' . $color . '" class="text">' . $TNR . '</td>';

                    if ($check != $zeile['hau_id'])
                        {
                        echo '<td bgcolor="' . $color . '" class="text"><a href="aufgabe_ansehen.php?hau_id='
                            . $zeile['hau_id'] . '">' . ($Titel) . '</a></td>';
                        }
                    else
                        {
                        echo '<td bgcolor="' . $color . '" class="text"> </td>';
                        }

                    echo '<td bgcolor="' . $color . '" class="text">' . $Bearbeiter . '</td>';

                    echo '<td bgcolor="' . $color . '" class="text">' . datum_anzeigen($P_ende) . '</td>';

                    echo '<td bgcolor="' . $color . '" class="text">' . datum_anzeigen($T_ende) . '</td>';
                    $check=$zeile['hau_id'];

                    echo '</tr>';
                    }
                }
            } // ende WHILE

        echo '</table>';

        ## Projektinfos anzeigen ###

        echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Projektinfos<br><br>';

        $sql='SELECT * FROM projekt_info 
	  LEFT JOIN projekte ON hpr_id = upj_pid 	
          WHERE hpr_aktiv = 1 AND hpr_fertig = 0 AND hpr_id = ' . $_REQUEST['hpr_id'] . ' 
          ORDER BY upj_datum DESC';

        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }

        echo '<table class="element" cellspacing="1" cellpadding="3" width="900">';

        echo '<tr>';

        echo '<td class="tabellen_titel">Datum</td>';

        echo '<td class="tabellen_titel">Info</td>';

        echo '</tr>';

        echo '<tr><td colspan="2"> </td></tr>';

        while ($zeile=mysql_fetch_array($ergebnis))
            {
            echo '<tr>';

            echo '<td valign="top">' . datum_wandeln_useu($zeile['upj_datum']) . '</td>';

            echo '<td>' . nl2br($zeile['upj_text']) . '</td>';

            echo '</tr>';
            }

        echo '</table>';
        }
    }

include('segment_fuss.php');
?>