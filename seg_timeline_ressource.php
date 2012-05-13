<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$start=$_REQUEST['start'];

$ende=$_REQUEST['ende'];
$hma_id=$_REQUEST['hma_id'];

require_once('konfiguration.php');

# some settings
$font_face=dirname(__FILE__) . '/css/verdana.ttf'; // put the file in the same directory
$font_size=9;                                  //(int) pixels in GD 1, or points in GD 2
define("RAHMENSTAERKE", 1);
$abstand_pro_tag=30;
$mittig=$abstand_pro_tag / 2;
$abstand_pro_task=20;
$abstand_oben=20;
$abstand_unten=100;
$laenge=100;

# Berechne alle Aufgaben mit offenem Ende

$sql_task='SELECT DISTINCT(hau_id) FROM aufgaben
                LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid
                LEFT JOIN mitarbeiter ON hma_id = uau_hmaid  
                WHERE  hau_aktiv = 1 AND uau_status = 0 AND uau_hmaid = '
    . $hma_id . ' AND hau_pende = "9999-01-01"';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_task=mysql_query($sql_task, $verbindung))
    {
    fehler();
    }

$aufgaben_offenes_ende=mysql_num_rows($ergebnis_task);

# Berechne alle fälligen Aufgaben

$sql_task='SELECT DISTINCT(hau_id) FROM aufgaben
                LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid
                LEFT JOIN mitarbeiter ON hma_id = uau_hmaid  
                WHERE  hau_aktiv = 1 AND uau_status = 0 AND uau_hmaid = '
    . $hma_id . ' AND hau_pende < "' . $start . '"';


    
// Frage Datenbank nach Suchbegriff
if (!$ergebnis_task=mysql_query($sql_task, $verbindung))
    {
    fehler();
    }

$aufgaben_faellig=mysql_num_rows($ergebnis_task);

# Finde alle Aufgaben im Intervall

$sql_task='SELECT hau_pende, hau_dauer, hma_login FROM aufgaben
                LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid
                LEFT JOIN mitarbeiter ON hma_id = uau_hmaid  
                WHERE  hau_aktiv = 1 AND uau_status = 0 AND uau_hmaid = '
    . $hma_id . ' AND (hau_pende>="' . $start . '" AND DATE_SUB(hau_pende, INTERVAL (hau_dauer) DAY)<"' . $ende
        . '") ORDER BY hau_pende ASC';

#echo $sql_task;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_task=mysql_query($sql_task, $verbindung))
    {
    fehler();
    }

$menge=array();
$datum=$start;
$planende=array();
$dauer=array();

while ($zeile_task=mysql_fetch_array($ergebnis_task))
    {
    $planende[] = $zeile_task['hau_pende'];
    $dauer[]=(int)$zeile_task['hau_dauer'];
    }
#var_dump($planende);

for ($tag=1; $tag < 15; $tag++)
    {
    foreach ($planende AS $index => $taskdatum)
        {
        # echo $datum.'#'.$taskdatum.'<br>';
        if ($taskdatum == $datum)
            {
            $menge[$tag]=$menge[$tag] + 1;

            if ($dauer[$index] > 1)
                {
                for ($t=$tag - 1; $t > ($tag - $dauer[$index]); $t--)
                    {
                    $menge[$t]=$menge[$t] + 1;
                    }
                }
            }
        }

    $datum_arr=explode("-", $datum);
    $datum_zeitstempel=mktime(0, 0, 0, $datum_arr[1], $datum_arr[2] + 1, $datum_arr[0]);
    $datum=date("Y-m-d", $datum_zeitstempel);
    }

#var_dump($menge);

if(COUNT($menge)>0)
{

if (max($menge) > $aufgaben_offenes_ende AND max($menge) > $aufgaben_faellig)
    {
    $hoehe=max($menge);
    }
else if ($aufgaben_offenes_ende > $aufgaben_faellig)
    {
    $hoehe=$aufgaben_offenes_ende;
    }
else
    {
    $hoehe=$aufgaben_faellig;
    }

} else
{
  if ($aufgaben_offenes_ende > $aufgaben_faellig)
    {
    $hoehe=$aufgaben_offenes_ende;
    }
else
    {
    $hoehe=$aufgaben_faellig;
    }  
}
$anzahltage=14;

