<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
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
    "nicht zugewiesene Aufgaben",
    "zugewiesene Aufgaben",
    "abgelehnte Aufgaben"
    );

$selection=array();
    
// unmapped tasks 
$selection[1]=' WHERE hau_abschluss = 0 AND uau_id is NULL AND hau_aktiv = 1 ' . $_SESSION['filterstring'];  

// open & mapped
$selection[2]=' WHERE hau_abschluss = 0 AND uau_id is NOT NULL AND uau_status = 0 AND uau_ma_status < 2 AND hau_aktiv = 1 ' . $_SESSION['filterstring']; 

// rejected
$selection[3]=' WHERE hau_abschluss = 0 AND uau_id is NOT NULL AND uau_status = 0 AND uau_ma_status = 2 AND hau_aktiv = 1 ' . $_SESSION['filterstring']; 


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

    $autolink='team_uebersicht.php?auto=off&option=' . $option;
    $autobild='bilder/icon_refresh.png';
    $autotext='Page-Refresh: ON!';
    }
else
    {

    $autolink='team_uebersicht.php?auto=on&option=' . $option;
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
    echo '<a href="team_uebersicht.php?option=' . $count . '&auto=' . $auto . '">' . $menu_ticker[$count] . ' ['
        . $total . ']</a>';

    echo '</li>';        

    

    }

echo '</ul>';

echo '</div>';

echo'<br><br>';

echo '<div id="header">';

echo '<ul>';

$sql=
    'SELECT *, m1.hma_login AS inhaber, m2.hma_login AS mitarbeiter, m3.hma_login AS teamleiter FROM aufgaben 
        LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
        LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id 
        LEFT JOIN mitarbeiter m2 ON uau_hmaid = m2.hma_id 
        LEFT JOIN mitarbeiter m3 ON hau_teamleiter = m3.hma_id 
         LEFT JOIN typ ON hau_typ = uty_id   
        LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
        LEFT JOIN level ON uaz_pg = ule_id 
        LEFT JOIN projekte ON hau_hprid = hpr_id 
        INNER JOIN prioritaet ON hau_prio = upr_nummer ';
        
switch ($option)
    {
    case 1:
        
        echo '<li><a href="#">Offene Aufgaben ohne Bearbeiter</a></li>'; 
        $i=1;
        $sql=$sql . $selection[$option].' GROUP BY hau_id ORDER BY hau_prio DESC, hau_pende';  
        $anzeigefelder=$anzeige_pool;
        $aktionenzahl=3;
        $aktionen=array(array
            (
            "inhalt" => "Ticket zuordnen",
            "bild" => "icon_uebernehmen.gif",
            "link" => "aufgabe_zuordnen.php"
            ));

        $aktionen[]=array
            (
            "inhalt" => "Ticket anzeigen",
            "bild" => "icon_anschauen.gif",
            "link" => "aufgabe_ansehen.php"
            );

        $aktionen[]=array
            (
            "inhalt" => "Ticket löschen",
            "bild" => "icon_loeschen.gif",
            "link" => "aufgabe_loeschen_team.php"
            );

        $infozahl=1;
        $infos=array('Datum' => 'hau_datumstyp');

        break;

    case 2:
        echo '<li><a href="#">Zugewiesene offene Aufgaben</a></li>';  
        $i=2;
        $sql=$sql . $selection[$option].' GROUP BY hau_id ORDER BY hau_prio DESC, hau_pende';   
        $anzeigefelder=$anzeige_delegiert;
        $aktionenzahl=4;
        $aktionen=array(array
            (
            "inhalt" => "Ticket zurücknehmen",
            "bild" => "icon_zuruecknehmen.gif",
            "link" => "team_aufgabe_zuruecknehmen.php"
            ));

        $aktionen[]=array
            (
            "inhalt" => "Ticket neu zuweisen",
            "bild" => "icon_mitarbeiter_loeschen.png",
            "link" => "aufgabe_neu_zuordnen.php"
            );

        $aktionen[]=array
            (
            "inhalt" => "Ticket anzeigen",
            "bild" => "icon_anschauen.gif",
            "link" => "aufgabe_ansehen.php"
            );

        $aktionen[]=array
            (
            "inhalt" => "Ticket löschen",
            "bild" => "icon_loeschen.gif",
            "link" => "aufgabe_loeschen_team.php"
            );

        $infozahl=5;
        $infos=array
            (
            'Datum' => 'hau_datumstyp',
            'Zuordnung' => 'uau_ma_status',
            'Status' => 'uau_stopp',
            'Gruppe' => 'hau_id',
            'Termin ändern' => 'hau_terminaendern'
            );

        break;

    case 3:

        echo '<li><a href="#">Abgelehnte Aufgaben</a></li>';  
        $i=3;
        $anzeigefelder=$anzeige_delegiert;
        $sql=$sql . $selection[$option].' GROUP BY hau_id ORDER BY hau_prio DESC, hau_pende';   
        $aktionenzahl=4;
        $aktionen=array(array
            (
            "inhalt" => "revoke task complete",
            "bild" => "icon_zuruecknehmen.gif",
            "link" => "team_aufgabe_zuruecknehmen.php"
            ));

        $aktionen[]=array
            (
            "inhalt" => "remove staff on STOPP only",
            "bild" => "icon_mitarbeiter_loeschen.png",
            "link" => "team_aufgabe_zuruecknehmen.php"
            );

        $aktionen[]=array
            (
            "inhalt" => "view task",
            "bild" => "icon_anschauen.gif",
            "link" => "aufgabe_ansehen.php"
            );

        $aktionen[]=array
            (
            "inhalt" => "erase ticket",
            "bild" => "icon_loeschen.gif",
            "link" => "aufgabe_loeschen_team.php"
            );

        $infozahl=5;
        $infos=array
            (
            'Datum' => 'hau_datumstyp',
            'Zuordnung' => 'uau_ma_status',
            'Status' => 'uau_stopp',
            'Gruppe' => 'hau_id',
            'Termin ändern' => 'hau_terminaendern'
            );

        break;

     }

echo '</ul>';

echo '</div>';
     
$teampage=1;
include('segment_liste.php');

include('segment_fuss.php');
?>