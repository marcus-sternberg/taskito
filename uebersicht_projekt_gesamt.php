<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
error_reporting(E_ALL);

ini_set('display_errors', '1');

require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');

//var_dump(gd_info());

$Projekte=array();
$heute=date("Y-m-d", time());

$sql_projekt='SELECT hma_name, hpr_start, hpr_id, hpr_titel, hpr_pende FROM projekte LEFT JOIN mitarbeiter on hpr_inhaber = hma_id 
 WHERE hpr_fertig = 0 AND hpr_id > 10 and hpr_aktiv="1" ORDER BY hpr_titel';

if (isset($_GET['sortkey']))
    {
    if ($_SESSION['z'] == 1)
        {

        $sql_projekt=substr($sql_projekt, 0, my_strrpos($sql_projekt, "ORDER BY") + 9) . $_GET['sortkey'] . ' DESC,'
            . substr($sql_projekt, my_strrpos($sql_projekt, "ORDER BY") + 8);
        }

    if ($_SESSION['z'] > 1)
        {
        $sql_projekt=substr($sql_projekt, 0, my_strrpos($sql_projekt, "ORDER BY") + 9) . $_GET['sortkey'] . ' ASC,'
            . substr($sql_projekt, my_strrpos($sql_projekt, "ORDER BY") + 8);
        }
    }


// Frage Datenbank nach Suchbegriff
if (!$ergebnis_name=mysql_query($sql_projekt, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis_name);

echo
    '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Liste aller Projekte in der Implementierung [Anzahl: '
    . $anzahl
        . '] <br>&nbsp;&nbsp;&nbsp;<span class="text_klein">[<a href="uebersicht_projekt_timeline.php">zeige GANTT-Diagramm</a>]</span><br><br>';

while ($zeile_name=mysql_fetch_array($ergebnis_name))
    {
    $Projekte[$zeile_name['hpr_id']]['hpr_id'] = $zeile_name['hpr_id'];
    $Projekte[$zeile_name['hpr_id']]['hpr_titel']=$zeile_name['hpr_titel'];
    $Projekte[$zeile_name['hpr_id']]['hpr_pende']=$zeile_name['hpr_pende'];
    $Projekte[$zeile_name['hpr_id']]['hpr_start']=$zeile_name['hpr_start'];
    $Projekte[$zeile_name['hpr_id']]['hpr_inhaber']=$zeile_name['hma_name'];
    }

foreach ($Projekte as $element)
    {

    $sql = 'SELECT uau_tende from aufgaben_mitarbeiter RIGHT JOIN aufgaben ON hau_id = uau_hauid WHERE hau_hprid = '
        . $element['hpr_id']
            . ' AND uau_tende !="9999-01-01" AND uau_status = 0 AND hau_aktiv = 1 ORDER BY uau_tende DESC LIMIT 1';


    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {

        $Projekte[$element['hpr_id']]['hpr_tende']=$zeile['uau_tende'];
        }

    if (!(isset($Projekte[$element['hpr_id']]['hpr_tende'])))
        {
        $Projekte[$element['hpr_id']]['hpr_tende']='9999-01-01';
        }

    if ($Projekte[$element['hpr_id']]['hpr_tende']
        > $Projekte[$element['hpr_id']]['hpr_pende'] AND $Projekte[$element['hpr_id']]['hpr_tende'] != '9999-01-01')
        {
        $Projekte[$element['hpr_id']]['hpr_pende']=$Projekte[$element['hpr_id']]['hpr_tende'];
        }

    if ($Projekte[$element['hpr_id']]['hpr_tende'] == '9999-01-01')
        {
        $Projekte[$element['hpr_id']]['hpr_tende']='';
        }

    $sql=
        'SELECT COUNT(hau_id) AS anzahl from aufgaben LEFT JOIN aufgaben_mitarbeiter ON uau_hauid = hau_id WHERE hau_hprid = '
        . $element['hpr_id'] . ' AND hau_aktiv = 1 GROUP BY hau_hprid';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        $Projekte[$element['hpr_id']]['hau_anzahl']=$zeile['anzahl'];
        }

    if (!(isset($Projekte[$element['hpr_id']]['hau_anzahl'])))
        {
        $Projekte[$element['hpr_id']]['hau_anzahl']='0';
        }

    // ################## TASK ID

    $sql='SELECT DISTINCT hau_id, hau_hprid from aufgaben WHERE hau_hprid = ' . $element['hpr_id']
        . ' AND hau_aktiv = 1 ';

    $menge_prozent=0;

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {


        // ################## STATUS ID

        $sql_status = 'SELECT uau_status, uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid=' . $zeile['hau_id'];

        if (!$ergebnis_status=mysql_query($sql_status, $verbindung))
            {
            fehler();
            }

        while ($zeile_status=mysql_fetch_array($ergebnis_status))
            {

            // ################## PROZENT

            $sql_prozent = 'SELECT SUM( ulo_fertig ) AS prozent FROM log WHERE ulo_aufgabe = ' . $zeile['hau_id']
                . ' AND ulo_ma = ' . $zeile_status['uau_hmaid'] . ' GROUP BY ulo_aufgabe';


            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_prozent=mysql_query($sql_prozent, $verbindung))
                {
                fehler();
                }


            //   echo 'Schleife für :'.$zeile['hau_id'].'<br>';

            if (mysql_num_rows($ergebnis_prozent) == 0 AND $zeile_status['uau_status'] == 1)
                {
                $menge_prozent=$menge_prozent + 100;
                }

            while ($zeile_prozent=mysql_fetch_array($ergebnis_prozent))
                {
                if ($zeile_status['uau_status'] == 1)
                    {
                    $menge_prozent=$menge_prozent + 100;
                    }
                else
                    {
                    if ($zeile_prozent['prozent'] > 100)
                        {
                        $zeile_prozent['prozent']=100;
                        }
                    $menge_prozent=$menge_prozent + $zeile_prozent['prozent'];
                    }
                }

            if ($Projekte[$element['hpr_id']]['hau_anzahl'] == 0)
                {
                $Projekte[$element['hpr_id']]['hau_anzahl']=1;
                }
            $Projekte[$element['hpr_id']]['hpr_fertig']=($menge_prozent / $Projekte[$element['hpr_id']]['hau_anzahl']);

            // exit;
            } // Status schelife
        }     // task_id Schleife

    if (!(isset($Projekte[$element['hpr_id']]['hpr_fertig'])))
        {
        $Projekte[$element['hpr_id']]['hpr_fertig']='0';
        }
    }

