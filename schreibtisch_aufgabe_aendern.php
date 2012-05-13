<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-27 09:20:17 +0200 (Sa, 27 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_init.php');
$typen=array();
$bereiche=array();
$gruppenwechsel = 0;
$t=0;
$alter_stand_aufgabe = array();

if (isset($_GET['hau_id']))
    {
    $task_id=$_GET['hau_id'];
    }

     
$sql='SELECT * FROM typ ORDER BY uty_name';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

// Die Datensaetze werden einzeln gelesen
while ($zeile=mysql_fetch_array($ergebnis))
    {
    // Key fuer den zweidim. Array ermitteln
    $ax = $zeile["uty_id"];

    // Die Informationen aus dem Datensatz werden
    // Ueber den Key im zweidim. Array gespeichert
    $typen[$ax]["uty_name"]=$zeile["uty_name"];
    $typen[$ax]["uty_id"]=$zeile["uty_id"];
    }

$sql='SELECT * FROM level WHERE ule_id > 1 AND ule_id < 99 AND ule_aktiv = 1 ORDER BY ule_sort';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

// Die Datensaetze werden einzeln gelesen
while ($zeile=mysql_fetch_array($ergebnis))
    {
    // Key fuer den zweidim. Array ermitteln
    $ax = $zeile["ule_id"];

    // Die Informationen aus dem Datensatz werden
    // Ueber den Key im zweidim. Array gespeichert
    $bereiche[$ax]["ule_kurz"]=$zeile["ule_kurz"];
    $bereiche[$ax]["ule_id"]=$zeile["ule_id"];
    }


if (!isset($_POST['speichern']))
    {

    $sql_aufgabe=
        'SELECT * FROM aufgaben LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id WHERE hau_aktiv = 1 AND hau_id = '
        . $task_id . ' GROUP BY hau_id';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_aufgabe=mysql_query($sql_aufgabe, $verbindung))
        {
        fehler();
        }

    while ($zeile_aufgaben=mysql_fetch_array($ergebnis_aufgabe))
        {

        require_once('segment_kopf.php');

        echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Aufgabe ändern<br><br>';

        echo '<form action="schreibtisch_aufgabe_aendern.php" method="post">';
        if ((isset($_GET['return_to_task']) && $_GET['return_to_task'] == 1)
              ||
            (isset($_POST['return_to_task']) && $_POST['return_to_task'] == 1)
            )
             echo"<input type='hidden' name='return_to_task' value='1'>";

        echo '<input type="hidden" name="hau_id" value="' . $task_id . '">';

        echo '<table border="0" cellspacing="5" cellpadding="0">';

        echo '<tr>';

        echo '<td class="text_klein">Aufgabe: </td><td>' . $zeile_aufgaben['hau_id'] . '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein">Titel: </td><td><input type="text" name="hau_titel" value="'
            . ($zeile_aufgaben['hau_titel']) . '" style="width:340px;"></td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein">Referenz: </td><td><input type="text" name="hau_ticketnr" value="'
            . $zeile_aufgaben['hau_ticketnr'] . '" style="width:340px;"></td>';

        echo '</tr>';

        echo '<tr>';

       #$zeile_aufgaben['hau_beschreibung'] = htmlentities($zeile_aufgaben['hau_beschreibung']);
       #$zeile_aufgaben['hau_beschreibung'] = str_replace("\\r\\n", "<br />", $zeile_aufgaben['hau_beschreibung']);
        
        echo
            '<td class="text_klein" valign="top">Beschreibung:&nbsp;&nbsp;</td><td><textarea wrap="physical" cols="80" rows="5" name="hau_beschreibung">'
            .(htmlspecialchars($zeile_aufgaben['hau_beschreibung'])) . '</textarea></td>';

        echo '</tr>';

        echo '<tr>';

        echo
            '<td class="text_klein" valign="top">Zugehörige Links:&nbsp;&nbsp;</td><td><textarea cols="80" rows="1" name="hau_links">'
            . ($zeile_aufgaben['hau_links']) . '</textarea></td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein">Projekt: </td><td>';

        echo '<select size="1" name="hau_hprid">';

        $sql='SELECT hpr_titel, hpr_id FROM projekte 
             WHERE hpr_aktiv="1"  AND hpr_fertig = 0 ' .
            'ORDER BY hpr_sort, hpr_titel';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }

        while ($zeile=mysql_fetch_array($ergebnis))
            {
            if ($zeile_aufgaben['hau_hprid'] == $zeile['hpr_id'])
                {
                echo '<option value="' . $zeile['hpr_id'] . '" selected><span class="text">' . ($zeile['hpr_titel'])
                    . '</span></option>';
                }
            else
                {
                echo '<option value="' . $zeile['hpr_id'] . '"><span class="text">' . ($zeile['hpr_titel'])
                    . '</span></option>';
                }
            }

        echo '</td></tr>';


        ################################ Aufgabenart ################################

        echo '<tr>';

        echo '<td class="text_klein" valign="top">Gruppe und Typ der Aufgabe: </td><td>';

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
                if ($bereich['ule_id'] == $zeile_aufgaben['uaz_pg'] AND $typ['uty_id'] == $zeile_aufgaben['hau_typ'])
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

        echo '</td></tr>';

        ###########################################################################################

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
            if ($zeile_aufgaben['hau_prio'] == $zeile['upr_nummer'])
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

        echo "<td class='text_klein' valign='middle'>Plan-Ende: </td><td><input type='text' name='hau_pende' style='width:100px;' id='hau_pende' value='" . datum_anzeigen($zeile_aufgaben['hau_pende']) . "'><img src='bilder/date_go.gif' alt='Anklicken für Kalenderansicht' onclick='kalender(document.getElementById(\"hau_pende\"));'/>";
            

        echo '<span class="text_klein" valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';

        switch ($zeile_aufgaben['hau_datumstyp'])
            {
            case 1:
                echo
                    '<input type="radio" name="hau_datumstyp" value="2"><span class="text_klein"> fällig bis</span>&nbsp;&nbsp;&nbsp;';

                echo
                    '<input type="radio" name="hau_datumstyp" value="3"><span class="text_klein"> exakter Termin</span>&nbsp;&nbsp;&nbsp;';

                echo
                    '<input type="radio" name="hau_datumstyp" value="1" checked><span class="text_klein"> ohne Endtermin</span> ';
                break;

            case 2:
                echo
                    '<input type="radio" name="hau_datumstyp" value="2" checked><span class="text_klein"> fällig bis</span>&nbsp;&nbsp;&nbsp;';

                echo
                    '<input type="radio" name="hau_datumstyp" value="3"><span class="text_klein"> exakter Termin</span>&nbsp;&nbsp;&nbsp;';

                echo '<input type="radio" name="hau_datumstyp" value="1"><span class="text_klein"> ohne Endtermin</span> ';
                break;

            case 3:
                echo
                    '<input type="radio" name="hau_datumstyp" value="2"><span class="text_klein"> fällig bis</span>&nbsp;&nbsp;&nbsp;';

                echo
                    '<input type="radio" name="hau_datumstyp" value="3" checked><span class="text_klein"> exakter Termin</span>&nbsp;&nbsp;&nbsp;';

                echo '<input type="radio" name="hau_datumstyp" value="1"><span class="text_klein"> ohne Endtermin</span> ';
                break;
            }

        if ($zeile_aufgaben['hau_kalender'] == 1)
            {
            echo
                '<input type="checkbox" name="hau_kalender" checked><span class="text_klein"> Kalendereintrag?</span> ';
            }
        else
            {
            echo '<input type="checkbox" name="hau_kalender"><span class="text_klein"> Kalendereintrag?</span> ';
            }

        if ($zeile_aufgaben['hau_nonofficetime'] == 1)
            {
            echo
                '<input type="checkbox" name="hau_nonofficetime" checked><span class="text_klein"> Außerhalb Tagschicht?</span> ';
            }
        else
            {
            echo '<input type="checkbox" name="hau_nonofficetime"><span class="text_klein"> Außerhalb Tagschicht?</span> ';
            }

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo
            '<td class="text_klein">Dauer [d]: </td><td><input type="text" name="hau_dauer" style="width:100px;" value="'
            . $zeile_aufgaben['hau_dauer'] . '"></td>';

        echo '</tr>';

        echo
            '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Änderungen speichern" class="formularbutton" /></td></tr>';

        echo '</table>';

        echo '</form>';
        }
    }
