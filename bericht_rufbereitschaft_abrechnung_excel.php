<?php
###### Editnotes ####
#$LastChangedDate: 2012-02-10 10:03:53 +0100 (Fr, 10 Feb 2012) $
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

// Berechne Vormonat

 switch($xMonth)
{
    case 1:
    $vormonat = 12;
    $xVorYear = $xYear-1;
    break;
      
    default:
    $vormonat = $xMonth-1;
    $xVorYear = $xYear; 
    break;    
}

// ermittle die letzten Tag des Vormonats und des gewählten Monats

$letzter_tag_vormonat = date("Y-m-t",strtotime($xVorYear.'-'.$vormonat.'-01'));
$letzter_tag_monat = date("Y-m-t",strtotime($xYear.'-'.$xMonth.'-01'));


## Bestimme alle Mitarbeiter-IDs, die in diesem Monat Bereitschaft hatten

$sql = 'SELECT DISTINCT(hka_hmaid) FROM `kalender`
                LEFT JOIN mitarbeiter ON hma_id = hka_hmaid     
        WHERE (hka_bereit = 1 OR hka_backup = 1) AND 
        hka_tag BETWEEN "'.$letzter_tag_vormonat.'" AND "'.$letzter_tag_monat.'"
        ORDER BY hma_name';   


        
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
     $ma_mit_rb[] = $zeile['hka_hmaid'];
    }

## Ermittle nun pro Mitarbeiter die einzelnen Daten
# Setze den Feldindex auf 0

$feldindex = 0;

foreach($ma_mit_rb AS $ma_id)
{

$rufbereitschaftstage = array();
$backupbereitschaftstage = array();
$anzahl_rufbereitschaft = 0;
$anzahl_backup = 0;
    
# Ermittle alle Tage Bereitschaft

        $sql = 'SELECT hka_tag FROM `kalender` 
                WHERE hka_bereit = 1 AND 
                hka_tag BETWEEN "'.$letzter_tag_vormonat.'" AND "'.$letzter_tag_monat.'" 
                AND hka_hmaid = '.$ma_id;
                
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
     $rufbereitschaftstage[] = $zeile['hka_tag'];
    }
    
$rufbereitschaftstage=array_unique($rufbereitschaftstage);

# Ermittle alle Tage Backup

        $sql = 'SELECT hka_tag FROM `kalender` 
                WHERE hka_backup = 1 AND 
                hka_tag BETWEEN "'.$letzter_tag_vormonat.'" AND "'.$letzter_tag_monat.'" 
                AND hka_hmaid = '.$ma_id;
                
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
     $backupbereitschaftstage[] = $zeile['hka_tag'];
    }
  
$backupbereitschaftstage=array_unique($backupbereitschaftstage);

foreach($rufbereitschaftstage AS $einzeltag) 
{ 
   $vorgaenger = date("Y-m-d", (strtotime($einzeltag)-86400));     
   if (in_array($vorgaenger, $rufbereitschaftstage)) 
         {$anzahl_rufbereitschaft++;} 
}   
    
$abrechnung_bereitschaft[$ma_id]['bereit'] = $anzahl_rufbereitschaft;
    
foreach($backupbereitschaftstage AS $einzeltag) 
{ 
   $vorgaenger = date("Y-m-d", (strtotime($einzeltag)-86400));     
   if (in_array($vorgaenger, $backupbereitschaftstage)) 
         {$anzahl_backup++;} 
} 

$abrechnung_bereitschaft[$ma_id]['backup'] = $anzahl_backup;

if($abrechnung_bereitschaft[$ma_id]['bereit'] <0) {$abrechnung_bereitschaft[$ma_id]['bereit'] = 0;}
if($abrechnung_bereitschaft[$ma_id]['backup'] <0) {$abrechnung_bereitschaft[$ma_id]['backup'] = 0;}  
}

   $excel_ausgabe = "Abrechnung \t Bereitschaft \t ".$xMonth."-".$xYear." \t \t \t \t \n \n";
   $excel_ausgabe.= "Name \t Team \t Bereitschaft |Tage| \t Backup |Tage| \t Betrag EUR \t abgerechnet \t \n";

foreach($ma_mit_rb AS $ma_nummer)
{
    
if(!($abrechnung_bereitschaft[$ma_nummer]['bereit']==0 AND $abrechnung_bereitschaft[$ma_nummer]['backup']==0))
{ 
    
$rb_pay = 0;
$index = 0;    
$tag=array();
$berechnen=array();

    
   $sql = 'SELECT hma_name, hma_vorname, ule_name FROM mitarbeiter LEFT JOIN level ON hma_level = ule_id 
           WHERE hma_id = '.$ma_nummer;

                
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
     $excel_ausgabe.= $zeile['hma_name'].', '.$zeile['hma_vorname']."\t ";
     $excel_ausgabe.= $zeile['ule_name']."\t ";
    }
    
    $excel_ausgabe.= $abrechnung_bereitschaft[$ma_nummer]['bereit']."\t ";
    $excel_ausgabe.= $abrechnung_bereitschaft[$ma_nummer]['backup']."\t ";      
    
    $summe = ($abrechnung_bereitschaft[$ma_nummer]['bereit']*86) + ($abrechnung_bereitschaft[$ma_nummer]['backup']*35.17);
    
    $excel_ausgabe.= $summe."\t ";  
   
   
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

}

    header('Content-type: application/ms-excel');
    header('Content-Disposition: attachment; filename='.$filename);
    echo $excel_ausgabe;

?>
