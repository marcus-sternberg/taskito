<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

// Definiere Variablen

$Gesamtsumme=0;
$Zwischensumme=0;
$Bereichssumme=0;
$heute=date("Y-m-d");
$heute_zeitstempel_array=explode('-', $heute);
$heute_zeitstempel=
    mktime(0, 0, 0, $heute_zeitstempel_array[1], $heute_zeitstempel_array[2] + 1, $heute_zeitstempel_array[0]);
$start_zeitstempel=
    mktime(0, 0, 0, $heute_zeitstempel_array[1], $heute_zeitstempel_array[2] - 14, $heute_zeitstempel_array[0]);
$start=date("Y-m-d", $start_zeitstempel);
$ende_zeitstempel=
    mktime(0, 0, 0, $heute_zeitstempel_array[1], $heute_zeitstempel_array[2] + 14, $heute_zeitstempel_array[0]);
$ende=date("Y-m-d", $ende_zeitstempel);
$year=date("Y");

echo '<form action="uebersicht_ressource_ma.php" method="post">';

# Baue Tabelle

echo '<table border=0>';

echo '<tr>';

echo '<td class="text_klein">Mitarbeiter: </td><td>';

echo '<select size="1" name="hma_id">';

echo '<option value="-1"><span class="text">Bitte Teammitglied auswählen</span></option>';

$sql='SELECT hma_id, hma_name, hma_vorname FROM mitarbeiter WHERE hma_level >2 AND ' .
    'hma_aktiv = 1 ORDER BY hma_name';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    if (isset($_REQUEST['hma_id']) && $_REQUEST['hma_id'] == $zeile['hma_id'])
        {
        echo '<option value="' . $zeile['hma_id'] . '" selected><span class="text">' . $zeile['hma_name'] . ', '
            . $zeile['hma_vorname'] . '</span></option>';
        }
    else
        {
        echo '<option value="' . $zeile['hma_id'] . '"><span class="text">' . $zeile['hma_name'] . ', '
            . $zeile['hma_vorname'] . '</span></option>';
        }
    }

echo '</select>';

echo '</td>';

echo '<td align="right">';

echo '<input type="submit" value="Zeige Arbeitslast" class="formularbutton">';

echo '</td></tr>';

echo '</table>';

echo '</form>';

