#!/usr/bin/php  
<?php
###### Editnotes ####
#$LastChangedDate: 2011-12-30 13:16:54 +0100 (Fr, 30 Dez 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

# Schreibe den eigenen Zeitstempel (I am alive)

$sql='UPDATE watch_log SET zeitstempel_watchdog = NOW()';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }                              

# Hole nun den Zeitstempel vom Mailparser

$sql='SELECT zeitstempel_parser, uwa_alarm FROM watch_log';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }  

while ($zeile=mysql_fetch_array($ergebnis))
    {
        $zeitstempel_parser = $zeile['zeitstempel_parser'];
        $uwa_alarm = $zeile['uwa_alarm'];
    }
    
$zeitstempel_parser_unix = strtotime($zeitstempel_parser);   

if((time()-$zeitstempel_parser_unix >= 600) AND ($uwa_alarm==0))
{
    $sql='UPDATE watch_log SET uwa_alarm = 1';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }         
    
            $sql = 'insert into eventlog ( 
                    hel_area,
                    hel_type,
                    hel_referer,
                    hel_text) values ( 
                    "CRON", 
                    "Error", 
                    "",
                    "Zeitstempel der letzten Ausführung liegt mehr als 10 Minuten zurück: '.$zeitstempel_parser_unix.'")';   
        
            if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }

            $sql='INSERT INTO aufgaben (' .
                'hau_titel, ' .
                'hau_beschreibung, ' .
                'hau_anlage, ' .
                'hau_inhaber, ' .
                'hau_prio, ' .
                'hau_pende, ' .
                'hau_kalender, ' .
                'hau_nonofficetime, ' .
                'hau_zeitstempel, ' .
                'hau_aktiv, ' .
                'hau_terminaendern, ' .
                'hau_teamleiter, ' .
                'hau_datumstyp, ' .
                'hau_hprid, ' .
                'hau_typ, ' .
                'hau_tl_status, ' .
                'hau_dauer, ' .
                'hau_links, ' .
                'hau_utcid, ' .  
                'hau_ticketnr) ' .
                'VALUES ( ' .
                '"' . mysql_real_escape_string("Mailparser prüfen!").'", ' .
                '"' . mysql_real_escape_string("Es liegt kein aktueller Zeitstempel vor, möglicherweise wird der Parser nicht mehr ausgeführt. Bitte nach Behebung in der Tabelle watch_log in der Datenbank taskscout24 das Feld uwa_alarm wieder auf 0 setzen, damit wird der Watchdog wieder scharf geschaltet."). '", ' .
                'NOW(), ' .
                '"1", ' .
                '"2", ' .
                'NOW(), ' .
                '"0", ' .
                '"0", ' .
                'NOW(), ' .
                '"1", ' .
                '"0", ' .
                '"999", ' .
                '"2", ' .
                '"1", ' .
                '"16", ' .
                '"1", ' .
                '"1", ' .
                '"", ' .
                '"0", ' .   
                '"")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                } 
   
               $hau_id=mysql_insert_id();

            $sql='INSERT INTO aufgaben_zuordnung
                (uaz_hauid, uaz_pg) ' .
                'VALUES ("' . $hau_id . '", "4")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
   
}     
?>