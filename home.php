<?php
###### Editnotes ####
#$LastChangedDate: 2011-11-11 09:00:33 +0100 (Fr, 11 Nov 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (!isset($_GET['auto']))
    {
    $auto='on';
    }
else
    {
    $auto=$_GET['auto'];
    }

if (!isset($_POST['ac_log']))
    {
    $_SESSION['ac_log']='Nur markierte Einträge';
    }
else
    {
    $_SESSION['ac_log']=$_POST['ac_log'];
    }    
    
if (!isset($_REQUEST['note']))
    {
    $note=' ulok_gruppe = ' . $_SESSION['hma_level'] . ' ';
    }
else
    {
    $note=' ulok_gruppe = ' . $_REQUEST['note'] . ' ';
    }

if (isset($_REQUEST['note']) AND $_REQUEST['note'] == 0)
    {
    $note=' ulok_gruppe LIKE "%%"';
    }

$jahr=date("Y");
$month=date("m");
$day=date("d");
$ergebnis_check=array();

########################  Definiere Variablen ################################

if ($auto == 'on')
    {

    $autolink='home.php?auto=off';
    $autobild='bilder/icon_refresh.png';
    $autotext='Page-Refresh: ON!';
    }
else
    {

    $autolink='home.php?auto=on';
    $autobild='bilder/icon_refresh_off.png';
    $autotext='Page-Refresh: OFF!';
    }

#####################################################################################
############################ Ausgabe Werte ##########################################

include('segment_kopf_reload_home.php');

// Gebe Überschrift aus

echo '<br><table class="matrix" cellpadding = "5">';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">';

#echo '<img src="bilder/block.gif">&nbsp;Status |';
echo 'Status |';

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

    echo ' <a href="defcon_log.php">DefCon</a></td>';

    echo '<td align="center" bgcolor="' . $color . '"><strong>' . $zeile['ude_status'] . '</strong> : ' . $status
        . '</td>';
    }

$sql='SELECT hcl_id FROM checklists WHERE hcl_datum = "' . $jahr . '-' . $month . '-' . $day . '"';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    $hcl_id=$zeile['hcl_id'];
    }

if (!isset($hcl_id))
    {
    $hcl_id=0;
    }

$sql='SELECT * FROM check_matrix WHERE hcm_hclid = "' . $hcl_id . '"';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis) == 0)
    {
    $ergebnis_check[]=0;
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    $ergebnis_check[]=$zeile['hcm_status'];
    }

asort($ergebnis_check);

foreach ($ergebnis_check AS $feld_id => $resultat)
    {
    if ($resultat == 0)
        {
        $color='#c2c2c2';
        $status='ungeprüft';
        break;
        }

    if ($resultat == 1)
        {
        $color='#EE775F';
        $status='KRITISCH';
        break;
        }

    if ($resultat == 2)
        {
        $color='#FFF8B3';
        $status='WARNUNG';
        break;
        }

    if ($resultat == 3)
        {
        $color='#C1E2A5';
        $status='OK';
        break;
        }

    if ($resultat == 4)
        {
        $color='#CED1F0';
        $status='SKIP';
        break;
        }
    }

echo '<th class="is24"><a href="checkliste_neu.php">Checklist</a></td>';

echo '<td align="center" bgcolor="' . $color . '">' . $status . '</td>';

echo '</tr>';

echo '</table>';

################## BEGINN STATUS ANZEIGEN #############################
#echo '<br>';

#echo '<br>';

# echo ' <a href="http://hamlmm01/OD/dokuwiki/doku.php">Knowledgebase</a>';

echo '<br>';

echo '<table border=0 width="1400">';

echo '<tr>';

echo '<td valign="top">';


################### Acitivty Log ######################

// Starte Tabelle

echo '<table class="ts24">';

echo '<form action="home.php" method="post">';

echo '<caption class="is24">Aktivitäts-Log&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';  