else
    {

    $fehlermeldung=array();
    $anzahl_fehler=0;

    foreach ($_POST as $varname => $value)
        {
        $Daten[$varname]=$value;
        }

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
        // Ermittle Anzahl der gesetzten Haekchen

        foreach ($Daten['aufgabenart'] AS $feld)
            {
            $t=$t + count($feld);
            }
        }

    if (!isset($Daten['aufgabenart']))
        {

        $anzahl_fehler++;
        $fehlermeldung['aufgabenart']='Bitte einen Aufgabentyp festlegen!';
        }
    else if ($t > 1)
        {

        $anzahl_fehler++;
        $fehlermeldung['aufgabenart']='Bitte nur einen Aufgabentyp definieren!';
        }
    else
        {
        $fehlermeldung['aufgabenart']='';

        // Alle Datensaetze mit allen Inhalten anzeigen
        while (list($dsname, $dswert)=each($Daten['aufgabenart']))
            {
            // Der Key wird ausgegeben
            $Daten['hau_pg'] = $dsname;

            // Informationen aus dem Datensatz ausgeben
            while (list($name, $wert)=each($dswert))
                {
                $Daten['hau_typ']=$wert;
                }
            }
        }
    
$sql_aufgabe=
            'SELECT * FROM aufgaben 
            LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
            LEFT JOIN level ON ule_id = uaz_pg 
            WHERE hau_aktiv = 1 AND hau_id = '
        . $Daten['hau_id'] . ' GROUP BY hau_id';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_aufgabe=mysql_query($sql_aufgabe, $verbindung))
        {
        fehler();
        }

    while ($zeile_aufgaben=mysql_fetch_array($ergebnis_aufgabe))
        {
            $alter_stand_aufgabe['hau_titel'] = $zeile_aufgaben['hau_titel'];
            $alter_stand_aufgabe['hau_ticketnr'] = $zeile_aufgaben['hau_ticketnr'];
            $alter_stand_aufgabe['hau_beschreibung'] = $zeile_aufgaben['hau_beschreibung'];
            $alter_stand_aufgabe['hau_hprid'] = $zeile_aufgaben['hau_hprid'];
            $alter_stand_aufgabe['hau_links'] = $zeile_aufgaben['hau_links'];
            $alter_stand_aufgabe['hau_typ'] = $zeile_aufgaben['hau_typ'];
            $alter_stand_aufgabe['hau_prio'] = $zeile_aufgaben['hau_prio'];
            $alter_stand_aufgabe['hau_pende'] = $zeile_aufgaben['hau_pende'];            
            $alter_stand_aufgabe['hau_kalender'] = $zeile_aufgaben['hau_kalender'];
            $alter_stand_aufgabe['hau_nonofficetime'] = $zeile_aufgaben['hau_nonofficetime'];
            $alter_stand_aufgabe['hau_datumstyp'] = $zeile_aufgaben['hau_datumstyp'];
            $alter_stand_aufgabe['hau_dauer'] = $zeile_aufgaben['hau_dauer'];
            $alter_stand_aufgabe['hau_hprid'] = $zeile_aufgaben['hau_hprid'];
            $alter_stand_aufgabe['uaz_pg'] = $zeile_aufgaben['uaz_pg'];
            $alter_stand_aufgabe['ule_kurz'] = $zeile_aufgaben['ule_kurz'];
            $alter_stand_aufgabe['hau_hprid'] = $zeile_aufgaben['hau_hprid']; 
        }

