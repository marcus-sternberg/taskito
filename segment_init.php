<?php
###### Editnotes ####
#$LastChangedDate: 2012-02-03 13:37:38 +0100 (Fr, 03 Feb 2012) $
#$Author: msternberg $ 
#####################
error_reporting(E_ALL);

if (!isset($_SESSION['z']))
    {
    $_SESSION['z']=0;
    }

if (isset($_GET['sortierschluessel']) OR isset($_GET['sortkey']))
    {
    $_SESSION['z']=$_SESSION['z'] + 1;
    }

if (!isset($_SESSION['filterstring']))
    {
    $_SESSION['filterstring']='';
    }


// Belegung der Anzeigefelder

$anzeige_changes=array
    (
    'TNR' => 'hau_id',
    'Prio' => 'upr_name',
    'Aufgabe' => 'hau_titel',
    'angelegt' => 'hau_anlage',
    'P-Ende' => 'hau_pende',
    'Eigner' => 'hma_login',
    'Freigabe' => 'urs_freigabe_ok', 
    'am' => 'urs_zeit', 
    'durch' => 'urs_freigabe_durch', 
    'Changetyp' => 'utc_name'
    );

$anzeige_pool=array
    (
    'Ticket' => 'hau_ticketnr',
    'TNR' => 'hau_id',
    'Prio' => 'upr_name',
    'Aufgabe' => 'hau_titel',
    'angelegt' => 'hau_anlage',
    'P-Ende' => 'hau_pende',
    'Eigner' => 'inhaber',
    'Projekt' => 'hpr_titel',
    'Gruppe' => 'ule_name',
    'Typ' => 'uty_name',  
    'Mitarbeiter' => 'mitarbeiter',
    'aktualisiert' => 'ulo_zeitstempel'
    );

$anzeige_pool_ma=array
    (
    'TNR' => 'hau_id',
    'Ticket' => 'hau_ticketnr',
    'Pref' => 'uau_prio',
    'Prio' => 'upr_name',
    'Projekt' => 'hpr_titel',
    'Aufgabe' => 'hau_titel',
    'angelegt' => 'hau_anlage',
    'P-Ende' => 'hau_pende',
    'Eigner' => 'inhaber',
    'Gruppe' => 'ule_name',
    'Typ' => 'uty_name'
    );

$anzeige_suche=array
    (
    'Ticket' => 'hau_ticketnr',
    'TNR' => 'hau_id',
    'Prio' => 'upr_name',
    'Aufgabe' => 'hau_titel',
    'angelegt' => 'hau_anlage',
    'P-Ende' => 'hau_pende',
    'Eigner' => 'hma_login',
    'Projekt' => 'hpr_titel',
    'Gruppe' => 'ule_name',
    'Typ' => 'uty_name'
    );

$anzeige_jobs=array
    (
    'Ticket' => 'hau_ticketnr',
    'TNR' => 'hau_id',
    'Prio' => 'upr_name',
    'Aufgabe' => 'hau_titel',
    'angelegt' => 'hau_anlage',
    'P-Ende' => 'hau_pende',
    'Projekt' => 'hpr_titel',
    'Gruppe' => 'ule_name',
    'Typ' => 'uty_name',
    'Bearbeiter' => 'mitarbeiter',
    'Fortschritt [%]' => 'sum_fertig'
    );

$anzeige_angenommen=array
    (
    'TNR' => 'hau_id',
    'Ticket' => 'hau_ticketnr',
    'Pref' => 'uau_prio',
    'Prio' => 'upr_name',
    'Projekt' => 'hpr_titel',
    'Aufgabe' => 'hau_titel',
    'angelegt' => 'hau_anlage',
    'P-Ende' => 'hau_pende',
    'Eigner' => 'inhaber',
    'aktualisiert' => 'ulo_zeitstempel',
    'Fortschritt [%]' => 'sum_fertig'
    );

$anzeige_ir=array
    (
    'ID' => 'hau_id',
    'subject' => 'hir_titel',
    'impact' => 'hau_ticketnr',
    'prio' => 'upr_name',
    'created' => 'hau_anlage',
    'owner' => 'inhaber'
    );

