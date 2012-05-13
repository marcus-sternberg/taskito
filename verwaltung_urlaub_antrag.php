<?php
###### Editnotes ####
#$LastChangedDate: 2012-01-03 11:47:11 +0100 (Di, 03 Jan 2012) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

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

if (!isset($_POST['antrag']))
    {
    require_once('segment_kopf.php');
    $ruecksprung="verwaltung_urlaub_antrag.php";

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

    $xMonth=0;
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

    $sql_level='SELECT ule_id FROM level WHERE ule_id > 1 AND ule_id < 99 AND ule_aktiv = 1 AND ule_id = ' . $_SESSION['hma_level'];

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_level=mysql_query($sql_level, $verbindung))
        {
        fehler();
        }

    while ($zeile_level=mysql_fetch_array($ergebnis_level))
        {

        $sql_ma = 'SELECT hma_id FROM mitarbeiter WHERE hma_aktiv = 1 AND hma_aktiv = 1 AND hma_id = ' . $_SESSION['hma_id'];

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
                            if ((!isset($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Bereitschaft']))
                                            OR ($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test][
                                                'Bereitschaft']) == 0)
                                {
                                $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Bereitschaft']
                                    =$zeile_eintrag['hka_bereit'];
                                }

                            if ((!isset($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Backup']))
                                            OR ($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test][
                                                'Backup']) == 0)
                                {
                                $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Backup']
                                    =$zeile_eintrag['hka_backup'];
                                }
                                
                            if ((!isset(
                                $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Schicht']))
                                    OR ($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Schicht']) == 1)
                                {
                                $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Schicht']
                                    =$zeile_eintrag['hka_schicht'];
                                }

                            if ((!isset(
                                $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Abwesend']))
                                    OR ($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Abwesend']) == 0)
                                {
                                $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Abwesend']
                                    =$zeile_eintrag['hka_abwesend'];
                                }

                            if ((!isset(
                                $kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Release']))
                                    OR ($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Release']) == 0)
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
     '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Teamkalender <a href="verwaltung_urlaub_antrag.php?xShort=1">(15 Tagezeitraum aktuell)</a><br><br>'; 

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

    $sql_level='SELECT ule_id, ule_name FROM level WHERE ule_id > 1 AND ule_id < 99 AND ule_aktiv = 1 AND ule_id = ' . $_SESSION['hma_level']
        . ' ORDER BY ule_sort';

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

        $sql_ma='SELECT hma_id, hma_vorname, hma_name  FROM mitarbeiter WHERE hma_aktiv = 1 AND  hma_id = '
            . $_SESSION['hma_id'] . ' ORDER BY hma_name';

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
                        $stil='<td class="early">';
                        break;

                    case 3: // Late Shift
                        $stil='<td class="late">';
                        break;

                    case 4: // Home office
                        $bild='<img src="bilder/icon_home.png" alt="Homeoffice" title="Homeoffice">';
                        $stil='<td>';
                        break;

                    case 5: // Flex Shift
                        $stil='<td class="flex">';
                        break;

                        default:
        $stil = '<td>';
                    }

                switch ($kalender[$zeile_level['ule_id']][$zeile_ma['hma_id']][$test]['Abwesend'])
                    {
                    case 1: // Annual
                        $bild='<img src="bilder/icon_annual.png" alt="Urlaub" title="Urlaub">';
                        $stil='<td class="absent">';
                        break;

                    case 2: // Training
                        $bild='<img src="bilder/icon_training.png" alt="Schulung" title="Schulung">';
                        $stil='<td class="absent">';
                        break;

                    case 3: // Krank
                        $bild='<img src="bilder/icon_sick.png" alt="Krank" title="Krank">';
                        $stil='<td class="absent">';
                        break;

                    case 4: // other
                        $bild='<img src="bilder/icon_other.png" alt="Sonstige" title="Sonstige">';
                        $stil='<td class="absent">';
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
        '<span class="text_klein">Legende: grau = abwesend / blau = Spätschicht / Magenta = Frühschicht / grün = Flex-Schicht</span><br><br>';

    echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Status ändern<br><br>';

    echo '<form action="verwaltung_urlaub_antrag.php" method="post">';

    echo '<table border="0" cellspacing="5" cellpadding="0">';

    echo '<tr>';

    echo "<td class='text_klein' valign='middle'>Mitarbeiter: </td><td>";

    echo '<select size="1" name="hka_hmaid" class="liste">';
    $sql_filter='SELECT hma_id, hma_login FROM mitarbeiter WHERE hma_id > 3 AND hma_aktiv = 1 ' .
        'ORDER BY hma_login';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_filter=mysql_query($sql_filter, $verbindung))
        {
        fehler();
        }

    while ($zeile_filter=mysql_fetch_array($ergebnis_filter))
        {
        if ($_SESSION['hma_id'] == $zeile_filter['hma_id'])
            {
            echo '<option value="' . $zeile_filter['hma_id']
                . '" style="background-color:#E28B78;" selected><span class="text">' . $zeile_filter['hma_login']
                . '</span></option>';
            }
        else
            {
            echo '<option value="' . $zeile_filter['hma_id'] . '"><span class="text_mitte">'
                . $zeile_filter['hma_login'] . '</span></option>';
            }
        }

    echo '</select>';

    echo '</td></tr>';

    echo '<tr>';

    echo
        "<td class='text_klein' valign='middle'>Start: </td><td colspan='2'><input type='text' name='hka_von' style='width:100px;' id='hka_von'><img src='bilder/date_go.gif' alt='Anklicken für Kalenderansicht' onclick='kalender(document.getElementById(\"hka_von\"));'/>";

    echo '</tr>';

    echo '<tr>';

    echo
        "<td class='text_klein' valign='middle'>Ende (freilassen, wenns nur ein Tag ist): </td><td colspan='2'><input type='text' name='hka_bis' style='width:100px;' id='hka_bis'><img src='bilder/date_go.gif' alt='Anklicken für Kalenderansicht' onclick='kalender(document.getElementById(\"hka_bis\"));'/>";

    echo '</tr>';

    echo '<tr>';

    echo '<td colspan="3"><hr></td></tr>';

    echo '<tr><td class="text_klein">Status?: </td>';

    echo '<td><select size="1" name="hka_abwesend">';

    echo '<option value="0"><span class="text">Auf Arbeit</span></option>';

    echo '<option value="1"><span class="text">Urlaub</span></option>';

    echo '<option value="2"><span class="text">Training</span></option>';

    echo '<option value="3"><span class="text">Krankheit</span></option>';

    echo '<option value="4"><span class="text">Sonstiges</span></option>';

    echo '</select>';

    echo '</td></tr>';

    echo '<tr>';

    echo '<td colspan="3"><hr></td></tr>';

    echo '<tr>';

    echo '<td class="text_klein" colspan="3">Status: </td></tr>';

    echo '<tr><td class="text_klein" colspan="3">';
    
    echo '<table>';
    
    echo '<tr>';
    
    echo '<td><input type="radio" name="hka_schicht" value="2">&nbsp;Frühschicht&nbsp;</td>';

    echo '<td><input type="radio" name="hka_schicht" value="3">&nbsp;Spätschicht&nbsp;</td>';

    echo '<td><input type="radio" name="hka_schicht" value="5">&nbsp;Flex-Schicht&nbsp;</td>';

    echo '<td><input type="radio" name="hka_schicht" value="4">&nbsp;Homeoffice</td>';
    
    echo '</tr>';
    
    echo '<tr>';
  
    echo '<td><input type="checkbox" name="hka_bereit" value="1">&nbsp;OnCall&nbsp;</td>';

    echo '<td><input type="checkbox" name="hka_backup" value="1">&nbsp;OnCall (Backup)</td>';  
    
    echo '<td>&nbsp;</td>';

    echo '<td>&nbsp;</td>'; 
  
    echo '</tr>';

    echo '<tr>'; 
    
    echo '<td><input type="checkbox" name="hka_release" value="1">&nbsp;Release</td>';    
    
    echo '<td>&nbsp;</td>'; 
    echo '<td>&nbsp;</td>'; 
    echo '<td>&nbsp;</td>'; 
    
    echo '</tr>';
    
    echo '</table>';
    
    echo '</td>';

    echo '</tr>';

    echo
        '<tr><td colspan="3" style="text-align:right; padding-top:10px;"><input type="submit" name="antrag" value="Status ändern" class="formularbutton" /></td></tr>';

    echo '</table>';

    echo '</form>';
    }
else
    {
    foreach ($_POST as $varname => $value)
        {
        $Daten[$varname]=$value;
        }
 
    ### Change Status ###########

    $datumselement=array();

    if (empty($Daten['hka_bis']))
        {
        $datumarray=explode(".", $Daten['hka_von']);

        $starttag=$datumarray[0];
        $startmonat=$datumarray[1];
        $startjahr=$datumarray[2];
        $testdate=mktime(0, 0, 0, $startmonat, $starttag, $startjahr);
        $datumselement[]=date("Y-m-d", $testdate);
        }
    else
        {
        $datumarray=explode(".", $Daten['hka_von']);

        $starttag=$datumarray[0];
        $startmonat=$datumarray[1];
        $startjahr=$datumarray[2];

        $datumarray=explode(".", $Daten['hka_bis']);

        $endetag=$datumarray[0];
        $endemonat=$datumarray[1];
        $endejahr=$datumarray[2];

        $vdate=mktime(0, 0, 0, $startmonat, $starttag, $startjahr);
        $bdate=mktime(0, 0, 0, $endemonat, $endetag, $endejahr);

        $tage=($bdate - $vdate) / 86400;

        for ($i=0; $i <= $tage; $i++)
            {

            $testdate = mktime(0, 0, 0, $startmonat, $starttag + $i, $startjahr);
            $test=date("Y-m-d", $testdate);

            $datumselement[]=$test;
            }
        }
  /*
    if (!isset($Daten['hka_dienst']))
        {
        $Daten['hka_bereit']=0;
        $Daten['hka_backup']=0; 
        } else
        {
            switch ($Daten['hka_dienst'])
            {
                case 1:
                $Daten['hka_bereit']=1;
                $Daten['hka_backup']=0;
                break;
                case 2:
                $Daten['hka_bereit']=0;
                $Daten['hka_backup']=1;
                break;                
                                
            }
        }
    */

        
    if (!isset($Daten['hka_schicht']))
        {
        $Daten['hka_schicht']=1;
        }

    if (!isset($Daten['hka_release']))
        {
        $Daten['hka_release']=0;
        }

    if (!isset($Daten['hka_bereit']))
        {
        $Daten['hka_bereit']=0;
        }
        
    if (!isset($Daten['hka_backup']))
        {
        $Daten['hka_backup']=0;
        }
  
# Trotz Urlaub solls es Bereitschaft geben
        
/*    if ($Daten['hka_abwesend'] != 0)
        {
        $Daten['hka_bereit']=0;
        $Daten['hka_schicht']=1;
        }
*/  
       
# Ermittle Timestamps für die Sortierung der Datumseingaben       
     
       $abwesenheitszeit = array();
     
       foreach($datumselement AS $datum)
       {
       $abwesenheitszeit[$datum] = strtotime($datum);
       }

       asort($abwesenheitszeit); 
               
 # letzter Tag der Abwesenheit   
        
       $xLetzterTag = date("d.m.Y",end($abwesenheitszeit));

  # erster Tag der Abwesenheit   
                 
       $xErsterTag = date("d.m.Y",reset($abwesenheitszeit)); 
       
                       
    foreach ($datumselement AS $Kalendertag)
        {

        $datum_feld = explode("-", $Kalendertag);
        $tag=$datum_feld[2];
        $monat=$datum_feld[1];
        $jahr=$datum_feld[0];
        $timestamp=mktime(0, 0, 0, $monat, $tag, $jahr);

        if (date("N", $timestamp) > 5)
            {
            $abwesend=0;
            $schicht=1;
            $release=0;    
            $bereit=$Daten['hka_bereit'];
            $backup=$Daten['hka_backup'];  
            }
        else
            {
            $abwesend=$Daten['hka_abwesend'];
            $schicht=$Daten['hka_schicht'];
            $release=$Daten['hka_release']; 
            $bereit=$Daten['hka_bereit'];
            $backup=$Daten['hka_backup']; 
            }
            
        switch($abwesend)
        {
            case 0:
            $xgrund = "Auf Arbeit";
            break;
            
            case 1:
            $xgrund = "Urlaub";
            break;    

            case 2:
            $xgrund = "Training";
            break;
            
            case 3:
            $xgrund = "Krankheit";
            break;      

            case 4:
            $xgrund = "Sonstiges";
            break;
        }
            
        $sql_check=
            'SELECT * FROM kalender WHERE hka_hmaid = "' . $Daten['hka_hmaid'] . '" AND hka_tag = "' . $Kalendertag
            . '"';

        if (!$ergebnis_check=mysql_query($sql_check, $verbindung))
            {
            fehler();
            }

        if (mysql_num_rows($ergebnis_check) > 0)
            {
            $sql='UPDATE kalender SET
            hka_abwesend = "' . $abwesend . '",        
            hka_bereit = "' . $bereit . '", 
            hka_release = "' . $release . '",    
            hka_backup = "' . $backup . '",    
            hka_schicht = "' . $schicht . '"
            WHERE hka_hmaid = "' . $Daten['hka_hmaid'] . '" AND hka_tag = "' . $Kalendertag . '"';
            }
        else
            {
            $sql='INSERT INTO kalender 
            (hka_tag,
            hka_abwesend,
            hka_hmaid,
            hka_schicht,
            hka_backup,   
            hka_release,
            hka_bereit) 
            VALUES
            ("' . $Kalendertag . '", 
            "' . $abwesend . '", 
            "' . $Daten['hka_hmaid'] . '",
            "' . $schicht . '", 
            "' . $backup . '",    
            "' . $release . '",
            "' . $bereit . '"
            )';
            }
           
        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }
        }

                
             
        $sql_log='INSERT INTO eventlog (' .
            'hel_area, ' .
            'hel_type, ' .
            'hel_referer, ' .
            'hel_text) ' .
            'VALUES ( ' .
            '"Team", ' .
            '"Abwesend", ' .
            '"' . $_SESSION['hma_login'] . '" ,' .
            '"hat [' . $xgrund . '] für den Zeitraum von '. $xErsterTag. ' bis '.$xLetzterTag.' eingetragen.")';

        if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
            {
            fehler();
            }
        
    header('Location: verwaltung_urlaub_antrag.php');
    exit;
    } // Ende else

echo
    '<div id="bn_frame" style="position:absolute; display:none; height:198px; width:205px; background-color:#ced7d6; overflow:hidden;">';

echo
    '<iframe src="bytecal.php" style="width:208px; margin-left:-1px; border:0px; height:202px; background-color:#ced7d6; overflow:hidden;" border="0"></iframe>';

echo '</div>';
?>