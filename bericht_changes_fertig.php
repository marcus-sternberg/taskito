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

############# Nicht freigegebene Changes ######################

$sql=   'SELECT COUNT(hau_id) AS anzahl FROM aufgaben
        LEFT JOIN rollen_status ON hau_id=urs_hauid 
        WHERE hau_hprid = 6 AND urs_freigabe_ok = 0 AND hau_aktiv = 1 AND hau_abschluss = 0 ';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
{
$anzahl_nicht_freigegebene_changes=$zeile['anzahl'];
}
echo '<li><a href="bericht_changes_offen.php">Nicht freigegebene Changes ('.$anzahl_nicht_freigegebene_changes.')</a></li>';

############ Freigegebene Changes ######################
 $sql=   'SELECT COUNT(hau_id) AS anzahl FROM aufgaben
        LEFT JOIN rollen_status ON hau_id=urs_hauid  
        WHERE hau_hprid = 6 AND urs_freigabe_ok = 1 AND hau_aktiv = 1 AND hau_abschluss = 0 ';
        
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
{
$anzahl_freigegebene_changes=$zeile['anzahl'];
}
echo '<li><a href="bericht_changes_frei.php">Freigegebene Changes ('.$anzahl_freigegebene_changes.')</a></li>';

############ ABGESCHLOSSENE Changes ######################

echo '<li id="current"><a href="bericht_changes_fertig.php">Abgeschlossene Changes (Letzte 20)</a></li>';

echo '</ul>';

echo '</div>';

echo '<br>';
echo '<br>';
echo '<br>';

$_SESSION['uau_hmaid']='';

// Aufgaben in Warteschlange

echo '<div id="header">';

echo '<ul>';

echo '<li><a href="#">Abgeschlossene Changes (Letzte 20)</a></li>';

echo '</ul>';

echo '</div>';

$anzeigefelder=array
    (
    'Ticket' => 'hau_id',
    'Prio' => 'upr_name',
    'Aufgabe' => 'hau_titel',
    'angelegt' => 'hau_anlage',
    'P-Ende' => 'hau_pende',
    'Eigner' => 'inhaber',
    'Freigabe durch' => 'freigeber',  
    'am' => 'urs_zeit',    
    'abgeschlossen am' => 'hau_abschlussdatum',
    'Change' => 'utc_name'
    );

$sql='SELECT *, m1.hma_login AS inhaber, m2.hma_login AS freigeber FROM aufgaben 
    LEFT JOIN rollen_status ON hau_id=urs_hauid 
    LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
    LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id 
    LEFT JOIN mitarbeiter m2 ON urs_freigabe_durch = m2.hma_id 
    INNER JOIN prioritaet ON hau_prio = upr_nummer
    LEFT JOIN typ_change ON hau_utcid = utc_id    
    WHERE urs_freigabe_ok=1 AND hau_hprid = 6 AND hau_aktiv = 1 AND hau_abschluss = 1 
    GROUP BY hau_id 
    ORDER BY hau_abschlussdatum DESC, hau_pende limit 20';
    
include('segment_liste_changes.php');

include('segment_fuss.php');
?>