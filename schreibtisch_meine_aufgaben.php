<?php
###### Editnotes ####
#$LastChangedDate: 2011-11-14 09:17:20 +0100 (Mo, 14 Nov 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_init.php');

if (isset($_GET['sort_todo']))
    {
    $sort_todo=$_GET['sort_todo'];
    }
else
    {
    $sort_todo='uto_prio DESC, uto_enddatum DESC';
    }

if (!isset($_GET['auto']))
    {
    $auto='on';
    }
else
    {
    $auto=$_GET['auto'];
    }

if ($auto == 'on')
    {

    $autolink='schreibtisch_meine_aufgaben.php?auto=off';
    $autobild='bilder/icon_refresh.png';
    $autotext='Page-Refresh: ON.';
    }
else
    {

    $autolink='schreibtisch_meine_aufgaben.php?auto=on';
    $autobild='bilder/icon_refresh_off.png';
    $autotext='Page-Refresh: OFF.';
    }

include('segment_kopf_reload.php');

echo '<div id="header">';

echo '<ul>';

############# GRUPPEN ######################

$sql = 'SELECT * FROM aufgaben 
        LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
        LEFT JOIN mitarbeiter ON uau_hmaid = hma_id 
        LEFT JOIN typ ON hau_typ = uty_id 
        LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
        LEFT JOIN level ON uaz_pg = ule_id 
        LEFT JOIN projekte ON hau_hprid = hpr_id  
        LEFT JOIN log ON hau_id = ulo_aufgabe     
        INNER JOIN prioritaet ON hau_prio = upr_nummer
        WHERE uaz_pg = ' . $_SESSION['hma_level'] . ' 
        AND uaz_pba = 0 
        AND hau_aktiv =1 
        AND hau_abschluss = 0 ' . $_SESSION['filterstring'] . ' 
        GROUP BY hau_id 
        ORDER BY hau_zeitstempel DESC';
  
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<li><a href="schreibtisch_meine_gruppenaufgaben.php">Gruppenaufgaben ('.$anzahl.')</a></li>';

############ AUFGABEN ######################

   # $sql=$sql_schreibtisch_aktuelle_aufgaben;

$sql =  'SELECT * FROM aufgaben 
        LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
        LEFT JOIN mitarbeiter ON uau_hmaid = hma_id 
        LEFT JOIN typ ON hau_typ = uty_id 
        LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
        LEFT JOIN level ON uaz_pg = ule_id 
        LEFT JOIN projekte ON hau_hprid = hpr_id  
        LEFT JOIN log ON hau_id = ulo_aufgabe     
        INNER JOIN prioritaet ON hau_prio = upr_nummer
        WHERE 
        uau_hmaid = ' . $_SESSION['hma_id'] . ' AND hau_aktiv = 1 
        AND uau_status = 0 AND uau_ma_status = 0 ' . $_SESSION['filterstring'] . '
        GROUP BY hau_id 
        ORDER BY uau_prio, hau_pende';

                
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl_queue=mysql_num_rows($ergebnis);

#$sql=$sql_schreibtisch_aufgaben_angenommen;

$sql='SELECT * FROM (

(SELECT hau_id, hau_zeitstempel FROM aufgaben
LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id 
LEFT JOIN mitarbeiter m2 ON uau_hmaid = m2.hma_id 
LEFT JOIN typ ON hau_typ = uty_id
LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
LEFT JOIN level ON uaz_pg = ule_id 
LEFT JOIN projekte ON hau_hprid = hpr_id
INNER JOIN prioritaet ON hau_prio = upr_nummer 
LEFT JOIN log ON ulo_aufgabe = hau_id
LEFT JOIN log_status ON uls_uloid = ulo_id
        WHERE 
        uau_hmaid = ' . $_SESSION['hma_id'] . ' AND 
        hau_aktiv = 1 AND 
        uau_status = 0 AND 
        uau_ma_status = 1 ' . $_SESSION['filterstring'] . ')
UNION
(
     SELECT hau_id, hau_zeitstempel FROM aufgaben
LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id 
LEFT JOIN mitarbeiter m2 ON uau_hmaid = m2.hma_id 
LEFT JOIN typ ON hau_typ = uty_id
LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
LEFT JOIN level ON uaz_pg = ule_id 
LEFT JOIN projekte ON hau_hprid = hpr_id
INNER JOIN prioritaet ON hau_prio = upr_nummer 
LEFT JOIN log ON ulo_aufgabe = hau_id
LEFT JOIN log_status ON uls_uloid = ulo_id
           WHERE 
           hau_aktiv = 1 AND 
           hau_abschluss = 0 AND 
           uls_ping_an = ' . $_SESSION['hma_id'] . '
           ' . $_SESSION['filterstring'] . ' ) 
 ) bezeichner   
