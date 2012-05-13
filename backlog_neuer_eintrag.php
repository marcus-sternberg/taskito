<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
# Integriere Module

require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (!isset($_POST['speichern']))
    {
    require_once('segment_kopf.php');

    echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Erstelle Backlogeintrag<br><br>';

    echo '<form action="backlog_neuer_eintrag.php" method="post">';

    echo '<table border="0" cellspacing="5" cellpadding="0">';

    echo '<tr>';

    echo '<td class="text_klein">Titel: </td><td><input type="text" name="hba_titel" style="width:340px;"></td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein">Projekt: </td><td>';

    echo '<select size="1" name="hba_hprid">';

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

    echo '<td class="text_klein">Mitarbeiter: </td><td>';

    echo '<select size="1" name="hba_hmaid">';
    $sql='SELECT hma_id, hma_login FROM mitarbeiter ' .
        'WHERE hma_level > 1 AND hma_level < 99 AND hma_aktiv = 1 ORDER BY hma_login';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        if ($zeile['hma_id'] == $_SESSION['hma_id'])
            {
            echo '<option value="' . $zeile['hma_id'] . '" selected><span class="text">' . $zeile['hma_login']
                . '</span></option>';
            }
        else
            {
            echo '<option value="' . $zeile['hma_id'] . '"><span class="text">' . $zeile['hma_login']
                . '</span></option>';
            }
        }

    echo '</select>';

    echo '</td></tr>';
    
    echo '<tr>';

    echo '<td class="text_klein">Priorität: </td><td>';

    echo '<select size="1" name="hba_prio">';
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

    

    echo
        '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Speichern" class="formularbutton" />';

    echo '</td></tr>';

  
    echo '</table>';

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

$sql = 'SELECT hma_level FROM mitarbeiter WHERE hma_id = '.$Daten['hba_hmaid'];

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
            $xGruppe = $zeile['hma_level'];
        }
                
    if($Daten['hba_hprid']<10) {$Daten['hba_hprid']=1;} // Setze alle internen Projekttypen auf Tagesgeschäft 
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
                '"' . mysql_real_escape_string($Daten['hba_titel']) . '", ' .
                '"' . $Daten['hba_hprid'] . '", ' .
                '"' . $Daten['hba_prio'] . '", ' .
                '"' . $xGruppe . '", ' .
                '"' . $Daten['hba_hmaid'] . '", ' .
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
    
    // Zurueck zur Liste

header('Location: backlog_liste.php?xGruppe='.$xGruppe.'&xProjekt='.$Daten['hba_hprid']);
exit;
    
    }

include('segment_fuss.php');
?>