if( $alter_stand_aufgabe['uaz_pg']!=$Daten['hau_pg'])
{
    $sql_check = 'SELECT * FROM aufgaben_mitarbeiter WHERE uau_hauid = '.$Daten['hau_id'];
    
        if (!$ergebnis_check=mysql_query($sql_check, $verbindung))
        {
        fehler();
        }
    if(mysql_num_rows($ergebnis_check)>0)
    {
        $gruppenwechsel = 1;
        $anzahl_fehler++;    
    }
}

    $task_id=$_POST['hau_id'];

    if ($Daten['hau_titel'] == '')
        {
        $anzahl_fehler++;
        $fehlermeldung['hau_titel']='Bitte einen Aufgabentitel eingeben!';
        }
    else
        {
        $fehlermeldung['hau_titel']='';
        }


        
    if (empty($Daten['hau_pende']) AND $Daten['hau_datumstyp'] != '1')
        {
        $anzahl_fehler++;
        $fehlermeldung['hau_pende']='Bitte ein Datum eingeben!';
        }
    else if (!empty($Daten['hau_pende']) AND $Daten['hau_datumstyp'] == '1' AND $Daten['hau_pende'] != 'open')
        {
        $anzahl_fehler++;
        $fehlermeldung['hau_pende']='Bitte Datum löschen, Sie haben ein offenes Ende gewählt!';
        }
    else
        {

        list($anzahl_fehler, $fehlermeldung['hau_pende'])=datum_check($Daten['hau_pende'], 'hau_pende', $anzahl_fehler);
        }

    if (($anzahl_fehler > 0 AND !isset($_SESSION['gruppenwechsel'])))
        {

        require_once('segment_kopf.php');

        echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Aufgabe ändern<br><br>';

        echo '<form action="schreibtisch_aufgabe_aendern.php" method="post">';

        echo '<input type="hidden" name="hau_id" value="' . $task_id . '">';
 
        echo '<table border="0" cellspacing="5" cellpadding="0">';

        echo '<tr>';

        echo '<td class="text_klein">Aufgabe: </td><td>' . $Daten['hau_id'] . '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td colspan="2" class="text_rot">&nbsp;&nbsp;' . $fehlermeldung['hau_titel'] . '</td></tr><tr>';

        echo '<td class="text_klein">Titel: </td><td><input type="text" name="hau_titel" value="'
            . htmlspecialchars($Daten['hau_titel']) . '" style="width:340px;"></td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein">Referenz: </td><td><input type="text" name="hau_ticketnr" value="'
            . $Daten['hau_ticketnr'] . '" style="width:340px;"></td>';

        echo '</tr>';

        echo '<tr>';


        echo
            '<td class="text_klein" valign="top">Beschreibung:&nbsp;&nbsp;</td><td><textarea cols="80" rows="5" name="hau_beschreibung">'
            . htmlspecialchars(($Daten['hau_beschreibung'])) . '</textarea></td>';

        echo '</tr>';

        echo '<tr>';

        echo
            '<td class="text_klein" valign="top">Zugehörige Links:&nbsp;&nbsp;</td><td><textarea cols="80" rows="1" name="hau_links">'
            . $Daten['hau_links'] . '</textarea></td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein">Projekt: </td><td>';

        echo '<select size="1" name="hau_hprid">';

        $sql='SELECT hpr_titel, hpr_id FROM projekte 
            WHERE hpr_aktiv="1"  AND hpr_fertig = 0 ' .
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
                echo '<option value="' . $zeile['hpr_id'] . '" selected><span class="text">' . ($zeile['hpr_titel'])
                    . '</span></option>';
                }
            else
                {
                echo '<option value="' . $zeile['hpr_id'] . '"><span class="text">' . ($zeile['hpr_titel'])
                    . '</span></option>';
                }
            }

        echo '</td></tr>';

        ######################################### Aufgabenart ###############################

        echo '<tr><td colspan="2" class="text_rot">&nbsp;&nbsp;' . $fehlermeldung['aufgabenart'] . '</td></tr>';

        if($gruppenwechsel==1)
        {
            echo '<tr><td colspan="2" class="text_rot">&nbsp;&nbsp;Speichern der Änderung der Gruppe löscht alle bisher zugeordneten Bearbeiter und verschiebt die Aufgabe in die neue gewählte Gruppe</td></tr>';
            $_SESSION['gruppenwechsel']=1;
        }
        
        echo '<tr>';

        echo '<td class="text_klein" valign="top">Gruppe und Aufgabentyp: </td><td>';

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
                if (isset($Daten['hau_pg']) AND isset($Daten['hau_typ']))
                    {
                    if ($bereich['ule_id'] == $Daten['hau_pg'] AND $typ['uty_id'] == $Daten['hau_typ'])
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
                else
                    {
                    echo $stil . '<input type="checkbox" name="aufgabenart[' . $bereich['ule_id'] . ']['
                        . $typ['uty_id'] . ']" value="' . $typ['uty_id'] . '"></td>';
                    }
                $zaehler++;
                }

            echo '<td class="text_klein">' . $typ['uty_name'] . '</td>';

            echo '</tr>';
            }

        echo '</table>';

        echo '</td></tr>';

        ############################################################################################

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

        echo "<td class='text_klein' valign='middle'>Plan-Ende: </td><td><input type='text' name='hau_pende' style='width:100px;' id='hau_pende' value='" . $Daten['hau_pende'] . "'><img src='bilder/date_go.gif' alt='Anklicken für Kalenderansicht' onclick='kalender(document.getElementById(\"hau_pende\"));'/>";

        echo '<span class="text_klein" valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';

        switch ($Daten['hau_datumstyp'])
            {
            case 1:
                echo
                    '<input type="radio" name="hau_datumstyp" value="2"><span class="text_klein"> fällig bis</span>&nbsp;&nbsp;&nbsp;';

                echo
                    '<input type="radio" name="hau_datumstyp" value="3"><span class="text_klein"> exakter Termin</span>&nbsp;&nbsp;&nbsp;';

                echo
                    '<input type="radio" name="hau_datumstyp" value="1" checked><span class="text_klein"> ohne Endtermin</span> ';
                break;

            case 2:
                echo
                    '<input type="radio" name="hau_datumstyp" value="2" checked><span class="text_klein"> fällig bis</span>&nbsp;&nbsp;&nbsp;';

                echo
                    '<input type="radio" name="hau_datumstyp" value="3"><span class="text_klein"> exakter Termin</span>&nbsp;&nbsp;&nbsp;';

                echo '<input type="radio" name="hau_datumstyp" value="1"><span class="text_klein"> ohne Endtermin</span> ';
                break;

            case 3:
                echo
                    '<input type="radio" name="hau_datumstyp" value="2"><span class="text_klein"> fällig bis</span>&nbsp;&nbsp;&nbsp;';

                echo
                    '<input type="radio" name="hau_datumstyp" value="3" checked><span class="text_klein"> exakter Termin</span>&nbsp;&nbsp;&nbsp;';

                echo '<input type="radio" name="hau_datumstyp" value="1"><span class="text_klein"> ohne Endtermin</span> ';
                break;
            }

        if ($Daten['hau_kalender'] == 1)
            {
            echo
                '<input type="checkbox" name="hau_kalender" checked><span class="text_klein"> Kalendereintrag?</span> ';
            }
        else
            {
            echo '<input type="checkbox" name="hau_kalender"><span class="text_klein"> Kalendereintrag?</span> ';
            }

        if ($Daten['hau_nonofficetime'] == 1)
            {
            echo
                '<input type="checkbox" name="hau_nonofficetime" checked><span class="text_klein"> Außerhalb Tagschicht?</span> ';
            }
        else
            {
            echo '<input type="checkbox" name="hau_nonofficetime"><span class="text_klein"> Außerhalb Tagschicht?</span> ';
            }

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo
            '<td class="text_klein">Dauer [d]: </td><td><input type="text" name="hau_dauer" style="width:100px;" value="'
            . $Daten['hau_dauer'] . '"></td>';

        echo '</tr>';

        echo
            '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Änderungen speichern" class="formularbutton" /></td></tr>';

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

                    # Wurde ein Change angelegt?

        if ($Daten['hau_hprid'] == 6 AND $alter_stand_aufgabe['hau_hprid']!= 6) // Neuer Change
            {
                
 

            $sql='INSERT INTO rollen_status (' .
                'urs_hauid) ' .
                'VALUES ( ' .
                '"' . $Daten['hau_id'] . '")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $Daten['hau_id'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
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

                $hauid = $Daten['hau_id'];
                $initiator=$_SESSION['hma_id'];
                $empfaenger=$zeile['hma_id'];
                $info='Ein neuer Change wurde zur Freigabe eingereicht.';

                include('segment_news.php');

                //////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

                ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

                $mailtag='ume_aufgabestatus';
                $mail_hma_id=$empfaenger;                 
                $mail_hau_id=$Daten['hau_id'];
                $text="\nEin neuer Change wurde eingereicht:\n";
                $mail_info='Neuer Change';
                $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
                $telefon = $_SESSION['hma_telefon'];

                include('segment_mail_senden.php');

                ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
                }    

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
                '"' . $Daten['hau_pg'] . '", ' .
                '"' . $_SESSION['hma_id'] . '", ' .
                '"1", ' .
                'NOW())';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $hau_id=mysql_insert_id();

            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $Daten['hau_id'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                . '", "Backlog angelegt", NOW() )';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                } 
                
            $sql = 'DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid = '.$Daten['hau_id'];
            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                } 
                           
            $sql = 'UPDATE aufgaben SET hau_aktiv = 0 WHERE hau_id = '.$Daten['hau_id'];
            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }            
                
                       $sql='INSERT INTO eventlog (hel_area, hel_type, hel_referer, hel_text, hel_timestamp) ' .
            'VALUES ("TASK", "DELETE", "' . $_SESSION['hma_login'] . '", "hat folgende Aufgabe ins backlog geschoben: ' . $Daten['hau_id'] . '.", NOW() )';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            } 
                
            header('Location: backlog_liste.php?xGruppe='.$Daten['hau_pg'].'&xProjekt='.$Daten['hau_hprid']);
            exit;
}
else
{
  
        if ($Daten['speichern'] == 'Änderungen speichern')
            {
            // Speichere den Datensatz

            $sql='UPDATE aufgaben SET ' .

            'hau_titel = "' . mysql_real_escape_string($Daten['hau_titel']) . '",' .
                'hau_beschreibung = "' . mysql_real_escape_string($Daten['hau_beschreibung']) . '", ' .
                'hau_prio = "' . $Daten['hau_prio'] . '", ' .
                'hau_pende = "' . $Daten['hau_pende'] . '", ' .
                'hau_kalender = "' . $Daten['hau_kalender'] . '", ' .
                'hau_nonofficetime = "' . $Daten['hau_nonofficetime'] . '", ' .
                'hau_zeitstempel =NOW(), ' .
                'hau_datumstyp = "' . $Daten['hau_datumstyp'] . '", ' .
                'hau_hprid = "' . $Daten['hau_hprid'] . '", ' .
                'hau_typ = "' . $Daten['hau_typ'] . '", ' .
                'hau_ticketnr = "' . $Daten['hau_ticketnr'] . '", ' .
                'hau_links = "' . $Daten['hau_links'] . '", ' .
                'hau_dauer = "' . $Daten['hau_dauer'] . '" ' .
                'WHERE hau_id = ' . $Daten['hau_id'];
               
            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $task_id=$Daten['hau_id'];
            

### Bearbeiter löschen?

if($_SESSION['gruppenwechsel']==1)
{
    unset($_SESSION['gruppenwechsel']);

## Ermittle die alten Bearbeiter

$sql_ma_alt = 'SELECT * FROM aufgaben_mitarbeiter WHERE uau_hauid = '.$task_id;

    if (!($ergebnis_ma_alt=mysql_query($sql_ma_alt, $verbindung)))
    {
        fehler();
    } 
    
        
    while ($zeile_ma_alt=mysql_fetch_array($ergebnis_ma_alt))
        {
            
        $hauid=$task_id;
        $initiator=$_SESSION['hma_id'];
        $empfaenger=$zeile_ma_alt['uau_hmaid'];
        $info='Die Aufgabe wurde zurückgenommen durch ' . $_SESSION['hma_login'];

        include('segment_news.php');

        ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
        $level_gruppe = 0;  
        $mail_hma_id=$zeile_ma_alt['uau_hmaid'];
        $mail_hau_id=$task_id;
        $text="Die Aufgabe wurde zurückgenommen durch " . $_SESSION['hma_login'];
        $mail_info='Aufgabe zurückgenommen';
        $mailtag='ume_aufgabestatus';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];

        include('segment_mail_senden.php');
        }
    
    $sql_loeschen = 'DELETE FROM aufgaben_mitarbeiter WHERE uau_hauid = '.$task_id;

    if (!($ergebnis_loeschen=mysql_query($sql_loeschen, $verbindung)))
    {
        fehler();
    }    

     $sql_loeschen = 'DELETE FROM aufgaben_zuordnung WHERE uaz_hauid = '.$task_id;

    if (!($ergebnis_loeschen=mysql_query($sql_loeschen, $verbindung)))
    {
        fehler();
    } 
    
    $sql_anlegen = 'INSERT INTO aufgaben_zuordnung (uaz_hauid, uaz_pg) VALUES ("'.$task_id.'", "'.$Daten['hau_pg'].'")';

    if (!($ergebnis_anlegen=mysql_query($sql_anlegen, $verbindung)))
    {
        fehler();
    } 

            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                . '", "Gruppe wurde verschoben aus '.$alter_stand_aufgabe['ule_kurz'].' und die Bearbeiter gelöscht.", NOW() )';


            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }  

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
        $level_gruppe = $Daten['hau_pg'];

        $sql_check = 'SELECT hau_titel FROM aufgaben WHERE hau_id = '.$task_id;

    if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
        {
        fehler();
        }

    while ($zeile_check=mysql_fetch_array($ergebnis_check))
        {
           $text = "Die Aufgabe [".$zeile_check['hau_titel']."] wurde durch ".$_SESSION['hma_vorname']." ".$_SESSION['hma_name']." an Deine Gruppe weitergereicht.";
        }        
      
            $mail_hau_id = $task_id;
            $mailtag='ume_gruppe';
            $mail_info='Neue Gruppenaufgabe';
        $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
        $telefon = $_SESSION['hma_telefon'];
            include('segment_mail_senden.php');

            ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
    
    
}
else
{
$sql='UPDATE aufgaben_zuordnung SET uaz_pg = '.$Daten['hau_pg'].' WHERE uaz_hauid = '.$task_id;
                           
            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

}
                
