<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$sql=
    'SELECT uau_hauid, hau_titel, uau_hmaid, uaz_hauid, uaz_pba, hma_level, uaz_pg, hma_name FROM aufgaben_zuordnung 
LEFT JOIN aufgaben ON hau_id = uaz_hauid 
LEFT JOIN aufgaben_mitarbeiter ON uau_hauid = uaz_hauid 
LEFT JOIN mitarbeiter ON hma_id = uau_hmaid 
LEFT JOIN level ON ule_id = hma_level 
WHERE uau_status = 0';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    if ($zeile['uaz_pba'] == 0)
        {
        $sql_insert='UPDATE aufgaben_zuordnung SET uaz_pba = ' . $zeile['uau_hmaid'] . ' WHERE uaz_hauid = '
            . $zeile['uaz_hauid'] . ' AND uaz_pba = 0';

        if (!($ergebnis_insert=mysql_query($sql_insert, $verbindung)))
            {
            fehler();
            }
        }

    if ($zeile['uaz_pba'] == 0)
        {
        $sql_insert='UPDATE aufgaben_zuordnung SET uaz_pba = ' . $zeile['uau_hmaid'] . ' WHERE uaz_hauid = '
            . $zeile['uaz_hauid'] . ' AND uaz_pba = 0';

        if (!($ergebnis_insert=mysql_query($sql_insert, $verbindung)))
            {
            fehler();
            }
        }

    if ($zeile['uaz_pg'] != $zeile['hma_level'])
        {
        $sql_change='UPDATE aufgaben_zuordnung SET uaz_pg = ' . $zeile['hma_level'] . ' WHERE uaz_hauid = '
            . $zeile['uau_hauid'] . ' AND uaz_pba = ' . $zeile['uau_hmaid'];

        echo $sql_change;

        if (!($ergebnis_change=mysql_query($sql_change, $verbindung)))
            {
            fehler();
            }
        }
    }
?>