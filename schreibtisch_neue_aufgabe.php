<?php
###### Editnotes ####
#$LastChangedDate: 2012-04-23 17:30:50 +0200 (Mo, 23 Apr 2012) $
#$Author: bpetersen $ 
#####################
# Integriere Module

require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

# Definiere globale Variablen
if(isset($_REQUEST['jump'])) {$jump = $_REQUEST['jump'];} else {$jump = 'none';}
$typen=array();
$bereiche=array();
$t=0;

if($jump != 'change') 
{
    $jumpstring = 'In den Pool';
    $pagestring = 'Erstelle neue Aufgabe';
} else
{
    $jumpstring = 'Change stellen';
    $pagestring = 'Erstelle neuen Change';
}


if($jump != 'change')         // brauche ich beim Change nicht
{

# Lese die Matrix zur Aufgabenklassifizierung ein

# Zunaechst die vorhandenen Typen

$sql='SELECT * FROM typ ORDER BY uty_name';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

// Die Datensaetze werden einzeln gelesen
while ($zeile=mysql_fetch_array($ergebnis))
    {
    // Schluessel fuer das zweidim. Feld ermitteln
    $ax = $zeile["uty_id"];

    // Die Informationen aus dem Datensatz werden
    // ueber den Schluessel im zweidim. Array gespeichert
    $typen[$ax]["uty_name"]=$zeile["uty_name"];
    $typen[$ax]["uty_id"]=$zeile["uty_id"];
    }

# Jetzt lese die Gruppen aus

$sql='SELECT ule_id, ule_kurz FROM level WHERE ule_id > 1 AND ule_id <99  AND ule_aktiv = 1 ORDER BY ule_sort';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

// Die Datensätze werden einzeln gelesen
while ($zeile=mysql_fetch_array($ergebnis))
    {
    // Key für den zweidim. Array ermitteln
    $ax = $zeile["ule_id"];

    // Die Informationen aus dem Datensatz werden
    // Über den Key im zweidim. Array gespeichert
    $bereiche[$ax]["ule_kurz"]=$zeile["ule_kurz"];
    $bereiche[$ax]["ule_id"]=$zeile["ule_id"];
    }

}
if (!isset($_POST['speichern']))
    {
    require_once('segment_kopf.php');

    echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;'.$pagestring.'<br><br>';

    echo '<form action="schreibtisch_neue_aufgabe.php?jump='.$jump.'" method="post" enctype="multipart/form-data">';

    echo '<table border="0" cellspacing="5" cellpadding="0">';

    echo '<tr>';

    echo '<td class="text_klein">Titel: </td><td><input type="text" name="hau_titel" style="width:340px;">';
    
    if($jump == 'change') 
    {
        echo '&nbsp;&nbsp;Changetyp: ';
        echo '<select size="1" name="hau_utcid">';

        $sql = 'SELECT utc_id, utc_name FROM typ_change  
                ORDER BY utc_sort';

        if (!$ergebnis=mysql_query($sql, $verbindung))
        {
            fehler();
        }

        while ($zeile=mysql_fetch_array($ergebnis))
        {
            if($zeile['utc_id']==2)
            {                 
            echo '<option value="' . $zeile['utc_id'] . '" selected><span class="text">' . $zeile['utc_name'] . '</span></option>';                
            } else
            {
            echo '<option value="' . $zeile['utc_id'] . '"><span class="text">' . $zeile['utc_name'] . '</span></option>';               
            }
         }

    echo '</select>'; 
    }

    echo '</td>';

    echo '</tr>';
    
    if($jump != 'change') {
    
        echo '<tr>';

    echo
        '<td class="text_klein">Referenz: </td><td><input type="text" name="hau_ticketnr" style="width:340px;"></td>';

    echo '</tr>';

    echo '<tr>';
    }
    
    if($jump != 'change') {  
    
    echo
        '<td class="text_klein" valign="top">Beschreibung:&nbsp;&nbsp;</td><td><textarea cols="80" rows="5" name="hau_beschreibung"></textarea></td>';
    
    } else
    {
    echo  
        '<td class="text_klein" valign="top">Beschreibung:&nbsp;&nbsp;</td><td><textarea cols="80" rows="20" name="hau_beschreibung"></textarea></td>';    
    }

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein" valign="top">Zugehörige Links:&nbsp;&nbsp;</td><td><textarea cols="80" rows="1" name="hau_links"></textarea></td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein" valign="top">Anlagen:&nbsp;&nbsp;</td><td><input type="file" name="hau_datei"></td>';

    echo '</tr>';

    if($jump != 'change') {
    
    echo '<tr>';

    echo '<td class="text_klein">Projekt: </td><td>';

    echo '<select size="1" name="hau_hprid">';

    $sql='SELECT hpr_id, hpr_titel FROM projekte  
            WHERE hpr_aktiv="1" AND hpr_fertig = 0 ' .
        'ORDER BY hpr_sort, hpr_titel';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        echo '<option value="' . $zeile['hpr_id'] . '"><span class="text">' . $zeile['hpr_titel'] . '</span></option>';
        }

    echo '</select>';

    echo '</td></tr>';
    
    echo '<tr>';

    echo '<td class="text_klein" valign="top">Aufgabentyp: </td><td>';

    echo '<table border="0" cellpadding="6">';

    echo '<tr>';

    echo '<td>';

    echo '<table class="matrix">';

    echo '<tr>';

    foreach ($bereiche as $idb => $bereich)
        {
        echo '<th class="text_klein">' . $bereich['ule_kurz'] . '</th>';
        }

    echo '<td>&nbsp;</td>';

    echo '</tr>';
    $zaehler=0;

    foreach ($typen as $idt => $typ)
        {
        echo '<tr>';

        if (fmod($zaehler, 2) == 1 && $zaehler > 0)
            {
            $stil='<td>';
            }
        else
            {
            $stil='<td class="alt">';
            }

        foreach ($bereiche as $idb => $bereich)
            {
            echo $stil . '<input type="checkbox" name="aufgabenart[' . $bereich['ule_id'] . '][' . $typ['uty_id']
                . ']" value="' . $typ['uty_id'] . '"></td>';
            }

        echo '<td class="text_klein">' . $typ['uty_name'] . '</td>';

        echo '</tr>';
        $zaehler++;
        }

    echo '</table>';

    echo '</td>';

    echo '<td valign="top">'; #####################

    echo '<table class="element" cellpadding = "5">';

    echo '<tr>';

    echo '<td class="text">Legende:</td>';

    echo '</tr>';

    $sql_leg='SELECT ule_name, ule_kurz FROM level WHERE ule_id > 1 AND ule_id < 99 ORDER BY ule_sort';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_leg=mysql_query($sql_leg, $verbindung))
        {
        fehler();
        }

    while ($zeile_leg=mysql_fetch_array($ergebnis_leg))
        {
        echo '<tr>';

        echo '<td class="text" valign="top">' . $zeile_leg['ule_kurz'] . ' = ' . $zeile_leg['ule_name'] . '</td>';

        echo '<tr>';
        }

    echo '</table>';

    echo '</td></tr>';

    echo '</table>';

    echo '</td></tr>';

    } // Ausgabe Matrix bei Change unterdrückt
    
    echo '<tr>';

    echo '<td class="text_klein">Priorität: </td><td>';

    echo '<select size="1" name="hau_prio">';
    $sql='SELECT upr_nummer, upr_name FROM prioritaet ' .
        'ORDER BY upr_sort';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        if ($zeile['upr_nummer'] == 1)
            {
            echo '<option value="' . $zeile['upr_nummer'] . '" selected><span class="text">' . $zeile['upr_name']
                . '</span></option>';
            }
        else
            {
            echo '<option value="' . $zeile['upr_nummer'] . '"><span class="text">' . $zeile['upr_name']
                . '</span></option>';
            }
        }

    echo '</select>';

    echo '</td></tr>';

    echo '<tr>';

    echo
        "<td class='text_klein' valign='middle'>Plan-Ende: </td><td><input type='text' name='hau_pende' style='width:100px;' id='hau_pende'><img src='bilder/date_go.gif' alt='Anklicken für Kalenderansicht' onclick='kalender(document.getElementById(\"hau_pende\"));'/>";

    echo '<span class="text_klein" valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';

    echo
        '<input type="radio" name="hau_datumstyp" value="2"><span class="text_klein"> fällig bis</span>&nbsp;&nbsp;&nbsp;';

    echo
        '<input type="radio" name="hau_datumstyp" value="3"><span class="text_klein"> exakt zum Termin!</span>&nbsp;&nbsp;&nbsp;';

    echo
        '<input type="radio" name="hau_datumstyp" value="1" checked><span class="text_klein"> offen</span> &nbsp;&nbsp;&nbsp;';

    echo '<input type="checkbox" name="hau_kalender"><span class="text_klein"> in den Kalender schreiben?</span> ';

    echo
        '<input type="checkbox" name="hau_nonofficetime"><span class="text_klein"> Außerhalb des Tagesbetriebs?</span> ';

    echo '</td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein">Dauer [d]: </td><td><input type="text" name="hau_dauer" value="1" style="width:100px;"></td>';

    echo '</tr>';

    echo
        '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="'.$jumpstring.'" class="formularbutton" />';
        
    if($jump != 'change') {
    
        echo '&nbsp;&nbsp;<input type="submit" name="speichern" value="Selbst übernehmen" class="formularbutton" />';
        echo '&nbsp;&nbsp;<input type="submit" name="speichern" value="Zuordnen" class="formularbutton" />';
        echo '&nbsp;&nbsp;<input type="submit" name="speichern" value="Dauerauftrag" class="formularbutton" />';
     #  echo '&nbsp;&nbsp;<input type="submit" name="speichern" value="speichern und Ticket CC" class="formularbutton" />';
    }
    
    echo '</td></tr>';

    if($jump != 'change') {
        
    echo '<tr><td colspan="8"><hr>';

    echo '<table border=0><tr>';

    echo '<td>';

    echo '</td>';

    echo '<td>';

    echo '<table class="element" cellpadding = "5">';

    echo '<tr>';

    echo '<td class="text">Schnellerfassung:</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein">Aufwand in [min]: </td><td>';

    echo '<input type="text" name="ulo_aufwand">';

    echo '</td></tr>';

    echo '<tr>';

    echo '<td class="text_klein">Kopie ins Activity-Log?</td><td>';

    echo '<input type="checkbox" name="ulo_extra">';

    echo '</td></tr>';

    echo
        '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Speichern & Schließen" class="formularbutton" />';

    echo '</table>';

    echo '</td></tr>';

    echo '</table>';

    echo '</table>';
    }
    echo '</form>';
    }
