<?php
###### Editnotes ####
#$LastChangedDate: 2011-11-14 09:24:37 +0100 (Mo, 14 Nov 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');

echo '<div id="header">';

echo '<ul>';

############# NICHT ZUGEWIESENE GRUPPENJOBS ######################

$sql=   'SELECT COUNT(uaz_hauid) AS anzahl FROM aufgaben_zuordnung
        LEFT JOIN aufgaben ON hau_id=uaz_hauid 
        LEFT JOIN aufgaben_mitarbeiter ON uau_hmaid = uaz_pba  
        LEFT JOIN level ON ule_id = uaz_pg 
        WHERE uaz_pba = 0 AND hau_aktiv = 1 AND hau_abschluss = 0 '
        . $_SESSION['filterstring'];

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
{
$anzahl_ohne_bearbeiter=$zeile['anzahl'];
}
echo '<li id="current"><a href="#">Gruppenaufgaben ohne Bearbeiter ('.$anzahl_ohne_bearbeiter.')</a></li>';

############ ZUGEWIESENE GRUPPENAUFGABEN ######################

$sql='SELECT COUNT(DISTINCT hau_id) AS anzahl FROM aufgaben 
    LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
    LEFT JOIN mitarbeiter ON uau_hmaid = hma_id 
        LEFT JOIN aufgaben_zuordnung ON uaz_pg = hma_level
        LEFT JOIN level ON uaz_pg = ule_id 
    WHERE uau_status = 0 AND uau_ma_status =0 
    AND hau_abschluss = 0 AND hau_aktiv = 1 ' . $_SESSION['filterstring'];

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
{
$anzahl=$zeile['anzahl'];
}

$sql='SELECT COUNT(DISTINCT hau_id) AS anzahl FROM aufgaben 
    LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
    LEFT JOIN mitarbeiter ON uau_hmaid = hma_id 
        LEFT JOIN aufgaben_zuordnung ON uaz_pg = hma_level
        LEFT JOIN level ON uaz_pg = ule_id 
    WHERE uau_status = 0 AND uau_ma_status = 1 
    AND hau_abschluss = 0 AND hau_aktiv = 1 ' . $_SESSION['filterstring'];

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
{
$anzahl+=$zeile['anzahl'];
}
    

echo '<li><a href="bericht_gruppenaufgaben_zugewiesen.php">Gruppenaufgaben in Bearbeitung ('.$anzahl.')</a></li>';

############ ABGESCHLOSSENE GRUPPENAUFGABEN ######################

$sql='SELECT COUNT(DISTINCT hau_id) AS anzahl FROM aufgaben 
    LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
    LEFT JOIN mitarbeiter ON uau_hmaid = hma_id 
        LEFT JOIN aufgaben_zuordnung ON uaz_pg = hma_level
        LEFT JOIN level ON uaz_pg = ule_id  
      WHERE hau_abschluss = 1 AND 
      hau_abschlussdatum BETWEEN "'.date('Y-m-d',mktime(0,0,0,date('n'),date('j')-7,date('Y'))).'" 
      AND "'.date('Y-m-d',mktime(0,0,0,date('n'),date('j'),date('Y'))).'" 
      AND hau_aktiv = 1  ' . 
            $_SESSION['filterstring'];

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
{
$anzahl=$zeile['anzahl'];
}

echo '<li><a href="bericht_gruppenaufgaben_abgeschlossen.php">abgeschlossene Gruppenaufgaben letzte Woche ('.$anzahl.')</a></li>';

echo '</ul>';

echo '</div>';

echo '<br>';
echo '<br>';
echo '<br>';

$_SESSION['uau_hmaid']='';

// Aufgaben in Warteschlange

echo '<div id="header">';

echo '<ul>';

echo '<li><a href="#">Gruppenaufgaben ohne Bearbeiter ('.$anzahl_ohne_bearbeiter.')</a></li>';

echo '</ul>';

echo '</div>';

$anzeigefelder=array
    (
    'Projekt' => 'hpr_titel',
    'Ticket' => 'hau_ticketnr',
    'TNR' => 'hau_id',
    'Prio' => 'upr_name',
    'Aufgabe' => 'hau_titel',
    'angelegt' => 'hau_anlage',
    'P-Ende' => 'hau_pende',
    'Eigner' => 'inhaber',
    'Gruppe' => 'ule_kurz',
    'aktualisiert' => 'letzte_aktualisierung'
    );

$sql='SELECT *, m1.hma_login AS inhaber FROM aufgaben 
    LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
    LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id 
    LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
    LEFT JOIN level ON uaz_pg = ule_id 
    LEFT JOIN projekte ON hau_hprid = hpr_id   
    INNER JOIN prioritaet ON hau_prio = upr_nummer   
    WHERE uaz_pba = 0 AND hau_aktiv = 1 AND hau_abschluss = 0 '
    . $_SESSION['filterstring'] . 
    'GROUP BY hau_id ' .
    'ORDER BY hau_prio DESC, hau_pende';
    
include('segment_liste_gruppe.php');

include('segment_fuss.php');
?>
