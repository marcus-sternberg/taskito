<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
error_reporting(E_ALL);

# Baue allgemein gültige Grenzdaten

$min_jahr_start = date("Y-m-d", mktime(0,0,0,1,1,date("Y")-1));
$max_jahr_start = date("Y-m-d", mktime(0,0,0,1,1,date("Y")+1)); 

ini_set('display_errors', '1');
require_once('konfiguration.php');

# some settings
$font_face=dirname(__FILE__) . '/css/verdana.ttf'; // put the file in the same directory
$font_size=9;                                  //(int) pixels in GD 1, or points in GD 2
define("RAHMENSTAERKE", 1);
$abstand_pro_tag=15;
$abstand_pro_projekt=20;
# Calculate start of timescale

# Ask the DB for the longest projecttitle of the projects

$sql_projekt='SELECT LENGTH(hpr_titel) AS laenge FROM projekte 
                WHERE hpr_fertig = 0 AND hpr_id > 10 and hpr_aktiv="1"';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_projekt=mysql_query($sql_projekt, $verbindung))
    {
    fehler();
    }

$laenge=0;
$anzahlprojekte=mysql_num_rows($ergebnis_projekt);

while ($zeile_projekt=mysql_fetch_array($ergebnis_projekt))
    {
    if ($zeile_projekt['laenge'] > $laenge)
        {
        $laenge=$zeile_projekt['laenge'];
        }
    }
$laenge=$laenge * 8;
# Ask the DB for the oldest startdate of the projects

$sql_projekt='SELECT hpr_start FROM projekte 
                WHERE hpr_fertig = 0 AND hpr_id > 10 and hpr_aktiv="1" ORDER BY hpr_start ASC LIMIT 1';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_projekt=mysql_query($sql_projekt, $verbindung))
    {
    fehler();
    }

while ($zeile_projekt=mysql_fetch_array($ergebnis_projekt))
    {
    $projekt_start=$zeile_projekt['hpr_start'];
    }

# Ask the DB for the latest enddate of the projects

$sql_projekt='SELECT hpr_pende FROM projekte 
                WHERE hpr_fertig = 0 AND hpr_id > 10 and hpr_aktiv="1" ORDER BY hpr_pende DESC LIMIT 1';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_projekt=mysql_query($sql_projekt, $verbindung))
    {
    fehler();
    }

while ($zeile_projekt=mysql_fetch_array($ergebnis_projekt))
    {
    $projekt_ende=$zeile_projekt['hpr_pende'];
    }


if($projekt_start<$min_jahr_start) {$projekt_start=$min_jahr_start;}
if($projekt_start>$max_jahr_start) {$projekt_start=$max_jahr_start;} 
if($projekt_ende<$min_jahr_start) {$projekt_ende=$min_jahr_start;}
if($projekt_ende>$max_jahr_start) {$projekt_ende=$max_jahr_start;}  
 
$start_arr=explode("-", $projekt_start);
$start=mktime(0, 0, 0, $start_arr[1], $start_arr[2], $start_arr[0]);

$ende_arr=explode("-", $projekt_ende);
$ende=mktime(0, 0, 0, $ende_arr[1], $ende_arr[2], $ende_arr[0]);

$anzahltage=(($ende - $start) / 60 / 60 / 24) + 2;
 # echo $anzahltage;
$datum=$projekt_start;
$datum_arr=explode('-', $datum);
$timestamp=mktime(0, 0, 0, $datum_arr[1], $datum_arr[2], $datum_arr[0]);

$chart=ImageCreate($anzahltage * $abstand_pro_tag + $laenge,
    $anzahlprojekte * $abstand_pro_projekt + 100) or die("Cannot Initialize new GD image stream");

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

$sql_projekt='SELECT hpr_start, hpr_pende, hpr_titel FROM projekte 
                WHERE hpr_fertig = 0 AND hpr_id > 10 and hpr_aktiv="1" ORDER BY hpr_titel';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_projekt=mysql_query($sql_projekt, $verbindung))
    {
    fehler();
    }

$i=$abstand_pro_projekt + 30;
$zaehler=0;

while ($zeile_projekt=mysql_fetch_array($ergebnis_projekt))
    {
    $i = $i + $abstand_pro_projekt;
    $projekt_name=($zeile_projekt['hpr_titel']);

    if (fmod($zaehler, 2) == 1 && $zaehler > 0)
        {
        $hintergrundfarbe=$weiss;
        }
    else
        {
        $hintergrundfarbe=$hellgrau;
        }

    imagefilledrectangle($chart, 0, $i - $abstand_pro_projekt, $anzahltage * $abstand_pro_tag + $laenge, $i,
        $hintergrundfarbe);
    imagettftext($chart, $font_size, 0, 3, $i - 5, $black, $font_face, $projekt_name);
    $zaehler++;
    }

