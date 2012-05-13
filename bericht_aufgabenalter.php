<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

########################  Definiere Variablen ################################

$zaehler=0;
$summe_horizontal=0;

#####################################################################################
############################ Ausgabe Werte ##########################################

// Gebe Überschrift aus

echo '<br><table class="element" cellpadding = "5">';

echo '<tr>';

echo '<td class="text_mitte">';

echo '<img src="bilder/block.gif">&nbsp;Bearbeitungszeitraum von Aufgaben';

echo '</td>';

echo '</tr>';

echo '</table>';

echo '<br><br>';

// Starte Tabelle

echo '<table border="1" class="element" width="600">';

echo '<tr>';

echo '<td colspan = "6" align="center" bgcolor = "#c9c9c9">Alter einer Aufgabe pro Mitarbeiter</td>';

echo '</tr>';

echo '<tr>';

echo '<td class="text">Name</td>';

echo '<td class="text" align="center">weniger als 30 d</td>';

echo '<td class="text" align="center">30 - 60 d</td>';

echo '<td class="text" align="center">60 - 90 d</td>';

echo '<td class="text" align="center">mehr denn 90 d</td>';

echo '<td class="text" align="center">Total</td>';

echo '</tr>';

$sql_ma=
    'SELECT hma_id, CONCAT(hma_name, ", ", hma_vorname) AS ma_name FROM mitarbeiter WHERE hma_aktiv = 1 AND hma_level > 2 ORDER BY ma_name';

if (!$ergebnis_ma=mysql_query($sql_ma, $verbindung))
    {
    fehler();
    }

while ($zeile_ma=mysql_fetch_array($ergebnis_ma))
    {

    $summe_horizontal = 0;

    echo '<tr>';

    echo '<td class="text">' . $zeile_ma['ma_name'] . '</td>';

    $sql='SELECT DISTINCT * FROM aufgaben ' .
        'LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid ' .
        'LEFT JOIN mitarbeiter ON uau_hmaid = hma_id ' .
        'WHERE hau_abschluss = 0 AND hma_level > 2 AND uau_hmaid = ' . $zeile_ma['hma_id'] .
        ' AND hau_anlage AND hau_anlage > DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY hau_id';

    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    $anzahl=mysql_num_rows($ergebnis);

    $summe_horizontal=$summe_horizontal + $anzahl;

    echo '<td class="text" align="right"><a href="bericht_aufgabenalter_detail.php?hma_id=' . $zeile_ma['hma_id']
        . '&zeitraum=30">' . $anzahl . '</a></td>';

    $sql='SELECT DISTINCT * FROM aufgaben ' .
        'LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid ' .
        'LEFT JOIN mitarbeiter ON uau_hmaid = hma_id ' .
        'WHERE hau_abschluss = 0 AND hma_level > 2 AND uau_hmaid = ' . $zeile_ma['hma_id'] .
        ' AND hau_anlage AND hau_anlage < DATE_SUB(NOW(), INTERVAL 30 DAY) AND hau_anlage > ((DATE_SUB(NOW(), INTERVAL 60 DAY))) GROUP BY hau_id';

    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    $anzahl=mysql_num_rows($ergebnis);

    $summe_horizontal=$summe_horizontal + $anzahl;

    echo '<td class="text" align="right"><a href="bericht_aufgabenalter_detail.php?hma_id=' . $zeile_ma['hma_id']
        . '&zeitraum=60">' . $anzahl . '</a></td>';

    $sql='SELECT DISTINCT * FROM aufgaben ' .
        'LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid ' .
        'LEFT JOIN mitarbeiter ON uau_hmaid = hma_id ' .
        'WHERE hau_abschluss = 0 AND hma_level > 2 AND uau_hmaid = ' . $zeile_ma['hma_id'] .
        ' AND hau_anlage AND hau_anlage < DATE_SUB(NOW(), INTERVAL 60 DAY) AND hau_anlage > ((DATE_SUB(NOW(), INTERVAL 90 DAY))) GROUP BY hau_id';

    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    $anzahl=mysql_num_rows($ergebnis);

    $summe_horizontal=$summe_horizontal + $anzahl;

    echo '<td class="text" align="right"><a href="bericht_aufgabenalter_detail.php?hma_id=' . $zeile_ma['hma_id']
        . '&zeitraum=90">' . $anzahl . '</a></td>';

    $sql='SELECT DISTINCT * FROM aufgaben ' .
        'LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid ' .
        'LEFT JOIN mitarbeiter ON uau_hmaid = hma_id ' .
        'WHERE hau_abschluss = 0 AND hma_level > 2 AND uau_hmaid = ' . $zeile_ma['hma_id'] .
        ' AND hau_anlage AND hau_anlage < DATE_SUB(NOW(), INTERVAL 90 DAY) GROUP BY hau_id';

    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    $anzahl=mysql_num_rows($ergebnis);
    $summe_horizontal=$summe_horizontal + $anzahl;

    echo '<td class="text" align="right"><a href="bericht_aufgabenalter_detail.php?hma_id=' . $zeile_ma['hma_id']
        . '&zeitraum=100">' . $anzahl . '</a></td>';

    echo '<td class="text" align="right">' . $summe_horizontal . '</td>';

    echo '<tr>';
    }

echo '</table>';

include('segment_fuss.php');
?>