<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (isset($_REQUEST['hau_id']))
    {
    $task_id=$_REQUEST['hau_id'];
    }

// Suche die Informationen der Aufgabe

$sql_aufgabe='SELECT * FROM aufgaben WHERE hau_id = ' . $task_id;

// Sende Statement zur Datenbank
if (!$ergebnis_aufgabe=mysql_query($sql_aufgabe, $verbindung))
    {
    fehler();
    }

// Für gefundene Aufgabe die Bearbeiterdaten anpassen

while ($zeile_aufgabe=mysql_fetch_array($ergebnis_aufgabe))
    {

# Prüfe, ob es einen Eintrag für den Mitarbeiter gibt

    $sql_uam = 'SELECT * FROM aufgaben_mitarbeiter WHERE uau_hauid = '.$task_id.' AND uau_hmaid = '.$_SESSION['hma_id'];
    
// Sende Statement zur Datenbank
if (!$ergebnis_uam=mysql_query($sql_uam, $verbindung))
    {
    fehler();
    }  
    
if(mysql_num_rows($ergebnis_uam)!=0)   // Es gibt einen Eintrag für den Mitarbeiter
{
    
    $sql_status = 'SELECT uau_ma_status FROM aufgaben_mitarbeiter WHERE uau_hauid = '.$task_id.' AND uau_hmaid = '.$_SESSION['hma_id'];
    
// Sende Statement zur Datenbank
if (!$ergebnis_status=mysql_query($sql_status, $verbindung))
    {
    fehler();
    }      
      while ($zeile_status=mysql_fetch_array($ergebnis_status))
    {
        if($zeile['uau_ma_status']==0) // Der Status wird geändert
        {
            $sql_update = 'UPDATE aufgaben_mitarbeiter SET uau_ma_status = 1 WHERE uau_hauid = '.$task_id.' AND uau_hmaid = '.$_SESSION['hma_id'];
            // Sende Statement zur Datenbank
            if (!$ergebnis_update=mysql_query($sql_update, $verbindung))
            {
            fehler();
            } 
        }
    }
    
} else // Es gibt keinen Eintrag
{   
        
          // Erzeuge Eintrag in der Mitarbeitertabelle für die Aufgabe (teile Aufgabe zu)

        $sql_insert='INSERT INTO aufgaben_mitarbeiter (' .
            'uau_id, ' .
            'uau_hmaid, ' .
            'uau_hauid, ' .
            'uau_status, ' .
            'uau_prio, ' .
            'uau_stopp, ' .
            'uau_tende, ' .
            'uau_zeitstempel, ' .
            'uau_ma_status) ' .
            'VALUES ( ' .
            'NULL, ' .
            '"' . $_SESSION['hma_id'] . '", ' .
            '"' . $task_id . '", ' .
            '"0", ' .
            '"99", ' .
            '"0", ' .
            '"' . $zeile_aufgabe['hau_pende'] . '", ' .
            'NOW(), ' .
            '"1")';

        if (!($ergebnis_insert=mysql_query($sql_insert, $verbindung)))
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
            
  // Erzeuge einen Kommentar im Aufgabenlog

$sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
    'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
    . '", "Aufgabe übernommen", NOW() )';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }
}
}  // Ende der Aufgabeneintragung

 
# Pruefe, ob ich ggf. auswählen bin

$sql_sba=
    'SELECT uaz_sba FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $task_id . ' AND uaz_sba = ' . $_SESSION['hma_id'];

if (!($ergebnis_sba=mysql_query($sql_sba, $verbindung)))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis_sba) == 0) // Nein, ich bin kein SBA, also neu anlegen
    {

    # Pruefe, ob es für die Aufgabe einen Eintrag gibt mit leerem PBA

    $sql_pg='SELECT uaz_id FROM aufgaben_zuordnung WHERE uaz_hauid = ' . $task_id . ' AND uaz_pba = 0';

    if (!($ergebnis_pg=mysql_query($sql_pg, $verbindung)))
        {
        fehler();
        }

    if (mysql_num_rows($ergebnis_pg) == 0) // Nein, es gibt keine verwaiste Gruppe, also neu anlegen
        {
        $sql='INSERT INTO aufgaben_zuordnung (uaz_pba,uaz_pg, uaz_hauid) VALUES ("' . $_SESSION['hma_id'] . '", "'
            . $_SESSION['hma_level'] . '", "' . $task_id . '")';
        }
    else
        {
        $sql='UPDATE aufgaben_zuordnung SET uaz_pba = "' . $_SESSION['hma_id'] . '", uaz_pg = "'
            . $_SESSION['hma_level'] . '" WHERE uaz_hauid = "' . $task_id . '" AND uaz_pba = 0';
        }

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    } // Doch, ich war schon auswählen, also nichts zu tun


// Zurueck zur Liste

header('Location: schreibtisch_meine_aufgaben.php');
exit;
?>