GROUP BY hau_id
ORDER BY hau_zeitstempel DESC'
;

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl_working=mysql_num_rows($ergebnis);

$anzahl = $anzahl_working + $anzahl_queue;

echo '<li id="current"><a href="#">Aufgaben ('.$anzahl.')</a></li>';

############ PROJEKTE ######################

$sql='SELECT *, DATEDIFF(hpr_pende,curdate()) as diff FROM projekte 
        LEFT JOIN mitarbeiter ON hpr_inhaber = hma_id    
        WHERE hpr_inhaber = "' . $_SESSION['hma_id'] . '" AND hpr_fertig = "0" AND hpr_aktiv = "1" 
        ORDER BY hpr_prio, hpr_pende';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<li><a href="schreibtisch_meine_projekte.php">Projekte ('.$anzahl.')</a></li>';

############ PING ######################

$sql=$sql_schreibtisch_aufgaben_mit_PING;

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

$sql=$sql_schreibtisch_aufgaben_angenommen;

echo '<li><a href="schreibtisch_meine_pings.php">Pings ('.$anzahl.')</a></li>';

############ TODO ######################

$sql='SELECT uto_id FROM todo WHERE uto_status = 0 AND uto_hmaid = ' . $_SESSION['hma_id'];

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<li><a href="schreibtisch_meine_todos.php">ToDos ('.$anzahl.')</a></li>';

echo '</ul>';

echo '</div>';

echo '<br>';
echo '<br>';
echo '<br>';

$_SESSION['uau_hmaid']='';

// Aufgaben in Warteschlange

echo '<div id="header">';

echo '<ul>';

#$sql=$sql_schreibtisch_aktuelle_aufgaben;

$sql =  '  SELECT *, m1.hma_login AS inhaber, m2.hma_login AS mitarbeiter FROM aufgaben  
        LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
        LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id 
        LEFT JOIN mitarbeiter m2 ON uau_hmaid = m2.hma_id 
        LEFT JOIN typ ON hau_typ = uty_id 
        LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
        LEFT JOIN level ON uaz_pg = ule_id 
        LEFT JOIN projekte ON hau_hprid = hpr_id  
        LEFT JOIN log ON hau_id = ulo_aufgabe     
        INNER JOIN prioritaet ON hau_prio = upr_nummer
        WHERE 
        uau_hmaid = ' . $_SESSION['hma_id'] . ' AND hau_aktiv = 1 
        AND uau_status = 0 AND uau_ma_status = 0 ' . $_SESSION['filterstring'] . '
        GROUP BY hau_id 
        ORDER BY uau_prio, hau_pende';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<li><a href="#">Aufgaben in Warteschlange ('.$anzahl.')</a></li>';

echo '</ul>';

echo '</div>';

#$sql=$sql_schreibtisch_aktuelle_aufgaben;
$anzeigefelder=$anzeige_pool_ma;

$aktionenzahl=3;
$aktionen=array(array
    (
    "inhalt" => "Aufgabe zurückgeben",
    "bild" => "icon_abgelehnt.gif",
    "link" => "aufgabe_zurueckgeben_selbst.php"
    ));

$aktionen[]=array
    (
    "inhalt" => "Aufgabe beginnen",
    "bild" => "icon_uebernehmen.gif",
    "link" => "aufgabe_zuordnen_selbst.php"
    );

$aktionen[]=array
    (
    "inhalt" => "Aufgabe ansehen",
    "bild" => "icon_anschauen.gif",
    "link" => "aufgabe_ansehen.php"
    );
    

$infozahl=3;
$infos=array
    (
    'Datum' => 'hau_datumstyp',
    'Status' => 'uau_stopp',
    'Abhängigkeit' => 'hau_reihe'
    );

include('segment_liste.php');

echo '<br>';

// Aufgaben in Warteschlange

echo '<div id="header">';

echo '<ul>';


#$sql=$sql_schreibtisch_aufgaben_angenommen;
$sql='SELECT * FROM (