# Was wurde geändert?

$text = '';

if($alter_stand_aufgabe['hau_titel']!=$Daten['hau_titel'])
{
    $text = "Vorheriger Titel: ".$alter_stand_aufgabe['hau_titel']."<br>Neuer Titel: ".$Daten['hau_titel']."<br>";
}

if($alter_stand_aufgabe['hau_ticketnr']!=$Daten['hau_ticketnr'])
{
    $text.= "Vorherige Referenz: ".$alter_stand_aufgabe['hau_ticketnr']."<br>Neue Referenz: ".$Daten['hau_ticketnr']."<br>";
}

if($alter_stand_aufgabe['hau_beschreibung']!=$Daten['hau_beschreibung'])
{
    $text.= "Vorherige Beschreibung: ".$alter_stand_aufgabe['hau_beschreibung']."<br>Neue Beschreibung: ".$Daten['hau_beschreibung']."<br>";
}

if($alter_stand_aufgabe['hau_hprid']!=$Daten['hau_hprid'])
{
    $sql_aenderung = 'SELECT hpr_titel FROM projekte WHERE hpr_id = '.$alter_stand_aufgabe['hau_hprid'];
    
    if (!($ergebnis_aenderung=mysql_query($sql_aenderung, $verbindung)))
        {
          fehler();
        } 
    
    while ($zeile_aenderung=mysql_fetch_array($ergebnis_aenderung))
        {
            $alter_stand_aufgabe['hpr_titel'] = $zeile_aenderung['hpr_titel'];
        }

    $sql_aenderung = 'SELECT hpr_titel FROM projekte WHERE hpr_id = '.$Daten['hau_hprid'];
    
    if (!($ergebnis_aenderung=mysql_query($sql_aenderung, $verbindung)))
        {
          fehler();
        } 
    
    while ($zeile_aenderung=mysql_fetch_array($ergebnis_aenderung))
        {
           
            $text.= "Vorherige Zuordnung: ".$alter_stand_aufgabe['hpr_titel']."<br>Neue Zuordnung: ".$zeile_aenderung['hpr_titel']."<br>";
        }
}

