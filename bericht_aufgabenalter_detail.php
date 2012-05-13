<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

$hma_id=$_GET['hma_id'];

if (!(isset($_REQUEST['zeitraum'])) OR $_REQUEST['zeitraum'] == '')
    {
    $_REQUEST['zeitraum']='';
    $zeitfenster=' ';
    $color_all='#F29900';
    $zeit='not defined (all)';
    }
else
    {
    SWITCH ($_REQUEST['zeitraum'])
        {
        case 30:
            $zeitfenster=' AND hau_anlage > DATE_SUB(NOW(), INTERVAL 30 DAY) ';
            $color_30='#F29900';
            $zeit='< 30 days';
            break;

        case 60:
            $zeitfenster=
                ' AND hau_anlage < DATE_SUB(NOW(), INTERVAL 30 DAY) AND hau_anlage > ((DATE_SUB(NOW(), INTERVAL 60 DAY))) ';
            $color_60='#F29900';
            $zeit='between 30 and 60 days';
            break;

        case 90:
            $zeitfenster=
                ' AND hau_anlage < DATE_SUB(NOW(), INTERVAL 60 DAY) AND hau_anlage > ((DATE_SUB(NOW(), INTERVAL 90 DAY))) ';
            $color_90='#F29900';
            $zeit='between 60 and 90 days';
            break;

        case 100:
            $zeitfenster=' AND hau_anlage < DATE_SUB(NOW(), INTERVAL 90 DAY) ';
            $color_100='#F29900';
            $zeit='more than 90 days';
            break;
        }
    }

$sql='SELECT * FROM mitarbeiter WHERE hma_id =' . $hma_id;

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    $ma_name=$zeile['hma_name'] . ', ' . $zeile['hma_vorname'];
    }


// Gebe Überschrift aus
echo '<span class="text_mitte"><br><br><img src="bilder/block.gif">&nbsp;Details für die Aufgaben von ' . $ma_name
    . ' <br><span class="text_klein">[ Aufgabealter: ' . $zeit . ']</span><br><br>';

// Starte Tabelle

echo '<table border="1" class="element" width="600">';

echo '<tr>';

echo '<td>TNR</td>';

echo '<td>Prio</td>';

echo '<td>Aufgabe</td>';

echo '<td>angelegt</td>';

echo '<td>P-Ende</td>';

echo '<td>Eigner</td>';

echo '<td>Projekt</td>';

echo '<td>Gruppe</td>';

echo '<td>Typ</td>';

echo '<td>Status</td>';

echo '<tr>';

$sql='SELECT *, m1.hma_login AS inhaber, m2.hma_login AS mitarbeiter FROM aufgaben ' .
    'LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid ' .
    'LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id ' .
    'LEFT JOIN mitarbeiter m2 ON uau_hmaid = m2.hma_id ' .
    'LEFT JOIN prioritaet ON upr_nummer = hau_prio ' .
    'LEFT JOIN projekte ON hau_hprid = hpr_id ' .
    'LEFT JOIN typ ON hau_typ = uty_id ' .
    'LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id ' .
    'LEFT JOIN level ON uaz_pg = ule_id ' .
    'WHERE hau_abschluss = 0 AND m2.hma_id = ' . $hma_id .
    ' ' . $zeitfenster . 'GROUP BY hau_id ORDER BY hau_anlage ASC';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {

    $task_id = $zeile['hau_id'];
    include('segment_zeilenfarbe.php');

    echo '<tr>';

    echo '<td bgcolor="' . $color . '" class="' . $font . '"><a href="aufgabe_ansehen.php?hau_id=' . $zeile['hau_id']
        . '">' . $zeile['hau_id'] . '</td>';

    echo '<td bgcolor="' . $color . '" class="' . $font . '">' . $zeile['upr_name'] . '</td>';

    echo '<td bgcolor="' . $color . '" class="' . $font . '"><a href="aufgabe_ansehen.php?hau_id=' . $zeile['hau_id']
        . '">' . html_entity_decode($zeile['hau_titel']) . '</a></td>';

    echo '<td bgcolor="' . $color . '" class="' . $font . '">' . datum_anzeigen($zeile['hau_anlage']) . '</td>';

    echo '<td bgcolor="' . $color . '" class="' . $font . '">' . datum_anzeigen($zeile['hau_pende']) . '</td>';

    echo '<td bgcolor="' . $color . '" class="' . $font . '">' . $zeile['inhaber'] . '</td>';

    echo '<td bgcolor="' . $color . '" class="' . $font . '">' . $zeile['hpr_titel'] . '</td>';

    echo '<td bgcolor="' . $color . '" class="' . $font . '">' . $zeile['ule_name'] . '</td>';

    echo '<td bgcolor="' . $color . '" class="' . $font . '">' . $zeile['uty_name'] . '</td>';

    switch ($zeile['uau_stopp'])
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

    echo '</tr>';
    }

echo '</table>';

include('segment_fuss.php');
?>