(SELECT m1.hma_login AS inhaber, m2.hma_login AS mitarbeiter, hau_reihe, hau_titel, hau_beschreibung, hau_ticketnr, uau_prio, hau_hprid, hpr_titel, hau_anlage, hau_pende, hau_datumstyp, upr_name, hau_id, hau_zeitstempel FROM aufgaben
LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id 
LEFT JOIN mitarbeiter m2 ON uau_hmaid = m2.hma_id 
LEFT JOIN typ ON hau_typ = uty_id
LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
LEFT JOIN level ON uaz_pg = ule_id 
LEFT JOIN projekte ON hau_hprid = hpr_id
INNER JOIN prioritaet ON hau_prio = upr_nummer 
LEFT JOIN log ON ulo_aufgabe = hau_id
LEFT JOIN log_status ON uls_uloid = ulo_id
        WHERE 
        uau_hmaid = ' . $_SESSION['hma_id'] . ' AND 
        hau_aktiv = 1 AND 
        uau_status = 0 AND 
        uau_ma_status = 1 ' . $_SESSION['filterstring'] . ')  
      UNION
(SELECT m1.hma_login AS inhaber, m2.hma_login AS mitarbeiter, hau_reihe, hau_titel, hau_beschreibung, hau_ticketnr, uau_prio, hau_hprid, hpr_titel, hau_anlage, hau_pende, hau_datumstyp, upr_name, hau_id, hau_zeitstempel FROM aufgaben  
LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id 
LEFT JOIN mitarbeiter m2 ON uau_hmaid = m2.hma_id 
LEFT JOIN typ ON hau_typ = uty_id
LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
LEFT JOIN level ON uaz_pg = ule_id 
LEFT JOIN projekte ON hau_hprid = hpr_id
INNER JOIN prioritaet ON hau_prio = upr_nummer 
LEFT JOIN log ON ulo_aufgabe = hau_id
LEFT JOIN log_status ON uls_uloid = ulo_id
           WHERE 
           hau_aktiv = 1 AND 
           hau_abschluss = 0 AND 
           uls_ping_an = ' . $_SESSION['hma_id'] . '
           ' . $_SESSION['filterstring'] . ' ) 
 ) bezeichner
GROUP BY hau_id
ORDER BY hau_zeitstempel DESC'
;  

    
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<li><a href="#">Aufgaben in Bearbeitung ('.$anzahl.')</a></li>';

echo '</ul>';

echo '</div>';



$anzeigefelder=$anzeige_angenommen;

$aktionenzahl=3;
$aktionen=array(array
    (
    "inhalt" => "Aufgabe zurückgeben",
    "bild" => "icon_abgelehnt.gif",
    "link" => "aufgabe_zurueckgeben_selbst.php"
    ));

$aktionen[]=array
    (
    "inhalt" => "Aufgabe ins Backlog legen",
    "bild" => "icon_erneut.gif",
    "link" => "schreibtisch_aufgabe_backlog.php"
    );
    
$aktionen[]=array
    (
    "inhalt" => "Aktivität eintragen",
    "bild" => "icon_arbeit.gif",
    "link" => "aufgabe_ansehen.php"
    );
    
$infozahl=5;
$infos=array
    (
    'Datum' => 'hau_datumstyp',
    'Status' => 'uau_stopp',
    'PING' => 'uls_ping_an',
    'Kommentar' => 'uls_komm_an',
    'Abhängigkeit' => 'hau_reihe'
    );


include('segment_liste.php');

echo '<br>';

// Letzte geschlossene Aufgaben von mir

echo '<div id="header">';

echo '<ul>';

echo '<li><a href="#">abgeschlossene Aufgaben (letzte 20)</a></li>';

echo '</ul>';

echo '</div>';

$sql='SELECT *, m1.hma_login AS inhaber, m2.hma_login AS mitarbeiter, m3.hma_login AS teamleiter FROM aufgaben 
        LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
        LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id 
        LEFT JOIN mitarbeiter m2 ON uau_hmaid = m2.hma_id 
        LEFT JOIN mitarbeiter m3 ON hau_teamleiter = m3.hma_id 
        LEFT JOIN typ ON hau_typ = uty_id 
        LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
        LEFT JOIN level ON uaz_pg = ule_id 
        LEFT JOIN projekte ON hau_hprid = hpr_id 
        LEFT JOIN prioritaet ON hau_prio = upr_nummer 
        LEFT JOIN log ON hau_id = ulo_aufgabe     
        WHERE hau_abschluss = 1 AND uau_hmaid = '.$_SESSION['hma_id'].' 
        AND hau_aktiv = 1' . $_SESSION['filterstring'] .
        ' GROUP BY hau_id 
        ORDER BY hau_abschlussdatum DESC LIMIT 20';

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

include('segment_fuss.php');
?>