if($_SESSION['ac_log']=='Nur markierte Einträge')
{
    echo '<input type="submit" name="ac_log" value="Alle Einträge" class="formularbutton" /></th>';
} else
{
    echo '<input type="submit" name="ac_log" value="Nur markierte Einträge" class="formularbutton" /></th>';       
}
echo '</form></caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Zeit</th>';

echo '<th class="is24">Mitarbeiter</th>';

echo '<th class="is24">Aufgabe</th>';

echo '<th class="is24">Aktion</th>';

echo '</tr>';

echo '</thead>';       

if($_SESSION['ac_log']=='Nur markierte Einträge')
{
$sql=
    'SELECT ulo_datum, hau_id, hma_name, ulo_text, hau_titel, hau_abschluss FROM aufgaben 
         LEFT JOIN log ON ulo_aufgabe = hau_id 
         LEFT JOIN mitarbeiter ON ulo_ma = hma_id 
         WHERE hau_aktiv =1 AND ulo_extra = 1  
         ORDER BY ulo_datum DESC LIMIT 10';
} else
{
$sql=
    'SELECT ulo_datum, hau_id, hma_name, ulo_text, hau_titel, hau_abschluss FROM aufgaben 
         LEFT JOIN log ON ulo_aufgabe = hau_id 
         LEFT JOIN mitarbeiter ON ulo_ma = hma_id 
         WHERE hau_aktiv =1  
         ORDER BY ulo_datum DESC LIMIT 10';    
}

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$zaehler=0;

echo '<tbody class="is24">';

while ($zeile=mysql_fetch_array($ergebnis))
    {
    if (fmod($zaehler, 2) == 1 && $zaehler > 0)
        {
        $tr_stil='<tr class="is24">';
        }
    else
        {
        $tr_stil='<tr class="is24_odd">';
        }

    echo $tr_stil;

    echo '<td class="is24">' . zeitstempel_anzeigen($zeile['ulo_datum']) . '&nbsp;</td>';

    echo '<td class="is24">' . $zeile['hma_name'] . '&nbsp;</td>';

    if ($zeile['hau_id'] > 1)
        {
        echo '<td class="is24"><a href="aufgabe_ansehen.php?hau_id=' . $zeile['hau_id'] . '">' . ($zeile['hau_titel'])
            . '</a>&nbsp;</td>';
        }
    else
        {
        echo '<td class="is24">' . ($zeile['hau_titel']) . '&nbsp;</td>';
        }
    
 #   $zeile['ulo_text'] = htmlentities($zeile['ulo_text']);
 #   $zeile['ulo_text'] = str_replace("\\r\\n", "<br />", $zeile['ulo_text']);

    echo '<td class="is24">' . nl2br(htmlspecialchars($zeile['ulo_text'])) . '&nbsp;</td>';

    echo '</tr>';

    $zaehler++;
    }

echo '</tbody>';    

echo '<form action="home_log_speichern.php" method="post">';

// Datum Kommentar

echo '<tr class="is24">';

echo '<td  class="is24">Datum: </td><td class="is24" colspan="6"><input type="text" name="ulo_datum" value="'
    . date("d.m.Y H:i") . '" style="width:340px;"></td>';

echo '</tr>';

// ID ces Schreibenden

echo '<input type="hidden" name="ulo_ma" value="' . $_SESSION['hma_id'] . '">';

// Zuordnung zur Aufgabe

echo '<input type="hidden" name="ulo_aufgabe" value="1">';

// Text des Kommentars

echo '<tr class="is24">';

echo
    '<td class="is24" valign="top">Kommentar</td><td  class="is24" colspan="6"><textarea cols="80" rows="10" name="ulo_text"></textarea></td>';

echo '</tr>';

// Formularbutton 

echo '<tr class="is24">';

echo '<th class="is24" colspan="4" align="right"><input type="submit" name="speichern" value="Eintrag speichern" class="formularbutton" /></th>';

echo '</tr>';

 
echo '</form>';

echo '</table>';

################ ACTIVITY LOG ENDE #######################

#################### Performance #################################

// Wirf den Performance-Status aus:

$sql='SELECT * FROM gomez_daten WHERE ugd_datum = "' . date('Y-m-d') . '"';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