if (isset($_REQUEST['hma_id']))
    {

    $sql='SELECT hma_name, hma_vorname FROM mitarbeiter ' .
        'WHERE hma_id = ' . $_REQUEST['hma_id'];

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {

        // Starte Tabelle
        echo '<br><br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Aktuelle Arbeitslast '
            . $zeile['hma_vorname'] . ' ' . $zeile['hma_name'] . ' [2 Wochen]<br><br>';
        }

    echo '<img src="seg_timeline_ressource.php?start=' . $heute . '&ende=' . $ende . '&hma_id=' . $_REQUEST['hma_id']
        . '"/>';

    # Current Tasks

    echo '<br><br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Aktuelle Aufgaben<br><br>';

    $color='#C1E2A5';

    echo '<table class="element" width="1000">';

    echo '<tr>';

    echo '<td class="text_klein">Ticketnr.</td>';

    echo '<td class="text_klein">Aufgabe</td>';

    echo '<td class="text_klein">angelegt</td>';

    echo '<td class="text_klein">fällig zum</td>';

    echo '<td class="text_klein">Projekt</td>';

    echo '<td class="text_klein">Typ</td>';

    echo '<td class="text_klein">Dauer</td>';

    echo '</tr>';
    // Ermittle alle offenen im Zeitraum

    $sql_aufgaben=
        'SELECT DISTINCT hau_anlage, hau_pende, hau_dauer, hau_ticketnr, hpr_id, ule_name, uty_name, hpr_titel, hau_id, hau_titel FROM aufgaben '
        .
        'LEFT JOIN aufgaben_mitarbeiter ON uau_hauid = hau_id ' .
        'LEFT JOIN projekte ON hau_hprid = hpr_id ' .
        'LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id ' .
        'LEFT JOIN level ON uaz_pg = ule_id ' .
        'LEFT JOIN typ ON hau_typ = uty_id ' .
        'WHERE  hau_aktiv = 1 AND uau_status = 0 AND uau_hmaid = ' . $_REQUEST['hma_id'] . ' AND (hau_pende>="' . $heute
            . '" AND DATE_SUB(hau_pende, INTERVAL (hau_dauer) DAY)<"' . $ende . '") ORDER BY hau_pende ASC';


    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_aufgaben=mysql_query($sql_aufgaben, $verbindung))
        {
        fehler();
        }

    while ($zeile_aufgaben=mysql_fetch_array($ergebnis_aufgaben))
        {
        echo '<tr>';

        echo '<td class="text_klein" bgcolor="' . $color . '" >' . $zeile_aufgaben['hau_ticketnr'] . '</td>';

        echo '<td class="text_klein" bgcolor="' . $color . '"><a href="aufgabe_ansehen.php?hau_id='
            . $zeile_aufgaben['hau_id'] . '">' . ($zeile_aufgaben['hau_titel']) . '</a></td>';

        echo '<td class="text_klein" bgcolor="' . $color . '" >' . datum_anzeigen($zeile_aufgaben['hau_anlage'])
            . '</td>';

        echo '<td class="text_klein" bgcolor="' . $color . '" >' . datum_anzeigen($zeile_aufgaben['hau_pende'])
            . '</td>';

        if ($zeile_aufgaben['hpr_id'] > 10)
            {
            echo '<td class="text_klein" bgcolor="' . $color . '" ><a href="uebersicht_projekt.php?hpr_id='
                . $zeile_aufgaben['hpr_id'] . '">' . ($zeile_aufgaben['hpr_titel']) . '</a></td>';
            }
        else
            {
            echo '<td class="text_klein" bgcolor="' . $color . '" >' . $zeile_aufgaben['hpr_titel'] . '</td>';
            }

        echo '<td class="text_klein" bgcolor="' . $color . '" >' . $zeile_aufgaben['uty_name'] . '</td>';

        echo '<td class="text_klein" bgcolor="' . $color . '" >' . $zeile_aufgaben['hau_dauer'] . ' d</td>';

        echo '</tr>';
        }

    echo '<tr>';

    echo '</table>';

    # Overdue Tasks

    echo '<br><br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Überfällige Aufgaben<br><br>';

    $color='#FFBFA0';

    echo '<table class="element" width="1000">';

    echo '<tr>';

    echo '<td class="text_klein">Ticketnr.</td>';

    echo '<td class="text_klein">Aufgabe</td>';

    echo '<td class="text_klein">angelegt</td>';

    echo '<td class="text_klein">fällig zum</td>';

    echo '<td class="text_klein">Projekt</td>';

    echo '<td class="text_klein">Typ</td>';

    echo '<td class="text_klein">Dauer</td>';

    echo '</tr>';
    // Ermittle alle offenen im Zeitraum

    $sql_aufgaben=
        'SELECT DISTINCT hau_anlage, hau_pende, hau_dauer, hau_ticketnr, hpr_id, ule_name, uty_name, hpr_titel, hau_id, hau_titel FROM aufgaben '
        .
        'LEFT JOIN aufgaben_mitarbeiter ON uau_hauid = hau_id ' .
        'LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id ' .
        'LEFT JOIN projekte ON hau_hprid = hpr_id ' . 
        'LEFT JOIN level ON uaz_pg = ule_id ' .
        'LEFT JOIN typ ON hau_typ = uty_id ' .
        'WHERE  hau_aktiv = 1 AND uau_status = 0 AND uau_hmaid = ' . $_REQUEST['hma_id'] . ' AND hau_pende < "' . $heute
            . '"ORDER BY hau_pende';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_aufgaben=mysql_query($sql_aufgaben, $verbindung))
        {
        fehler();
        }

    while ($zeile_aufgaben=mysql_fetch_array($ergebnis_aufgaben))
        {
        echo '<tr>';

        echo '<td class="text_klein" bgcolor="' . $color . '" >' . $zeile_aufgaben['hau_ticketnr'] . '</td>';

        echo '<td class="text_klein" bgcolor="' . $color . '"><a href="aufgabe_ansehen.php?hau_id='
            . $zeile_aufgaben['hau_id'] . '">' . ($zeile_aufgaben['hau_titel']) . '</a></td>';

        echo '<td class="text_klein" bgcolor="' . $color . '" >' . datum_anzeigen($zeile_aufgaben['hau_anlage'])
            . '</td>';

        echo '<td class="text_klein" bgcolor="' . $color . '" >' . datum_anzeigen($zeile_aufgaben['hau_pende'])
            . '</td>';

        if ($zeile_aufgaben['hpr_id'] > 10)
            {
            echo '<td class="text_klein" bgcolor="' . $color . '" ><a href="uebersicht_projekt.php?hpr_id='
                . $zeile_aufgaben['hpr_id'] . '">' . ($zeile_aufgaben['hpr_titel']) . '</a></td>';
            }
        else
            {
            echo '<td class="text_klein" bgcolor="' . $color . '" >' . $zeile_aufgaben['hpr_titel'] . '</td>';
            }

        echo '<td class="text_klein" bgcolor="' . $color . '" >' . $zeile_aufgaben['uty_name'] . '</td>';

        echo '<td class="text_klein" bgcolor="' . $color . '" >' . $zeile_aufgaben['hau_dauer'] . ' d</td>';

        echo '</tr>';
        }

    echo '<tr>';

    echo '</table>';

    # OPEN TASKS

    echo '<br><br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Aufgaben ohne Endtermin<br><br>';

    $color='#CED9E7';

    echo '<table class="element" width="1000">';

    echo '<tr>';

    echo '<td class="text_klein">Ticketnr.</td>';

    echo '<td class="text_klein">Aufgabe</td>';

    echo '<td class="text_klein">angelegt</td>';

    echo '<td class="text_klein">fällig zum</td>';

    echo '<td class="text_klein">Projekt</td>';

    echo '<td class="text_klein">Typ</td>';

    echo '<td class="text_klein">Dauer</td>';

    echo '</tr>';
    // Ermittle alle offenen im Zeitraum

    $sql_aufgaben=
        'SELECT DISTINCT hau_anlage, hau_pende, hau_dauer, hau_ticketnr, hpr_id, ule_name, uty_name, hpr_titel, hau_id, hau_titel FROM aufgaben '
        .
        'LEFT JOIN aufgaben_mitarbeiter ON uau_hauid = hau_id ' .
        'LEFT JOIN projekte ON hau_hprid = hpr_id ' .
        'LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id ' .
        'LEFT JOIN level ON uaz_pg = ule_id ' .
        'LEFT JOIN typ ON hau_typ = uty_id ' .
        'WHERE  hau_aktiv = 1 AND uau_status = 0 AND uau_hmaid = ' . $_REQUEST['hma_id']
            . ' AND hau_pende = "9999-01-01"ORDER BY hau_pende';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_aufgaben=mysql_query($sql_aufgaben, $verbindung))
        {
        fehler();
        }

    while ($zeile_aufgaben=mysql_fetch_array($ergebnis_aufgaben))
        {
        echo '<tr>';

        echo '<td class="text_klein" bgcolor="' . $color . '" >' . $zeile_aufgaben['hau_ticketnr'] . '</td>';

        echo '<td class="text_klein" bgcolor="' . $color . '"><a href="aufgabe_ansehen.php?hau_id='
            . $zeile_aufgaben['hau_id'] . '">' . ($zeile_aufgaben['hau_titel']) . '</a></td>';

        echo '<td class="text_klein" bgcolor="' . $color . '" >' . datum_anzeigen($zeile_aufgaben['hau_anlage'])
            . '</td>';

        echo '<td class="text_klein" bgcolor="' . $color . '" >' . datum_anzeigen($zeile_aufgaben['hau_pende'])
            . '</td>';

        if ($zeile_aufgaben['hpr_id'] > 10)
            {
            echo '<td class="text_klein" bgcolor="' . $color . '" ><a href="uebersicht_projekt.php?hpr_id='
                . $zeile_aufgaben['hpr_id'] . '">' . $zeile_aufgaben['hpr_titel'] . '</a></td>';
            }
        else
            {
            echo '<td class="text_klein" bgcolor="' . $color . '" >' . ($zeile_aufgaben['hpr_titel']) . '</td>';
            }

        echo '<td class="text_klein" bgcolor="' . $color . '" >' . $zeile_aufgaben['uty_name'] . '</td>';

        echo '<td class="text_klein" bgcolor="' . $color . '" >' . $zeile_aufgaben['hau_dauer'] . ' d</td>';

        echo '</tr>';
        }

    echo '<tr>';

    echo '</table>';
    }
?>