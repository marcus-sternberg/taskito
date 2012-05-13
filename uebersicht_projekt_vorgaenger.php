<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');

echo '<form action="uebersicht_projekt_vorgaenger.php" method="post">';

echo '<br><span class="text">Bitte das gewünschte Projekt auswählen: </span>';

echo '<select size="1" name="hpr_id">';
$sql='SELECT hpr_id, hpr_titel FROM projekte 
            WHERE hpr_fertig = 0 and hpr_aktiv="1" AND hpr_id > 10 ' .
    'ORDER BY hpr_titel';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    if ($zeile['hpr_id'] > 10)
        {
        echo '<option value="' . $zeile['hpr_id'] . '"><span class="text">' . $zeile['hpr_titel'] . '</span></option>';
        }
    }

echo '</select> ';

echo '<input type="submit" value="Zeige Projektaufgaben" class="formularbutton" />';

echo '</form><br>';


////////////////////// Beginne Anzeige /////////////////////////////

if (isset($_REQUEST['hpr_id']))
    {
    $check=0;

    echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Aufgaben für das gewählte Projekt<br><br>';

    $sql='SELECT hau_id, hau_ticketnr FROM projekte ' .
        'LEFT JOIN aufgaben ON hpr_id = hau_hprid ' .
        'LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid ' .
        'WHERE hpr_id = ' . $_REQUEST['hpr_id'] . ' AND hau_aktiv = 1 ' .
        'GROUP BY hau_id ' .
        'ORDER BY hau_id';

    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    echo '<form action="uebersicht_projekt_vorgaenger_speichern.php" method="post">';

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
                'LEFT JOIN projekte ON hpr_id = hau_hprid ' .
                'WHERE  hau_aktiv = 1 AND hau_id = ' . $zeile['hau_id'];

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
                $Reihe=$zeile_daten['hau_reihe'];

                echo '<tr>';

                echo '<td bgcolor="' . $color . '" class="text">' . $TNR . '</td>';

                if ($check != $zeile['hau_id'])
                    {
                    echo '<td bgcolor="' . $color . '" class="text"><a href="aufgabe_ansehen.php?hau_id='
                        . $zeile['hau_id'] . '">' . $Titel . '</a></td>';
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

                echo '<td bgcolor="' . $color . '" class="text"><input type="text" name="hau_reihe[' . $zeile['hau_id']
                    . ']" value="' . $Reihe . '"></td>';

                $check=$zeile['hau_id'];

                echo '</tr>';
                }
            } // Ende IS_NULL
        }

    echo '<tr>';

    echo '<td colspan="10" align="right">';

    echo '<input type="submit" value="Sichere Vorgänger" class="formularbutton" />';

    echo '</td>';

    echo '</tr>';

    echo '</table>';

    echo '</form><br>';
    }

echo '<br><span class="text">Bitte bei mehreren Vorgängern die Aufgabennummern durch # trennen.</span>';

include('segment_fuss.php');
?>