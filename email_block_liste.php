<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$session_frei = 1;
require_once('konfiguration.php');
include('segment_session_pruefung.php'); 
include('segment_kopf.php'); 

$zaehler=0; # Zeilenfarbe Tabelle

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
        . substr(datum_wandeln_useu($zeile['usb_zeitstempel']), 0, 10) . '</td>';

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

include('segment_fuss.php');
?>
