<?php
###### Editnotes ####
#$LastChangedDate: 2011-10-12 11:11:37 +0200 (Mi, 12 Okt 2011) $
#$Author: msternberg $ 
#####################
error_reporting(E_ALL);
$session_frei = 1;
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');
function easter($year = null)
    {
    #if(is_null($year)) $year = $this->getYear();

    if (strlen(strval($year)) == 2)
        {
        if ($year < 70)
            $year+=2000;
        else
            $year+=1900;
        }

    if ($year > 2038 || $year < 1901)
        return false; # limitations of date()/mktime(), if OS == Win change 1901 to 1970!
    $d=(((255 - 11 * ($year % 19)) - 21) % 30) + 21;
    $delta=$d + ($d > 48) + 6 - (($year + $year / 4 + $d + ($d > 48) + 1) % 7);
    $easter=strtotime("+$delta days", mktime(0, 0, 0, 3, 1, $year));
    return $easter;
    }

function getGermanPublicHolidays($year = null)
    {
    #if(is_null($year)) $year = $this->getYear();

    if (!$easter=easter($year))
        return false;
    else
        {
        $holidays['neujahr']=mktime(0, 0, 0, 1, 1, $year);
        $holidays['tagderarbeit']=mktime(0, 0, 0, 5, 1, $year);
        $holidays['karfreitag']=strtotime("-2 days", $easter);
        $holidays['ostermontag']=strtotime("+1 day", $easter);
        $holidays['himmelfahrt']=strtotime("+39 days", $easter);
        $holidays['pfingstmontag']=strtotime("+50 days", $easter);
        $holidays['tagdereinheit']=mktime(0, 0, 0, 10, 3, $year);
        #         $holidays['heiligabend']            = mktime(0,0,0,12,24,$year);
        $holidays['weihnachtsfeiertag1']=mktime(0, 0, 0, 12, 25, $year);
        $holidays['weihnachtsfeiertag2']=mktime(0, 0, 0, 12, 26, $year);
        #         $holidays['sylvester']                = mktime(0,0,0,12,31,$year);
        return $holidays;
        }
    }

$ruecksprung="verwaltung_urlaub_gesamt.php";

include('seg_abfrage_monat.php');

if (isset($_REQUEST['xShort'])) // zeige Ausschnitt
    {
    $heute=date("d.m.Y");
    $spalten='22';
    $zurueck10=date('d.m.Y', strtotime('-5 days', strtotime($heute)));
    $vor20=date('d.m.Y', strtotime('+15 days', strtotime($heute)));
    $xMonth=date("m");
    }
else   // zeige ganzen Monat
    {
    $ende=date('t', strtotime('01.' . $xMonth . '.' . $xYear));
    $spalten=$ende + 11;
    $zurueck10=date('d.m.Y', strtotime('-5 days', strtotime('01.' . $xMonth . '.' . $xYear)));
    $vor20=date('d.m.Y', strtotime('+5 days', strtotime($ende . '.' . $xMonth . '.' . $xYear)));
    }

$feiertage=getGermanPublicHolidays($xYear);

#$xMonth=0;
$kalender=array(); // Definiere das Kalenderfeld

$datumarray=explode(".", $zurueck10);

$starttag=$datumarray[0];
$startmonat=$datumarray[1];
$startjahr=$datumarray[2];

$datumarray=explode(".", $vor20);

$endetag=$datumarray[0];
$endemonat=$datumarray[1];
$endejahr=$datumarray[2];

$vdate=mktime(0, 0, 0, $startmonat, $starttag, $startjahr);
$bdate=mktime(0, 0, 0, $endemonat, $endetag, $endejahr);

$tage=($bdate - $vdate) / 86400;

$sql_level='SELECT ule_id FROM level WHERE ule_id > 1 AND ule_id < 99 AND ule_aktiv = 1 AND ule_id < 99';

     // Frage Datenbank nach Suchbegriff
if (!$ergebnis_level=mysql_query($sql_level, $verbindung))
    {
    fehler();
    }

