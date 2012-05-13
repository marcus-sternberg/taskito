<?php
###### Editnotes ####
#$LastChangedDate: 2012-01-03 12:06:44 +0100 (Di, 03 Jan 2012) $
#$Author: msternberg $ 
#####################
# Integriere Module

require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

# Definiere globale Variablen
$typen=array();
$bereiche=array();
$t=0;
$jumpstring = 'Change stellen';   

if(isset($_REQUEST['jump'])) {$jump = $_REQUEST['jump'];} else {$jump = '1';}   

switch($jump)
{
    case 1:
        $pagestring = 'Erstelle neuen Change';
        break;
        
    case 2:
        $pagestring = 'Erstelle neue Funktionsgruppe';
        break;
}

if (!isset($_POST['speichern']))
    {
    require_once('segment_kopf.php');

    echo '<div id="header">';

    echo '<ul>';
    
switch($jump)
{
    case 1:
        echo '<li id="current"><a href="#">Allgemeiner Change</a></li>';
        echo '<li><a href="schreibtisch_neuer_change.php?jump=2">Neue Funktionsgruppe</a></li>';   
        break;
        
    case 2:
        echo '<li><a href="schreibtisch_neuer_change.php?jump=1">Allgemeiner Change</a></li>';
        echo '<li id="current"><a href="#">Neue Funktionsgruppe</a></li>';   
        break;
}    

  
    echo '</ul>';

    echo '</div>';     
    
    echo '<br><br><br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;'.$pagestring.'<br><br>';

    echo '<form action="schreibtisch_neuer_change.php?jump='.$jump.'" method="post" enctype="multipart/form-data">';
    
    echo '<input type="hidden" name="hau_jump" value="'.$jump.'">';

    echo '<table border="0" cellspacing="5" cellpadding="0">';

    echo '<tr>';

switch($jump)
{
    case 1:
        echo '<td class="text_klein">Titel: </td><td><input type="text" name="hau_titel" style="width:340px;">';  
        break;
        
    case 2:
        echo '<td class="text_klein">Name der FG: </td><td><input type="text" name="hau_titel" style="width:340px;">';
        break;
}  
    
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
    
    if($jump==2)
    {
    
    # Referenzgruppe
    
    echo '<tr>';  
    echo '<td class="text_klein">Referenzgruppe: </td><td><input type="text" name="hau_ref" style="width:100px;"> <span class="text_klein">(falls vorhanden)</span>';       
    echo '</td></tr>';
      
    # RAM
    
    echo '<tr>';     
    echo '<td class="text_klein">RAM in GB: </td><td><input type="text" name="hau_gb" value="1" style="width:100px;"> <span class="text_klein">(Mindestgröße 1 GB)</span>';  
    echo '</td></tr>'; 
            
    # YADT
    
    echo '<tr>';

    echo
        "<td class='text_klein' valign='middle'>RPMisiert (YADT):</td>";

    echo '<td><span class="text_klein" valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';

    echo
        '<input type="radio" name="hau_yadt" value="1"><span class="text_klein"> ja</span>&nbsp;&nbsp;&nbsp;';

    echo
        '<input type="radio" name="hau_yadt" value="0"><span class="text_klein"> nein</span>&nbsp;&nbsp;&nbsp;';
        
    echo '</td>';

    echo '</tr>'; 

    # Frontend
    
    echo '<tr>';

    echo
        "<td class='text_klein' valign='middle'>Frontend:</td>";

    echo '<td><span class="text_klein" valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';

    echo
        '<input type="radio" name="hau_frontend" value="1"><span class="text_klein"> ja</span>&nbsp;&nbsp;&nbsp;';

    echo
        '<input type="radio" name="hau_frontend" value="0"><span class="text_klein"> nein</span>&nbsp;&nbsp;&nbsp;';
        
    echo '</td>';

    echo '</tr>';     

    # FC-Anschluß
    
    echo '<tr>';

    echo
        "<td class='text_klein' valign='middle'>FC-Anschluß:</td>";

    echo '<td><span class="text_klein" valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';

    echo
        '<input type="radio" name="hau_fc" value="1"><span class="text_klein"> ja</span>&nbsp;&nbsp;&nbsp;';

    echo
        '<input type="radio" name="hau_fc" value="0"><span class="text_klein"> nein</span>&nbsp;&nbsp;&nbsp;';
        
    echo '</td>';

    echo '</tr>'; 

    # RedHat-Version
    
    echo '<tr>';

    echo
        "<td class='text_klein' valign='middle'>Red Hat Enterprise Linux:</td>";

    echo '<td><span class="text_klein" valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';

    echo
        '<input type="radio" name="hau_rh" value="0"><span class="text_klein"> RedHat 5 (64bit)</span>&nbsp;&nbsp;&nbsp;';

    echo
        '<input type="radio" name="hau_rh" value="1"><span class="text_klein"> RedHat 6 (64bit)</span>&nbsp;&nbsp;&nbsp;';
        
    echo '</td>';

    echo '</tr>'; 

    # Service-Klasse
    
      echo '<tr>';

    echo
        "<td class='text_klein' valign='middle'>Serviceklasse PROD:</td>";
        
        echo '<td><select size="1" name="hau_skp">';
        echo '<option value="1"><span class="text">Serviceklasse I</span></option>';      
        echo '<option value="2"><span class="text">Serviceklasse II</span></option>';  
        echo '<option value="3" selected><span class="text">Serviceklasse III</span></option>';  
        echo '<option value="4"><span class="text">Serviceklasse IV</span></option>';           
  
    echo '</select>'; 

    echo '</td></tr>';

      echo '<tr>';

    echo
        "<td class='text_klein' valign='middle'>Serviceklasse TUV/DEV:</td>";
        
        echo '<td><select size="1" name="hau_skt">';
        echo '<option value="1"><span class="text">Serviceklasse I</span></option>';      
        echo '<option value="2"><span class="text">Serviceklasse II</span></option>';  
        echo '<option value="3" selected><span class="text">Serviceklasse III</span></option>';  
        echo '<option value="4"><span class="text">Serviceklasse IV</span></option>';           
  
    echo '</select>'; 

    echo '</td></tr>';
    echo '<tr>';
    echo "<td>&nbsp;</td><td class='text_klein' valign='middle'><a href='https://wiki.iscout.local/x/dwBE' target='_blank'>Hinweise zu den Serviceklassen</a></td>";
    echo '</tr>';

    # /data Filesystemgröße
        
                 
    echo '<tr>';  
    echo '<td class="text_klein">Größe Filesystem: </td><td><input type="text" name="hau_fs" style="width:100px;"> <span class="text_klein">(GB)</span>';       
    echo '</td></tr>';

    echo '<tr>';  
    
    echo  
        '<td class="text_klein" valign="top">Software Server</td><td><textarea cols="80" rows="5" name="hau_sws"></textarea></td>';    

    echo '</tr>';

    echo '<tr>';  
    
    echo  
        '<td class="text_klein" valign="top">Sonstiges</td><td><textarea cols="80" rows="10" name="hau_sonstiges"></textarea></td>';    

    echo '</tr>';
    
    }
    else
    {

    echo '<tr>';  
    
    echo  
        '<td class="text_klein" valign="top">Beschreibung:&nbsp;&nbsp;</td><td><textarea cols="80" rows="20" name="hau_beschreibung"></textarea></td>';    

    echo '</tr>';
        
    echo '<tr>';

    echo
        '<td class="text_klein" valign="top">Zugehörige Links:&nbsp;&nbsp;</td><td><textarea cols="80" rows="1" name="hau_links"></textarea></td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein" valign="top">Anlagen:&nbsp;&nbsp;</td><td><input type="file" name="hau_datei"></td>';

    echo '</tr>';

    }
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

  
    echo '</td>';

    echo '</tr>';

    echo
        '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="'.$jumpstring.'" class="formularbutton" />';
        
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

if($Daten['hau_jump']==2)
{

$Daten['hau_beschreibung'] = "Bitte eine neue Funktionsgruppe einrichten mit den Parametern:\n\n";

$Daten['hau_beschreibung'] .= "Referenzgruppe: ".$Daten['hau_ref']."\n";
$Daten['hau_beschreibung'] .= "RAM: ".$Daten['hau_gb']." GB\n"; 

if($Daten['hau_yadt']==0) {$Daten['hau_yadt']='Nein';} else {$Daten['hau_yadt']='Ja';}
$Daten['hau_beschreibung'] .= "RPMisiert: ".$Daten['hau_yadt']."\n"; 

if($Daten['hau_frontend']==0) {$Daten['hau_frontend']='Nein';} else {$Daten['hau_frontend']='Ja';}
$Daten['hau_beschreibung'] .= "Frontend: ".$Daten['hau_frontend']."\n"; 

if($Daten['hau_fc']==0) {$Daten['hau_fc']='Nein';} else {$Daten['hau_fc']='Ja';}
$Daten['hau_beschreibung'] .= "FC-Anschluß: ".$Daten['hau_fc']."\n"; 

if($Daten['hau_rh']==0) {$Daten['hau_rh']='RedHat 5 (64 Bit)';} else {$Daten['hau_rh']='RedHat 6 (64 Bit)';}
$Daten['hau_beschreibung'] .= "RedHat Enterprise Linux: ".$Daten['hau_rh']."\n"; 

$Daten['hau_beschreibung'] .= "Serviceklasse Produktion: ".$Daten['hau_skp']."\n";  
$Daten['hau_beschreibung'] .= "Serviceklasse TUV/DEV: ".$Daten['hau_skt']."\n";  
$Daten['hau_beschreibung'] .= "/data Filesystemgröße: ".$Daten['hau_fs']." GB\n"; 
$Daten['hau_beschreibung'] .= "\nSoftware Server:\n ".$Daten['hau_sws']."\n"; 
$Daten['hau_beschreibung'] .= "\nSonstiges: \n".$Daten['hau_sonstiges']."\n"; 

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

         if ($Daten['hau_datumstyp'] != 1)
            {

            $Daten['hau_pende']=pruefe_datum($Daten['hau_pende']);
            }
        else
            {
            $Daten['hau_pende']='9999-01-01';
            }

####################### Prüfe, ob die Aufgabe ins Backlog gehört ###################################

   

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
                '"0", ' .
                '"0", ' .
                'NOW(), ' .
                '"1", ' .
                '"0", ' .
                '"999", ' .
                '"' . $Daten['hau_datumstyp'] . '", ' .
                '"' . $Daten['hau_hprid'] . '", ' .
                '"5", ' .
                '"1", ' .
                '"0", ' .
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
                'VALUES ("' . $hau_id . '", "' . $_SESSION['hma_level']. '", "' . $_SESSION['hma_id'] . '")';

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

        // Zurueck zur Liste
   
                header('Location: schreibtisch_meine_aufgaben.php');
                exit;
                break;

    }

echo
    '<div id="bn_frame" style="position:absolute; display:none; height:198px; width:205px; background-color:#ced7d6; overflow:hidden;">';

echo
    '<iframe src="bytecal.php" style="width:208px; margin-left:-1px; border:0px; height:202px; background-color:#ced7d6; overflow:hidden;" border="0"></iframe>';

echo '</div>'; 

include('segment_fuss.php');
?>