if($alter_stand_aufgabe['hau_links']!=$Daten['hau_links'])
{
    $text.= "Vorheriger Link: ".$alter_stand_aufgabe['hau_links']."<br>Neuer Link: ".$Daten['hau_links']."<br>";
}

if($alter_stand_aufgabe['hau_typ']!=$Daten['hau_typ'])
{
        $sql_aenderung = 'SELECT uty_name FROM typ WHERE uty_id = '.$alter_stand_aufgabe['hau_typ'];
    
    if (!($ergebnis_aenderung=mysql_query($sql_aenderung, $verbindung)))
        {
          fehler();
        } 
    
    while ($zeile_aenderung=mysql_fetch_array($ergebnis_aenderung))
        {
            $alter_stand_aufgabe['uty_name'] = $zeile_aenderung['uty_name'];
        }
    
    $sql_aenderung = 'SELECT uty_name FROM typ WHERE uty_id = '.$Daten['hau_typ'];
    
    if (!($ergebnis_aenderung=mysql_query($sql_aenderung, $verbindung)))
        {
          fehler();
        } 
    
    while ($zeile_aenderung=mysql_fetch_array($ergebnis_aenderung))
        {
           $text.= "Vorheriger Aufgabentyp: ".$alter_stand_aufgabe['uty_name']."<br>Neuer Aufgabentyp: ".$zeile_aenderung['uty_name']."<br>";
        }
}

