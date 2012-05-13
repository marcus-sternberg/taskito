<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 14:56:26 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
# Integriere Module

require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if(isset($_REQUEST['hau_id_ref'])) {$hau_id_ref = $_REQUEST['hau_id_ref'];}


if (!isset($_POST['speichern']))
    {
    require_once('segment_kopf.php');

    $sql = 'SELECT hau_titel, hau_beschreibung FROM aufgaben WHERE hau_id = '.$hau_id_ref;
    
    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        $titel = $zeile['hau_titel'];
        $beschreibung = $zeile['hau_beschreibung'];
        }    

    
    
    echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Stelle neuen Change<br><br>';

    echo '<form action="schreibtisch_aufgabe_in_change.php?hau_id='.$hau_id_ref.'" method="post" enctype="multipart/form-data">';

    echo '<input type="hidden" name="hau_id_ref" value="'.$hau_id_ref.'">';
    
    echo '<table border="0" cellspacing="5" cellpadding="0">';

    echo '<tr>';

    echo '<td class="text_klein">Titel: </td><td><input type="text" name="hau_titel" value="'.$titel.'" style="width:340px;">';
    
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

    echo '</td>';

    echo '</tr>';
    
    echo  
        '<td class="text_klein" valign="top">Beschreibung:&nbsp;&nbsp;</td><td><textarea cols="80" rows="20" name="hau_beschreibung">'.$beschreibung.'</textarea></td>';    

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein" valign="top">Zugehörige Links:&nbsp;&nbsp;</td><td><textarea cols="80" rows="1" name="hau_links">http://taskscout24.rz.is24.loc/aufgabe_ansehen.php?hau_id='.$hau_id_ref.'</textarea></td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein" valign="top">Anlagen:&nbsp;&nbsp;</td><td><input type="file" name="hau_datei"></td>';

    echo '</tr>';

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
        '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" class="formularbutton" />';
        
    echo '</td></tr>';

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

     $Daten['hau_hprid']=6;

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

        // Die Gruppe wird nach dem Eingeber festgelegt
        $Daten['uaz_pg'] = $_SESSION['hma_level'];

       // Der Typ wird auf Sonstiges gesetzt
        $Daten['hau_typ']= 5; // OTHER

        $Daten['ulo_extra']=0;
        $checked='';

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

        echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Stelle neuen Change<br><br>';

        echo '<form action="schreibtisch_aufgabe_in_change.php?hau_id_ref='.$Daten['hau_id_ref'].'" method="post" enctype="multipart/form-data">';

      echo '<input type="hidden" name="hau_id_ref" value="'.$Daten['hau_id_ref'].'">';    
    
        echo '<table border="0" cellspacing="5" cellpadding="0">';

        echo '<tr>';

        echo '<td colspan="2" class="text_rot">&nbsp;&nbsp;' . $fehlermeldung['hau_titel'] . '</td></tr><tr>';

        echo '<td class="text_klein">Titel: </td><td><input type="text" name="hau_titel" value="' . htmlspecialchars($Daten['hau_titel'])
            . '" style="width:340px;">';
            
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

            
        echo '</td>';

        echo '</tr>';

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
            '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" class="formularbutton" />';
            

        echo '</td></tr>';
        
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
                'hau_utcid) ' .
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
                '"' . $Daten['hau_utcid'] . '")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
                                
            $hau_id=mysql_insert_id();
            
            $sql='UPDATE aufgaben SET hau_links = "http://taskscout24.rz.is24.loc/aufgabe_ansehen.php?hau_id='.$hau_id.'" WHERE hau_id = '.$hau_id_ref;
            
            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }              

            $sql='INSERT INTO aufgaben_zuordnung
                (uaz_hauid, uaz_pg, uaz_pba) ' .
                'VALUES ("' . $hau_id . '", "' . $Daten['uaz_pg'] . '", "'.$_SESSION['hma_id'].'")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            $sql='INSERT INTO aufgaben_mitarbeiter
                (uau_hmaid, uau_hauid, uau_status, uau_prio, uau_stopp, uau_tende, uau_ma_status) ' .
                'VALUES ("'.$_SESSION['hma_id'].'", "' . $hau_id . '", "0", "99", "0", "'.$Daten['hau_pende'].'", "1")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
  
            $sql='INSERT INTO rollen_status (' .
                'urs_hauid) ' .
                'VALUES ( ' .
                '"' . $hau_id . '")';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

