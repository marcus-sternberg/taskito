<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

########################  Definiere Variablen ################################

$ruecksprung = 'bericht_rufbereitschaft_abrechnung.php';
$bereitschaftstage = array();
$rb_pay = 0;

#####################################################################################


if(!isset($_POST['speichern']))
{

include('segment_kopf.php');


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

// Frage gewünschten Monat ab

include('seg_abfrage_monat.php');

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

echo '<form action="bericht_rufbereitschaft_abrechnung.php" method="post">';
   
echo '<table class="matrix">';

echo '<tr><td>Name</td><td>Team</td><td>Bereitschaft [Tage]</td><td>Anzahl</td><td>Betrag</td><td>abgerechnet</td><td>am</td></tr>';
   
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
   echo '<td valign="top">'.$zeile_ma['hma_vorname'].' '.$zeile_ma['hma_name'].'</td><td valign="top">'.$zeile_ma['ule_kurz'].'</td>';
   }
   

   echo '<td valign="top">';
   foreach($rb[$ma_nummer] AS $rb_info)
   {
    $einsatz_diff = 0;
    echo datum_wandeln_useu($rb_info['start']).' - '.datum_wandeln_useu($rb_info['ende']);
    $zeitstempel_start = strtotime($rb_info['start']);
    $zeitstempel_ende = strtotime($rb_info['ende']);
    $dauer = $zeitstempel_ende - $zeitstempel_start;
    $einsatz_diff  = floor($dauer / (3600*24))+1;
    if($einsatz_diff > 3)
    {$berechnen[$ma_nummer][$xMonth]++;}       
    echo '  ['.$einsatz_diff.']';
       
   echo '<br>';
   }
   echo '</td>';
   echo '<td valign="bottom" align="center">'.$berechnen[$ma_nummer][$xMonth].' x</td>';
   echo '<td valign="bottom">'.($berechnen[$ma_nummer][$xMonth]*400).' €</td>';
   
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
