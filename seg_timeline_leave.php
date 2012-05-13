<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

# some settings
$font_face=dirname(__FILE__) . '/css/verdana.ttf'; // put the file in the same directory
$font_size=9;                                  //(int) pixels in GD 1, or points in GD 2
define("RAHMENSTAERKE", 1);
$abstand_pro_tag=20;
$abstand_pro_task=20;
$laenge=300;
$jahr=date("Y");
$month=date("m");


# Calculate number of all leaves in that timeframe

$sql_task='SELECT hma_name, hma_vorname, uuv_startdatum, uuv_enddatum FROM mitarbeiter
                LEFT JOIN mitarbeiter_urlaub ON hma_id = uuv_hmaid
                WHERE (YEAR(uuv_startdatum) = "' . $jahr . '" AND MONTH(uuv_startdatum) = "' . $month . '")
                OR (YEAR(uuv_enddatum) = "' . $jahr . '" AND MONTH(uuv_enddatum) = "' . $month . '")
                ORDER BY hma_name DESC';

echo $sql_task;
exit;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_task=mysql_query($sql_task, $verbindung))
    {
    fehler();
    }

$anzahltasks=mysql_num_rows($ergebnis_task);

# Ask the DB for the oldest enddate of the tasks to calculate the earliest startdate for all tasks

$sql_task='SELECT hau_pende, hau_dauer FROM aufgaben
                WHERE hau_abschluss = 0 AND hau_kalender = 1 and hau_aktiv="1" ORDER BY hau_pende ASC LIMIT 1';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_task=mysql_query($sql_task, $verbindung))
    {
    fehler();
    }

while ($zeile_task=mysql_fetch_array($ergebnis_task))
    {
    $start_arr = explode("-", $zeile_task['hau_pende']);
    $start=mktime(0, 0, 0, $start_arr[1], $start_arr[2] - $zeile_task['hau_dauer'], $start_arr[0]);
    $start_datum=date("Y-m-d", $start);
    }

# Ask the DB for the youngest enddate of the tasks to calculate the latest enddate for all tasks

$sql_task='SELECT hau_pende FROM aufgaben
                WHERE hau_abschluss = 0 AND hau_kalender = 1 and hau_aktiv="1" ORDER BY hau_pende DESC LIMIT 1';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_task=mysql_query($sql_task, $verbindung))
    {
    fehler();
    }

while ($zeile_task=mysql_fetch_array($ergebnis_task))
    {
    $ende_arr = explode("-", $zeile_task['hau_pende']);
    $ende=mktime(0, 0, 0, $ende_arr[1], $ende_arr[2], $ende_arr[0]);
    }

$anzahltage=(($ende - $start) / 60 / 60 / 24) + 2;
$start_zeitstempel=$start;
$chart=@ImageCreate($anzahltage * $abstand_pro_tag + $laenge,
    $anzahltasks * $abstand_pro_task + 100) or die("Cannot Initialize new GD image stream");

# define colors
$background_color=ImageColorAllocate($chart, 255, 255, 255);
$black=ImageColorAllocate($chart, 0, 0, 0);
$red=ImageColorAllocate($chart, 255, 0, 0);
$weiss=ImageColorAllocate($chart, 255, 255, 255);
$hellblau=ImageColorAllocate($chart, 148, 211, 237);
$hellgrau=ImageColorAllocate($chart, 205, 203, 203);
$green=ImageColorAllocate($chart, 72, 163, 65);
$yellow=ImageColorAllocate($chart, 240, 235, 100);
$line_color=$black;

# Ask the DB for the projects sorted by customer

$sql_task=
    'SELECT hau_pende, hau_dauer, hau_titel, hma_login FROM aufgaben 
                LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid
                LEFT JOIN mitarbeiter ON hma_id = uau_hmaid  
                WHERE hau_abschluss = 0 AND hau_kalender = 1 and hau_aktiv="1" ORDER BY hma_login';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_task=mysql_query($sql_task, $verbindung))
    {
    fehler();
    }

$i=$abstand_pro_task + 30;
$zaehler=0;

while ($zeile_task=mysql_fetch_array($ergebnis_task))
    {
    $i = $i + $abstand_pro_task;
    $task_name=$zeile_task['hma_login'];

    if (fmod($zaehler, 2) == 1 && $zaehler > 0)
        {
        $hintergrundfarbe=$weiss;
        }
    else
        {
        $hintergrundfarbe=$hellgrau;
        }

    imagefilledrectangle($chart, 0, $i - $abstand_pro_task, $anzahltage * $abstand_pro_tag + $laenge, $i,
        $hintergrundfarbe);
    imagettftext($chart, $font_size, 0, 3, $i - 5, $black, $font_face, $task_name);
    $zaehler++;
    }

