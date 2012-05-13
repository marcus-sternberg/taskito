<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$Login='';

foreach ($infos as $schluessel => $info)
    {
    switch ($schluessel)
        {

        // Art des Termins anzeigen (Fix, Enddatum und offen)

        case 'Datum':
            switch ($zeile[$info])
                {
                case '1': // offen
                    echo '<td align="center" style="border-left:1px solid grey;">&nbsp;</td>';

                    break;

                case '2': // festes Ende
                    echo
                        '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_enddatum.png" alt="due until" title="due until"></td>';
                    break;

                case '3': // Fix für einen Tag
                    echo
                        '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_fixdatum.png" alt="definite date" title="definite date"></td>';
                    break;
                }

            break;

        // Anzeige, ob Aufgaben gestoppt wurden

        case 'Status':
            if (isset($zeile[$info]))
                {
                switch ($zeile[$info])
                    {
                    case '0': // Aufgabe in Arbeit
                        echo
                            '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_gruen.png" alt="task enabled" title="task enabled"></td>';
                        break;

                    case '1': // Aufgabe gestoppt
                        echo
                            '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_rot.png" alt="On hold (internally)" title="On hold (internally)"></td>';
                        break;

                    case '2': // Aufgabe gestoppt
                        echo
                            '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_gelb.png" alt="Waiting for CC" title="Waiting for CC"></td>';
                        break;

                    case '3': // Aufgabe delegiert
                        echo
                            '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_blau.png" alt="Task is delegated" title="Task is delegated"></td>';
                        break;

                    default:
                        echo '<td align="center" style="border-left:1px solid grey;">&nbsp;</td>';
                        break;
                    }
                }
            else
                {
                echo '<td align="center" style="border-left:1px solid grey;">&nbsp;</td>';
                }

            break;

        // Zeige, ob es Kommentare gibt

        case 'Kommentar':

            $sql_kommentar = 'SELECT ulo_id FROM log WHERE ulo_aufgabe = ' . $zeile['hau_id']; 

                    // Frage Datenbank nach Suchbegriff
                    if (!$ergebnis_kommentar=mysql_query($sql_kommentar, $verbindung))
                        {
                        fehler();
                        }

                    $anzahl_kommentar = mysql_num_rows($ergebnis_kommentar);
                    
                    if($anzahl_kommentar>0)
                    {
                    echo '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_kommentar.gif" border=0 alt="'.$anzahl_kommentar.' Kommentar(e)" title="'.$anzahl_kommentar.' Kommentar(e)"></td>';    
                    }    
                    else
                    {
                    echo '<td align="center" style="border-left:1px solid grey;">&nbsp;</td>';
                    }
            break;
            
        // Zeige, ob es einen Vorgaenger gibt und seinen Status

        case 'Abhängigkeit':
            $lock=0;

            if ($zeile['hau_reihe'] != '')
                {
                $Reihe=explode("#", $zeile['hau_reihe']);
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
                    echo '<td align="center" style="border-left:1px solid grey;"><a href="aufgabe_ansehen.php?hau_id='
                        . $locked_task . '" target="_blank"><img src="bilder/icon_lock.gif" border=0 alt="parent task ('
                        . $locked_task . ')is not closed" title="parent task (' . $locked_task
                            . ') is not closed"></a></td>';
                    }
                else
                    {
                    echo '<td align="center" style="border-left:1px solid grey;"><a href="aufgabe_ansehen.php?hau_id='
                        . $unlocked_task
                            . '" target="_blank"><img src="bilder/icon_unlock.gif"  border=0 alt="parent task ('
                        . $unlocked_task . ') is done" title="parent task (' . $unlocked_task . ') is done"></a></td>';
                    }
                }
            else
                {
                echo '<td align="center" style="border-left:1px solid grey;">&nbsp;</td>';
                }
            break;


        // Anzeige, ob ein oder mehrere Bearbeiter eingeteilt sind

        case 'Gruppe':

            $sql_gruppe=
                'SELECT COUNT(uau_id) as Anzahl FROM aufgaben_mitarbeiter WHERE uau_ma_status < 2 AND uau_hauid = '
                . $zeile[$info];

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_gruppe=mysql_query($sql_gruppe, $verbindung))
                {
                fehler();
                }

            while ($zeile_gruppe=mysql_fetch_array($ergebnis_gruppe))
                {
                if ($zeile_gruppe['Anzahl'] > 1)
                    {
                    echo
                        '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_viele.gif" alt="more than one member mapped" title="more than one member mapped"></td>';
                    }
                else
                    {
                    echo
                        '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_ein.gif" alt="one member mapped" title="one member mapped"></td>';
                    }
                }

            break;

        // Gab es eine Terminänderung ?

        case 'Termin ändern':

            $sql_gruppe='SELECT hau_terminaendern FROM aufgaben WHERE hau_id = ' . $zeile['hau_id'];

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_gruppe=mysql_query($sql_gruppe, $verbindung))
                {
                fehler();
                }

            if (mysql_num_rows($ergebnis_gruppe) == 0)
                {
                echo '<td align="center" style="border-left:1px solid grey;">&nbsp;</td>';
                }
            else
                {
                while ($zeile_gruppe=mysql_fetch_array($ergebnis_gruppe))
                    {
                    if ($zeile_gruppe['hau_terminaendern'] == 1)
                        {
                        echo
                            '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_termin.gif" alt="Termin wurde geändert und liegt hinter dem Plan-Termin" title="Termin wurde geändert und liegt hinter dem Plan-Termin"></td>';
                        }
                    else
                        {
                        echo '<td align="center" style="border-left:1px solid grey;">&nbsp;</td>';
                        }
                    }
                }
            break;

        // Gibt es ein PING für mich?

        case 'PING':

            $sql_ping='SELECT uls_ping_an FROM log_status ' .
                'LEFT JOIN log ON ulo_id = uls_uloid ' .
                'LEFT JOIN aufgaben ON hau_id = ulo_aufgabe ' .
                'WHERE hau_abschluss = 0 AND uls_ping_an = "' . $_SESSION['hma_id'] . '" AND hau_id = '
                . $zeile['hau_id'];

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_ping=mysql_query($sql_ping, $verbindung))
                {
                fehler();
                }

            if (mysql_num_rows($ergebnis_ping) != 0)
                {
                echo
                    '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_ping.gif" alt="PING von Kollegen" title="PING von Kollegen"></td>';
                }
            else
                {
                echo '<td align="center" style="border-left:1px solid grey;">&nbsp;</td>';
                }

            break;

        // Gibt es einen neuen Kommentar fuer mich?

        case 'Kommentar':

            $sql_ping='SELECT uls_komm_von FROM log_status ' .
                'LEFT JOIN log ON ulo_id = uls_uloid ' .
                'WHERE uls_komm_an = "' . $_SESSION['hma_id'] . '" AND ulo_aufgabe = ' . $zeile['hau_id'];

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_ping=mysql_query($sql_ping, $verbindung))
                {
                fehler();
                }

            if (mysql_num_rows($ergebnis_ping) != 0)
                {
                echo
                    '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_kommentar.gif" alt="new comment" title="new comment"></td>';
                }
            else
                {
                echo '<td align="center" style="border-left:1px solid grey;">&nbsp;</td>';
                }

            break;

        case 'Zuordnung':

            $sql_team='SELECT uau_ma_status FROM aufgaben_mitarbeiter ' .
                'INNER JOIN aufgaben ON hau_id = uau_hauid ' .
                'WHERE hau_aktiv = "1" AND hau_id = ' . $zeile['hau_id'] .
                ' GROUP BY hau_id';

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_team=mysql_query($sql_team, $verbindung))
                {
                fehler();
                }

            if (mysql_num_rows($ergebnis_team) == 0)
                {
                echo
                    '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_pool.gif" alt="task queued in Pool" title="task queued in Pool"></td>';
                }
            else
                {
                while ($zeile_team=mysql_fetch_array($ergebnis_team))
                    {
                    if ($zeile_team['uau_ma_status'] == 0)
                        {
                        echo
                            '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_offen.gif" alt="task mapped but queued" title="task mapped but queued"></td>';
                        }
                    else if ($zeile_team['uau_ma_status'] == 1)
                        {
                        echo
                            '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_akzeptiert.gif" alt="task in progress" title="task in progress"></td>';
                        }
                    else
                        {
                        echo
                            '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_abgelehnt.gif" alt="task rejected" title="task rejected"></td>';
                        }
                    }
                }
            break;

        case 'Zugewiesen':

            $sql_ping='SELECT uau_ma_status FROM aufgaben_mitarbeiter ' .
                'INNER JOIN aufgaben ON hau_id = uau_hauid ' .
                'WHERE hau_inhaber = "' . $_SESSION['hma_id'] . '" AND hau_aktiv = "1" AND hau_id = ' . $zeile['hau_id']
                .
                ' GROUP BY hau_id';

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_ping=mysql_query($sql_ping, $verbindung))
                {
                fehler();
                }

            if (mysql_num_rows($ergebnis_ping) == 0)
                {
                echo
                    '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_pool.gif" alt="task queued in Pool" title="task queued in Pool"></td>';
                }
            else
                {
                while ($zeile_ping=mysql_fetch_array($ergebnis_ping))
                    {
                    if ($zeile_ping['uau_ma_status'] == 0)
                        {
                        echo
                            '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_offen.gif" alt="task mapped but queued" title="task mapped but queued"></td>';
                        }
                    else if ($zeile_ping['uau_ma_status'] == 1)
                        {
                        echo
                            '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_akzeptiert.gif" alt="task in progress" title="task in progress"></td>';
                        }
                    else
                        {
                        echo
                            '<td align="center" style="border-left:1px solid grey;"><img src="bilder/icon_abgelehnt.gif" alt="task rejected" title="task rejected"></td>';
                        }
                    }
                }
            break;
        }
    }
?>