# draw the time line

for ($i=1; $i < $anzahltage; $i++)
    {

    # Check for current date
    if ($datum == date("Y-m-d"))
        {
        $text_color=$red;
        $line_color=$red;
        }
    else
        {
        $text_color=$black;
        $line_color=$black;
        }

    $tag=date("d.m.", $timestamp);
    $datum_arr=explode('-', $datum);
    $timestamp=mktime(0, 0, 0, $datum_arr[1], $datum_arr[2] + 1, $datum_arr[0]);
    $datum=date("Y-m-d", $timestamp);

    if (date("w", $timestamp) == 0 OR date("w", $timestamp) == 6)
        {
        imagefilledrectangle($chart, ($i * $abstand_pro_tag) + $laenge, 50, ($i
            * $abstand_pro_tag) + $laenge + $abstand_pro_tag, $anzahlprojekte * $abstand_pro_projekt + 50, $hellblau);
        }

    imageline($chart, ($i * $abstand_pro_tag) + $laenge, 50, ($i * $abstand_pro_tag) + 20 + $laenge, 50,
        $line_color); # x-linie
    imageline($chart, ($i * $abstand_pro_tag) + $laenge, 40, ($i * $abstand_pro_tag) + $laenge, 50,
        $line_color); # kurzer y-Marker
    imageline($chart, ($i * $abstand_pro_tag) + $laenge, 50, ($i * $abstand_pro_tag) + $laenge,
        $anzahlprojekte * $abstand_pro_projekt + 50, $line_color); # lange y-linie

    imagettftext($chart, $font_size, 90, ($i * $abstand_pro_tag) + $laenge + 5, 40, $text_color, $font_face, $tag);
    }

# Ask the DB for the projects sorted by customer

$sql_projekt_detail='SELECT hpr_start, hpr_pende, hpr_titel FROM projekte 
                WHERE hpr_fertig = 0 AND hpr_id > 10 and hpr_aktiv="1" ORDER BY hpr_titel';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_projekt_detail=mysql_query($sql_projekt_detail, $verbindung))
    {
    fehler();
    }
$i=$abstand_pro_projekt + 30;

while ($zeile_projekt_detail=mysql_fetch_array($ergebnis_projekt_detail))
    {
    if ($zeile_projekt_detail['hpr_pende'] < date("Y-m-d"))
        {
        $projektfarbe=$red;
        }
    else
        {
        $projektfarbe=$green;
        }

    $i=$i + $abstand_pro_projekt;
    $projekt_name=($zeile_projekt_detail['hpr_titel']);

    if($zeile_projekt_detail['hpr_start']<$min_jahr_start) {$zeile_projekt_detail['hpr_start']=$min_jahr_start;}
    if($zeile_projekt_detail['hpr_start']>$max_jahr_start) {$zeile_projekt_detail['hpr_start']=$max_jahr_start;}  
    if($zeile_projekt_detail['hpr_pende']<$min_jahr_start) {$zeile_projekt_detail['hpr_pende']=$min_jahr_start;}
    if($zeile_projekt_detail['hpr_pende']>$max_jahr_start) {$zeile_projekt_detail['hpr_pende']=$max_jahr_start;}  
       
    
    $projekt_start_detail=$zeile_projekt_detail['hpr_start'];
    $start_arr_detail=explode("-", $projekt_start_detail);
    $start_detail=mktime(0, 0, 0, $start_arr_detail[1], $start_arr_detail[2], $start_arr_detail[0]);

    $projekt_ende_detail=$zeile_projekt_detail['hpr_pende'];
    $ende_arr_detail=explode("-", $projekt_ende_detail);
    $ende_detail=mktime(0, 0, 0, $ende_arr_detail[1], $ende_arr_detail[2] + 1, $ende_arr_detail[0]);
        
    $anzahltage_projekt=($ende_detail - $start_detail) / 60 / 60 / 24;
    $anzahltage_kalenderstart_projekt=($start_detail - $start) / 60 / 60 / 24;

    imagefilledrectangle($chart, $laenge + $abstand_pro_tag + ($anzahltage_kalenderstart_projekt * $abstand_pro_tag),
        $i - $abstand_pro_projekt + 2, (($anzahltage_kalenderstart_projekt + $anzahltage_projekt) * $abstand_pro_tag)
        + $laenge, $i - 2, $projektfarbe);
    imagettftext($chart, $font_size, 0,
        $laenge + $abstand_pro_tag + ($anzahltage_kalenderstart_projekt * $abstand_pro_tag) + 10, $i - 5, $black,
        $font_face, $projekt_name);
    }

header("Content-type: image/png");
ImagePng($chart);
imagedestroy($chart);
?>