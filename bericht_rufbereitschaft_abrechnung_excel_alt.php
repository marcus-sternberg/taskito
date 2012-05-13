<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
    $filename ="abrechnung_rufbereitschaft.xls";
    $contents = "testdata1 \t testdata2 \t testdata3 \t \n";
    $contents.= "testdata1 \t testdata2 \t testdata3 \t \n";


########################  Definiere Variablen ################################

$bereitschaftstage = array();
$rb_pay = 0;

############################ Ausgabe Werte ##########################################

if (isset($_REQUEST['xMonth']))
    {
    $xMonth=$_REQUEST['xMonth'];
    }
else
    {
    $xMonth=date('m');
    }

if (isset($_REQUEST['xYear']))
    {
    $xYear=$_REQUEST['xYear'];
    }
else
    {
    $xYear=date('Y');
    }

// Starte Tabelle

$starttag = $xYear.'-'.$xMonth.'-01';
$ma_mit_rb = array();
$rb_pro_ma = array();

$sql = 'SELECT hka_hmaid FROM `kalender` LEFT JOIN mitarbeiter ON hma_id = hka_hmaid WHERE hka_bereit = 1 AND 
hka_tag BETWEEN "'.$starttag.'" AND LAST_DAY("'.$starttag.'") '. #'DATE_ADD(LAST_DAY("'.$starttag.'")) '. #, INTERVAL 2 MONTH)
' ORDER BY hma_level, hma_name';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
     $ma_mit_rb[] = $zeile['hka_hmaid'];
    }
    $ma_mit_rb = array_values(array_unique($ma_mit_rb));
   # var_dump($ma_mit_rb);

   $excel_ausgabe = "Abrechnung \t Bereitschaft \t ".$xMonth."-".$xYear." \t \t \t \t \n \n";
   $excel_ausgabe.= "Name \t Team \t Bereitschaft |Tage| \t Anzahl \t Betrag EUR \t abgerechnet \t \n";

foreach($ma_mit_rb AS $ma_nummer)
{
$rb_pay = 0;
$index = 0;    
$tag=array();
$berechnen=array();

$sql = 'SELECT hka_tag FROM `kalender` WHERE hka_bereit = 1 AND 
hka_tag BETWEEN "'.$starttag.'" AND LAST_DAY("'.$starttag.'")
 AND hka_hmaid = '.$ma_nummer.' ORDER BY hka_tag';

 if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
   
      while($zeile=mysql_fetch_array($ergebnis)) {
       $tag[] = $zeile['hka_tag'];
   }
   
   $anzahl_tage = count($tag);
   $index_zaehler = 1;
   $rb[$ma_nummer][$index_zaehler]['start'] = $tag[0];
   $berechnen[$ma_nummer][$xMonth] = 0;
   
   for($i=0; $i<$anzahl_tage-1; $i++)
   {
    $zeitstempel_tag_1 = strtotime($tag[$i]);
    $zeitstempel_tag_2 = strtotime($tag[$i+1]);
    $differenz = $zeitstempel_tag_2 - $zeitstempel_tag_1;
    if($differenz == 82800 OR $differenz == 90000) {$differenz=86400;} // Sommerzeit / Winterzeit jeweils 1 h weniger / mehr
    $tage_diff  = floor($differenz / (3600*24));
    if($tage_diff > 1) // Sprung in der Datenreihenfolge
    {
        $rb[$ma_nummer][$index_zaehler]['ende'] = $tag[$i];
        $index_zaehler++;  
        $rb[$ma_nummer][$index_zaehler]['start'] = $tag[$i+1];
    }
   }
   $rb[$ma_nummer][$index_zaehler]['ende'] = $tag[$anzahl_tage-1];   
   
   $sql_ma = 'SELECT hma_name, hma_vorname, ule_kurz FROM mitarbeiter INNER JOIN level ON ule_id = hma_level WHERE hma_id = '.$ma_nummer;
   
    if (!$ergebnis_ma=mysql_query($sql_ma, $verbindung))
    {
    fehler();
    }
   
      while($zeile_ma=mysql_fetch_array($ergebnis_ma)) {
   $excel_ausgabe.= $zeile_ma['hma_vorname']." ".$zeile_ma['hma_name']." \t ".$zeile_ma['ule_kurz']." \t";
   }
   
   foreach($rb[$ma_nummer] AS $rb_info)
   {
    $einsatz_diff = 0;
    $excel_ausgabe.= datum_wandeln_useu($rb_info['start'])." - ".datum_wandeln_useu($rb_info['ende'])." | ";
    $zeitstempel_start = strtotime($rb_info['start']);
    $zeitstempel_ende = strtotime($rb_info['ende']);
    $dauer = $zeitstempel_ende - $zeitstempel_start;
    $einsatz_diff  = floor($dauer / (3600*24))+1;
    if($einsatz_diff > 3)
    {$berechnen[$ma_nummer][$xMonth]++;}       
    $excel_ausgabe.= $einsatz_diff." | ";
   }
   $excel_ausgabe.=" \t ";
   $excel_ausgabe.= $berechnen[$ma_nummer][$xMonth]." \t ";
   $excel_ausgabe.= ($berechnen[$ma_nummer][$xMonth]*400)." \t ";
   
   # Prüfe, ob bereits abgerechnet wurde
   
   $sql_pay = 'SELECT har_abgerechnet FROM abrechnung_rb WHERE har_jahr = "'.$xYear.'" AND har_monat = "'.$xMonth.'" AND har_hmaid = '.$ma_nummer;
   
       if (!$ergebnis_pay=mysql_query($sql_pay, $verbindung))
    {
    fehler();
    }
   
      while($zeile_pay=mysql_fetch_array($ergebnis_pay)) {
      $rb_pay = $zeile_pay['har_abgerechnet'];   
      }
      
      if($rb_pay==1)
      {   $excel_ausgabe.= "Ja \t ";}
      else 
      {   $excel_ausgabe.= "Nein \t ";}

      $excel_ausgabe.= " \n ";


}

    header('Content-type: application/ms-excel');
    header('Content-Disposition: attachment; filename='.$filename);
    echo $excel_ausgabe;

?>
