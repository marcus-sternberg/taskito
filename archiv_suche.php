<?php
###### Editnotes ####
#$LastChangedDate: 2012-03-19 19:05:54 +0100 (Mo, 19 Mrz 2012) $
#$Author: bpetersen $ 
#####################
#Hallo
require_once ('konfiguration.php');

include ('segment_session_pruefung.php');
include ('segment_init.php');
include ('segment_kopf.php');

$Daten=array();

foreach ($_POST as $varname => $value)
    {
    $Daten[$varname]=trim($value);
    }

if (isset($_POST['suchbegriff']))
    {
    $suchbegriff=$_POST['suchbegriff'];
    }
else
    $suchbegriff='';

if (isset($_POST['hau_ticketnr']))
    {
    $ticket=$_POST['hau_ticketnr'];
    }
else
    $ticket='';

unset ($_POST['suchen']);
unset ($_POST['suchbegriff']);
unset ($_POST['hau_ticketnr']);
$suchstring='';

foreach ($_POST as $bezeichner => $inhalt)
    {
    if ($bezeichner == 'hma_id')
        {
        $bezeichner='m1.hma_id';
        }

    switch ($inhalt)
        {
        case "0": break;

        case '': break;

        default:
            $suchstring.=' ' . $bezeichner . '= "' . $inhalt . '" AND ';

            break;
        }
    }

   
if ($suchbegriff == '' AND $ticket == '')
    {
    $suchstring=(substr($suchstring, 0, (strlen($suchstring) - 4)));
    }
else if ($suchbegriff != '')
    {
		 $suchstring.=' (hau_titel LIKE "%' . $suchbegriff . '%" OR hau_beschreibung LIKE "%' . $suchbegriff . '%" OR ulo_text LIKE "%' . $suchbegriff . '%") ';
    }
else if ($ticket != '')
    {
    $suchstring.=' (hau_ticketnr LIKE "%' . $ticket . '%") ';
    }

if ($suchstring == '')
    {
    $xUnd='';
    }
else
    {
    $xUnd='AND ';
    }

$subsuchstring=$suchstring;
$suchstring   =$xUnd . $suchstring;

if ($suchstring != '')
    {
    $_SESSION['suchstring']=stripslashes($suchstring);
    }

// Aufgaben in Warteschlange

echo '<br><span class="text_mitte"><img src="bilder/block.gif"> Such-Ergebnisse<br><br>';

echo '<span class="box">Offene Aufgaben</span>';

$sql=' SELECT DISTINCT uau_hauid, 
            hau_hprid,
            hau_prio,
            hau_id,
            hau_ticketnr, 
            upr_name, 
            hau_titel, 
            hau_anlage,  
            hau_beschreibung,
            hau_pende, 
            hma_login, 
            hau_abschluss,
            hau_datumstyp,
            hpr_titel, 
            ule_name, 
            uty_name 
				FROM aufgaben
        LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
        LEFT JOIN mitarbeiter ON hau_inhaber = hma_id 
        LEFT JOIN typ ON hau_typ = uty_id 
        LEFT join aufgaben_zuordnung ON hau_id = uaz_hauid
        LEFT JOIN level ON uaz_pg = ule_id 
        LEFT JOIN projekte ON hau_hprid = hpr_id 
        LEFT JOIN prioritaet ON hau_prio = upr_nummer 
		  LEFT JOIN log ON ulo_aufgabe = hau_id 
        WHERE hau_abschluss = 0
        AND hau_aktiv = 1 ' . $_SESSION['filterstring'] . $_SESSION['suchstring'] . 'GROUP BY hau_id 
        ORDER BY hau_prio';
          
$anzeigefelder=$anzeige_suche;
$aktionenzahl=1;

$aktionen=array(array
    (
    "inhalt" => "Aufgabe ansehen",
    "bild"   => "icon_anschauen.gif",
    "link"   => "aufgabe_ansehen.php"
    ));

$infozahl=1;

$infos=array('Datum' => 'hau_datumstyp');

include ('segment_liste.php');

echo '<span class="box"><br>Geschlossene Aufgaben (Limit 150)</span>';

$sql          =' SELECT DISTINCT uau_hauid, 
            hau_hprid,
            hau_prio,
            hau_id,
            hau_ticketnr, 
            upr_name, 
            hau_titel, 
            hau_beschreibung,
            hau_datumstyp,   
            hau_anlage,  
            hau_pende, 
            hma_login, 
            hau_abschluss, 
            hpr_titel, 
            ule_name, 
            uty_name 
        FROM aufgaben 
        LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
        LEFT JOIN mitarbeiter ON hau_inhaber = hma_id 
        LEFT JOIN typ ON hau_typ = uty_id 
        LEFT join aufgaben_zuordnung ON hau_id = uaz_hauid
        LEFT JOIN level ON uaz_pg = ule_id 
        LEFT JOIN projekte ON hau_hprid = hpr_id 
        LEFT JOIN prioritaet ON hau_prio = upr_nummer 
		  LEFT JOIN log ON ulo_aufgabe = hau_id 
        WHERE hau_abschluss = 1
        AND hau_aktiv = 1 ' . $_SESSION['filterstring'] . $_SESSION['suchstring'] . 'GROUP BY hau_id 
        ORDER BY hau_anlage DESC LIMIT 150';
        
$anzeigefelder=$anzeige_suche;

$aktionenzahl =1;

$aktionen=array(array
    (
    "inhalt" => "Aufgabe ansehen",
    "bild"   => "icon_anschauen.gif",
    "link"   => "aufgabe_ansehen.php"
    ));

$infozahl=1;

$infos=array('Datum' => 'hau_datumstyp');

include ('segment_liste.php');

include ('segment_fuss.php');

exit;
// Zurueck zur Liste

header ('Location: verwaltung_bereich.php');
exit;
?>
