<?php
###### Editnotes ####
#$LastChangedDate: 2012-02-10 09:57:04 +0100 (Fr, 10 Feb 2012) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

########################  Definiere Variablen ################################

$ruecksprung = 'bericht_rufbereitschaft_abrechnung.php';
$abrechnung_bereitschaft = array();
$rb_pay = 0;

#####################################################################################

   
if(!isset($_POST['speichern']))
{

include('segment_kopf.php');


### Ermittle alle vorhandenen Einträge der Bereitschaft ###

## Zunächst ermittle den aktuellen Monat ##

// Frage gewünschten Monat ab

include('seg_abfrage_monat.php');

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


      
############################ Ausgabe Werte ##########################################

// Gebe Überschrift aus

echo '<br><table class="matrix" cellpadding = "5">';

echo '<tr>';

echo '<td class="text_mitte">';

echo '<img src="bilder/block.gif">&nbsp;Abrechnung der Rufbereitschaft';

echo '</td>';

echo '</tr>';

echo '</table>';

echo '<br><br>';

echo '<form action="bericht_rufbereitschaft_abrechnung.php" method="post">';
   
echo '<table class="matrix">';

echo '<tr><td>Name</td><td>Team</td><td>Bereitschaft [Tage]</td><td>Backup [Tage]</td><td>Betrag</td><td>abgerechnet</td><td>am</td></tr>';
   
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
     echo '<td valign="top">'.$zeile['hma_name'].', '.$zeile['hma_vorname'].'</td>';
     echo '<td valign="top">'.$zeile['ule_name'].'</td>';
    }
    
    echo  '<td valign="top" align="center">'.$abrechnung_bereitschaft[$ma_nummer]['bereit'].'</td>';
    echo  '<td valign="top" align="center">'.$abrechnung_bereitschaft[$ma_nummer]['backup'].'</td>';      
    
    $summe = ($abrechnung_bereitschaft[$ma_nummer]['bereit']*86) + ($abrechnung_bereitschaft[$ma_nummer]['backup']*35.17);
    
    $summe = number_format($summe, 2, ',', '.');
    
    echo  '<td valign="top">'.$summe.' €</td>';     
    
   # Prüfe, ob bereits abgerechnet wurde
   
   $sql_pay = 'SELECT har_abgerechnet, har_zeitstempel FROM abrechnung_rb WHERE har_jahr = "'.$xYear.'" AND har_monat = "'.$xMonth.'" AND har_hmaid = '.$ma_nummer;
   
    if (!$ergebnis_pay=mysql_query($sql_pay, $verbindung))
    {
    fehler();
    }
   
      while($zeile_pay=mysql_fetch_array($ergebnis_pay)) {
      $rb_pay = $zeile_pay['har_abgerechnet'];  
      $zeitstempel = $zeile_pay['har_zeitstempel'];  
      }
      echo '<td valign="middle" align="center">';
      
      if($rb_pay==1)
      {   echo '<input type="checkbox" checked name="abrechnung['.$ma_nummer.']">';}
      else 
      {   echo '<input type="checkbox" name="abrechnung['.$ma_nummer.']">';}
      echo '</td>';

      echo '<td valign="middle" align="center">';    
      if(isset($zeitstempel))
      {echo zeitstempel_anzeigen($zeitstempel);}
      else
      {echo '- -';}
      echo '</td>';    
            
   echo '</tr>';
  }
}
echo '<tr><td colspan="6" align="right"><input type="submit" value="Speichern" class="formularbutton" name="speichern"/></td></tr>';
echo '</table>';

echo '<input type="hidden" name="har_monat" value="'.$xMonth.'">';
echo '<input type="hidden" name="har_jahr" value="'.$xYear.'">';
$feld_der_rb_ma = implode('-', $ma_mit_rb);
echo '<input type="hidden" name="ma_menge" value="'.$feld_der_rb_ma.'">';
echo '</form>';
echo '<br><a href="bericht_rufbereitschaft_abrechnung_excel.php?xMonth='.$xMonth.'&xYear='.$xYear.'">In Excel öffnen</a>';

} else // Speichern wurde gedrückt, Werte sichern
{

$abgerechnet = array();
    
$formular_feld = explode('-',$_POST['ma_menge']);

foreach($formular_feld AS $ma_nummer)
{
    if(isset($_POST['abrechnung'][$ma_nummer])) 
    {    $abgerechnet[$ma_nummer]=1;}
    else
    {    $abgerechnet[$ma_nummer]=0;}    
}

foreach($abgerechnet AS $ma_nr=>$status)
{
    
    $sql = 'INSERT INTO abrechnung_rb (har_jahr, har_monat, har_hmaid, har_abgerechnet) VALUES 
             ("'.$_POST['har_jahr'].'", "'.$_POST['har_monat'].'", "'.$ma_nr.'", "'.$status.'")  
             ON DUPLICATE KEY UPDATE har_abgerechnet = "'.$status.'"';
             
            
    if (!$ergebnis=mysql_query($sql, $verbindung))
   {
     fehler();
   }
}
// Zurueck zur Liste

header ('Location: bericht_rufbereitschaft_abrechnung.php?xMonth='.$_POST['har_monat'].'&xYear='.$_POST['har_jahr']);
exit;    
    
}


include('segment_fuss.php');
?>
