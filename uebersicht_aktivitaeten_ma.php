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

$ruecksprung='uebersicht_aktivitaeten_ma.php';
$Anzeige='MA';

include('seg_abfrage_kw_und_monat.php');

if (isset($_REQUEST['xJahr']))
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
        echo '<span class="text_mitte"><img src="bilder/block.gif">&nbsp;Summary Activities for '
            . $zeile['hma_vorname'] . ' ' . $zeile['hma_name'] . ' during ' . $anzeigestring . '<br><br>';
        }

    $Gruppenname='';

    echo '<table>';

    // Ermittle alle Aufgaben im Zeitraum

    $sql_aufgaben=
        'SELECT DISTINCT hau_ticketnr, hpr_id, ule_name, uty_name, hpr_titel, hau_id, hau_titel FROM aufgaben ' .
        'LEFT JOIN log ON hau_id = ulo_aufgabe ' .
        'LEFT JOIN projekte ON hau_hprid = hpr_id ' .
        'LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id ' .
        'LEFT JOIN level ON uaz_pg = ule_id ' .
        'LEFT JOIN typ ON hau_typ = uty_id ' .
        'WHERE  hau_aktiv = 1 AND ulo_ma = ' . $_REQUEST['hma_id'] . $filterstring_ua . ' AND YEAR(ulo_datum) = "'
        . $_REQUEST['xJahr'] . '" ORDER BY ulo_datum';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_aufgaben=mysql_query($sql_aufgaben, $verbindung))
        {
        fehler();
        }

    while ($zeile_aufgaben=mysql_fetch_array($ergebnis_aufgaben))
        {
        if ($Gesamtsumme != 0)
            {
            echo '<tr>';

            echo '<td class="text_klein" colspan="6" align="right">' . round($Bereichssumme / 60, 2) . ' h</td>';

            echo '</tr>';
            }

        echo '</table>';

        $Bereichssumme=0;
        $Zwischensumme=0;

        echo '<br><br><table  class="element" border=0 width="700">';

        echo '<tr>';

        echo '<td class="text_klein">Date</td>';

        echo '<td class="text_klein">Ticketnr.</td>';

        echo '<td class="text_klein">Sector</td>';

        echo '<td class="text_klein">Type</td>';

        echo '<td class="text_klein">Task</td>';

        echo '<td class="text_klein">Effort</td>';

        echo '</tr>';

        $sql_aufwand=
            'SELECT SUM(ulo_aufwand) as Anzahl, hau_typ, ulo_datum, uaz_pg FROM log INNER JOIN aufgaben ON hau_id = ulo_aufgabe '
            .
            'LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id ' .
            'LEFT JOIN level ON uaz_pg = ule_id ' .
            'WHERE ulo_ma = ' . $_REQUEST['hma_id'] . ' AND ulo_aufgabe = ' . $zeile_aufgaben['hau_id']
                . $filterstring_ua . ' AND YEAR(ulo_datum) = "' . $_REQUEST['xJahr']
                . '" GROUP BY ulo_aufgabe ORDER BY ulo_datum';


        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_aufwand=mysql_query($sql_aufwand, $verbindung))
            {
            fehler();
            }

        while ($zeile_aufwand=mysql_fetch_array($ergebnis_aufwand))
            {

            $Aufwand = $zeile_aufwand['Anzahl'];
            $Zwischensumme=$Aufwand;
            $ulo_datum=$zeile_aufwand['ulo_datum'];
            }

        // Ermittle, ob für die Aufgabe Bearbeiter existieren

        $sql_ma=
            'SELECT uau_id FROM aufgaben_mitarbeiter WHERE uau_status=0 AND uau_hauid = ' . $zeile_aufgaben['hau_id'];

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_ma=mysql_query($sql_ma, $verbindung))
            {
            fehler();
            }

        if (mysql_num_rows($ergebnis_ma) > 0)
            {
            $color='#FFBFA0';
            }
        else
            {
            $color='#C1E2A5';
            }

        if (round(($Aufwand) / 60) <= 1)
            {
            $Aufwand.=' min';
            }
        else
            {

            $Aufwand=round(($Aufwand) / 60, 2) . ' h';
            }

        echo '<tr>';

        echo '<td class="text_klein" bgcolor="' . $color . '" width="50">' . datum_anzeigen($ulo_datum) . '</td>';

        echo '<td class="text_klein" bgcolor="' . $color . '" width="50">' . $zeile_aufgaben['hau_ticketnr'] . '</td>';

        if ($zeile_aufgaben['hpr_id'] > 3)
            {
            echo '<td class="text_klein" bgcolor="' . $color . '" width="50"><a href="uebersicht_projekt.php?hpr_id='
                . $zeile_aufgaben['hpr_id'] . '">' . ($zeile_aufgaben['hpr_titel']) . '</a></td>';
            }
        else
            {
            echo '<td class="text_klein" bgcolor="' . $color . '" width="50">' . ($zeile_aufgaben['hpr_titel'])
                . '</td>';
            }

        echo '<td class="text_klein" bgcolor="' . $color . '" width="50">' . $zeile_aufgaben['ule_name'] . '</td>';

        echo '<td class="text_klein" bgcolor="' . $color . '" width="50">' . $zeile_aufgaben['uty_name'] . '</td>';

        echo '<td class="text_klein" bgcolor="' . $color . '"><a href="aufgabe_ansehen.php?hau_id='
            . $zeile_aufgaben['hau_id'] . '">' . ($zeile_aufgaben['hau_titel']) . '</a></td>';

        echo '<td class="text_klein" bgcolor="' . $color . '" width="50">' . $Aufwand . '</td>';

        echo '</tr>';

        $Bereichssumme=$Bereichssumme + $Zwischensumme;
        $Gesamtsumme=$Gesamtsumme + $Zwischensumme;
        }

    echo '<tr>';

    echo '<td class="text_klein" colspan="6" align="right">' . round($Bereichssumme / 60, 2) . ' h</td>';

    echo '</tr>';

    echo '</table>';
    }

echo '<table class="element" width="400">';

echo '<tr>';

echo '<td class="text_klein" colspan="6" align="right"><br>Total :' . round($Gesamtsumme / 60, 2) . ' h</td>';

echo '</tr>';

echo '</table>';

echo '<span class="text_klein">green = done / red = in progress</span>';
?>