echo '<table class="is24">';

echo '<caption class="is24">';


if (mysql_num_rows($ergebnis) != 0)
    {
    while ($zeile=mysql_fetch_array($ergebnis))
        {
        echo 'GOMEZ-Performancemessung vom ' . datum_anzeigen($zeile['ugd_datum']) . '';
        }
    }
else
    {
    echo
        'GOMEZ-Performancemessung';
    }
    
echo '</caption>';  

echo '<thead class="is24">'; 

echo '<tr class="is24">';

echo '<th class="is24">Durchschnitt</th>';

echo '<th class="is24"></th>';

echo '<th class="is24">DTAG</th>';

echo '<th class="is24">COLT</th>';

echo '<th class="is24">Telefonica</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';

// Wirf den Performance-Status aus:

$sql='SELECT * FROM gomez_daten WHERE ugd_datum = "' . date('Y-m-d') . '"';


// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis) != 0)
    {
    while ($zeile=mysql_fetch_array($ergebnis))
        {
        echo '<tr class="is24">';

        if (round((($zeile['ugd_dtag'] + $zeile['ugd_colt'] + $zeile['ugd_telefonica']) / 3), 2) < 95)
            {
            $td_stil = 'is24_r';
            }
        else
            {
            $td_stil = 'is24_g';  
            }

        echo '<td class="'.$td_stil.'">'
            . round((($zeile['ugd_dtag'] + $zeile['ugd_colt'] + $zeile['ugd_telefonica']) / 3), 2) . ' %</td>';

        echo '<td class="is24">&nbsp</td>';

        if ($zeile['ugd_dtag'] < 95)
            {
            $td_stil = 'is24_r';
            }
        else
            {
            $td_stil = 'is24_g';  
            }

        echo '<td class="'.$td_stil.'"">' . $zeile['ugd_dtag'] . ' %</td>';

        if ($zeile['ugd_colt'] < 95)
            {
            $td_stil = 'is24_r';
            }
        else
            {
            $td_stil = 'is24_g';  
            }

        echo '<td class="'.$td_stil.'">' . $zeile['ugd_colt'] . ' %</td>';

        if ($zeile['ugd_telefonica'] < 95)
              {
            $td_stil = 'is24_r';
            }
        else
            {
            $td_stil = 'is24_g';  
            }

        echo '<td class="'.$td_stil.'">' . $zeile['ugd_telefonica'] . ' %</td>';

        echo '</tr>';
        }
    }
else
    {
    echo '<tr class="is24">';

    echo '<td class="is24" colspan="5">Keine Werte gefunden</td>';

    echo '</tr>';
    }
    
echo '</tbody>';

echo '</table>';

echo
    '<a href="http://chartbuilder.rz.is24.loc/js_datas/18.template" target="_blank">Messwerte in unserer Verantwortung</a>';

echo '<br><br><br>';

#################### Performance ENDE #################################

echo '</td>';

echo '<td>&nbsp;&nbsp;</td>';

echo '<td valign="top">';

######################  Kalender #####################

// Starte Tabelle

echo '<table class="is24">';

echo '<caption class="is24">';

echo 'Kalender&nbsp;&nbsp;&nbsp;&nbsp;<span class="text_klein">[<a href="uebersicht_task_timeline.php">zeige GANTT-Diagram</a>]</span>';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Aufgabe</th>';

echo '<th class="is24">fällig am</th>';

echo '<th class="is24">Dauer [d]</th>';

echo '<th class="is24">Ansprechpartner</th>';

echo '<th class="is24">Nachtschicht</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody class="is24">'; 

$sql=
    'SELECT hau_dauer, hau_id, hau_titel, hau_pende, hau_nonofficetime FROM aufgaben 
         WHERE hau_aktiv =1 AND hau_kalender = 1 AND hau_pende >= CURDATE() AND hau_abschluss = 0 
         ORDER BY hau_pende';


// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$zaehler = 0;
    
