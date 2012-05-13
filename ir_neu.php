<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_kopf.php'); 

# Lese gewünschten IR aus

$hir_id=$_REQUEST['hir_id'];

echo '<br>';
    # Baue Layout-Tabelle
    echo '<table width=100%><tr><td width="10">&nbsp;</td><td>';

    # Prüfe, ob der IR bereits gesperrt ist
    
    $sql = 'SELECT * FROM ir_sperre WHERE uisp_hirid = '.$hir_id;
    
        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }    
    
    if(mysql_num_rows($ergebnis)==0) // Nein, es gibt eine Sperre
    {
    # Sperre den IR
    
        $sql_sperre = 'INSERT INTO ir_sperre (uisp_hmaid, uisp_hirid) VALUES ("' . $_SESSION['hma_id'] . '", "'
            . $_REQUEST['hir_id'] . '")';

        if (!($ergebnis_sperre=mysql_query($sql_sperre, $verbindung)))
            {
            fehler();
            }
     }

    function status_klick($zaehler, $text, $status, $id)
        {
        if ($zaehler < $status)
            {
            $farbe='#3CB500';
            $bild='ja.gif';
            }
        else
            {
            $farbe='#EC6A47';
            $bild='nein.gif';
            }

        if ($zaehler == 5)
            {
            echo '
            <div style="width: 100%; height:30px; background-color: ' . $farbe . '; border-spacing: 0; margin: 0px;"> 
            <div style="margin: 0px; padding: 5px; word-wrap: break-word; display: table-cell; vertical-align: middle;">
            <a href="ir_status_change.php?status=' . $zaehler . '&hir_id=' . $id . '" onclick="return window.confirm(\'IR archivieren?\');">
            <img src="bilder/' . $bild . '" border="0">
            </a>  
            </div>
            <div style="margin: 0; padding: 5px;width: 100%; display: table-cell; vertical-align: middle;"> 
            <a href="ir_status_change.php?status=' . $zaehler . '&hir_id=' . $id . '" onclick="return window.confirm(\'IR archivieren?\');">  ' . $text . '
            </a>  
            </div>
             </div>  ';
            }
        else
            {
            echo '
            <div style="width: 100%; height:30px; background-color: ' . $farbe . '; border-spacing: 0; margin: 0px;"> 
            <div style="margin: 0px; padding: 5px; word-wrap: break-word; display: table-cell; vertical-align: middle;">
            <a href="ir_status_change.php?status=' . $zaehler . '&hir_id=' . $id . '">
            <img src="bilder/' . $bild . '" border="0">
            </a>  
            </div>
            <div style="margin: 0; padding: 5px;width: 100%; display: table-cell; vertical-align: middle;"> 
            <a href="ir_status_change.php?status=' . $zaehler . '&hir_id=' . $id . '">  ' . $text . '
            </a>  
            </div>             
            </div>  ';
            }
        }


    # Untermenü einblenden

    $sql='SELECT hir_id,hir_status FROM ir_stammdaten WHERE hir_id = "' . $hir_id . '"';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
    while ($zeile=mysql_fetch_array($ergebnis))
        {
        
        echo '<br><table class="matrix">';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">';

echo 'Incident Report: ' . $zeile['hir_id'] . ' ';

echo ' | ';


    $sql_count='SELECT COUNT(uir_id) AS menge FROM ir_log WHERE uir_hirid = "' . $hir_id . '"';

    if (!($ergebnis_count=mysql_query($sql_count, $verbindung)))
        {
        fehler();
        }
    // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
    while ($zeile_count=mysql_fetch_array($ergebnis_count))
        {
        echo '<a href="#Kommentar">Logeinträge: ' . $zeile_count['menge'] . '</a>';
        }


    echo ' | ';


    $sql_count='SELECT COUNT(uir_id) AS menge FROM ir_todo WHERE uir_hirid = "' . $hir_id . '"';

    if (!($ergebnis_count=mysql_query($sql_count, $verbindung)))
        {
        fehler();
        }

    // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
    while ($zeile_count=mysql_fetch_array($ergebnis_count))
        {
        echo '<a href="#todo">ToDos: ' . $zeile_count['menge'] . '</a>';
        }



    echo ' | ';



    echo 'aktueller Status:  ';



    # Statusanzeige

    $status_array=array
        (
        "1" => "eröffnet",
        "2" => "Analyse",
        "3" => "Fixing",
        "4" => "Testen",
        "5" => "geschlossen"
        );


    echo $status_array[$zeile['hir_status']];
    
        echo ' | ';

        echo '<td class="text_mitte" bgcolor="#ff000c" align="center"><a href="ir_unlock.php?hir_id=' . $hir_id
        . '"><span style="color:#ffffff;">ENTSPERRE IR</span></a></td>';  

    echo '</th>';
 
        $hir_status = $zeile['hir_status'];
        }

    echo '</tr>';

    echo '</table>'; // Ende Statusanzeige IR

    echo '<br><br>';

    echo '<table width="1000">';

    echo '<tr><td>'; // Große Datentabelle bauen

    # Stammdaten des IR auslesen

    $sql='SELECT * FROM ir_stammdaten WHERE hir_id = "' . $hir_id . '"';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
    while ($zeile=mysql_fetch_array($ergebnis))
        {

        $meeting = $zeile['hir_meeting'];
        $ood=$zeile['hir_ood'];
        $agent_id=$zeile['hir_agent'];

        echo '<form action="ir_speichern.php?toggle=1" method="post">';

        echo '<input type="hidden" name="hir_id" value="' . $hir_id . '">';
        
    
    echo '<table class="matrix"  width="700">';

echo '<tr><td class="text_mitte" bgcolor="#FFCA5E" align="center">Daten des Incidents</td></tr>';

echo '<tr>';

    echo '<tr>';

    echo '<td  class="is24_ir_head">';

        echo 'Thema des Incidents:';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td>';

        echo '<input style="width:700px" type="text" value="' . htmlspecialchars($zeile['hir_problem']) . '" name="hir_problem">';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein_fett">';

        echo 'Problembeschreibung:';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td>';

        echo '<textarea cols="85" rows="8" name="hir_beschreibung">' . htmlspecialchars($zeile['hir_beschreibung']) . '</textarea>';

        echo '</td>';

        echo '</tr>';

        echo '<tr><td>';

        echo '<table>';

        echo '<tr>';

        echo '<td class="text_klein_fett">Auswirkung: </td>';

        echo '<td class="text_klein_fett">Priorität: </td>';

        echo '<td class="text_klein_fett">Kategorie: </td>';

        echo '<td class="text_klein_fett">Release (falls Ursache für Incident): </td></tr>';
        
        echo '<tr>';

        echo '<td>';

        echo '<select size="1" name="hir_auswirkung">';
        $sql_impact='SELECT uia_id, uia_name FROM impact ' .
            'ORDER BY uia_sort';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_impact=mysql_query($sql_impact, $verbindung))
            {
            fehler();
            }

        while ($zeile_impact=mysql_fetch_array($ergebnis_impact))
            {
            if ($zeile['hir_auswirkung'] == $zeile_impact['uia_id'])
                {
                echo '<option value="' . $zeile_impact['uia_id'] . '" selected><span class="text">'
                    . $zeile_impact['uia_name'] . '</span></option>';
                }
            else
                {
                echo '<option value="' . $zeile_impact['uia_id'] . '"><span class="text">' . $zeile_impact['uia_name']
                    . '</span></option>';
                }
            }

        echo '</select>';

        echo '</td>';

        echo '<td>';

        echo '<select size="1" name="hir_prio">';
        $sql_prio='SELECT upr_nummer, upr_name FROM prioritaet ' .
            'ORDER BY upr_sort';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_prio=mysql_query($sql_prio, $verbindung))
            {
            fehler();
            }

        while ($zeile_prio=mysql_fetch_array($ergebnis_prio))
            {
            if ($zeile['hir_prio'] == $zeile_prio['upr_nummer'])
                {
                echo '<option value="' . $zeile_prio['upr_nummer'] . '" selected><span class="text">'
                    . $zeile_prio['upr_name'] . '</span></option>';
                }
            else
                {
                echo '<option value="' . $zeile_prio['upr_nummer'] . '"><span class="text">' . $zeile_prio['upr_name']
                    . '</span></option>';
                }
            }

        echo '</select>';

        echo '</td>';

        echo '<td>';

        echo '<input style="width:200px" type="text" value="' . $zeile['hir_kategorie'] . '" name="hir_kategorie">';

        echo '</td>';

        echo '<td>';

        echo '<input style="width:200px" type="text" value="' . $zeile['hir_release'] . '" name="hir_release">';

        echo '</td>';
        
        echo '</tr>';

        echo '</table>';

        echo '</td></tr>';

        echo '<tr>';

        echo '<td class="text_klein_fett">';

        echo 'Analyse / Ergebnisse:';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td>';

        echo '<textarea cols="85" rows="5" name="hir_analyse">' . $zeile['hir_analyse'] . '</textarea>';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein_fett">';

        echo 'Getroffene Maßnahmen / Anpassungen:';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td>';

        echo '<textarea cols="85" rows="5" name="hir_massnahme">' . $zeile['hir_massnahme'] . '</textarea>';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein_fett">';

        echo 'Lessons Learned:';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td>';

        echo '<textarea cols="85" rows="3" name="hir_lessons">' . $zeile['hir_lessons'] . '</textarea>';

        echo '</td>';

        echo '</tr>';

        echo
            '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Änderungen speichern" class="formularbutton" /></td></tr>';

        echo '</table>';

        echo '</form>';
        }

    echo '</td>'; # Datentabelle Teil eins

    echo '<td valign="top">';

    # Beginn der Statustabelle

