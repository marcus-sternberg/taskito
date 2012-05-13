<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

// Regulaerer Ausdruck fuer den eMail-Check

//$email_match = '/^([_a-zA-Z0-9-]+)(\.[a-zA-Z0-9_-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,8})$/';
#$email_match='/^([_a-zA-Z0-9-]+)(\.[a-zA-Z0-9_-]*)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,8})$/';

$zaehler=0; # Zeilenfarbe Tabelle
$anzahl_fehler=0;
$fehlermeldung['usb_email']='';

$Daten=array();

foreach ($_POST as $varname => $value)
    {
    $Daten[$varname]=$value;
    }

echo '<br><table class="element" cellpadding = "5">';

echo '<tr>';

echo '<td class="text_mitte">';

echo '<img src="bilder/block.gif">&nbsp;geblockte eMail-Adressen';

echo '</td>';

echo '</tr></table>';

echo '<br><br>';

if (isset($Daten['speichern']))
    {

    $Daten['usb_email']=trim($Daten['usb_email']);

    if ($Daten['usb_email'] == '')
        {
        $anzahl_fehler++;
        $fehlermeldung['usb_email']='Bitte geben Sie eine eMail ein.';
        }
        /* wird nicht benötigt, da auch inbvalide Mails geblockt werden sollen
    else if ((!preg_match($email_match, $Daten['usb_email'])))
        {
        $anzahl_fehler++;
        $fehlermeldung['usb_email']='Keine gültige eMail-Adresse.';
        } */
    else
        {

        $sql_check='SELECT usb_email FROM spam_block
                    WHERE usb_email = "' . $Daten['usb_email'] . '"';

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_check=mysql_query($sql_check, $verbindung))
            {
            fehler();
            }

        if (mysql_num_rows($ergebnis_check) > 0)
            {
            $anzahl_fehler++;
            $fehlermeldung['usb_email']='Diese Mailadresse ist bereits in der Datenbank enthalten.';
            }
        }

    if ($anzahl_fehler > 0)
        {
        echo '<form action="email_block_uebersicht.php" method="post">';

        echo '<table>';

        echo '<tr><td colspan="2" class="text_rot">&nbsp;&nbsp;' . $fehlermeldung['usb_email'] . '</td></tr>';

        echo '<tr><td>eMail: </td><td><input type="text" name="usb_email" value="' . $Daten['usb_email']
            . '" style="width:550px;"></td></tr>';

        echo
            '<tr><td colspan="2" align="right"><input type="submit" name="speichern" value="eMail blocken" class="formularbutton" /></td></tr>';

        echo '</table>';

        echo '</form>';
        }
    else
        {

        $sql='INSERT INTO spam_block (' .
            'usb_email, ' .
            'usb_zeitstempel, ' .
            'usb_hmaid )' .
            'VALUES ( ' .
            '"' . $Daten['usb_email'] . '", ' .
            'NOW(), ' .
            '"' . $_SESSION['hma_id'] . '")';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        $sql_log='INSERT INTO eventlog (' .
            'hel_area, ' .
            'hel_type, ' .
            'hel_referer, ' .
            'hel_text) ' .
            'VALUES ( ' .
            '"eMailblock", ' .
            '"Edit", ' .
            '"' . $_SESSION['hma_login'] . '" ,' .
            '"hat folgende eMail gesperrt: ' . $Daten['usb_email'] . '")';

        if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
            {
            fehler();
            }
        }
    }

if ($anzahl_fehler == 0)
    {
    echo '<form action="email_block_uebersicht.php" method="post">';

    echo '<table>';

    echo '<tr><td>eMail: </td><td><input type="text" name="usb_email" style="width:550px;"></td></tr>';

    echo
        '<tr><td colspan="2" align="right"><input type="submit" name="speichern" value="eMail blocken" class="formularbutton" /></td></tr>';

    echo '</table>';

    echo '</form>';
    }

echo '<br>';

echo '<table border=0 width=300>';

echo '<tr>';

echo '<td valign="top"></td>';

echo '<td>&nbsp;&nbsp;</td>';

echo '<td>';

echo
    '<span class="box">Die folgenden eMails sind als geblockt gespeichert (aktuellster Eintrag oben):</span><br><br><a href="email_string.php">String ausgeben</a>';

$sql='SELECT * FROM spam_block
        LEFT JOIN mitarbeiter ON usb_hmaid = hma_id
        ORDER BY usb_zeitstempel DESC';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }


// Beginne mit Tabellenausgabe
echo '<table style="border: solid, 1px, black;" cellspacing="1" cellpadding="3" width="600" class="element">';

echo '<tr>';

echo '<tr>';

echo '<td>eMail</td>';

echo '<td>gesperrt am</td>';

echo '<td>von</td>';

echo '<td>&nbsp;</td>';

echo '<tr>';

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {
    if (fmod($zaehler, 2) == 1 && $zaehler > 0)
        {
        $hintergrundfarbe='#ffffff';
        }
    else
        {
        $hintergrundfarbe='#CED1F0';
        }

    // Beginne Datenausgabe
    echo '<tr>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . $zeile['usb_email'] . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">'
        . substr($zeile['usb_zeitstempel'], 0, 10) . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . $zeile['hma_login'] . '</td>';

    if (date("Y-m-d") == substr($zeile['usb_zeitstempel'], 0, 10))
        {
        echo '<td><a href="email_block_loeschen.php?usb_id=' . $zeile['usb_id'] . '&m=' . $zeile['usb_email']
            . '"><img src="bilder/icon_loeschen.gif" title="Mailadresse aus Liste entfernen" border="0" alt="Mailadresse aus Liste entfernen"></a></td>';
        }
    else
        {
        echo '<td>&nbsp;</td>';
        }

    echo '</tr>';

    $zaehler++;
    }

echo '</table>';

echo '<br><a href="email_string.php">String ausgeben</a>';

include('segment_fuss.php');
?>