### Schreibe Kommentar für die alte Aufgabe
                
            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $Daten['hau_id_ref'] . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                . '", "Change mit der Ticket-ID '.$hau_id.' für diese Aufgabe gestellt.", NOW() )';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
                
### Schreibe Kommentar im neuen Change
                
            $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                . '", "Change erstellt aus Aufgabe '.$Daten['hau_id_ref'].'", NOW() )';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
                
# Schreibe für die Benutzer eine Info in die alte Aufgabe
            
    $sql='INSERT INTO log (' .
        'ulo_id, ' .
        'ulo_aufgabe, ' .
        'ulo_text, ' .
        'ulo_zeitstempel, ' .
        'ulo_ma, ' .
        'ulo_datum, ' .
        'ulo_aufwand, ' .
        'ulo_fertig, ' .
        'ulo_extra, ' .
        'ulo_requestor, ' .
        'ulo_mail, '.
        'ulo_ping) ' .
        'VALUES ( ' .
        'NULL, ' .
        '"' . $Daten['hau_id_ref'] . '", ' .
        '"Für diese Aufgabe wurde Change '.$hau_id.' gestellt.", ' .
        'NOW(), ' .
        '"' . $_SESSION['hma_id'] . '", ' .
        'NOW(), ' .
        '"0", ' .
        '"0", ' .
        '"0", ' .
        '"1", ' .
        '"0", ' .  
        '"0")';
        

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
        
# Schreibe für die Benutzer eine Info in die neue Aufgabe
            
    $sql='INSERT INTO log (' .
        'ulo_id, ' .
        'ulo_aufgabe, ' .
        'ulo_text, ' .
        'ulo_zeitstempel, ' .
        'ulo_ma, ' .
        'ulo_datum, ' .
        'ulo_aufwand, ' .
        'ulo_fertig, ' .
        'ulo_extra, ' .
        'ulo_requestor, ' .
        'ulo_mail, '.
        'ulo_ping) ' .
        'VALUES ( ' .
        'NULL, ' .
        '"' . $hau_id . '", ' .
        '"Dieser Change wurde aus der Aufgabe '.$Daten['hau_id_ref'].' erstellt.", ' .
        'NOW(), ' .
        '"' . $_SESSION['hma_id'] . '", ' .
        'NOW(), ' .
        '"0", ' .
        '"0", ' .
        '"0", ' .
        '"1", ' .
        '"0", ' .  
        '"0")';

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
        

        if ($_FILES["hau_datei"]["tmp_name"] != '')
            {

            $oldumask = umask(0); 
            mkdir("anhang/" . $hau_id, 0777); 
            umask($oldumask); 
            
            if (($_FILES["hau_datei"]["error"] == 3) OR ($_FILES["hau_datei"]["error"] == 4))
                {
                echo "Fehler: Die Datei wurde nur teilweise oder gar nicht hochgeladen. <br />";
                }
            else
                {
                move_uploaded_file($_FILES["hau_datei"]["tmp_name"],
                    "anhang/" . $hau_id . "/" . $_FILES["hau_datei"]["name"]);
                }
            }
            
      }
            // Zurueck zur Liste
        header('Location: aufgabe_ansehen.php?hau_id='.$Daten['hau_id_ref']);
        exit;
    
    }
    



echo
    '<div id="bn_frame" style="position:absolute; display:none; height:198px; width:205px; background-color:#ced7d6; overflow:hidden;">';

echo
    '<iframe src="bytecal.php" style="width:208px; margin-left:-1px; border:0px; height:202px; background-color:#ced7d6; overflow:hidden;" border="0"></iframe>';

echo '</div>';

include('segment_fuss.php');
?>