while ($zeile=mysql_fetch_array($ergebnis))
    {
        
    if (fmod($zaehler, 2) == 1 && $zaehler > 0)
        {
        $tr_stil='<tr class="is24">';
        }
    else
        {
        $tr_stil='<tr class="is24_odd">';
        }

        
    if ($zeile['hau_nonofficetime'] == 0)
        {
        $td_stil='is24';
        }
    else
        {
        $td_stil='is24_text_rot';
        }

    echo $tr_stil; 
       
    echo '<td class="is24"><a href="aufgabe_ansehen.php?hau_id=' . $zeile['hau_id'] . '">' . ($zeile['hau_titel'])
        . '&nbsp;</td>';

    echo '<td class="' . $td_stil . '">' . datum_anzeigen($zeile['hau_pende']) . '&nbsp;</td>';

    echo '<td class="' . $td_stil . '">' . $zeile['hau_dauer'] . '&nbsp;</td>';

    $sql_owner='SELECT hma_id, hma_name, hma_vorname FROM mitarbeiter
                    LEFT JOIN aufgaben_mitarbeiter ON hma_id = uau_hmaid
                    WHERE uau_hauid = ' . $zeile['hau_id'] . ' 
                    ORDER BY hma_name';

    if (!$ergebnis_owner=mysql_query($sql_owner, $verbindung))
        {
        fehler();
        }

    echo '<td class="' . $td_stil . '" nowrap>';

    while ($zeile_owner=mysql_fetch_array($ergebnis_owner))
        {
        echo $zeile_owner['hma_vorname'] . ' ' . $zeile_owner['hma_name'] . ' | ';

        $sql_menge='SELECT ulo_id,SUM(ulo_fertig) as Menge FROM log ' .
            'WHERE ulo_aufgabe = ' . $zeile['hau_id'] . ' AND ulo_ma = "' . $zeile_owner['hma_id'] . '" ' .
            'GROUP BY ulo_aufgabe';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_menge=mysql_query($sql_menge, $verbindung))
            {
            fehler();
            }

        if (mysql_num_rows($ergebnis_menge) != 0)
            {
            while ($zeile_menge=mysql_fetch_array($ergebnis_menge))
                {
                $bisheriger_aufwand=$zeile_menge['Menge'];
                }
            }
        else
            {
            $bisheriger_aufwand=0;
            }

        echo $bisheriger_aufwand . ' %<br>';
        }

    echo '</td>';

    echo '<td class="' . $td_stil . '">';

    if ($zeile['hau_nonofficetime'] == 1)
        {
        echo 'Yes';
        }
    else
        {
        echo 'No';
        }

    echo '</td>';

    echo '</tr>';
    
    $zaehler++;
    
    }

echo '</tbody>';
    
echo '</table>';

######################  Kalender ENDE #####################

######################  Changes #####################

echo '<br><br>';

// Starte Tabelle

echo '<table class="is24">';

echo '<caption class="is24">';

echo 'Freigegebene Changes (letzte 5)&nbsp;&nbsp;&nbsp;<span class="text_klein">[<a href="bericht_changes_offen.php">zeige Übersicht</a>]</span>';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Thema</th>';

echo '<th class="is24">beantragt</th>';

echo '<th class="is24">durch</th>';

echo '<th class="is24">Freigabe</th>';

echo '<th class="is24">durch</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';


$sql='SELECT urs_zeit, m1.hma_login AS freigeber, hau_id, hau_anlage, hau_titel, m2.hma_login AS beantrager FROM aufgaben 
         LEFT JOIN rollen_status ON urs_hauid = hau_id
     LEFT JOIN mitarbeiter m1 ON urs_freigabe_durch = m1.hma_id 
     LEFT JOIN mitarbeiter m2 ON hau_inhaber = m2.hma_id 
         WHERE hau_abschluss = 0 AND hau_hprid = 6 AND hau_aktiv = 1 AND urs_freigabe_ok = 1 
         ORDER BY urs_zeit DESC LIMIT 5';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$zaehler = 0;
    
