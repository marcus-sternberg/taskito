<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');

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

echo '<li><a href="schreibtisch_meine_aufgaben.php">Aufgaben ('.$anzahl.')</a></li>';

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

echo '<li id="current"><a href="#">Pings ('.$anzahl.')</a></li>';

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

echo '<div id="header">';

echo '<ul>';

$sql=$sql_schreibtisch_aufgaben_mit_PING;

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<li><a href="#">Meine Pings ('.$anzahl.')</a></li>';

echo '</ul>';

echo '</div>';

// Aufgaben mit PING von mir
  
$sql= $sql_schreibtisch_aufgaben_mit_PING; 

$anzeigefelder = $anzeige_ping;

$aktionenzahl = 1;
$aktionen=array(array("inhalt"=>"view task","bild"=>"icon_anschauen.gif","link"=>"aufgabe_ansehen.php")); 

$infozahl = 2;  
$infos=array('date'=>'hau_datumstyp', 'enabled'=>'uau_stopp');  


include ('segment_liste.php'); 

include ('segment_fuss.php');
?>