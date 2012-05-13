<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$task_id=$_GET['hau_id'];

$sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
    'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
    . '", "Bearbeiter wurder der Aufgabe zugeordnet.", NOW() )';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$sql='INSERT INTO aufgaben_mitarbeiter (
            uau_hmaid, 
            uau_hauid, 
            uau_status, 
            uau_prio, 
            uau_stopp, 
            uau_ma_status) 
            VALUES ( 
            "' . $_SESSION['hma_id'] . '",                   
            "' . $task_id . '",         
            "0",  
            "99",          
            "0",  
            "1")';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$sql='INSERT INTO aufgaben_zuordnung (uaz_hauid, uaz_pba, uaz_pg) VALUES("' . $task_id . '", "' . $_SESSION['hma_id']
    . '", "' . $_SESSION['hma_level'] . '")';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

# Pruefe, ob es sich um eine Aufgabe im MR handelt

$sql_mr=
    'SELECT hau_pende, hpr_id, hau_nonofficetime FROM projekte LEFT JOIN aufgaben ON hau_hprid = hpr_id WHERE hau_id = '
    . $task_id;

if (!($ergebnis_mr=mysql_query($sql_mr, $verbindung)))
    {
    fehler();
    }

while ($zeile_mr=mysql_fetch_array($ergebnis_mr))
    {
    if ($zeile_mr['hpr_id'] == 5) // Es ist ein MR
        {
        $einsatzdauer=array();

        // Stelle fest, ob es ein Nachteinsatz ist
        if ($zeile_mr['hau_nonofficetime'] == 1)
            {
            $einsatzdauer[]=$zeile_mr['hau_pende'];
            $einsatzdauer[]=date("Y-m-d", strtotime("-1 day", strtotime($zeile_mr['hau_pende'])));
            }
        else
            {
            $einsatzdauer[]=$zeile_mr['hau_pende'];
            }

        foreach ($einsatzdauer AS $einsatztag)
            {
            $sql_kal = 'INSERT INTO kalender 
                    (hka_tag,
                    hka_hmaid,
                    hka_release) 
                    VALUES
                    ("' . $einsatztag . '", 
                     "' . $_SESSION['hma_id'] . '",
                     "1")';

            if (!($ergebnis_kal=mysql_query($sql_kal, $verbindung)))
                {
                fehler();
                }
            }
        }
    }

// Zurueck zur Liste

header('Location: aufgabe_ansehen.php?hau_id=' . $task_id);
exit;
?>