$anzeige_ping=array
    (
    'PING an' => 'angepingt',
    'Projekt' => 'hpr_titel',
    'Ticket' => 'hau_ticketnr',
    'TNR' => 'hau_id',
    'Prio' => 'upr_name',
    'Aufgabe' => 'hau_titel',
    'angelegt' => 'hau_anlage',
    'P-Ende' => 'hau_pende',
    'Eigner' => 'inhaber',
    'Fortschritt [%]' => 'sum_fertig'
    );

$anzeige_delegiert=array
    (
    'Projekt' => 'hpr_titel',
    'Ticket' => 'hau_ticketnr',
    'TNR' => 'hau_id',
    'Prio' => 'upr_name',
    'Aufgabe' => 'hau_titel',
    'angelegt' => 'hau_anlage',
    'P-Ende' => 'hau_pende',
    'Eigner' => 'inhaber',
    'Mitarbeiter' => 'mitarbeiter',
    'Fortschritt [%]' => 'sum_fertig',
    'Gruppe' => 'ule_kurz'  
    );

$anzeige_gruppe=array
    (
    'Projekt' => 'hpr_titel',
    'Ticket' => 'hau_ticketnr',
    'TNR' => 'hau_id',
    'Prio' => 'upr_name',
    'Aufgabe' => 'hau_titel',
    'angelegt' => 'hau_anlage',
    'P-Ende' => 'hau_pende',
    'Eigner' => 'inhaber',
    'Mitarbeiter' => 'mitarbeiter',
    'Fortschritt [%]' => 'sum_fertig',
    'Gruppe' => 'ule_kurz'
    );

$anzeige_ma_status=array
    (
    'ticket' => 'hau_ticketnr',
    'TNR' => 'hau_id',
    'prio' => 'upr_name',
    'task' => 'hau_titel',
    'created' => 'hau_anlage',
    'P-End' => 'hau_pende',
    'R-End' => 'hau_tende',
    'owner' => 'inhaber',
    'staff member' => 'mitarbeiter',
    'progress [%]' => 'hau_fertig'
    );

$anzeige_non_pool=array
    (
    'ticket' => 'hau_ticketnr',
    'TNR' => 'hau_id',
    'prio' => 'upr_name',
    'task' => 'hau_titel',
    'created' => 'hau_anlage',
    'start' => 'hau_start',
    'P-End' => 'hau_pende'
    );

$anzeigefelder=array
    (
    'ticket' => 'hau_ticketnr',
    'TNR' => 'hau_id',
    'prio' => 'upr_name',
    'task' => 'hau_titel',
    'created' => 'hau_anlage',
    'start' => 'hau_start',
    'P-End' => 'hau_pende',
    'R-End' => 'hau_tende',
    'closed' => 'hau_zeitstempel'
    );

$anzeige_jobende=array
    (
    'Projekt' => 'hpr_titel',
    'Ticket' => 'hau_ticketnr',
    'TNR' => 'hau_id',
    'Prio' => 'upr_name',
    'Aufgabe' => 'hau_titel',
    'angelegt' => 'hau_anlage',
    'Eigner' => 'inhaber',
    'Mitarbeiter' => 'mitarbeiter',
    'Gruppe' => 'ule_kurz',
    'P-Ende' => 'hau_pende',
    'abgeschlossen' => 'hau_abschlussdatum'
    );
    
$anzeige_serie=array
    (
    'Titel' => 'utr_titel',
    'Nächste Ausführung' => 'utr_next_date',
    'fällig nach' => 'utr_pende_wert',
    'Wiederholungen' => 'utr_wiederholungwert',
    'Intervall' => 'utr_intervalltyp'
    );


// Statements

// Standard-Statement für alle JOINS