while ($zeile_level=mysql_fetch_array($ergebnis_level))
    {

    $sql_ma = 'SELECT hma_id FROM mitarbeiter WHERE hma_aktiv = 1 AND hma_level = ' . $zeile_level['ule_id'];

  
    
    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_ma=mysql_query($sql_ma, $verbindung))
        {
        fehler();
        }

    while ($zeile_ma=mysql_fetch_array($ergebnis_ma))
        {

        $sql_eintrag = 'SELECT * FROM kalender WHERE hka_hmaid = ' . $zeile_ma['hma_id'];

        
        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_eintrag=mysql_query($sql_eintrag, $verbindung))
            {
            fehler();
            }
            
        if (mysql_num_rows($ergebnis_eintrag) > 0)
            {
            while ($zeile_eintrag=mysql_fetch_array($ergebnis_eintrag))
                {
                for ($i=0; $i <= $tage; $i++)
                    {

                    $testdate = mktime(0, 0, 0, $startmonat, $starttag + $i, $startjahr);
                    $test=date("Y-m-d", $testdate);

                    If ($zeile_eintrag["hka_tag"] == $test)
                        {
                        if ((!isset(
                            $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Bereitschaft']))
                                OR ($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Bereitschaft']) == 0)
                            {
                            $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Bereitschaft']
                                =$zeile_eintrag['hka_bereit'];
                            }

                        if ((!isset(
                            $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Backup']))
                                OR ($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Backup']) == 0)
                            {
                            $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Backup']
                                =$zeile_eintrag['hka_backup'];
                            }    
                    
                            
                        if ((!isset(
                            $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Schicht'])) OR ($kalender[
                                $zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Schicht']) == 1)
                            {
                            $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Schicht']
                                =$zeile_eintrag['hka_schicht'];
                            }

                        if ((!isset(
                            $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Abwesend'])) OR ($kalender[
                                $zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Abwesend']) == 0)
                            {
                            $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Abwesend']
                                =$zeile_eintrag['hka_abwesend'];
                            }

                        if ((!isset(
                            $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Release'])) OR ($kalender[
                                $zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Release']) == 0)
                            {
                            $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Release']
                                =$zeile_eintrag['hka_release'];
                            }
                        }
                    else
                        {
                        if (!isset($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Bereitschaft']))
                            {
                            $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Bereitschaft']=0;
                            }
                        if (!isset($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Backup']))
                            {
                            $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Backup']=0;
                            }
                        if (!isset($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Schicht']))
                            {
                            $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Schicht']=1;
                            }

                        if (!isset($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Abwesend']))
                            {
                            $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Abwesend']=0;
                            }

                        if (!isset($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Release']))
                            {
                            $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Release']=0;
                            }
                        } // IF im Datum
        
                    }     // For Tage Schleife
                }         // Eintrag
            }
        else
            {
            for ($i=0; $i <= $tage; $i++)
                {

                $testdate = mktime(0, 0, 0, $startmonat, $starttag + $i, $startjahr);
                $test=date("Y-m-d", $testdate);

                if (!isset($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Bereitschaft']))
                    {
                    $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Bereitschaft']=0;
                    }
                if (!isset($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Backup']))
                    {
                    $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Backup']=0;
                    }
                if (!isset($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Schicht']))
                    {
                    $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Schicht']=1;
                    }

                if (!isset($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Abwesend']))
                    {
                    $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Abwesend']=0;
                    }

                if (!isset($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Release']))
                    {
                    $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Release']=0;
                    }
                } // For Tage Schleife
            }
        }         // Ende MA

    }             // Ende Level
   
echo
    '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Teamkalender <a href="verwaltung_urlaub_gesamt.php?xShort=1">(15 Tagezeitraum aktuell)</a><br><br>';

################### Beginne Tabellenausgabe ######################################

echo '<table id="Kalender">';

echo '<tr>';

echo '<td>&nbsp;</td>';

# Baue Hauptspalten Tage

for ($i=0; $i <= $tage; $i++)
    {
    $testdate = mktime(0, 0, 0, $startmonat, $starttag + $i, $startjahr);
    $test=date("d.m.Y", $testdate);

    if ($test == date("d.m.Y"))
        {
        echo '<th class="cu">' . date("D", $testdate) . '<br>' . date("d.m.", $testdate) . '</td>';
        }
    else if ((date("N", $testdate) > 5) OR (in_array($testdate, $feiertage)))
        {
        echo '<th class="we">' . date("D", $testdate) . '<br>' . date("d.m.", $testdate) . '</td>';
        }
    else
        {
        echo '<th>' . date("D", $testdate) . '<br>' . date("d.m.", $testdate) . '</td>';
        }
    }

echo '</tr>';

echo '</tr>';

# Gebe Teamlevel aus

$sql_level='SELECT ule_id, ule_name FROM level WHERE ule_id > 1 AND ule_id < 99 AND ule_aktiv = 1 ORDER BY ule_sort';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_level=mysql_query($sql_level, $verbindung))
    {
    fehler();
    }

while ($zeile_level=mysql_fetch_array($ergebnis_level))
    {
    echo '<tr>';

    echo '<td colspan="' . $spalten . '" class="text" bgcolor="#EDCA5F" align="center">' . $zeile_level['ule_name']
        . '</td></tr>';

    $sql_ma='SELECT hma_id, hma_vorname, hma_name  FROM mitarbeiter WHERE hma_aktiv = 1 AND  hma_level = '
        . $zeile_level['ule_id'] . ' ORDER BY hma_name';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_ma=mysql_query($sql_ma, $verbindung))
        {
        fehler();
        }
    $zaehler=0;

    while ($zeile_ma=mysql_fetch_array($ergebnis_ma))
        {
        if (fmod($zaehler, 2) == 1 && $zaehler > 0)
            {
            $stil='<tr>';
            }
        else
            {
            $stil='<tr class="alt">';
            }

        echo $stil . '<td nowrap>';

        echo $zeile_ma['hma_name'] . ', ' . $zeile_ma['hma_vorname'];

        echo '</td>';

        for ($i=0; $i <= $tage; $i++)
            {

            $testdate = mktime(0, 0, 0, $startmonat, $starttag + $i, $startjahr);
            $test=date("Y-m-d", $testdate);
            $bild='';

            switch ($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Schicht'])
                {
                case 2: // early Shift
                    $stil='<td class="early" title="early">';
                    break;

                case 3: // Late Shift
                    $stil='<td class="late"  title="late">';
                    break;

                case 4: // Home office
                    $bild='<img src="bilder/icon_home.png" alt="Homeoffice" title="Homeoffice">';
                    $stil='<td title="homeoffice">';
                    break;

                case 5: // Flex Shift
                    $stil='<td class="flex" title="flex">';
                    break;

                    default:
                    $stil = '<td>';
                }

            switch ($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Abwesend'])
                {
                case 1: // Annual
                    $bild='<img src="bilder/icon_annual.png" alt="Urlaub" title="Urlaub">';
                    $stil='<td class="absent" title="vacation">';
                    break;

                case 2: // Training
                    $bild='<img src="bilder/icon_training.png" alt="Schulung" title="Schulung">';
                    $stil='<td class="absent" title="training">';
                    break;

                case 3: // Krank
                    $bild='<img src="bilder/icon_sick.png" alt="Krank" title="Krank">';
                    $stil='<td class="absent" title="sick">';
                    break;

                case 4: // other
                    $bild='<img src="bilder/icon_other.png" alt="Sonstige" title="Sonstige">';
                    $stil='<td class="absent" title="absent other">';
                    break;
                }

            if ($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Release'] == 1)
                {
                $bild_release='<img src="bilder/icon_release.png" alt="Release" title="Release">';
                }
            else
                {
                $bild_release=Null;
                }
                 
                if ($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Bereitschaft'] == 1)
                {
                $bild_bereit='<img src="bilder/icon_bereit.png" alt="OnCall" title="OnCall">';
                }
            else
                {
                $bild_bereit=Null;
                }
                  
                if ($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Backup'] == 1)
                {
                $bild_backup='<img src="bilder/icon_action.gif" alt="Backup" title="Backup">';
                }
            else
                {
                $bild_backup=Null;   
                }

                echo $stil . $bild . $bild_release . $bild_bereit . $bild_backup . '</td>';
                } // For Tage Schleife
            
            

        echo '</tr>';
        $zaehler++;
        } // Ende MA
    }     // Ende Level

  
echo '</table>';

echo
    '<span class="text_klein">Legende: grau = abwesend / blau = Spätschicht / Magenta = Frühschicht / grün = Flex-Schicht</span>';
?>