# draw the time line

for ($i=1; $i < $anzahltage; $i++)
    {

    # Check for current date
    if ($start_datum == date("Y-m-d"))
        {
        $text_color=$red;
        $line_color=$red;
        }
    else
        {
        $text_color=$black;
        $line_color=$black;
        }

    $tag=date("d.m.", $start_zeitstempel);
    $datum_arr=explode('-', $start_datum);
    $start_zeitstempel=mktime(0, 0, 0, $datum_arr[1], $datum_arr[2] + 1, $datum_arr[0]);
    $start_datum=date("Y-m-d", $start_zeitstempel);

    if (date("w", $start_zeitstempel) == 0 OR date("w", $start_zeitstempel) == 6)
        {
        imagefilledrectangle($chart, ($i * $abstand_pro_tag) + $laenge, 50, ($i * $abstand_pro_tag) + $laenge + 20,
            $anzahltasks * $abstand_pro_task + 50, $hellblau);
        }

    imageline($chart, ($i * $abstand_pro_tag) + $laenge, 50, ($i * $abstand_pro_tag) + 20 + $laenge, 50,
        $line_color); # x-linie
    imageline($chart, ($i * $abstand_pro_tag) + $laenge, 40, ($i * $abstand_pro_tag) + $laenge, 50,
        $line_color); # kurzer y-Marker
    imageline($chart, ($i * $abstand_pro_tag) + $laenge, 50, ($i * $abstand_pro_tag) + $laenge,
        $anzahltasks * $abstand_pro_task + 50, $line_color); # lange y-linie

    imagettftext($chart, $font_size, 90, ($i * $abstand_pro_tag) + $laenge + 5, 40, $text_color, $font_face, $tag);
    }

# Ask the DB for the projects sorted by customer

$sql_task_detail=
    'SELECT hau_pende, hau_dauer, hau_titel, hma_login, hau_nonofficetime FROM aufgaben 
                LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid
                LEFT JOIN mitarbeiter ON hma_id = uau_hmaid  
                WHERE hau_abschluss = 0 AND hau_kalender = 1 and hau_aktiv="1" ORDER BY hma_login';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_task_detail=mysql_query($sql_task_detail, $verbindung))
    {
    fehler();
    }
$i=$abstand_pro_task + 30;

while ($zeile_task_detail=mysql_fetch_array($ergebnis_task_detail))
    {
    if ($zeile_task_detail['hau_pende'] < date("Y-m-d"))
        {
        $projektfarbe=$red;
        }
    else if ($zeile_task_detail['hau_nonofficetime'] == 1)
        {
        $projektfarbe=$yellow;
        }
    else
        {
        $projektfarbe=$green;
        }

    $i=$i + $abstand_pro_task;
    $task_name=$zeile_task_detail['hau_titel'];

    $projekt_start_detail=$zeile_task_detail['hau_pende'];
    $start_arr_detail=explode("-", $projekt_start_detail);
    $start_detail=mktime(0, 0, 0, $start_arr_detail[1], $start_arr_detail[2] - $zeile_task_detail['hau_dauer'],
        $start_arr_detail[0]);

    $projekt_ende_detail=$zeile_task_detail['hau_pende'];
    $ende_arr_detail=explode("-", $projekt_ende_detail);
    $ende_detail=mktime(0, 0, 0, $ende_arr_detail[1], $ende_arr_detail[2] + 1, $ende_arr_detail[0]);

    $anzahltage_projekt=($ende_detail - $start_detail) / 60 / 60 / 24;
    $anzahltage_kalenderstart_projekt=($start_detail - $start) / 60 / 60 / 24;

    imagefilledrectangle($chart, $laenge + $abstand_pro_tag + ($anzahltage_kalenderstart_projekt * $abstand_pro_tag),
        $i - $abstand_pro_task + 2, (($anzahltage_kalenderstart_projekt + $anzahltage_projekt) * $abstand_pro_tag)
        + $laenge, $i - 2, $projektfarbe);
    imagettftext($chart, $font_size, 0,
        $laenge + $abstand_pro_tag + ($anzahltage_kalenderstart_projekt * $abstand_pro_tag) + 10, $i - 5, $black,
        $font_face, $task_name);
    }

header("Content-type: image/png");
ImagePng($chart);
?>