$sql_standard=
    ' /* '.$_SERVER['SCRIPT_FILENAME'].' */  SELECT *, m1.hma_login AS inhaber, m2.hma_login AS mitarbeiter, m3.hma_login AS teamleiter FROM aufgaben ' .
    'LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid ' .
    'LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id ' .
    'LEFT JOIN mitarbeiter m2 ON uau_hmaid = m2.hma_id ' .
    'LEFT JOIN mitarbeiter m3 ON hau_teamleiter = m3.hma_id ' .
    'LEFT JOIN typ ON hau_typ = uty_id ' .
    'LEFT JOIN log ON hau_id = ulo_aufgabe ' .     
    'LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id ' .
    'LEFT JOIN level ON uaz_pg = ule_id ' .
    'LEFT JOIN projekte ON hau_hprid = hpr_id  
                        INNER JOIN prioritaet ON hau_prio = upr_nummer ';

// Ergänzungsstatement LOG

$sql_log='LEFT JOIN log ON hau_id = ulo_aufgabe ';

// Berechne die längste Bearbeitungszeit der Bearbeiter für die Anzeige der eigenen Aufträge

$sql_schreibtisch_tende_berechnen=
    'SELECT *, m1.hma_login AS inhaber, m2.hma_login AS mitarbeiter, m3.hma_login AS teamleiter, ' .
    '(SELECT uau_tende FROM aufgaben_mitarbeiter ' .
    ' WHERE uau_hauid = hau_id ' .
    ' ORDER BY uau_tende DESC ' .
    ' LIMIT 1 ) AS uau_tende ' .
    ' FROM aufgaben ' .
    'LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid ' .
    'LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id ' .
    'LEFT JOIN mitarbeiter m2 ON uau_hmaid = m2.hma_id ' .
    'LEFT JOIN mitarbeiter m3 ON hau_teamleiter = m3.hma_id ' .
    'INNER JOIN typ ON hau_typ = uty_id ' .
    'LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id ' .
    'LEFT JOIN level ON uaz_pg = ule_id ' .
    'LEFT JOIN log ON hau_id = ulo_aufgabe ' .   
    'LEFT JOIN projekte ON hau_hprid = hpr_id  ' .
    'INNER JOIN prioritaet ON hau_prio = upr_nummer ';


// ##################  Start Einzelstatements  ##########################

// Übersicht

// Tickerabfragen
/*
$sql_ticker_offen = $sql_standard.
                    ' WHERE hau_aktiv = 1 AND hau_abschluss = 0 '.
                    $_SESSION['filterstring'].') '.
                    'GROUP BY hau_id '. 
                    'ORDER BY hau_anlage DESC, hau_zeitstempel DESC'; 

                    
                    
*/

$sql_ticker_offen=$sql_standard .
    'WHERE hau_id = ANY(SELECT DISTINCT hau_id FROM aufgaben LEFT JOIN aufgaben_mitarbeiter ON hau_id=uau_hauid ' .
    'WHERE (uau_status is NULL OR uau_status=0) AND (uau_ma_status IS NULL OR uau_ma_status <2) AND hau_aktiv = 1 '
    . $_SESSION['filterstring'] . ') ' .
    'GROUP BY hau_id ' .
    'ORDER BY hau_anlage DESC, hau_zeitstempel DESC';

$sql_ticker_hold=$sql_standard .
    'WHERE hau_id = ANY(SELECT DISTINCT hau_id FROM aufgaben LEFT JOIN aufgaben_mitarbeiter ON hau_id=uau_hauid ' .
    'WHERE uau_stopp > 0 AND (uau_status is NULL OR uau_status=0) AND (uau_ma_status IS NULL OR uau_ma_status <2) AND hau_aktiv = 1 '
    . $_SESSION['filterstring'] . ') ' .
    'GROUP BY hau_id ' .
    'ORDER BY hau_anlage DESC, hau_zeitstempel DESC';

$sql_ticker_offen_ohne_ma=$sql_standard .
    'WHERE hau_id = ANY(SELECT DISTINCT hau_id FROM aufgaben LEFT JOIN aufgaben_mitarbeiter ON hau_id=uau_hauid ' .
    'WHERE uau_id is NULL AND hau_aktiv = 1 ' . $_SESSION['filterstring'] . ') ' .
    'GROUP BY hau_id ' .
    'ORDER BY hau_anlage DESC, hau_zeitstempel DESC';

