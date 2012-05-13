<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$session_frei = 1;
include('segment_session_pruefung.php');
require_once('konfiguration.php');
   include('segment_kopf.php');  

$zaehler=0; # Zeilenfarbe Tabelle

echo '<br><br>';

echo '<table border=0 width=300>';

echo '<tr>';

echo '<td valign="top"></td>';

echo '<td>&nbsp;&nbsp;</td>';

echo '<td>';

echo '<span class="box">Die folgenden Grabber-IPs sind erfasst (aktuellster Eintrag oben):</span>';

echo '<br><br>';

$sql=
    'SELECT *, m1.hma_login AS sperrer, m2.hma_login AS entsperrer FROM grabber
        LEFT JOIN mitarbeiter m1 ON hgr_hmaid_sperren = m1.hma_id
        LEFT JOIN mitarbeiter m2 ON hgr_hmaid_entsperren = m2.hma_id
         ORDER BY hgr_datum_sperre DESC';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }


// Beginne mit Tabellenausgabe
echo '<table style="border: solid, 1px, black;" cellspacing="1" cellpadding="3" width="900" class="element">';

echo '<tr>';

echo '<tr>';

echo '<td>&nbsp;</td>';

echo '<td>IP</td>';

echo '<td>Grund</td>';

echo '<td>Provider</td>';

echo '<td>Bemerkung</td>';

echo '<td>Datum der Sperre</td>';

echo '<td>durch</td>';

echo '<td>Prüfen</td>';

echo '<td>entsperrt</td>';

echo '<td>durch</td>';

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

    if ($zeile['hgr_datum_entsperrt'] != '0000-00-00')
        {
        $zeile['hgr_datum_entsperrt']=datum_wandeln_useu($zeile['hgr_datum_entsperrt']);
        $sperr_icon='icon_erledigt.gif';
        $titel='IP entsperrt';
        }
    else
        {
        $zeile['hgr_datum_entsperrt']='';
        $sperr_icon='icon_zurueck.gif';
        $titel='IP gesperrt';
        }

    if ($zeile['hgr_pruefen'] != '0000-00-00')
        {
        $pruefdatum=datum_wandeln_useu($zeile['hgr_pruefen']);
        }
    else
        {
        $pruefdatum='';
        }

    echo '<td><img src="bilder/' . $sperr_icon . '" title="' . $titel . '" border="0" alt="' . $titel . '"></a></td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . $zeile['hgr_ip'] . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . ($zeile['hgr_grund']) . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . ($zeile['hgr_provider'])
        . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . ($zeile['hgr_bemerkung'])
        . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">'
        . datum_wandeln_useu($zeile['hgr_datum_sperre']) . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . ($zeile['sperrer']) . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . $pruefdatum . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">'
        . datum_wandeln_useu($zeile['hgr_datum_entsperrt']) . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . ($zeile['entsperrer']) . '</td>';

    echo '</tr>';

    $zaehler++;
    }

echo '</table>';

include('segment_fuss.php');
?>