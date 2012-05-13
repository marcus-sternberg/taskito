<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$ruecksprung='checkliste_uebersicht.php';

require_once('segment_kopf.php');
include('seg_abfrage_monat.php');

$zaehler=0;
$check_attribute=array();
$tage_des_monats=date('t', mktime(0, 0, 0, $xMonth, 1, $xYear));

echo '<br><table class="element" cellpadding = "5">';

echo '<tr>';

echo '<td class="text_mitte">';

echo '<img src="bilder/block.gif">&nbsp;Resultate der Checks im Monat ' . $xMonth . '.' . $xYear . '</td>';

echo '</tr>';

echo '</table>';

echo '<br>';

echo '<table border="0" cellspacing="3" cellpadding="0" class="matrix">';

# Berechne Tage des Monats

echo '<tr>';

echo '<td>&nbsp;</td>';

for ($tage=1; $tage <= $tage_des_monats; $tage++)
    {
    $hintergrundfarbe = '#ffffff';

    if (date('w', mktime(0, 0, 0, $xMonth, $tage, $xYear)) == 0 OR date('w', mktime(0, 0, 0, $xMonth, $tage, $xYear))
        == 6)
        {
        $hintergrundfarbe='#c9c9c9';
        }

    echo '<td bgcolor="' . $hintergrundfarbe . '"><a href="checkliste_neu.php?xDay=' . $tage . '&xMonth=' . $xMonth
        . '&xYear=' . $xYear . '">' . $tage . '</a></td>';
    }

echo '</tr>';
$zaehler=0;

# Lies die ID's der aktiven Checks ein

$sql='SELECT hck_id, hck_name, hck_url FROM checks WHERE hck_aktiv = 1';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<tr>';

    echo '<td width="300" bgcolor = "#c9c9c9">' . $zeile['hck_name'] . '</td>';

    for ($tage=1; $tage <= $tage_des_monats; $tage++)
        {
        # Suche den zugehörigen Status

        $sql_status = 'SELECT hcm_status FROM check_matrix 
        LEFT JOIN checklists ON hcm_hclid = hcl_id 
        WHERE hcm_hckid = ' . $zeile['hck_id']
            . ' AND DAY(hcl_datum)= ' . $tage . ' AND MONTH(hcl_datum) = "' . $xMonth . '" AND YEAR(hcl_datum)= "'
            . $xYear . '"';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_status=mysql_query($sql_status, $verbindung))
            {
            fehler();
            }

        if (mysql_num_rows($ergebnis_status) > 0)
            {
            $hcm_status=mysql_result($ergebnis_status, 0);

            switch ($hcm_status)
                {
                case 0:
                    $hintergrundfarbe='#2c2c2c';
                    break;

                case 1:
                    $hintergrundfarbe='#EE775F';
                    break;

                case 2:
                    $hintergrundfarbe='#FFF8B3';
                    break;

                case 3:
                    $hintergrundfarbe='#C1E2A5';
                    break;

                case 4:
                    $hintergrundfarbe='#CED1F0';
                    break;

                    default:
                           $hintergrundfarbe = '#2c2c2c';
                    break;
                }
            }
        else
            {

            $hintergrundfarbe='#2c2c2c';
            }

        if (date('w', mktime(0, 0, 0, $xMonth, $tage, $xYear))
            == 0 OR date('w', mktime(0, 0, 0, $xMonth, $tage, $xYear)) == 6)
            {
            $hintergrundfarbe='#c9c9c9';
            }

        echo '<td bgcolor="' . $hintergrundfarbe . '" width="20">&nbsp;</td>';
        }

    echo '</tr>';
    }

echo '</table>';
?>