$sql_ticker_geschlossen=$sql_standard .
    'WHERE hau_abschluss = 1 AND hau_aktiv = 1' . $_SESSION['filterstring'] . ' ' .
    'GROUP BY hau_id ' .
    'ORDER BY hau_abschlussdatum DESC LIMIT 30';


// Aufgaben anderer

$sql_uebersicht_in_gruppe=$sql_standard .
    'WHERE hau_id = ANY(SELECT uaz_hauid FROM aufgaben_zuordnung LEFT JOIN aufgaben ON hau_id=uaz_hauid 
                                LEFT JOIN level ON ule_id = uaz_pg WHERE uaz_pba = 0 AND hau_aktiv = 1 AND hau_abschluss = 0 '
    . $_SESSION['filterstring'] . ') ' .
    'GROUP BY hau_id ' .
    'ORDER BY hau_prio, hau_pende';

$sql_uebersicht_ma_im_pool=$sql_standard .
    'WHERE hau_id = ANY(SELECT uau_hauid FROM aufgaben_mitarbeiter LEFT JOIN aufgaben ON hau_id=uau_hauid WHERE uau_status = 0 AND uau_ma_status =0 '
    .
    'AND hau_tl_status = 1  AND hau_abschluss = 0 AND hau_aktiv = 1 ' . $_SESSION['filterstring'] . ') ' .
    'GROUP BY hau_id ' .
    'ORDER BY hau_prio, hau_pende';

$sql_uebersicht_gruppe_geschlossen=$sql_standard .
    'WHERE hau_id = ANY(SELECT hau_id FROM aufgaben LEFT JOIN aufgaben_mitarbeiter ON hau_id=uau_hauid LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id WHERE hau_abschluss = 1 AND (hau_abschlussdatum >= "'.date('Y-m-d',mktime(0,0,0,date('n'),date('j')-7,date('Y'))).'" AND hau_abschlussdatum <= "'.date('Y-m-d',mktime(0,0,0,date('n'),date('j'),date('Y'))).'") AND hau_aktiv = 1 ' . $_SESSION['filterstring'] . ') ' .
    'GROUP BY hau_id ' .
    'ORDER BY hau_abschlussdatum DESC';
    
$sql_uebersicht_ma_in_arbeit=$sql_standard .
    'WHERE hau_id = ANY(SELECT uau_hauid FROM aufgaben_mitarbeiter INNER JOIN aufgaben ON hau_id=uau_hauid ' .
    'WHERE uau_status = 0 AND uau_ma_status =1 AND hau_tl_status = 1 AND hau_aktiv = 1 ' . $_SESSION['filterstring']
    . ') ' .
    'GROUP BY hau_id ' .
    'ORDER BY hau_prio, hau_pende';


// Schreibtisch

// Aufträge

$sql_schreibtisch_aktuelle_auftraege=$sql_schreibtisch_tende_berechnen .
    'WHERE hau_abschluss = 0 AND hau_inhaber = "' . $_SESSION['hma_id'] . '" AND hau_aktiv = "1" AND hau_tl_status <2 '
    . $_SESSION['filterstring'] . ' ' .
    'GROUP BY hau_id ' .
    'ORDER BY hau_prio DESC, hau_pende';

$sql_schreibtisch_serienaufgaben='SELECT * FROM tracker WHERE utr_inhaber = ' . $_SESSION['hma_id'] .
    '  ORDER BY utr_next_date DESC';

$sql_schreibtisch_abgelehnte_auftraege=$sql_standard .
    'WHERE hau_inhaber = ' . $_SESSION['hma_id'] . ' AND hau_aktiv = "1" AND hau_tl_status="2" '
    . $_SESSION['filterstring'] .
    'GROUP BY hau_id ' .
    'ORDER BY uau_prio, hau_prio, hau_pende';