echo '<table class="matrix">';

echo '<tr><td class="text_mitte" bgcolor="#FFCA5E" align="center">Details zum IR</td></tr>';

echo '<tr>';

    echo '<tr>';

    $sql='SELECT ude_status, ude_zeitstempel FROM defcon
         ORDER BY ude_zeitstempel DESC LIMIT 1';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        switch ($zeile['ude_status'])
            {
            case 1:
                $color='#EE775F';
                $status='KRITISCH';
                break;

            case 2:
                $color='#F3C39B';
                $status='PROBLEM';
                break;

            case 3:
                $color='#FFF8B3';
                $status='WARNUNG';
                break;

            case 4:
                $color='#C1E2A5';
                $status='OK';
                break;
            }

        echo '<td class="text_mitte" align="center" bgcolor="' . $color . '" width="200">Aktueller DEFCON: <strong>'
            . $zeile['ude_status'] . '</strong>&nbsp;<br>(' . $status . ')</td>';
        }

    echo '</tr>';

    echo '<tr><td><hr></td></tr>';

    echo '<form action="ir_speichern.php?toggle=2" method="post">';

    echo '<input type="hidden" name="hir_id" value="' . $hir_id . '">';

    echo '<tr>';

    echo '<td class="text_klein_fett">';

    echo 'OOD:';

    echo '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td>';

    echo '<input style="width:200px" type="text" value="' . $ood . '" name="hir_ood">';

    echo '</td>';

    echo '</tr>';

    echo '<td class="text_klein_fett">';

    echo 'angelegt von:';

    echo '</td></tr><tr>';

    echo '<td>';

    echo '<select size="1" name="hir_agent">';
    $sql_agent= 'SELECT hma_id, hma_name FROM mitarbeiter WHERE hma_level > 1 AND hma_level < 99 AND hma_aktiv = 1 ' .
                'ORDER BY hma_name';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_agent=mysql_query($sql_agent, $verbindung))
        {
        fehler();
        }

    while ($zeile_agent=mysql_fetch_array($ergebnis_agent))
        {
        if ($agent_id == $zeile_agent['hma_id'])
            {
            echo '<option value="' . $zeile_agent['hma_id'] . '" selected><span class="text">'
                . $zeile_agent['hma_name'] . '</span></option>';
            }
        else
            {
            echo '<option value="' . $zeile_agent['hma_id'] . '"><span class="text">' . $zeile_agent['hma_name']
                . '</span></option>';
            }
        }

    echo '</select>';

    echo '</td>';

    echo '<tr><td><hr></td></tr>';

    echo '<tr>';

    echo '<td class="text_klein_fett">';

    echo 'Nächstes Treffen:';

    echo '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td>';

    echo '<input style="width:200px" type="text" value="' . $meeting . '" name="hir_meeting">';

    echo '</td>';

    echo '</tr>';

    echo
        '<tr><td style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Details ändern" class="formularbutton" /></td></tr>';

    echo '</form>';

    echo '<tr><td><hr></td></tr>';

    echo '<tr>';

    echo '<td class="xnormal_sort" colspan="3">Attached files:</td>';

    echo '</tr>';

    $target_path="ir/" . $hir_id . "/";

    echo '<td class="xnormal_sort">';

    if (is_dir($target_path))
        {
        if ($handle=opendir($target_path))
            {
            while (false !== ($file=readdir($handle)))
                if ($file != '.' AND $file != '..')
                    {
                    echo '<a href="' . $target_path . $file . '" target="_blank">' . ($file)
                        . '</a>  <a href="ir_file_loeschen.php?name=' . $file . '&pfad=' . $target_path . '&ir='
                        . $hir_id
                        . '" onclick="return window.confirm(\'Delete File?\');"><img src="bilder/icon_loeschen.gif" border=0></a><br>';
                    }
            closedir($handle);
            }
        }

    echo '</td></tr>';
    echo '<tr><td><hr></td></tr>';     
    echo '<tr>';

    echo '<td class="xnormal_sort" colspan="3">Status:</td>';

    echo '</tr>';    
            # Statusanzeige

        $status_array=array
            (
            "1" => "eröffnet",
            "2" => "Analyse",
            "3" => "Fixing",
            "4" => "Testen",
            "5" => "geschlossen"
            );

        foreach ($status_array AS $zaehler => $status)
            {
            if ($zaehler < $hir_status)
                {
                echo '<tr><td>';

                status_klick($zaehler, $status, $hir_status, $hir_id);

                echo '</td></tr>';
                }
            else if ($zaehler == $hir_status)
                {
                echo '<tr><td>
                    <div style="height: 30px;font-weight:bold; background-color:#ECE247; margin: 0; padding: 5px; width: 100%; display: table-cell; vertical-align: middle;"> 
                        '
                    . $status . '</td></tr>';
                }
            else
                {
                echo '<tr><td>';
                status_klick($zaehler, $status, $hir_status, $hir_id);

                echo '</td></tr>';
                }
            }
    



    echo '</table>';

    echo '</td></tr>';

    echo '</table><br>'; # Datentabelle Ende

    echo '<table width="1000">';

    echo '<tr><td>'; // Activity Tabelle bauen

    echo '<form action="ir_kommentar_speichern.php" method="post" enctype="multipart/form-data">';

