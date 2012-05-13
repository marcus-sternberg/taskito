<?php
###### Editnotes ####
#$LastChangedDate: 2012-02-16 08:25:02 +0100 (Do, 16 Feb 2012) $
#$Author: msternberg $ 
#####################

######### Define Includes ##################

require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
########### Read parameter ##################

if (!isset($_GET['auto']))
    {
    $auto='on';
    }
else
    {
    $auto=$_GET['auto'];
    }

if (!isset($_GET['option']))
    {
    $option='1';
    }
else
    {
    $option=$_GET['option'];
    }

############ Define Controls ################

$background[$option]='#F29900';

if ($auto == 'on')
    {

    $autolink='uebersicht_ticker.php?auto=off&option=' . $option;
    $autobild='bilder/icon_refresh.png';
    $autotext='Page-Refresh: ON!';
    }
else
    {

    $autolink='uebersicht_ticker.php?auto=on&option=' . $option;
    $autobild='bilder/icon_refresh_off.png';
    $autotext='Page-Refresh: OFF!';
    }

if (!isset($_POST['loeschen']))
    {
    include('segment_kopf.php');

   echo '<br><br><span class="box"><a name="pool">Bitte markieren zum Schließen</a></span><br><br>';

    $sql=
        'SELECT *, m1.hma_login AS inhaber, m2.hma_login AS mitarbeiter, m3.hma_login AS teamleiter FROM aufgaben 
        LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid 
        LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id 
        LEFT JOIN mitarbeiter m2 ON uau_hmaid = m2.hma_id 
        LEFT JOIN mitarbeiter m3 ON hau_teamleiter = m3.hma_id 
        LEFT JOIN typ ON hau_typ = uty_id 
        LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
        LEFT JOIN level ON uaz_pg = ule_id 
        LEFT JOIN projekte ON hau_hprid = hpr_id 
        INNER JOIN prioritaet ON hau_prio = upr_nummer 
        WHERE hau_typ = "16" AND
               hau_id = ANY(
                    SELECT DISTINCT hau_id FROM aufgaben 
                    LEFT JOIN aufgaben_mitarbeiter ON hau_id=uau_hauid
                        WHERE hau_abschluss = 0 
                        AND hau_aktiv = 1 
                        AND hau_tl_status <2) 
               GROUP BY hau_id 
               ORDER BY hau_anlage DESC, hau_zeitstempel DESC';

    $anzeigefelder=array
        (
        'TNR' => 'hau_id',
        'Prio' => 'upr_name',
        'Aufgabe' => 'hau_titel',
        'angelegt' => 'hau_anlage',
        'P-Ende' => 'hau_pende',
        'Eigner' => 'inhaber',
        'Projekt' => 'hpr_titel',
        'Mitarbeiter' => 'mitarbeiter',
        'Typ' => 'uty_name'
        );

    ################################################################################


    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    // Beginne mit Tabellenausgabe
    echo '<form name="nachrichten" method="post" action="bulk_loeschen.php">';

    echo '<table class="matrix" cellspacing="1" cellpadding="3" width="1200" border=0>';

        echo '<tr><td colspan="10"><span class="text"><input type="radio" name="checkall" onclick="checkedall(true)" /> alle markieren <input type="radio" name="checkall" onclick="checkedall(false)" /> alle zurücksetzen </span>&nbsp;&nbsp;<input type="submit" name="loeschen" value="Markierte Tickets schließen" class="formularbutton" /></td></tr>';

    echo '<tr>';

    echo '<td></td>';

    foreach ($anzeigefelder as $bezeichner => $inhalt)
        {
        echo '<td class="tabellen_titel" valign="top"><span class="xnormal_sort">' . $bezeichner
            . '</span></td>';
        }

    echo '</tr>';

  #  echo '<tr><td><input type="checkbox" name="checkall" onclick="checkedall(true)"></td></tr>';
                                                             
    // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
    while ($zeile=mysql_fetch_array($ergebnis))
        {

        $task_id = $zeile['hau_id'];

        include('segment_zeilenfarbe.php');


        // Beginne Datenausgabe
        echo '<tr>';

        echo '<td width="10"><input type="checkbox" name="ticket[' . $zeile['hau_id'] . ']"></td>';

        foreach ($anzeigefelder as $bezeichner => $inhalt)
            {
            switch ($bezeichner)
                {
                case 'angelegt':
                    $zeile[$inhalt]=substr(datum_anzeigen($zeile[$inhalt]), 0, 8);
                    break;

                case 'P-Ende':
                    if ($zeile[$inhalt] == '9999-01-01')
                        {
                        $zeile[$inhalt]='open';
                        }
                    else
                        {
                        $zeile[$inhalt]=substr(datum_anzeigen($zeile[$inhalt]), 0, 8);
                        }
                    break;
                }
            }

        foreach ($anzeigefelder as $bezeichner => $inhalt)
            {
            switch ($bezeichner)
                {
                case 'TNR':
                    echo '<td bgcolor="' . $color . '" class="' . $font . '">';

                    echo '<a href="aufgabe_ansehen.php?hau_id=' . $zeile['hau_id'] . '">' . $zeile[$inhalt]
                        . '</a></td>';
                    break;

                case 'Aufgabe':
                    echo '<td bgcolor="' . $color . '" class="' . $font . '">';

                    echo '<a href="aufgabe_ansehen.php?hau_id=' . $zeile['hau_id'] . '" onmouseover="Tip(\''
                        . substr(preg_replace('/\r\n|\r|\n/', ' ', ($zeile["hau_beschreibung"])), 0, 300)
                        . '\')" onmouseout="UnTip()">' . $zeile[$inhalt] . '</a></td>';
                    break;

                case 'angelegt':
                    $zeile[$inhalt]=substr(datum_anzeigen($zeile[$inhalt]), 0, 8);

                    echo '<td bgcolor="' . $color . '" class="' . $font . '">';

                    echo $zeile[$inhalt] . '</td>';

                    break;

                case 'P-Ende':
                    if ($zeile[$inhalt] == '9999-01-01')
                        {
                        $zeile[$inhalt]='open';
                        }
                    else
                        {
                        $zeile[$inhalt]=substr(datum_anzeigen($zeile[$inhalt]), 0, 8);
                        }

                    echo '<td bgcolor="' . $color . '" class="' . $font . '">';

                    echo $zeile[$inhalt] . '</td>';
                    break;

                case 'Projekt':
                    if (($zeile['hau_hprid'] > 10))
                        {
                        echo '<td bgcolor="' . $color . '" class="' . $font . '">';

                        echo '<a href="uebersicht_projekt.php?hpr_id=' . $zeile['hau_hprid'] . '">' . $zeile[$inhalt]
                            . '</a></td>';
                        }
                    else
                        {
                        echo '<td bgcolor="' . $color . '" class="' . $font . '">' . $zeile[$inhalt] . '</td>';
                        }
                    break;
                default:
                    echo '<td bgcolor="' . $color . '" class="' . $font . '">' . ($zeile[$inhalt]) . '</td>';
                    break;
                }
            }

        echo '</tr>';
        }

    echo '<tr><td colspan="10"><span class="text"><input type="radio" name="checkall" onclick="checkedall(true)" /> alle markieren <input type="radio" name="checkall" onclick="checkedall(false)" /> alle zurücksetzen </span>&nbsp;&nbsp;<input type="submit" name="loeschen" value="Markierte Tickets schließen" class="formularbutton" /></td></tr>';

    echo '</table>';

    echo '</form>';
    }