$chart=ImageCreate($anzahltage * $abstand_pro_tag + $laenge + 3 * $abstand_pro_tag,
    $hoehe * $abstand_pro_task + $abstand_oben + $abstand_unten) or die("Cannot Initialize new GD image stream");

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
$text_color=$black;

# Draw single tasks

for ($i=0; $i < $anzahltage; $i++)
    {
    if ($menge[$i] > 0)
        {   
        imagefilledrectangle($chart, $i * $abstand_pro_tag + $laenge + 2 * $abstand_pro_tag - $mittig,
            $hoehe * $abstand_pro_task + $abstand_oben, $i * $abstand_pro_tag + $laenge + 2 * $abstand_pro_tag
            + $mittig, ($hoehe * $abstand_pro_task) - ($menge[$i] * $abstand_pro_task) + $abstand_oben, $green);
          } else
          {  
                      imagefilledrectangle($chart, $i * $abstand_pro_tag + $laenge + 2 * $abstand_pro_tag - $mittig,
            1 * $abstand_pro_task + $abstand_oben, $i * $abstand_pro_tag + $laenge + 2 * $abstand_pro_tag
            + $mittig, (1* $abstand_pro_task) + $abstand_oben, $green);
          }
    }

# Draw open tasks

imagefilledrectangle($chart, $laenge + $abstand_pro_tag - $mittig, $hoehe * $abstand_pro_task + $abstand_oben,
    $laenge + $abstand_pro_tag + $mittig, ($hoehe * $abstand_pro_task) - ($aufgaben_offenes_ende * $abstand_pro_task)
    + $abstand_oben, $hellgrau);

# Draw due tasks

imagefilledrectangle($chart, $laenge + 2 * $abstand_pro_tag - $mittig, $hoehe * $abstand_pro_task + $abstand_oben,
    $laenge + 2 * $abstand_pro_tag + $mittig, ($hoehe * $abstand_pro_task) - ($aufgaben_faellig * $abstand_pro_task)
    + $abstand_oben, $red);

# Draw the y-axis
imageline($chart, $laenge, $hoehe * $abstand_pro_task + $abstand_oben, $laenge, 0, $line_color); # lange y-linie

for ($i=0; $i < $hoehe; $i++)
    {
    imageline($chart, $laenge, $i * $abstand_pro_task + $abstand_oben, $laenge - 5,
        $i * $abstand_pro_task + $abstand_oben, $line_color); # kurzer y-Marker
    imageline($chart, $laenge, $i * $abstand_pro_task + $abstand_oben,
        $laenge + ($anzahltage * $abstand_pro_tag) + 3 * $abstand_pro_tag, $i * $abstand_pro_task + $abstand_oben,
        $line_color); # lange y-linie
    imagettftext($chart, $font_size, 0, $laenge - 25, $i * $abstand_pro_task + $abstand_oben + 5, $text_color,
        $font_face, $hoehe - $i);
    }

# draw line for open and due tasks

imageline($chart, $laenge, $hoehe * $abstand_pro_task + $abstand_oben, $laenge + 3 * $abstand_pro_tag,
    $hoehe * $abstand_pro_task + $abstand_oben, $line_color); # x-linie

imageline($chart, $laenge + $abstand_pro_tag, $hoehe * $abstand_pro_task + $abstand_oben, $laenge + $abstand_pro_tag,
    $hoehe * $abstand_pro_task + 10 + $abstand_oben, $line_color); # kurzer y-Marker

imageline($chart, $laenge + 2 * $abstand_pro_tag, $hoehe * $abstand_pro_task + $abstand_oben,
    $laenge + 2 * $abstand_pro_tag, $hoehe * $abstand_pro_task + 10 + $abstand_oben, $line_color); # kurzer y-Marker