$sql_schreibtisch_abgeschlossene_auftraege=$sql_standard .
    'WHERE hau_abschluss AND hau_inhaber = "' . $_SESSION['hma_id'] . '" AND hau_aktiv = "1" AND hau_tl_status <2 '
    . $_SESSION['filterstring'] . ' ' .
    'GROUP BY hau_id ' .
    'ORDER BY uau_zeitstempel DESC LIMIT 30';


// Aufgaben

$sql_schreibtisch_aktuelle_aufgaben=$sql_standard .
    'WHERE hau_id = ANY(SELECT DISTINCT hau_id FROM aufgaben LEFT JOIN aufgaben_mitarbeiter ON hau_id=uau_hauid ' .
    'WHERE uau_hmaid = "' . $_SESSION['hma_id'] . '" AND hau_aktiv = "1" ' .
    'AND uau_status = 0 AND uau_ma_status = 0 ' . $_SESSION['filterstring'] . ') AND uau_hmaid = "'
    . $_SESSION['hma_id'] . '" ' .
    'GROUP BY hau_id ' .
    'ORDER BY uau_prio, hau_pende';

$sql_schreibtisch_offene_gruppenjobs=$sql_standard .
    'WHERE uaz_pg = ' . $_SESSION['hma_level'] . ' AND uaz_pba = 0 AND hau_aktiv =1 AND
                                         hau_abschluss = 0 ' . $_SESSION['filterstring'] . ' ' .
    'GROUP BY hau_id ' .
    'ORDER BY hau_zeitstempel DESC';
    //'ORDER BY uau_prio, hau_pende';
/*
$sql_schreibtisch_aufgaben_angenommen=$sql_standard .
    'WHERE hau_id = ANY(SELECT DISTINCT hau_id FROM aufgaben LEFT JOIN aufgaben_mitarbeiter ON hau_id=uau_hauid ' .
    'WHERE uau_hmaid = "' . $_SESSION['hma_id'] . '" AND hau_aktiv = "1" ' .
    'AND uau_status = 0 AND uau_ma_status = 1 ' . $_SESSION['filterstring'] . ') AND uau_hmaid = "'
    . $_SESSION['hma_id'] . '" ' .
    'OR hau_id = ANY(SELECT DISTINCT hau_id FROM aufgaben ' .
    'LEFT JOIN log ON ulo_aufgabe = hau_id ' .
    'LEFT JOIN log_status ON uls_uloid = ulo_id ' .
    'WHERE hau_aktiv = "1" AND hau_abschluss = 0 AND uls_ping_an = ' . $_SESSION['hma_id'] . $_SESSION['filterstring']
    . ') ' .
    'GROUP BY hau_id ' .
    'ORDER BY hau_zeitstempel DESC';
    //'ORDER BY uau_prio, hau_pende';
*/

$sql_schreibtisch_aufgaben_angenommen=$sql_standard .
    'WHERE  
      	   hau_id  IN(
      	   					SELECT DISTINCT hau_id FROM aufgaben 
      	   										   LEFT JOIN aufgaben_mitarbeiter ON hau_id=uau_hauid 
    											   WHERE (
    											   			uau_hmaid = "' . $_SESSION['hma_id'] . '"
		    											   	AND hau_aktiv = "1"
    													    AND uau_status = 0 AND uau_ma_status = 1 
    													  ) 
    											   		OR 
    											   		hau_id  IN(
    											  					SELECT DISTINCT hau_id FROM aufgaben 
    																LEFT JOIN log ON (ulo_aufgabe = hau_id) 
    																LEFT JOIN log_status ON (uls_uloid = ulo_id) 
    																WHERE hau_aktiv = "1" AND hau_abschluss = 0 
    																AND uls_ping_an = ' . $_SESSION['hma_id'].'
    															  ) 
    				)
    		' . $_SESSION['filterstring'] . '
			GROUP BY hau_id 
			ORDER BY hau_zeitstempel DESC';
			//'ORDER BY uau_prio, hau_pende';


