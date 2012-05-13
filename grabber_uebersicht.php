<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

$Daten=array();

foreach ($_POST as $varname => $value)
    {
    $Daten[$varname]=$value;
    }

$zaehler=0; # Zeilenfarbe Tabelle
$anzahl_fehler=0;

echo '<br><table class="element" cellpadding = "5">';

echo '<tr>';

echo '<td class="text_mitte">';

echo '<img src="bilder/block.gif">&nbsp;Grabber-Übersicht';

echo '</td>';

echo '</tr></table>';

echo '<br><br>';

if (isset($Daten['speichern']))
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

    $trackerdatum=datum_wandeln_euus($Daten['hgr_pruefen']);

    list($anzahl_fehler, $fehlermeldung['hgr_pruefen'])=
        datum_check($Daten['hgr_pruefen'], 'hgr_pruefen', $anzahl_fehler);

    if ($anzahl_fehler > 0)
        {
        echo '<form action="grabber_uebersicht.php" method="post">';

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

        echo '</form>';
        }
    else
        {

        $sql='INSERT INTO grabber (' .
            'hgr_datum_sperre, ' .
            'hgr_ip, ' .
            'hgr_hmaid_sperren, ' .
            'hgr_grund, ' .
            'hgr_provider, ' .
            'hgr_bemerkung, ' .
            'hgr_pruefen, ' .
            'hgr_zeitstempel) ' .
            'VALUES ( ' .
            '"' . date("Y-m-d") . '", ' .
            '"' . $Daten['hgr_ip'] . '", ' .
            '"' . $_SESSION['hma_id'] . '", ' .
            '"' . mysql_real_escape_string($Daten['hgr_grund']) . '", ' .
            '"' . mysql_real_escape_string($Daten['hgr_provider']) . '", ' .
            '"' . mysql_real_escape_string($Daten['hgr_bemerkung']) . '", ' .
            '"' . datum_wandeln_euus($Daten['hgr_pruefen']) . '", ' .
            'NOW())';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        if ($Daten['hgr_pruefen']=!'')
            {

            $utr_ref=mysql_insert_id();

            $sql='INSERT INTO tracker (' .
                'utr_inhaber, ' .
                'utr_ref, ' .
                'utr_next_date, ' .
                'utr_prio, ' .
                'utr_bereich, ' .
                'utr_typ, ' .
                'utr_titel, ' .
                'utr_beschreibung, ' .
                'utr_zuordnung, ' .
                'utr_datumstyp, ' .
                'utr_wiederholung, ' .
                'utr_intervalltyp, ' .
                'utr_intervallwert, ' .
                'utr_intervalltag, ' .
                'utr_hmaid, ' .
                'utr_sid, ' .
                'utr_ticketnr, ' .
                'utr_pende_wert, ' .
                'utr_pende, ' .
                'utr_planende, ' .
                'utr_wiederholungwert, ' .
                'utr_zeitstempel) ' .
                'VALUES ( ' .
                '"' . $_SESSION['hma_id'] . '", ' .
                '"' . $utr_ref . '", ' .
                '"' . $trackerdatum . '", ' .
                '"1", ' .  # Prio = Standard
            '"1", ' .      # 1 = OD
            '"5", ' .      # 5 = Other
            '"' . mysql_real_escape_string('Grabber ' . $Daten['hgr_ip'] . ' pruefen.') . '", ' .
                '"' . mysql_real_escape_string(
                'Prüfen, ob der Grabber noch aktiv ist, IP ggf. entsperren und Liste aktualisieren.') . '", ' .
                '"1", ' .  # Drop into Pool
            '"3", ' .      # definite date
            '"2", ' .      # n-nicht endlos
            '"0", ' .      # daily
            '"1", ' .      # jeden Tag
            '"0", ' .
                '"-1", ' . # no staff
            '"1", ' .      # operation
            '"0", ' .
                '"0", ' .  # faellig nach
            '"' . $trackerdatum . '", ' .
                '"1", ' .  # nicht endlos
            '"1", ' .
                'NOW())';

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
            '"hat folgende IP gesperrt: ' . $Daten['hgr_ip'] . '")';

        if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
            {
            fehler();
            }
        }
    }

if ($anzahl_fehler == 0)
    {
    echo '<form action="grabber_uebersicht.php" method="post">';

    echo '<table>';

    echo '<tr><td>IP: </td><td><input type="text" name="hgr_ip"></td></tr>';

    echo '<tr><td>Grund der Sperre:</td><td><input type="text" name="hgr_grund"></td></tr>';

    echo '<tr><td>Provider:</td><td><input type="text" name="hgr_provider"></td></tr>';

    echo '<tr><td>Bemerkung:</td><td><textarea cols="80" rows="5" name="hgr_bemerkung"></textarea></td></tr>';

    echo '<tr><td>Prüfen am: </td><td><input type="text" name="hgr_pruefen"></td></tr>';

    echo
        '<tr><td colspan="2" align="right"><input type="submit" name="speichern" value="Sperre sichern" class="formularbutton" /></td></tr>';

    echo '</table>';

    echo '</form>';
    }

echo '<br><br><br>';

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
echo '<table style="border: solid, 1px, black;" cellspacing="1" cellpadding="3" width="1000" class="element">';

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

echo '<td>&nbsp;</td>';

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

    if ($titel == 'IP gesperrt')
        {
        echo '<td><a href="grabber_termin_aendern.php?hgr_id=' . $zeile['hgr_id']
            . '"><img src="bilder/icon_arbeit.gif" title="Prüftermin neu setzen" border="0" alt="Prüftermin neu setzen"></a></td>';

        echo '<td><a href="grabber_entsperren.php?hgr_id=' . $zeile['hgr_id'] . '&ip=' . $zeile['hgr_ip']
            . '"><img src="bilder/icon_projektwechsel.gif" title="Sperre aufheben" border="0" alt="Sperre aufheben"></a></td>';
        }
    else
        {
        echo '<td>&nbsp;</td>';

        echo '<td>&nbsp;</td>';
        }

    echo '</tr>';

    $zaehler++;
    }

echo '</table>';

include('segment_fuss.php');
?>