if($alter_stand_aufgabe['hau_prio']!=$Daten['hau_prio'])
{
    $sql_aenderung = 'SELECT upr_name FROM prioritaet WHERE upr_id = '.$alter_stand_aufgabe['hau_prio'];

       if (!($ergebnis_aenderung=mysql_query($sql_aenderung, $verbindung)))
        {
          fehler();
        } 
    
    while ($zeile_aenderung=mysql_fetch_array($ergebnis_aenderung))
        {
            $alter_stand_aufgabe['upr_name'] = $zeile_aenderung['upr_name'];
        }
   
    $sql_aenderung = 'SELECT upr_name FROM prioritaet WHERE upr_id = '.$Daten['hau_prio'];
    
    if (!($ergebnis_aenderung=mysql_query($sql_aenderung, $verbindung)))
        {
          fehler();
        } 
    
    while ($zeile_aenderung=mysql_fetch_array($ergebnis_aenderung))
        {
           $text.= "Vorherige Priorität: ".$alter_stand_aufgabe['upr_name']."<br>Neue Priorität: ".$zeile_aenderung['upr_name']."<br>";
        }
}

if($alter_stand_aufgabe['hau_pende']!=$Daten['hau_pende'])
{
    $text.= "Vorheriges Enddatum: ".date("d.m.Y",strtotime($alter_stand_aufgabe['hau_pende']))."<br>Neues Enddatum: ".date("d.m.Y",strtotime($Daten['hau_pende']))."<br>";
}