else
    {
    foreach ($_POST['ticket'] AS $ticket => $inhalt)
        {

        # Schreibe ins Log der Aufgabe

        $sql = 'INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
            'VALUES ("' . $ticket . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
            . '", "Aufgabe wurde geschlossen per BULK", NOW() )';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

    $sql='DELETE FROM aufgaben_zuordnung WHERE uaz_hauid = "' . $ticket . '"';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }            
           
   
    $sql='INSERT INTO aufgaben_zuordnung (uaz_hauid, uaz_pg) VALUES ("' . $ticket . '", "'.$_SESSION['hma_level'].'")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }
    
    $sql='DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid = "' . $ticket . '"';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }       

    $sql='INSERT INTO aufgaben_mitarbeiter (uau_hmaid, uau_hauid, uau_status, uau_ma_status) VALUES ("' . $_SESSION['hma_id'] . '", "' . $ticket . '", 1, 1)';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

    
         $sql="UPDATE aufgaben_mitarbeiter SET uau_status=1, uau_ma_status=1 WHERE uau_hauid = " . $ticket;

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        # Schließe die Hauptaufgabe

        $sql="UPDATE aufgaben SET hau_abschluss = 1, hau_abschlussdatum = NOW() WHERE hau_id = " . $ticket;

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }
        
        # Schließe ggf. offene Alarme
        
        $sql_alarm = 'SELECT COUNT(hal_id) FROM alarme WHERE hal_hauid = '.$ticket;
        
        if (!($ergebnis_alarm=mysql_query($sql_alarm, $verbindung)))
            {
            fehler();
            }        
        
        if(mysql_num_rows($ergebnis_alarm)>0)
        {
        
           $sql_alarm_info = 'SELECT * FROM alarme WHERE hal_hauid = '.$ticket;         

           if (!$ergebnis_alarm_info=mysql_query($sql_alarm_info, $verbindung))
           {
           fehler();
           }

           while($zeile_alarm_info = mysql_fetch_assoc($ergebnis_alarm_info)) { 
    
            $sql_insert = 'INSERT INTO alarme_historie (hal_nagiosid, hal_hauid, hal_meldung, hal_status, hal_cciid, hal_service)
                VALUES ("'.$zeile_alarm_info['hal_nagiosid'].'", "'.$zeile_alarm_info['hal_hauid'].'","'.$zeile_alarm_info['hal_meldung'].'","'.$zeile_alarm_info['hal_status'].'", "'.$zeile_alarm_info['hal_cciid'].'", "'.$zeile_alarm_info['hal_service'].'")';         
                 
                   
            if (!$ergebnis_insert=mysql_query($sql_insert, $verbindung)) 
            {
            fehler();
            }    
    
            $sql_delete = 'DELETE FROM alarme WHERE hal_hauid = "'.$ticket.'"';         

            if (!$ergebnis_delete=mysql_query($sql_delete, $verbindung))
            {
            fehler();
            }    
        }
        }
        
        
        
        }

    header('Location: schreibtisch_meine_gruppenaufgaben.php');
    exit;
    }

################################################################################

include('segment_fuss.php');
?>