echo '<table class="element" width="1000">';

echo '<tr>';

echo '<td class="tabellen_titel">Projekt</td>';

echo '<td class="tabellen_titel">Eigner</td>';

echo '<td class="tabellen_titel"><a href="' . $_SERVER['PHP_SELF'] . '?sortkey=hpr_start">Projektstart</a></td>';

echo '<td class="tabellen_titel"><a href="' . $_SERVER['PHP_SELF'] . '?sortkey=hpr_pende">Projektende</a></td>';

echo '<td class="tabellen_titel">Aufwand</td>';

echo '<td class="tabellen_titel">Status</td>';

echo '<td class="tabellen_titel">Diff.</td>';

echo '<td class="text_klein" width="50">O / S / &sum;</td>';

echo '<td class="tabellen_titel">&nbsp;</td>';

echo '</tr>';

foreach ($Projekte as $element)
    {

    $sql_gesamt = 'SELECT COUNT(hau_id) as anzahl_gesamt from aufgaben WHERE hau_hprid = ' . $element['hpr_id']
        . ' AND hau_aktiv = 1 ';

    if (!$ergebnis_gesamt=mysql_query($sql_gesamt, $verbindung))
        {
        fehler();
        }

    while ($zeile_gesamt=mysql_fetch_array($ergebnis_gesamt))
        {
        $anzahl_gesamt=$zeile_gesamt['anzahl_gesamt'];
        }

    $sql_stop=
        'SELECT COUNT(hau_id) as anzahl_stop from aufgaben LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid WHERE uau_status = 0 AND hau_hprid = '
        . $element['hpr_id'] . ' AND uau_stopp = 1 AND hau_aktiv = 1 ';

    if (!$ergebnis_stop=mysql_query($sql_stop, $verbindung))
        {
        fehler();
        }

    while ($zeile_stop=mysql_fetch_array($ergebnis_stop))
        {
        $anzahl_stop=$zeile_stop['anzahl_stop'];
        }

    $sql_offen='SELECT DISTINCT COUNT(hau_id) as anzahl_offen from aufgaben WHERE hau_abschluss = 0 AND hau_hprid = '
        . $element['hpr_id'] . ' AND hau_aktiv = 1';

// $sql_offen =    'SELECT hau_id, COUNT(*) as anzahl_offen from aufgaben LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid '.
//                 'WHERE (uau_status = 0 OR uau_status IS NULL) AND hau_hprid = '.$element['hpr_id'].' AND hau_aktiv = 1 '.
//                 'GROUP BY hau_id having count(*) > 1';

    if (!$ergebnis_offen=mysql_query($sql_offen, $verbindung))
        {
        fehler();
        }

    while ($zeile_offen=mysql_fetch_array($ergebnis_offen))
        {
        $anzahl_offen=$zeile_offen['anzahl_offen'];
        }

    $bgcolor='#D9E7F0';

    echo '<tr>';

    echo '<td class="text_klein" bgcolor="' . $bgcolor . '""><a href="uebersicht_projekt.php?hpr_id='
        . $element['hpr_id'] . '">' . $element['hpr_titel'] . '</a></td>';

    echo '<td class="text_klein" bgcolor="' . $bgcolor . '">' . $element['hpr_inhaber'] . '</td>';

    echo '<td class="text_klein" bgcolor="' . $bgcolor . '">' . datum_anzeigen($element['hpr_start']) . '</td>';

    echo '<td class="text_klein" bgcolor="' . $bgcolor . '">' . datum_anzeigen($element['hpr_pende']) . '</td>';
    // echo '<td class="text_klein" bgcolor="'.$bgcolor.'">'.datum_anzeigen($element['hpr_tende']).'</td>';


    ################

    $sql_effort='SELECT SUM(ulo_aufwand) AS effort FROM projekte ' .
        'INNER JOIN aufgaben ON hau_hprid = hpr_id ' .
        'INNER JOIN log ON hau_id = ulo_aufgabe ' .
        'WHERE hpr_id = ' . $element['hpr_id'];

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_effort=mysql_query($sql_effort, $verbindung))
        {
        fehler();
        }

    while ($zeile_effort=mysql_fetch_array($ergebnis_effort))
        {
        echo '<td class="text_klein" align="right" bgcolor="' . $bgcolor . '">'
            . (round($zeile_effort['effort'] / 60, 2)) . ' h</td>';
        }


    #################

    $drueber=0;

    if (($element['hpr_pende'] < $heute AND $element['hpr_pende'] != '9999-01-01' AND $element['hpr_tende']
        != '9999-01-01') AND $element['hpr_fertig'] < 100) //AND $element['hpr_tende']>0)
        {
        $drueber=1;
        $pende=explode("-", $element['hpr_pende']);
        $tende=explode("-", $element['hpr_tende']);
        $jetzt=explode("-", $heute);

        $pende_datum=mktime(0, 0, 0, $pende[1], $pende[2], $pende[0]);
        // $tende_datum = mktime(0, 0, 0, $tende[1], $tende[2], $tende[0]);
        $jetzt_datum=mktime(0, 0, 0, $jetzt[1], $jetzt[2], $jetzt[0]);

        echo '<td class="text_klein" bgcolor="#FFBFA0" align="center">' . floor(($jetzt_datum - $pende_datum) / 86400)
            . '</td>';
        }
    else
        {
        echo '<td class="text_klein" bgcolor="#D9E7F0">&nbsp;</td>';
        }

    if ($anzahl_offen > 0 AND $anzahl_offen != $anzahl_stop AND $drueber == 1)
        {
        $stopcolor='#FFBFA0';
        }
    else
        {
        $stopcolor='#C1E2A5';
        }

    echo '<td class="text_klein" bgcolor="' . $stopcolor . '">' . $anzahl_offen . ' / ' . $anzahl_stop . ' / '
        . $anzahl_gesamt . '</td>';

    if (($element['hpr_pende'] < $element['hpr_tende'] AND $element['hpr_fertig']
        < 100) OR ($element['hpr_pende'] < $heute AND $element['hpr_fertig'] < 100))
        {
        $deckung=1;
        }
    else
        {
        $deckung=2;
        }

    if ($element['hpr_pende'] == '9999-01-01')
        {
        $deckung=3;
        }

    echo '<td class="text_klein"><IMG SRC="balken.php?prozent=' . round($element['hpr_fertig'], 2) . '&deckung='
        . $deckung . '"></td>';

    if ($element['hpr_fertig'] >= 100 AND $anzahl_offen == 0 AND $anzahl_stop == 0)
        {
        echo '<td class="text_klein"><a href="schreibtisch_projekte_archivieren.php?hpr_id=' . $element['hpr_id']
            . '&toggle=liste"><IMG SRC="bilder/icon_erledigt.gif" border=0></a></td>';
        }
    else
        {
        echo '<td class="text_klein">&nbsp;</td>';
        }

    echo '</tr>';
    }

echo '</table>';

echo '<span class="text_klein">grün = in time / rot = Projekt ist überfällig / grau = kein Fälligkeitsdatum</span>';

include('segment_fuss.php');
?>