while ($zeile=mysql_fetch_array($ergebnis))
    {
    
            
    if (fmod($zaehler, 2) == 1 && $zaehler > 0)
        {
        $tr_stil='<tr class="is24">';
        }
    else
        {
        $tr_stil='<tr class="is24_odd">';
        }    
        
    echo $tr_stil;

    echo '<td class="is24" colspan="5"><a href="aufgabe_ansehen.php?hau_id=' . $zeile['hau_id'] . '">' . $zeile['hau_titel'] . '</a>&nbsp;</td>';

    echo '</tr>';

    echo $tr_stil;
    
    echo '<td class="is24" nowrap>&nbsp;</td>';
    
    echo '<td class="is24" nowrap>' . zeitstempel_anzeigen($zeile['hau_anlage']) . '&nbsp;</td>';
    
    echo '<td class="is24">' . $zeile['beantrager'] . '&nbsp;</td>';

    echo '<td class="is24" nowrap>' . zeitstempel_anzeigen($zeile['urs_zeit']) . '&nbsp;</td>';
    
    echo '<td class="is24">' . $zeile['freigeber'] . '&nbsp;</td>';
    
    echo '</tr>';
    
    $zaehler++;
    }

echo '</tbody>';

echo '</table>';

#################### WHITEBOARD ENDE #############################

######################  Whiteboard #####################

echo '<br><br>';

// Starte Tabelle

echo '<table class="is24">';

echo '<caption class="is24">';

echo '<form action="home.php" method="post">'; 

echo 'Notizen&nbsp;&nbsp;&nbsp;&nbsp;';

echo '<select size="1" name="note" >';

$sql_filter='SELECT ule_id, ule_name FROM level WHERE ule_id > 1 ' .
    'ORDER BY ule_name';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_filter=mysql_query($sql_filter, $verbindung))
    {
    fehler();
    }

echo '<option value="0"><span class="text_mitte">alle</span></option>';

while ($zeile_filter=mysql_fetch_array($ergebnis_filter))
    {
    if ($_SESSION['hma_level'] == $zeile_filter['ule_id'])
        {
        echo '<option value="' . $zeile_filter['ule_id']
            . '" selected style="background-color:#E28B78;"><span class="text">' . $zeile_filter['ule_name']
            . '</span></option>';
        }
    else
        {
        echo '<option value="' . $zeile_filter['ule_id'] . '"><span class="text">' . $zeile_filter['ule_name']
            . '</span></option>';
        }
    }

echo '</select>';

echo '<span style="vertical-align:top;">&nbsp;&nbsp;<input type="submit" name="speichern" value="anzeigen" class="formularbutton" />';

echo '</form>';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Zeit</th>';

echo '<th class="is24">Mitarbeiter</th>';

echo '<th class="is24">Notiz</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';

$sql='SELECT ulok_zeitstempel, hma_name, ulok_text, ule_kurz FROM log_kunde 
         LEFT JOIN mitarbeiter ON ulok_ma = hma_id 
         LEFT JOIN level ON ule_id = hma_level 
         WHERE ' . $note . ' AND ule_id > 1
         ORDER BY ulok_zeitstempel DESC';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$zaehler = 0;
    
while ($zeile=mysql_fetch_array($ergebnis))
    {
    
            
    if (fmod($zaehler, 2) == 1 && $zaehler > 0)
        {
        $tr_stil='<tr class="is24">';
        }
    else
        {
        $tr_stil='<tr class="is24_odd">';
        }    
        
    echo $tr_stil;

    echo '<td class="is24" nowrap>' . zeitstempel_anzeigen($zeile['ulok_zeitstempel']) . '&nbsp;</td>';

    echo '<td class="is24">' . $zeile['hma_name'] . ' (' . $zeile['ule_kurz'] . ')&nbsp;</td>';

    echo '<td class="is24">' . $zeile['ulok_text'] . '&nbsp;</td>';

    echo '</tr>';
    
    $zaehler++;
    }

echo '</tbody>';

echo '</table>';

#################### WHITEBOARD ENDE #############################

echo '<br>';


# Ende Haupttabelle

echo '</td>';

echo '</tr>';

echo '</table>';

include('segment_fuss.php');
?>