$sql_schreibtisch_aufgaben_mit_PING=
    'SELECT DISTINCT hau_datumstyp, hau_hprid, hau_beschreibung, hpr_titel, hau_ticketnr, hau_id, upr_name, hau_titel, hau_anlage, hau_pende, m1.hma_login AS inhaber, m2.hma_login AS angepingt FROM aufgaben
                                            LEFT JOIN aufgaben_mitarbeiter ON uau_hauid = hau_id
                                            LEFT JOIN log ON hau_id = ulo_aufgabe
                                            LEFT JOIN log_status ON uls_uloid = ulo_id 
                                            LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id
                                            LEFT JOIN mitarbeiter m2 ON ulo_ping = m2.hma_id
                                            INNER JOIN typ ON hau_typ = uty_id
                                            LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
                                            LEFT JOIN level ON uaz_pg = ule_id 
                                            LEFT JOIN projekte ON hau_hprid = hpr_id
                                            INNER JOIN prioritaet ON hau_prio = upr_nummer
                                            WHERE hau_aktiv = "1"
                                            AND hau_abschluss = 0
                                            AND uls_ping_an > 0
                                            AND uls_ping_von = "'
    . $_SESSION['hma_id'] . '" ' . $_SESSION['filterstring'] .
    'ORDER BY uls_zeit DESC';


// Team

// Zuteilung

$sql_team_im_pool=$sql_standard .
    'WHERE hau_id = ANY(SELECT DISTINCT hau_id FROM aufgaben LEFT JOIN aufgaben_mitarbeiter ON hau_id=uau_hauid ' .
    'WHERE hau_aktiv = "1" AND hau_tl_status = 0 ' .
    'AND IFNULL(uau_status,0) = 0 ' .
    'AND IFNULL(uau_ma_status,0) = 0 ' . $_SESSION['filterstring'] . ') ' .
    'GROUP BY hau_id ' .
    'ORDER BY hau_zeitstempel DESC';

$sql_team_delegiert=$sql_standard .
    'WHERE hau_id = ANY(SELECT DISTINCT hau_id FROM aufgaben LEFT JOIN aufgaben_mitarbeiter ON hau_id=uau_hauid ' .
    'WHERE hau_aktiv = "1" AND hau_tl_status = 1 ' . //AND hau_teamleiter = "'.$_SESSION['hma_id'].'"'.
'AND uau_status = 0 AND uau_ma_status < 2 ' . $_SESSION['filterstring'] . ') ' .
    'GROUP BY hau_id ' .
    'ORDER BY upr_nummer, hau_pende';

$sql_team_abgelehnt=$sql_standard .
    'WHERE hau_id = ANY(SELECT DISTINCT hau_id FROM aufgaben LEFT JOIN aufgaben_mitarbeiter ON hau_id=uau_hauid ' .
    'WHERE hau_aktiv = "1" AND hau_tl_status = 1 ' . //AND hau_teamleiter = "'.$_SESSION['hma_id'].'"'.
'AND uau_ma_status = 2 ' . $_SESSION['filterstring'] . ') ' .
    'GROUP BY hau_id ' .
    'ORDER BY hau_prio, hau_pende';

$sql_team_beendet=$sql_standard .
    'WHERE hau_abschluss=1 ' . $_SESSION['filterstring'] . ' ' .
    'GROUP BY hau_id ' .
    'ORDER BY uau_zeitstempel DESC LIMIT 30';


// Suche

// alle Statements direkt in archiv_suche.php


// ################## Ende Statements #############################


// Icons

$icons_pool=array(array
    (
    "inhalt" => "anschauen",
    "bild" => "icon_take.gif",
    "link" => "task_view_dc.php"
    ));

$icons_angenommen=array(array
    (
    "inhalt" => "bearbeiten",
    "bild" => "icon_edit.gif",
    "link" => "task_edit.php"
    ));

$icons_angenommen[]=(array
    (
    "inhalt" => "Aktivitäten",
    "bild" => "icon_action.gif",
    "link" => "task_action.php"
    ));

$icons_angenommen[]=(array
    (
    "inhalt" => "zurückgeben",
    "bild" => "icon_del.gif",
    "link" => "task_retour_dc.php"
    ));
?>