echo '<table class="matrix" width="500">';

echo '<tr><td class="text_mitte" bgcolor="#FFCA5E" align="center" colspan="5">Neuer Kommentar / neue Aktivität</td></tr>';

echo '<tr>';
    
    // Datum Kommentar

    echo '<tr>';

    echo '<td class="text_klein">Datum: </td><td colspan="5"><input type="text" name="uir_datum" value="'
        . date("d.m.Y H:i") . '" style="width:340px;"></td>';

    echo '</tr>';

    // ID ces Schreibenden

    echo '<input type="hidden" name="uir_hmaid" value="' . $_SESSION['hma_id'] . '">';

    // Zuordnung zum IR

    echo '<input type="hidden" name="uir_hirid" value="' . $hir_id . '">';

    // Text des Kommentars

    echo '<tr>';

    echo
        '<td class="text_klein" valign="top">Kommentar:&nbsp;&nbsp;</td><td colspan="5"><textarea cols="40" rows="10" name="uir_eintrag"></textarea></td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein">ins LOG kopieren?</td><td><input type="checkbox" name="ulo_extra"></td>';

    echo '</tr>';

    // Fileupload

    echo '<tr>';

    echo
        '<td class="text_klein" valign="top">Anlage:&nbsp;&nbsp;</td><td colspan="5"><input type="file" name="hau_datei"></td>';

    echo '</tr>';


    // Formularbutton

    echo
        '<tr><td colspan="5" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Speichere Eintrag" class="formularbutton" /></td>';

    echo '<tr><td colspan="5"><hr></td>';

    $sql='SELECT * FROM ir_log ' .
        'INNER JOIN mitarbeiter ON uir_hmaid = hma_id ' .
        'WHERE uir_hirid = ' . $hir_id .
        ' ORDER BY uir_datum DESC';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    echo
        '<tr><a name="Kommentar"><td class="tabellen_titel">Datum</td><td class="tabellen_titel">Kommentar</td><td>durch</td><td>&nbsp;</td></a><tr>';

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        echo '<tr>';

        echo '<td class="text_klein" valign="top">' . zeitstempel_anzeigen($zeile['uir_datum']) . '</td>';

        echo '<td class="text_klein" valign="top">' . nl2br($zeile['uir_eintrag']) . '</td>';

        echo '<td class="text_klein" valign="top">' . $zeile['hma_login'] . '</td>';

        echo '<td class="text_klein" valign="top"><a href="ir_kommentar_aendern.php?uir_id=' . $zeile['uir_id']
            . '"><img src="bilder/icon_aendern.gif" border="0" alt="change comment"></a></td>';

        echo '<td class="text_klein" valign="top"><a href="ir_kommentar_loeschen.php?uir_id=' . $zeile['uir_id']
            . '" onclick="return window.confirm(\'Delete Datarecord?\');"><img src="bilder/icon_loeschen.gif" border="0" alt="delete comment"></a></td>';

        echo '</tr>';
        }

    echo '</table>';
    
    
    echo '</form>';

    echo '<td valign="top">&nbsp;&nbsp;</td><td valign="top">'; // Zweite Spalte

    echo '<form action="ir_todo_speichern.php" method="post">';

    echo '<table class="matrix" width="500">';
    
    echo '<tr><td class="text_mitte" bgcolor="#FFCA5E" align="center" colspan="5">Neue Aufgabe</td></tr>';

    // Datum Kommentar

    echo '<tr>';

    echo '<td class="text_klein">Aufgabe: </td><td colspan="2"><input type="text" name="uir_todo" style="width:340px;"></td>';

    echo '</tr>';

    // Wer machts?

    echo '<tr>';

    echo '<td class="text_klein">verantwortlich: </td><td colspan="2"><input type="text" name="uir_wer" style="width:340px;"></td>';

    echo '</tr>';

    // Prio

    echo '<tr><td class="text_klein">Priorität: </td><td colspan="2">';

    echo '<select size="1" name="uir_prio">';
    $sql_prio='SELECT upr_nummer, upr_name FROM prioritaet ' .
        'ORDER BY upr_sort';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_prio=mysql_query($sql_prio, $verbindung))
        {
        fehler();
        }

    while ($zeile_prio=mysql_fetch_array($ergebnis_prio))
        {
        echo '<option value="' . $zeile_prio['upr_nummer'] . '"><span class="text">' . $zeile_prio['upr_name']
            . '</span></option>';
        }

    echo '</select>';

    echo '</td></tr>';

    // Zuordnung zum IR

    echo '<input type="hidden" name="uir_hirid" value="' . $hir_id . '">';

    // Formularbutton

    echo
        '<tr><td colspan="5" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Speichere Aufgabe" class="formularbutton" /></td>';

    echo '<tr><td colspan="5"><hr></td>';

    $sql='SELECT * FROM ir_todo ' .
        'INNER JOIN prioritaet ON uir_prio = upr_nummer ' .
        'WHERE uir_hirid = ' . $hir_id .
        ' ORDER BY uir_fertig ASC';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    echo
        '<tr><a name="todo"><td class="tabellen_titel">Verantwortlich</td><td class="tabellen_titel">Aufgabe</td><td class="tabellen_titel">Priorität</td><td>&nbsp;</td><td>&nbsp;</td></a><tr>';

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        echo '<tr>';

        echo '<td class="text_klein" valign="top">' . $zeile['uir_wer'] . '</td>';

        echo '<td class="text_klein" valign="top">' . nl2br($zeile['uir_todo']) . '</td>';

        echo '<td class="text_klein" valign="top">' . $zeile['upr_name'] . '</td>';

        if ($zeile['uir_fertig'] == 0)
            {
            echo '<td><a href="ir_todo_speichern.php?toggle=2&hir_id=' . $hir_id . '&uir_id=' . $zeile['uir_id']
                . '"  onclick="return window.confirm(\'ToDo schließen?\');"><img src="bilder/nein.gif" border="0" alt="ToDo schließen" title="ToDo schließen"></a></td>';
            }
        else
            {
            echo '<td><img src="bilder/ja.gif" border="0" alt="ToDo erledigt" title="ToDo erledigt"></a></td>';
            }

        if ($_SESSION['hma_id'] > 1)
            {
            echo '<td class="text_klein" valign="top"><a href="ir_transfer.php?uir_id=' . $zeile['uir_id']
                . '"><img src="bilder/icon_todo_transfer.png" border="0" alt="Erstelle Aufgabe aus dem ToDo" title="Erstelle Aufgabe aus dem ToDo"></a></td>';
            }
        else
            {
            echo '<td>&nbsp;</td>';
            }

        echo '</tr>';
        }

    echo '</table>';

        echo '</form>';
    
    echo '</td></tr>';

    echo '</table>'; # Datentabelle Ende
    # Schließe Layout-Tabelle
    echo '</tr></table>';

?>