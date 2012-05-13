<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$Daten=array();

foreach ($_POST as $varname => $value)
    {
    $Daten[$varname]=$value;
    }

if (isset($_REQUEST['hgr_id']))
    {
    $grabber_id=$_REQUEST['hgr_id'];
    }

$anzahl_fehler=0;

if (!isset($Daten['speichern']))
    {
    include('segment_kopf.php');

    echo '<br><table class="element" cellpadding = "5">';

    echo '<tr>';

    echo '<td class="text_mitte">';

    echo '<img src="bilder/block.gif">&nbsp;Grabber Termin ändern';

    echo '</td>';

    echo '</tr></table>';

    echo '<br><br>';

    $sql='SELECT * FROM grabber WHERE hgr_id = ' . $grabber_id;

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        echo '<form action="grabber_termin_aendern.php" method="post">';

        echo '<table>';

        echo '<tr><td>IP: </td><td><input type="text" name="hgr_ip" value="' . $zeile['hgr_ip'] . '"></td></tr>';

        echo '<tr><td>Grund der Sperre:</td><td><input type="text" name="hgr_grund" value="'
            . mysql_real_escape_string($zeile['hgr_grund']) . '"></td></tr>';

        echo '<tr><td>Provider:</td><td><input type="text" name="hgr_provider" value="'
            . mysql_real_escape_string($zeile['hgr_provider']) . '"></td></tr>';

        echo '<tr><td>Bemerkung:</td><td><textarea cols="80" rows="5" name="hgr_bemerkung">'
            . mysql_real_escape_string($zeile['hgr_bemerkung']) . '</textarea></td></tr>';

        echo '<tr><td>Prüfen am: </td><td><input type="text" name="hgr_pruefen" value="'
            . datum_wandeln_useu($zeile['hgr_pruefen']) . '"></td></tr>';

        echo
            '<tr><td colspan="2" align="right"><input type="submit" name="speichern" value="Sperre sichern" class="formularbutton" /></td></tr>';

        echo '</table>';

        echo '<input type="hidden" value="' . $grabber_id . '" name="hgr_id">';

        echo '</form>';
        }
    }
else
    {
    if ($Daten['hgr_grund'] == '')
        {
        $anzahl_fehler++;

        $fehlermeldung['hgr_grund']='Bitte geben Sie einen Grund für die Sperre an.';
        }
    else
        {
        $fehlermeldung['hau_titel']='';
        }

    list($anzahl_fehler, $fehlermeldung['hgr_pruefen'])=
        datum_check($Daten['hgr_pruefen'], 'hgr_pruefen', $anzahl_fehler);

    if ($anzahl_fehler > 0)
        {
        echo '<form action="grabber_termin_aendern.php" method="post">';

        echo '<table>';

        echo '<tr><td>IP: </td><td><input type="text" name="hgr_ip" value="' . $Daten['hgr_ip'] . '"></td></tr>';

        echo '<tr><td colspan="2" class="text_rot">&nbsp;&nbsp;' . $fehlermeldung['hgr_grund'] . '</td></tr>';

        echo '<tr><td>Grund der Sperre:</td><td><input type="text" name="hgr_grund" value="' . $Daten['hgr_grund']
            . '"></td></tr>';

        echo '<tr><td>Provider:</td><td><input type="text" name="hgr_provider" value="' . $Daten['hgr_provider']
            . '"></td></tr>';

        echo '<tr><td>Bemerkung:</td><td><textarea cols="80" rows="5" name="hgr_bemerkung">' . $Daten['hgr_bemerkung']
            . '</textarea></td></tr>';

        echo '<tr><td colspan="2" class="text_rot">&nbsp;&nbsp;' . $fehlermeldung['hgr_pruefen'] . '</td></tr>';

        echo '<tr><td>Prüfen am: </td><td><input type="text" name="hgr_pruefen" value="' . $Daten['hgr_pruefen']
            . '"></td></tr>';

        echo
            '<tr><td colspan="2" align="right"><input type="submit" name="speichern" value="Sperre sichern" class="formularbutton" /></td></tr>';

        echo '</table>';

        echo '<input type="hidden" value="' . $grabber_id . '" name="hgr_id">';

        echo '</form>';
        }
    else
        {

        $sql='UPDATE grabber SET 
                hgr_ip = "' . $Daten['hgr_ip'] . '",  
                hgr_grund = "' . mysql_real_escape_string($Daten['hgr_grund']) . '", 
                hgr_provider = "' . mysql_real_escape_string($Daten['hgr_provider']) . '",
                hgr_bemerkung = "' . mysql_real_escape_string($Daten['hgr_bemerkung']) . '" ,
                hgr_pruefen = "' . datum_wandeln_euus($Daten['hgr_pruefen']) . '"  
                WHERE hgr_id = ' . $grabber_id;

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        if ($Daten['hgr_pruefen'] != '')
            {

            $sql='UPDATE tracker SET utr_next_date = "' . datum_wandeln_euus($Daten['hgr_pruefen']) . '" 
             WHERE utr_ref = "' . $grabber_id . '"';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }
            }

        $sql_log='INSERT INTO eventlog (' .
            'hel_area, ' .
            'hel_type, ' .
            'hel_referer, ' .
            'hel_text) ' .
            'VALUES ( ' .
            '"Grabberlist", ' .
            '"Edit", ' .
            '"' . $_SESSION['hma_login'] . '" ,' .
            '"hat fuer folgende IP ' . $Daten['hgr_ip'] . ' das Pruefdatum geandert.")';

        if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
            {
            fehler();
            }


        // Zurueck zur Liste

        header('Location: grabber_uebersicht.php');
        exit;
        }
    }
?>