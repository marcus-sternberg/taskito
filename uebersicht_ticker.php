<?php
###### Editnotes ####
#$LastChangedDate: 2011-11-14 09:17:20 +0100 (Mo, 14 Nov 2011) $
#$Author: msternberg $ 
#####################

######### Define Includes ##################

require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

######## Define Variables #################

$menu_ticker=array
    (
    "dummy",
    "unerledigte Aufgaben",
    "nicht zugewiesen",
    "Wartestatus"
    );

$selection=array();
    
// all
$selection[1]=' WHERE hau_abschluss = 0 AND hau_aktiv = 1 ' . $_SESSION['filterstring'];  

// open & not mapped
$selection[2]=' WHERE hau_abschluss = 0 AND uau_id is NULL AND hau_aktiv = 1 ' . $_SESSION['filterstring']; 

// on hold

$selection[3]=' WHERE hau_abschluss = 0 AND uau_stopp > 0 AND hau_aktiv = 1 ' . $_SESSION['filterstring']; 
 
########### Read parameter ##################

if (!isset($_GET['auto']))
    {
    $auto='on';
    }
else
    {
    $auto=$_GET['auto'];
    }

if (!isset($_GET['option']))
    {
    $option='1';
    }
else
    {
    $option=$_GET['option'];
    }

############ Define Controls ################

if ($auto == 'on')
    {

    $autolink='uebersicht_ticker.php?auto=off&option=' . $option;
    $autobild='bilder/icon_refresh.png';
    $autotext='Page-Refresh: ON!';
    }
else
    {

    $autolink='uebersicht_ticker.php?auto=on&option=' . $option;
    $autobild='bilder/icon_refresh_off.png';
    $autotext='Page-Refresh: OFF!';
    }


#####################################################################################
############################ Ausgabe Werte ##########################################

include('segment_kopf_reload.php');

// Gebe Ueberschrift aus

echo '<div id="header">';

echo '<ul>';

for ($count=1; $count < 4; $count++)
    {

 $sql='SELECT COUNT(DISTINCT hau_id) AS anzahl FROM aufgaben 
        LEFT JOIN typ ON hau_typ = uty_id 
        LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
        LEFT JOIN level ON uaz_pg = ule_id 
        LEFT JOIN projekte ON hau_hprid = hpr_id 
        INNER JOIN prioritaet ON hau_prio = upr_nummer 
        LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
        LEFT JOIN log ON hau_id = ulo_aufgabe 
        LEFT JOIN mitarbeiter ON uau_hmaid = hma_id '
        . $selection[$count];
        

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

          while ($zeile = mysql_fetch_array($ergebnis)) 
  { 
   $total=$zeile['anzahl'];     
  }  
        

    if($count==$option)
    {
    echo '<li id="current">';
    } else
    {
        echo '<li>';
    }
    echo '<a href="uebersicht_ticker.php?option=' . $count . '&auto=' . $auto . '">' . $menu_ticker[$count] . ' ['
        . $total . ']</a>';

    echo '</li>';        

    

    }

echo '</ul>';

echo '</div>';

echo'<br><br>';

echo '<div id="header">';

echo '<ul>';

echo '<li><a href="#">Zuletzt eingestellte Aufgaben</a></li>';

echo '</ul>';

echo '</div>';


$sql='SELECT *, m1.hma_login AS inhaber, m2.hma_login AS mitarbeiter FROM aufgaben 
        LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
        LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id 
        LEFT JOIN mitarbeiter m2 ON uau_hmaid = m2.hma_id 
        LEFT JOIN typ ON hau_typ = uty_id 
        LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
        LEFT JOIN level ON uaz_pg = ule_id 
        LEFT JOIN log ON hau_id = ulo_aufgabe     
        LEFT JOIN projekte ON hau_hprid = hpr_id 
        INNER JOIN prioritaet ON hau_prio = upr_nummer '
    . $selection[$option].' GROUP BY hau_id ORDER BY hau_anlage DESC';       

$anzeigefelder=$anzeige_pool;
$aktionenzahl=2;
$infozahl=4;
$aktionen=array(array
    (
    "inhalt" => "Aufgabe übernehmen",
    "bild" => "icon_uebernehmen.gif",
    "link" => "aufgabe_zuordnen_selbst.php"
    ));

$aktionen[]=array
    (
    "inhalt" => "Aufgabe ansehen",
    "bild" => "icon_anschauen.gif",
    "link" => "aufgabe_ansehen.php"
    );

$infos=array
    (
    'Datum' => 'hau_datumstyp',
    'Status' => 'uau_stopp',
    'Kommentar' => 'ulo_aufgabe',
    'Abhängigkeit' => 'hau_reihe'
    );

include('segment_liste.php');

echo'<br><br>';

/*ho '<div id="header">';

echo '<ul>';

echo '<li><a href="#">Abgeschlossene Aufgaben [Letzten 30]</a></li>';

echo '</ul>';

echo '</div>';


  $sql='SELECT *, m1.hma_login AS inhaber, m2.hma_login AS mitarbeiter FROM aufgaben 
        LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
        LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id 
        LEFT JOIN mitarbeiter m2 ON uau_hmaid = m2.hma_id 
        LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
        LEFT JOIN level ON uaz_pg = ule_id 
        LEFT JOIN projekte ON hau_hprid = hpr_id 
        LEFT JOIN prioritaet ON hau_prio = upr_nummer 
        WHERE hau_abschluss = 1 '
        . ' AND hau_aktiv = 1' . $_SESSION['filterstring'] .
        ' GROUP BY hau_id 
        ORDER BY hau_abschlussdatum DESC LIMIT 30';

$anzeigefelder=$anzeige_jobende;
$aktionenzahl=1;
$aktionen=array(array
    (
    "inhalt" => "Aufgabe ansehen",
    "bild" => "icon_anschauen.gif",
    "link" => "aufgabe_ansehen.php"
    ));

$infozahl=1;
$infos=array('date' => 'hau_datumstyp');

include('segment_liste.php');
              */
include('segment_fuss.php');
?>