imagettftext($chart, $font_size, 90, $laenge + 2 * $abstand_pro_tag + 5,
    $hoehe * $abstand_pro_task + $abstand_oben + 50, $text_color, $font_face, "due");

imagettftext($chart, $font_size, 90, $laenge + $abstand_pro_tag + 5, $hoehe * $abstand_pro_task + $abstand_oben + 50,
    $text_color, $font_face, "open");

# Draw the week

$start_datum=$start;
$datum_arr=explode('-', $start_datum);
$start_zeitstempel=mktime(0, 0, 0, $datum_arr[1], $datum_arr[2], $datum_arr[0]);

for ($i=0; $i < $anzahltage; $i++)
    {
    $tag = date("d.m.", $start_zeitstempel);

    if (date("w", $start_zeitstempel) == 0 OR date("w", $start_zeitstempel) == 6)
        {
        $text_color=$hellgrau;
        }
    else
        {
        $text_color=$black;
        }
    $datum_arr=explode('-', $start_datum);
    $start_zeitstempel=mktime(0, 0, 0, $datum_arr[1], $datum_arr[2] + 1, $datum_arr[0]);
    $start_datum=date("Y-m-d", $start_zeitstempel);

    imageline($chart, ($i * $abstand_pro_tag) + $laenge + 3 * $abstand_pro_tag,
        $hoehe * $abstand_pro_task + $abstand_oben, ($i
        * $abstand_pro_tag) + $abstand_pro_tag + $laenge + 3 * $abstand_pro_tag,
        $hoehe * $abstand_pro_task + $abstand_oben, $line_color); # x-linie
    imageline($chart, ($i * $abstand_pro_tag) + $laenge + 3 * $abstand_pro_tag,
        $hoehe * $abstand_pro_task + $abstand_oben, ($i * $abstand_pro_tag) + $laenge + 3 * $abstand_pro_tag,
        $hoehe * $abstand_pro_task + 10 + $abstand_oben, $line_color); # kurzer y-Marker

    imagettftext($chart, $font_size, 90, ($i * $abstand_pro_tag) + $laenge + 5 + 3 * $abstand_pro_tag,
        $hoehe * $abstand_pro_task + 50 + $abstand_oben, $text_color, $font_face, $tag);
    }


/*

      
  $i = $i + $abstand_pro_task;  
  $task_name = $zeile_task_detail['hau_titel'];
  
  $projekt_start_detail = $zeile_task_detail['hau_pende'];
  $start_arr_detail = explode("-", $projekt_start_detail);
  $start_detail = mktime(0, 0, 0, $start_arr_detail[1], $start_arr_detail[2]-$zeile_task_detail['hau_dauer'], $start_arr_detail[0]);

  $projekt_ende_detail = $zeile_task_detail['hau_pende'];
  $ende_arr_detail = explode("-", $projekt_ende_detail);
  $ende_detail = mktime(0, 0, 0, $ende_arr_detail[1], $ende_arr_detail[2]+1, $ende_arr_detail[0]);

  $anzahltage_projekt=($ende_detail-$start_detail)/60/60/24; 
  $anzahltage_kalenderstart_projekt=($start_detail-$start)/60/60/24;

  imagefilledrectangle($chart,$laenge+$abstand_pro_tag+($anzahltage_kalenderstart_projekt*$abstand_pro_tag),$i-$abstand_pro_task+2,(($anzahltage_kalenderstart_projekt+$anzahltage_projekt)*$abstand_pro_tag)+$laenge,$i-2,$projektfarbe); 
  imagettftext($chart, $font_size, 0, $laenge+$abstand_pro_tag+($anzahltage_kalenderstart_projekt*$abstand_pro_tag)+10, $i-5, $black, $font_face, $task_name);
  
  }
  
*/
header("Content-type: image/png");
ImagePng($chart);
?>