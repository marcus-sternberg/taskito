<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
# include('segment_init.php');

 echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';

echo '<html>';

echo '<head>';

echo '<title>TaskScout24 - Task Organisation Management</title>';

echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8">';

echo '<meta http-equiv="refresh" content="60" >';  

echo '<link rel="stylesheet" type="text/css" href="css/tom.css">';

echo '<link rel="shortcut icon" href="tom.ico" type="image/x-icon">';

echo '<link rel="icon" href="tom.ico" type="image/x-icon">';

echo '</head>';

echo '<body>';

echo '<table border=0><tr><td width="10">&nbsp;</td><td>';

echo '<table border=0 width=900>';

echo '<tr>';

echo '<td width="150"><a href="index.php"><img src="bilder/tom_small.gif" width="112" height="56" border=0></a>';  

echo '<td width="550">';

echo '<table class="is24">';

echo '<caption class="is24">';

echo 'Neue nicht zugewiesene Gruppenaufgaben SE';

echo '</caption>';

echo '</table>';

echo '</td>';

echo '</tr>';

echo '<tr>';

echo '<td colspan=2  width="700">';

echo '</td>';

echo '<td width="200">&nbsp;';

echo '</td></tr>';

echo '</table>';

echo '</td></tr></table>';




$sql=$sql_uebersicht_in_gruppe;

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
    'Gruppe' => 'ule_kurz'
    );

$sql='SELECT *, m1.hma_login AS inhaber FROM aufgaben 
    LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
    LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id 
    LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
    LEFT JOIN level ON uaz_pg = ule_id 
    LEFT JOIN projekte ON hau_hprid = hpr_id   
    INNER JOIN prioritaet ON hau_prio = upr_nummer   
    WHERE uaz_pba = 0 AND hau_aktiv = 1 AND hau_abschluss = 0 AND uaz_pg = 7 
    GROUP BY hau_id 
    ORDER BY hau_prio DESC, hau_pende';
    
 include('segment_liste_gruppe.php');

include('segment_fuss.php');
?>