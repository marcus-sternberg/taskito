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

if (isset($Daten['speichern']))
    {

        $sql='INSERT INTO log_kunde (' .
            'ulok_ma, ' .
            'ulok_text, ' .
            'ulok_gruppe, ' .
            'ulok_zeitstempel) ' .
            'VALUES ( ' .
            '"' . $Daten['ulok_ma'] . '", ' .
            '"' . mysql_real_escape_string($Daten['ulok_text']) . '", ' .
            '"' . $_SESSION['hma_level'] . '", ' .
            'NOW())';

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
            '"Whiteboard", ' .
            '"Edit", ' .
            '"' . $_SESSION['hma_login'] . '" ,' .
            '"hat folgenden Eintrag angelegt: ' . mysql_real_escape_string($Daten['ulok_text']) . '")';

        if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
            {
            fehler();
            }
    }

// Kommentareingabe

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Notiz hinterlegen<br><br>';

echo '<form action="kunden_logfile.php" method="post">';

echo '<table class="element" cellspacing="5" cellpadding="0" width="800">';


// ID des Schreibenden

echo '<input type="hidden" name="ulok_ma" value="' . $_SESSION['hma_id'] . '">';


// Text des Kommentars

echo '<tr>';

echo
    '<td class="text_klein" valign="top">Notiz:&nbsp;&nbsp;</td><td><textarea cols="80" rows="10" name="ulok_text"></textarea></td>';

echo '</tr>';

// Formularbutton

echo
    '<tr><td style="text-align:right; padding-top:10px;" colspan="2"><input type="submit" name="speichern" value="Notiz speichern" class="formularbutton" /></td></tr>';

echo '</table>';

echo '</form>';

echo '<br>';

// Liste Aktivitäten

$sql='SELECT * FROM log_kunde 
                  WHERE ulok_gruppe = ' . $_SESSION['hma_level'] . '  
                  ORDER BY ulok_zeitstempel DESC';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

echo '<table  class="element" class="sample" cellspacing="1" cellpadding="3" width="800">';

echo '<tr><td class="tabellen_titel">Datum</td><td class="tabellen_titel">Notiz</td><td></td><td></td><tr>';

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<tr>';

    echo '<td class="text_klein" valign="top">' . zeitstempel_anzeigen($zeile['ulok_zeitstempel']) . '</td>';

    echo '<td class="text_klein" valign="top">' . nl2br(($zeile['ulok_text'])) . '</td>';

    # if($_SESSION['hma_id']==$zeile['ulok_ma']){
    echo '<td class="text_klein" valign="top"><a href="schreibtisch_log_aendern.php?ulok_id=' . $zeile['ulok_id']
        . '"><img src="bilder/icon_aendern.gif" border="0" title="Notiz ändern" alt="Notiz ändern"></a></td>';

    echo '<td class="text_klein" valign="top"><a href="schreibtisch_log_loeschen.php?ulok_id=' . $zeile['ulok_id']
        . '" onclick="return window.confirm(\'Delete Datarecord?\');"><img src="bilder/icon_loeschen.gif" border="0" title="Notiz löschen" alt="Notiz löschen"></a></td>';

    # } else {
    #     echo '<td>&nbsp;</td>';
    #     echo '<td>&nbsp;</td>';
    # }
    echo '</tr>';
    }

echo '</table>';

// AbschlussÂ¸

echo '</td></tr></table>';
include('segment_fuss.php');
?>