else
    {

    $fehlermeldung=array();
    $anzahl_fehler=0;

    foreach ($_POST as $varname => $value)
        {
        $Daten[$varname]=$value;
        }

    if($jump=='change') {$Daten['hau_hprid']=6;}

    if (isset($Daten['hau_kalender']))
        {
        $Daten['hau_kalender']=1;
        }
    else
        {
        $Daten['hau_kalender']=0;
        }

    if (isset($Daten['hau_nonofficetime']))
        {
        $Daten['hau_nonofficetime']=1;
        }
    else
        {
        $Daten['hau_nonofficetime']=0;
        }

    if (isset($Daten['aufgabenart']))
        {

        // Ermittle Anzahl der gesetzten Häkchen

        foreach ($Daten['aufgabenart'] AS $feld)
            {
            $t=$t + count($feld);
            }
        }

    if (!isset($Daten['aufgabenart']))
        {
            
    
        // Die Gruppe wird nach dem Eingeber festgelegt
            $Daten['uaz_pg'] = $_SESSION['hma_level'];

       // Der Typ wird auf Sonstiges gesetzt
             $Daten['hau_typ']= 5; // OTHER
        }
    else if ($t > 1)
        {

        $anzahl_fehler++;
        $fehlermeldung['aufgabenart']='Bitte nur einen Aufgabentyp markieren!';
        }
    else
        {
        $fehlermeldung['aufgabenart']='';

        // Alle Datensätze mit allen Inhalten anzeigen
        while (list($dsname, $dswert)=each($Daten['aufgabenart']))
            {
            // Der Key wird ausgegeben
            $Daten['uaz_pg'] = $dsname;

            // Informationen aus dem Datensatz ausgeben
            while (list($name, $wert)=each($dswert))
                {
                $Daten['hau_typ']=$wert;
                }
            }
        }

    if (isset($Daten['ulo_extra']))
        {
        if ($Daten['ulo_extra'] == 'on')
            {
            $Daten['ulo_extra']=1;
            $checked='checked';
            }
        }
    else
        {
        $Daten['ulo_extra']=0;
        $checked='';
        }

    if ($_POST['speichern'] == 'Speichern & Schließen' AND abs($Daten['ulo_aufwand']) == 0)
        {
        $anzahl_fehler++;
        $fehlermeldung['ulo_aufwand']='Bitte geben Sie den Aufwand für den Task an!';
        }
    else
        {
        $fehlermeldung['ulo_aufwand']='';
        }

    if ($Daten['hau_titel'] == '')
        {
        $anzahl_fehler++;
        $fehlermeldung['hau_titel']='Bitte geben Sie einen Titel für die Aufgaben an!';
        }
    else
        {
        $fehlermeldung['hau_titel']='';
        }

    if (empty($Daten['hau_pende']) AND $Daten['hau_datumstyp'] != '1')
        {
        $anzahl_fehler++;
        $fehlermeldung['hau_pende']='Bitte geben Sie ein Datum an!';
        }
    else if (!empty($Daten['hau_pende']) AND $Daten['hau_datumstyp'] == '1' AND $Daten['hau_pende'] != 'open')
        {
        $anzahl_fehler++;
        $fehlermeldung['hau_pende']='Sie haben offenes Ende für die Aufgabe gewählt, bitte das Datum löschen!';
        }
    else
        {

        list($anzahl_fehler, $fehlermeldung['hau_pende'])=datum_check($Daten['hau_pende'], 'hau_pende', $anzahl_fehler);
        }

    if ($anzahl_fehler > 0)
        {

        require_once('segment_kopf.php');

        echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;'.$pagestring.'<br><br>';

        echo '<form action="schreibtisch_neue_aufgabe.php?jump='.$jump.'" method="post" enctype="multipart/form-data">';

        echo '<table border="0" cellspacing="5" cellpadding="0">';

        echo '<tr>';

        echo '<td colspan="2" class="text_rot">&nbsp;&nbsp;' . $fehlermeldung['hau_titel'] . '</td></tr><tr>';

        echo '<td class="text_klein">Titel: </td><td><input type="text" name="hau_titel" value="' . htmlspecialchars($Daten['hau_titel'])
            . '" style="width:340px;">';
            
            if($jump == 'change') 
            {
            
            echo '&nbsp;&nbsp;Changetyp: ';
            echo '<select size="1" name="hau_utcid">';

            $sql = 'SELECT utc_id, utc_name FROM typ_change  
                    ORDER BY utc_sort';

            if (!$ergebnis=mysql_query($sql, $verbindung))
            {
                fehler();
            }

            while ($zeile=mysql_fetch_array($ergebnis))
            {
            if($zeile['utc_id']==$Daten['hau_utcid'])
            {                 
            echo '<option value="' . $zeile['utc_id'] . '" selected><span class="text">' . $zeile['utc_name'] . '</span></option>';                
            } else
            {
            echo '<option value="' . $zeile['utc_id'] . '"><span class="text">' . $zeile['utc_name'] . '</span></option>';               
            }
            }

            echo '</select>'; 
            }
               
        echo '</td>';

        echo '</tr>';

        if($jump != 'change') 
        {
        
        echo '<tr>';

        echo '<td class="text_klein">Referenz: </td><td><input type="text" name="hau_ticketnr" value="'
            . $Daten['hau_ticketnr'] . '"  style="width:340px;"></td>';

        echo '</tr>';
        }

        echo '<tr>';

       
        echo
            '<td class="text_klein" valign="top">Beschreibung:&nbsp;&nbsp;</td><td><textarea cols="80" rows="5" name="hau_beschreibung">'
            . htmlspecialchars($Daten['hau_beschreibung']) . '</textarea></td>';

        echo '</tr>';

        echo '<tr>';

        echo
            '<td class="text_klein" valign="top">Zugehörige Links:&nbsp;&nbsp;</td><td><textarea cols="80" rows="1" name="hau_links">'
            . $Daten['hau_links'] . '</textarea></td>';

        echo '</tr>';

        echo '<tr>';

        echo
            '<td class="text_klein" valign="top">Anlagen:&nbsp;&nbsp;</td><td><input type="file" name="hau_datei"></td>';

        echo '</tr>';

         if($jump != 'change') 
        {
        echo '<tr>';

        echo '<td class="text_klein">Projekt: </td><td>';

        echo '<select size="1" name="hau_hprid">';

        $sql='SELECT hpr_id, hpr_titel FROM projekte  
            WHERE hpr_aktiv="1" AND hpr_fertig = 0 ' .
            'ORDER BY hpr_sort, hpr_titel';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }

        while ($zeile=mysql_fetch_array($ergebnis))
            {
            if ($Daten['hau_hprid'] == $zeile['hpr_id'])
                {
                echo '<option value="' . $zeile['hpr_id'] . '" selected><span class="text">' . $zeile['hpr_titel']
                    . '</span></option>';
                }
            else
                {
                echo '<option value="' . $zeile['hpr_id'] . '"><span class="text">' . $zeile['hpr_titel']
                    . '</span></option>';
                }
            }

        echo '<tr><td colspan="2" class="text_rot">&nbsp;&nbsp;' . $fehlermeldung['aufgabenart'] . '</td></tr>';

        echo '<tr>';

        echo '<td class="text_klein" valign="top">Aufgabentyp: </td><td>';

        echo '<table border="0" cellpadding="6">';

        echo '<tr>';

        echo '<td>';

        echo '<table class="matrix">';

        echo '<tr>';

        foreach ($bereiche as $idb => $bereich)
            {
            echo '<th>' . $bereich['ule_kurz'] . '</th>';
            }

        echo '<td>&nbsp;</td>';

        echo '</tr>';
        $zaehler=0;

        foreach ($typen as $idt => $typ)
            {
            echo '<tr>';

            if (fmod($zaehler, 2) == 1 && $zaehler > 0)
                {
                $stil='<td>';
                }
            else
                {
                $stil='<td class="alt">';
                }

            foreach ($bereiche as $idb => $bereich)
                {
                if (isset($Daten['aufgabenart'][$bereich['ule_id']][$typ['uty_id']]))
                    {
                    echo $stil . '<input type="checkbox" name="aufgabenart[' . $bereich['ule_id'] . ']['
                        . $typ['uty_id'] . ']" checked value="' . $typ['uty_id'] . '"></td>';
                    }
                else
                    {
                    echo $stil . '<input type="checkbox" name="aufgabenart[' . $bereich['ule_id'] . ']['
                        . $typ['uty_id'] . ']" value="' . $typ['uty_id'] . '"></td>';
                    }
                }

            echo '<td class="text_klein">' . $typ['uty_name'] . '</td>';

            echo '</tr>';
            $zaehler++;
            }

        echo '</table>';

        echo '</td>';

        echo '<td valign="top">';

        echo '<table class="element" cellpadding = "5">';

        echo '<tr>';

        echo '<td class="text">Legende:</td>';

        echo '</tr>';

        $sql_leg='SELECT ule_name, ule_kurz FROM level WHERE ule_id > 1 AND ule_id < 99 ORDER BY ule_sort';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_leg=mysql_query($sql_leg, $verbindung))
            {
            fehler();
            }

        while ($zeile_leg=mysql_fetch_array($ergebnis_leg))
            {
            echo '<tr>';

            echo '<td class="text" valign="top">' . $zeile_leg['ule_kurz'] . ' = ' . $zeile_leg['ule_name'] . '</td>';

            echo '<tr>';
            }

        echo '</table>';

        echo '</td></tr>';

        echo '</table>';

        echo '</td></tr>';
        } // Ende jump ohne Change
        
        echo '<tr>';

        echo '<td class="text_klein">Priorität: </td><td>';

        echo '<select size="1" name="hau_prio">';
        $sql='SELECT upr_nummer, upr_name FROM prioritaet ' .
            'ORDER BY upr_sort';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }

        while ($zeile=mysql_fetch_array($ergebnis))
            {
            if ($Daten['hau_prio'] == $zeile['upr_nummer'])
                {
                echo '<option value="' . $zeile['upr_nummer'] . '" selected><span class="text">' . $zeile['upr_name']
                    . '</span></option>';
                }
            else
                {
                echo '<option value="' . $zeile['upr_nummer'] . '"><span class="text">' . $zeile['upr_name']
                    . '</span></option>';
                }
            }

        echo '</select>';

        echo '</td></tr>';

        echo '<tr>';

        echo '<td colspan="2" class="text_rot">&nbsp;&nbsp;' . $fehlermeldung['hau_pende'] . '</td></tr><tr>';

        echo
            "<td class='text_klein' valign='middle'>Plan-Ende: </td><td><input type='text' name='hau_pende' style='width:100px;' id='hau_pende' value='"
            . $Daten['hau_pende']
                . "'><img src='bilder/date_go.gif' alt='Anklicken für Kalenderansicht' onclick='kalender(document.getElementById(\"hau_pende\"));'/>";

        echo '<span class="text_klein" valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';

        switch ($Daten['hau_datumstyp'])
            {
            case 1:
                echo
                    '<input type="radio" name="hau_datumstyp" value="2"><span class="text_klein"> fällig zum</span>&nbsp;&nbsp;&nbsp;';

                echo
                    '<input type="radio" name="hau_datumstyp" value="3"><span class="text_klein"> exakt zum Termin</span>&nbsp;&nbsp;&nbsp;';

                echo
                    '<input type="radio" name="hau_datumstyp" value="1" checked><span class="text_klein"> offen</span> ';
                break;

            case 2:
                echo
                    '<input type="radio" name="hau_datumstyp" value="2" checked><span class="text_klein"> fällig zum</span>&nbsp;&nbsp;&nbsp;';

                echo
                    '<input type="radio" name="hau_datumstyp" value="3"><span class="text_klein"> exakt zum Termin</span>&nbsp;&nbsp;&nbsp;';

                echo '<input type="radio" name="hau_datumstyp" value="1"><span class="text_klein"> offen</span> ';
                break;

            case 3:
                echo
                    '<input type="radio" name="hau_datumstyp" value="2"><span class="text_klein"> fällig zum</span>&nbsp;&nbsp;&nbsp;';

                echo
                    '<input type="radio" name="hau_datumstyp" value="3" checked><span class="text_klein"> exakt zum Termin</span>&nbsp;&nbsp;&nbsp;';

                echo '<input type="radio" name="hau_datumstyp" value="1"><span class="text_klein"> offen</span> ';
                break;
            }

        if ($Daten['hau_kalender'] == 1)
            {
            echo
                '<input type="checkbox" name="hau_kalender" checked><span class="text_klein"> In den Kalender schreiben?</span> ';
            }
        else
            {
            echo
                '<input type="checkbox" name="hau_kalender"><span class="text_klein"> In den Kalender schreiben?</span> ';
            }

        if ($Daten['hau_nonofficetime'] == 1)
            {
            echo
                '<input type="checkbox" name="hau_nonofficetime" checked><span class="text_klein"> Außerhalb des Tagesbetriebs?</span> ';
            }
        else
            {
            echo
                '<input type="checkbox" name="hau_nonofficetime"><span class="text_klein"> Außerhalb des Tagesbetriebs?</span> ';
            }

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo
            '<td class="text_klein">Dauer [d]: </td><td><input type="text" name="hau_dauer" style="width:100px;" value="'
            . $Daten['hau_dauer'] . '"></td>';

        echo '</tr>';

        echo
            '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="'.$jumpstring.'" class="formularbutton" />';
            
                    if($jump != 'change') 
        {
        echo '&nbsp;&nbsp;<input type="submit" name="speichern" value="Selbst übernehmen" class="formularbutton" />';
        echo '&nbsp;&nbsp;<input type="submit" name="speichern" value="Zuordnen" class="formularbutton" />';
        echo '&nbsp;&nbsp;<input type="submit" name="speichern" value="Dauerauftrag" class="formularbutton" />';
    #    echo '&nbsp;&nbsp;<input type="submit" name="speichern" value="-> OTRS" class="formularbutton" />';
        }

        echo '</td></tr>';
                    if($jump != 'change') 
        {
        echo '<tr><td colspan="8"><hr>';

        echo '<table border=0><tr>';

        echo '<td>';

        echo '<td>';

        echo '<table class="element" cellpadding = "5">';

        echo '<tr>';

        echo '<td class="text">Schnellerfassung:</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein">Aufwand in [min]: </td><td>';

        echo '<input type="text" name="ulo_aufwand">';

        echo '</td></tr>';

        echo '<tr>';

        echo '<td class="text_klein">Kopie ins Activity-Log?</td><td>';

        echo '<input type="checkbox" name="ulo_extra">';

        echo '</td></tr>';

        echo
            '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Speichern & Schließen" class="formularbutton" />';

        echo '</table>';

        echo '</td></tr>';

        echo '</table>';
        }
        
        echo '</table>';

        echo '</form>';
        }
    else
        {

        if ($Daten['hau_datumstyp'] != 1)
            {

            $Daten['hau_pende']=pruefe_datum($Daten['hau_pende']);
            }
        else
            {
            $Daten['hau_pende']='9999-01-01';
            }

####################### Prüfe, ob die Aufgabe ins Backlog gehört ###################################

if($Daten['hau_typ']==18) // Backlog (Vorsicht, ggf. kann die ID in der Tabelle Typ anders gesetzt sein)
{

    if($Daten['hau_hprid']<10) {$Daten['hau_hprid']=1;} // Setze alle internen Projekttypen auf Tagesgeschäft 
    // Speichere den Datensatz

    $sql='INSERT INTO backlog (' .
                'hba_titel, ' .
                'hba_hprid, ' .
                'hba_uprid, ' .
                'hba_gruppe, ' .
                'hba_hmaid, ' .
                'hba_status, ' .
                'hba_anlage) ' .
                'VALUES ( ' .
                '"' . mysql_real_escape_string($Daten['hau_titel']) . '", ' .
                '"' . $Daten['hau_hprid'] . '", ' .
                '"' . $Daten['hau_prio'] . '", ' .
                '"' . $Daten['uaz_pg'] . '", ' .
                '"' . $_SESSION['hma_id'] . '", ' .
                '"1", ' .
                'NOW())';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $hau_id=mysql_insert_id();

            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                . '", "Backlog angelegt", NOW() )';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                } 
                
            header('Location: backlog_liste.php?xGruppe='.$Daten['uaz_pg'].'&xProjekt='.$Daten['hau_hprid']);
            exit;
}
else
{
            
        if ($Daten['speichern'] == 'In den Pool' OR $Daten['speichern'] == 'Zuordnen')
            {
            // Speichere den Datensatz

            $sql='INSERT INTO aufgaben (' .
                'hau_id, ' .
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
                'hau_datumstyp, ' .
                'hau_hprid, ' .
                'hau_typ, ' .
                'hau_tl_status, ' .
                'hau_dauer, ' .
                'hau_links, ' .
                'hau_utcid, ' .  
                'hau_ticketnr) ' .
                'VALUES ( ' .
                'NULL, ' .
                '"' . mysql_real_escape_string($Daten['hau_titel']) . '", ' .
                '"' . mysql_real_escape_string($Daten['hau_beschreibung']) . '", ' .
                'NOW(), ' .
                '"' . $_SESSION['hma_id'] . '", ' .
                '"' . $Daten['hau_prio'] . '", ' .
                '"' . $Daten['hau_pende'] . '", ' .
                '"' . $Daten['hau_kalender'] . '", ' .
                '"' . $Daten['hau_nonofficetime'] . '", ' .
                'NOW(), ' .
                '"1", ' .
                '"0", ' .
                '"' . $Daten['hau_datumstyp'] . '", ' .
                '"' . $Daten['hau_hprid'] . '", ' .
                '"' . $Daten['hau_typ'] . '", ' .
                '"0", ' .
                '"' . $Daten['hau_dauer'] . '", ' .
                '"' . $Daten['hau_links'] . '", ' .
                '"' . $Daten['hau_utcid'] . '", ' .   
                '"' . $Daten['hau_ticketnr'] . '")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $hau_id=mysql_insert_id();

            $sql='INSERT INTO aufgaben_zuordnung
                (uaz_hauid, uaz_pg) ' .
                'VALUES ("' . $hau_id . '", "' . $Daten['uaz_pg'] . '")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                . '", "Aufgabe angelegt", NOW() )';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
            }
     /*
        else if ($Daten['speichern'] == 'speichern und Ticket CC')
            {
            // Speichere den Datensatz

            $sql='INSERT INTO aufgaben (' .
                'hau_id, ' .
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
                'hau_datumstyp, ' .
                'hau_hprid, ' .
                'hau_typ, ' .
                'hau_tl_status, ' .
                'hau_dauer, ' .
                'hau_links, ' .
                'hau_utcid, ' .  
                'hau_ticketnr) ' .
                'VALUES ( ' .
                'NULL, ' .
                '"' . mysql_real_escape_string($Daten['hau_titel']) . '", ' .
                '"' . mysql_real_escape_string($Daten['hau_beschreibung']) . '", ' .
                'NOW(), ' .
                '"' . $_SESSION['hma_id'] . '", ' .
                '"' . $Daten['hau_prio'] . '", ' .
                '"' . $Daten['hau_pende'] . '", ' .
                '"' . $Daten['hau_kalender'] . '", ' .
                '"' . $Daten['hau_nonofficetime'] . '", ' .
                'NOW(), ' .
                '"1", ' .
                '"0", ' .
                '"' . $Daten['hau_datumstyp'] . '", ' .
                '"' . $Daten['hau_hprid'] . '", ' .
                '"' . $Daten['hau_typ'] . '", ' .
                '"0", ' .
                '"' . $Daten['hau_dauer'] . '", ' .
                '"' . $Daten['hau_links'] . '", ' .
                '"' . $Daten['hau_utcid'] . '", ' .   
                '"' . $Daten['hau_ticketnr'] . '")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $hau_id=mysql_insert_id();

            $sql='INSERT INTO aufgaben_zuordnung
                (uaz_hauid, uaz_pg) ' .
                'VALUES ("' . $hau_id . '", "' . $Daten['uaz_pg'] . '")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                . '", "Aufgabe angelegt und an OTRS geschickt", NOW() )';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
                
### Mailadresse hinterlegen im Ticket

            $sql='insert into ticket_info (uti_hauid,uti_mail,uti_status,uti_aktiv) values ("'.$hau_id.'", "ticket@otrs.cc.is24.loc","1","1")';  

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
                

                                           
### Mail an OTRS senden

            // Entfernt HTML-Umbruch
            $Daten['hau_beschreibung']=Preg_Replace('/<br(\s+)?\/?>/i', "\n",$Daten['hau_beschreibung']);   
   
            $mail_text
                .="Ticketbetreff: " . $Daten['hau_titel'] . "\r\n\r\n";
            $mail_text
                .="Ticketinhalt: " . htmlspecialchars($Daten['hau_beschreibung']) . "\r\n\r\n";
             $mail_text
                .="durch: " . $_SESSION['hma_vorname'] . " " . $_SESSION['hma_name'] . "\r\n\r\n";
             $mail_text
                .="um: " . date("d.m.Y H:i") . "\r\n\r\n";
             
            $betreff='Neues Ticket: >Ticket ID ' . $hau_id. '< '.$Daten['hau_titel'];  
       
            $header = "From: taskscout24@immobilienscout24.de (TaskScout 24)\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-type: text/plain; charset=utf-8\r\n";
            $header .= "Content-Transfer-Encoding: 8-bit\r\n";
            $header .= "Return-Path: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "Reply-To: taskscout24@immobilienscout24.de\r\n"; 
            $header .= "From: taskscout24@immobilienscout24.de (TaskScout 24)\r\n";
            $header .= "Date: " . date('r')."\r\n";
            $temp_ary = explode(' ', (string) microtime());
            $header .= "Message-Id: <" . date('YmdHis') . "." . substr($temp_ary[0],2) . "@immobilienscout24.de>\r\n"; 
            $header .= "X-TASKSCOUT-Priority: ".$Daten['hau_prio']."\r\n";   

            #echo $betreff.$mail_text.$header;
            mail('ticket@otrs.cc.is24.loc', $betreff, $mail_text, $header, '-ftaskscout24@immobilienscout24.de');
                
            }    */
        else if ($Daten['speichern'] == 'Dauerauftrag')
            {

            $_SESSION['Daten']=$Daten;
            header('Location: schreibtisch_serienaufgabe.php');
            exit;
            }
        else if ($Daten['speichern'] == 'Speichern & Schließen')
            {
            if ($Daten['hau_beschreibung'] == '')
                {
                $Daten['hau_beschreibung']=$Daten['hau_titel'];
                }


            // Speichere den Datensatz

            $sql='INSERT INTO aufgaben (' .
                'hau_id, ' .
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
                'hau_ticketnr, ' .
                'hau_abschluss, '.
                'hau_abschlussdatum) ' .
                'VALUES ( ' .
                'NULL, ' .
                '"' . mysql_real_escape_string($Daten['hau_titel']) . '", ' .
                '"' . mysql_real_escape_string($Daten['hau_beschreibung']) . '", ' .
                'NOW(), ' .
                '"' . $_SESSION['hma_id'] . '", ' .
                '"' . $Daten['hau_prio'] . '", ' .
                '"' . $Daten['hau_pende'] . '", ' .
                '"' . $Daten['hau_kalender'] . '", ' .
                '"' . $Daten['hau_nonofficetime'] . '", ' .
                'NOW(), ' .
                '"1", ' .
                '"0", ' .
                '"999", ' .
                '"' . $Daten['hau_datumstyp'] . '", ' .
                '"' . $Daten['hau_hprid'] . '", ' .
                '"' . $Daten['hau_typ'] . '", ' .
                '"1", ' .
                '"' . $Daten['hau_dauer'] . '", ' .
                '"' . $Daten['hau_links'] . '", ' .
                '"' . $Daten['hau_utcid'] . '", ' .   
                '"' . $Daten['hau_ticketnr'] . '", ' .
                '"1", '.
                'NOW())';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $hau_id=mysql_insert_id();

            $sql='INSERT INTO aufgaben_zuordnung
                (uaz_hauid, uaz_pg, uaz_pba) ' .
                'VALUES ("' . $hau_id . '", "' . $Daten['uaz_pg'] . '", "' . $_SESSION['hma_id'] . '")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $sql='INSERT INTO aufgaben_mitarbeiter (' .
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
                '"' . $hau_id . '", ' .
                '"1", ' .
                '"99", ' .
                '"0", ' .
                '"' . $Daten['hau_pende'] . '", ' .
                'NOW(), ' .
                '"1")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            # Pruefe, ob es sich um eine Aufgabe im MR handelt

            $sql_mr=
                'SELECT hau_pende, hpr_id, hau_nonofficetime FROM projekte LEFT JOIN aufgaben ON hau_hprid = hpr_id WHERE hau_id = '
                . $hau_id;

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

            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                . '", "Aufgabe angelegt und abgeschlossen", NOW() )';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $sql='INSERT INTO log (' .
                'ulo_id, ' .
                'ulo_aufgabe, ' .
                'ulo_text, ' .
                'ulo_zeitstempel, ' .
                'ulo_ma, ' .
                'ulo_datum, ' .
                'ulo_aufwand, ' .
                'ulo_extra, ' .
                'ulo_fertig) ' .
                'VALUES ( ' .
                'NULL, ' .
                '"' . $hau_id . '", ' .
                '"' . mysql_real_escape_string($Daten['hau_beschreibung']) . '", ' .
                'NOW(), ' .
                '"' . $_SESSION['hma_id'] . '", ' .
                '"' . date("Y-m-d H:i") . '", ' .
                '"' . $Daten['ulo_aufwand'] . '", ' .
                '"' . $Daten['ulo_extra'] . '", ' .
                '"100")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $sql_ende=
                'UPDATE aufgaben SET hau_abschlussdatum = NOW(), hau_abschluss = 1 WHERE hau_id = "' . $hau_id . '"';

            if (!($ergebnis_ende=mysql_query($sql_ende, $verbindung)))
                {
                fehler();
                }
            }
        else
            {
            // Speichere den Datensatz

            $sql='INSERT INTO aufgaben (' .
                'hau_id, ' .
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
                'NULL, ' .
                '"' . mysql_real_escape_string($Daten['hau_titel']) . '", ' .
                '"' . mysql_real_escape_string($Daten['hau_beschreibung']) . '", ' .
                'NOW(), ' .
                '"' . $_SESSION['hma_id'] . '", ' .
                '"' . $Daten['hau_prio'] . '", ' .
                '"' . $Daten['hau_pende'] . '", ' .
                '"' . $Daten['hau_kalender'] . '", ' .
                '"' . $Daten['hau_nonofficetime'] . '", ' .
                'NOW(), ' .
                '"1", ' .
                '"0", ' .
                '"999", ' .
                '"' . $Daten['hau_datumstyp'] . '", ' .
                '"' . $Daten['hau_hprid'] . '", ' .
                '"' . $Daten['hau_typ'] . '", ' .
                '"1", ' .
                '"' . $Daten['hau_dauer'] . '", ' .
                '"' . $Daten['hau_links'] . '", ' .
                '"' . $Daten['hau_utcid'] . '", ' .  
                '"' . $Daten['hau_ticketnr'] . '")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $hau_id=mysql_insert_id();

            $sql='INSERT INTO aufgaben_zuordnung
                (uaz_hauid, uaz_pg, uaz_pba) ' .
                'VALUES ("' . $hau_id . '", "' . $Daten['uaz_pg'] . '", "' . $_SESSION['hma_id'] . '")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $sql='INSERT INTO aufgaben_mitarbeiter (' .
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
                '"' . $hau_id . '", ' .
                '"0", ' .
                '"99", ' .
                '"0", ' .
                '"' . $Daten['hau_pende'] . '", ' .
                'NOW(), ' .
                '"1")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
            # Pruefe, ob es sich um eine Aufgabe im MR handelt

            $sql_mr=
                'SELECT hau_pende, hpr_id, hau_nonofficetime FROM projekte LEFT JOIN aufgaben ON hau_hprid = hpr_id WHERE hau_id = '
                . $hau_id;

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

            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                . '", "Aufgabe angelegt und zugeordnet", NOW() )';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
            }

        # Wurde ein Change angelegt?

        if ($Daten['hau_hprid'] == 6) // Change
            {

            $sql='INSERT INTO rollen_status (' .
                'urs_hauid) ' .
                'VALUES ( ' .
                '"' . $hau_id . '")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                . '", "Change erstellt", NOW() )';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            # Informiere die Changemanager

            $sql='SELECT * FROM mitarbeiter LEFT JOIN rollen_matrix ON urm_hmaid = hma_id WHERE hma_aktiv = 1 AND urm_uroid = 1';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            while ($zeile=mysql_fetch_array($ergebnis))
                {
                ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

                $hauid = $hau_id;
                $initiator=$_SESSION['hma_id'];
                $empfaenger=$zeile['hma_id'];
                $info='Ein neuer Change wurde zur Freigabe eingereicht.';

                include('segment_news.php');

                //////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

                ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

                $mailtag='ume_aufgabestatus';
                $mail_hma_id=$empfaenger;                 
                $mail_hau_id=$hau_id;
                $text="\nEin neuer Change wurde eingereicht:\n";
                $mail_info='Neuer Change';
                $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
                $telefon = $_SESSION['hma_telefon'];

                include('segment_mail_senden.php');

                ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
                }    
            }

        if ($_FILES["hau_datei"]["tmp_name"] != '')
            {

            $oldumask = umask(0); 
            mkdir("anhang/" . $hau_id, 0777); 
            umask($oldumask);     

            if (($_FILES["hau_datei"]["error"] == 3) OR ($_FILES["hau_datei"]["error"] == 4))
                {
            $sql = 'insert into eventlog ( hel_area,hel_type,hel_referer,hel_text) values ( "FILE", "Uploaderror", "'.$hau_id.'", "Fehler= '.$_FILES["hau_datei"]["error"].'")';   
        
            if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            } 
                }
            else
                {
                move_uploaded_file($_FILES["hau_datei"]["tmp_name"],
                    "anhang/" . $hau_id . "/" . $_FILES["hau_datei"]["name"]);
                                $sql = 'insert into eventlog ( hel_area,hel_type,hel_referer,hel_text) values ( "FILE", "Upload_OK", "'.$hau_id.'", "File= '.$_FILES["hau_datei"]["name"].' Fehler= '.$_FILES["hau_datei"]["error"].'")';   
        
            if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }
            
                        $sql = 'insert INTO anlagen (uan_name, uan_besitzer, uan_hauid) values ( "'.$_FILES["hau_datei"]["name"].'", "'.$_SESSION['hma_login'].'", "'.$hau_id.'")'; 
            
            if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }
                }
            }

        if ($Daten['ulo_extra'] == 1)
            {
            if ($Daten['speichern'] == 'Save & Close Task')
                {
                $Daten['ulo_text']=$Daten['hau_beschreibung'];
                }

            require_once('segment_kopf.php');

            echo '<form action="aufgabe_activity_log_speichern.php" method="post">';

            # Konnektiere Dich auf die ACTIVITY-LOG-Datenbank

            $rechnername="bersql03";
            $datenbankname="activitylog";
            $benutzername="activitylog";
            $passwort="activitylog";

            // Verbindung zum Host oeffnen
            if (!$verbindung=mysql_connect($rechnername, $benutzername, $passwort))
                die("Konnte keine Verbindung herstellen !</p>\n");

            // Datenbank auswaehlen
            if (!(mysql_select_db($datenbankname, $verbindung)))
                fehler();

            // Baue Tabelle

            echo '<table>';

            # Frage Name ab

            echo '<tr>';

            echo '<td class="text_klein">Eintragender: </td><td>';

            echo '<select size="1" name="ac_user">';
            $sql='SELECT id, firstname, lastname FROM users ' .
                'ORDER BY lastname';

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis=mysql_query($sql, $verbindung))
                {
                fehler();
                }

            while ($zeile=mysql_fetch_array($ergebnis))
                {
                if (trim($zeile['firstname']) == trim($_SESSION['hma_vorname']) AND trim($zeile['lastname'])
                    == trim($_SESSION['hma_name']))
                    {
                    echo '<option value="' . $zeile['id'] . '" selected="selected"><span class="text">'
                        . $zeile['lastname'] . ',' . $zeile['firstname'] . '</span></option>';
                    }
                else
                    {
                    echo '<option value="' . $zeile['id'] . '"><span class="text">' . $zeile['lastname'] . ','
                        . $zeile['firstname'] . '</span></option>';
                    }
                }

            echo '</select>';

            echo '</td></tr>';


            # Frage Plattform ab

            echo '<tr>';

            echo '<td class="text_klein">Plattform: </td><td>';

            echo '<select size="1" name="ac_environment">';
            $sql='SELECT id, name, recipients FROM environments ' .
                'ORDER BY name';

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis=mysql_query($sql, $verbindung))
                {
                fehler();
                }

            while ($zeile=mysql_fetch_array($ergebnis))
                {
                if ($zeile['id'] == 1)
                    {
                    echo '<option value="' . $zeile['id'] . '" selected><span class="text">' . $zeile['name']
                        . '</span></option>';
                    }
                else
                    {
                    echo '<option value="' . $zeile['id'] . '"><span class="text">' . $zeile['name']
                        . '</span></option>';
                    }
                }

            echo '</select>';

            echo '</td></tr>';

            # Frage Aktivität ab

            echo '<tr>';

            echo '<td class="text_klein">Aktivität: </td><td>';

            echo '<select size="1" name="ac_activity">';
            $sql='SELECT id, name FROM activities ' .
                'ORDER BY name';

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis=mysql_query($sql, $verbindung))
                {
                fehler();
                }

            while ($zeile=mysql_fetch_array($ergebnis))
                {
                if ($zeile['id'] == 7)
                    {
                    echo '<option value="' . $zeile['id'] . '" selected><span class="text">' . $zeile['name']
                        . '</span></option>';
                    }
                else
                    {
                    echo '<option value="' . $zeile['id'] . '"><span class="text">' . $zeile['name']
                        . '</span></option>';
                    }
                }

            echo '</select>';

            echo '</td></tr>';

            #Frage Bereich ab

            echo '<tr>';

            echo '<td class="text_klein">Bereich: </td><td>';

            echo '<select size="1" name="ac_area">';
            $sql='SELECT id, name FROM areas ' .
                'ORDER BY name';

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis=mysql_query($sql, $verbindung))
                {
                fehler();
                }

            while ($zeile=mysql_fetch_array($ergebnis))
                {
                if ($zeile['id'] == 5)
                    {
                    echo '<option value="' . $zeile['id'] . '" selected><span class="text">' . $zeile['name']
                        . '</span></option>';
                    }
                else
                    {
                    echo '<option value="' . $zeile['id'] . '"><span class="text">' . $zeile['name']
                        . '</span></option>';
                    }
                }

            echo '</select>';

            echo '</td></tr>';

            # Zeige Eintrag an

            echo '<tr>';

            echo '<td class="text_klein" valign="top">Eintrag:</td><td><textarea cols="80" rows="5" name="ac_eintrag">'
                . htmlspecialchars($Daten['ulo_text']) . '</textarea></td>';

            echo '</tr>';

            echo '<tr><td></td><td class="text_klein" valign="top" colspan="2"><a href="aufgabe_ansehen.php?hau_id='
                . $hau_id
                    . '">Nicht ins IS24 Activity Log schreiben</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="activitylog" value="Schreibe ins IS24 Activity-Log" class="formularbutton" /></td></tr>';

            echo '<input type="hidden" name="hau_id" value="' . $hau_id . '">';

            echo '</table>';

            echo '</form>';
            }
        // Zurueck zur Liste

        switch ($Daten['speichern'])
            {
            case 'In den Pool':

                header('Location: schreibtisch_meine_auftraege.php');
                exit;
                break;

            case 'speichern und Ticket CC':

                header('Location: schreibtisch_meine_auftraege.php');
                exit;
                break;
                
            case 'Selbst übernehmen':

                header('Location: schreibtisch_meine_aufgaben.php');
                exit;
                break;

            case 'Zuordnen':

                header('Location: aufgabe_zuordnen.php?hau_id=' . $hau_id . '&toggle=1');
                exit;
                break;

            case 'Speichern & Schließen':

                header('Location: schreibtisch_meine_aufgaben.php');
                exit;
                break;

            case 'Change stellen':

                header('Location: schreibtisch_meine_aufgaben.php');
                exit;
                break;
            }
        }
      }
    }

echo
    '<div id="bn_frame" style="position:absolute; display:none; height:198px; width:205px; background-color:#ced7d6; overflow:hidden;">';

echo
    '<iframe src="bytecal.php" style="width:208px; margin-left:-1px; border:0px; height:202px; background-color:#ced7d6; overflow:hidden;" border="0"></iframe>';

echo '</div>';

include('segment_fuss.php');
?>