if($alter_stand_aufgabe['hau_kalender']!=$Daten['hau_kalender'])
{
    $text.= "Kalendereintrag wurde geändert. <br>";
}

if($alter_stand_aufgabe['hau_dauer']!=$Daten['hau_dauer'])
{
    $text.= "Vorherige Dauer: ".$alter_stand_aufgabe['hau_dauer']."[d]<br>Aufgabendauer wurde geändert: ".$Daten['hau_dauer']."[d]<br>";
}

if($alter_stand_aufgabe['hau_datumstyp']!=$Daten['hau_datumstyp'])
{
        switch ($Daten['hau_datumstyp'])
            {
            case 1:
                $datumstyp = 'offen';
            case 2:
                $datumstyp = 'fällig zum';
            case 3:
                $datumstyp = 'exakt am';
            }

        switch ($alter_stand_aufgabe['hau_datumstyp'])
            {
            case 1:
                $datumstyp_alt = 'offen';
            case 2:
                $datumstyp_alt = 'fällig zum';
            case 3:
                $datumstyp_alt = 'exakt am';
            }
 
    $text.= "Vorheriger Datumstyp: ".$datumstyp_alt."<br>Datumstyp wurde geändert auf: ".$datumstyp."<br>";
}

 $text = mysql_real_escape_string($text);

            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $task_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                . '", "Aufgabe wurde geändert: '.$text.'", NOW() )';


            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }              
                  
            ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

            $sql_check='SELECT uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $task_id;

            if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
                {
                fehler();
                }

            while ($zeile_check=mysql_fetch_array($ergebnis_check))
                {
                $bearbeiter = $zeile_check['uau_hmaid'];

                if ($bearbeiter != $_SESSION['hma_id'])
                    {
                    $hauid=$task_id;
                    $initiator=$_SESSION['hma_id'];
                    $empfaenger=$bearbeiter;
                    $info='Die Aufgabe wurde geändert.';

                    include('segment_news.php');

                    ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

                    $mailtag='ume_aufgabestatus';
                    $mail_hma_id=$bearbeiter;
                    $mail_hau_id=$task_id;
                    // $text="\r\n\r\nDie Aufgabe wurde geändert.\r\n\r\n";
                    $mail_info='Die Aufgabe wurde geändert';
                    
                    $kommentator = $_SESSION['hma_vorname'].' '.$_SESSION['hma_name'];
                    $telefon = $_SESSION['hma_telefon'];

                    include('segment_mail_senden.php');

                    ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

                    }
                }

            ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

            }
        // Zurueck zur Liste
		if ($_POST['return_to_task'] == 1)
		{
			$tmp_fwd='Location: aufgabe_ansehen.php?hau_id='.$_POST['hau_id'];
			header($tmp_fwd);
		}
		else
        	header('Location: schreibtisch_meine_auftraege.php');
        exit;
        }
    

        
        
        } // Ende Fehler vorhanden
    }
    echo
    '<div id="bn_frame" style="position:absolute; display:none; height:198px; width:205px; background-color:#ced7d6; overflow:hidden;">';

echo
    '<iframe src="bytecal.php" style="width:208px; margin-left:-1px; border:0px; height:202px; background-color:#ced7d6; overflow:hidden;" border="0"></iframe>';

echo '</div>';

include('segment_fuss.php');
    ?>