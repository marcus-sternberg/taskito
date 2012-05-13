<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

unset($_SESSION['suchstring']);
$_SESSION['suchstring']='';
$_SESSION['neu_gesetzt']=0;

//if (!isset($_POST['suchen']))
//{
require_once('segment_kopf.php');

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Suche in Aufgaben<br><br>';

echo '<form action="archiv_suche.php" method="post">';

echo '<table border="0" cellspacing="5" cellpadding="0">';

echo '<tr>';

echo '<td class="text_klein">Ticketnummer: </td><td><input type="text" name="hau_id" style="width:60px;"></td>';

echo '</tr>';

echo '<tr>';

echo '<td class="text_klein">Referenznummer: </td><td><input type="text" name="hau_ticketnr" style="width:60px;"></td>';

echo '</tr>';

echo '<tr>';

echo '<td class="text_klein">Projekt: </td><td>';

echo '<select size="1" name="hau_hprid">';
$sql='SELECT hpr_id, hpr_titel FROM projekte 
            WHERE hpr_aktiv="1"  AND hpr_id > 6 AND hpr_fertig = 0 ' .
    'ORDER BY hpr_sort, hpr_titel';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

echo '<option value="0"><span class="text">alle</span></option>';

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<option value="' . $zeile['hpr_id'] . '"><span class="text">' . $zeile['hpr_titel'] . '</span></option>';
    }

echo '</select>';

echo '</td></tr>';

echo '<tr>';

echo '<td class="text_klein">Freitextsuche: </td><td><input type="text" name="suchbegriff" style="width:340px;"></td>';

echo '</tr>';

echo '<tr>';

echo '<td class="text_klein">Typ: </td><td>';

echo '<select size="1" name="hau_typ">';
$sql='SELECT uty_id, uty_name FROM typ ' .
    'ORDER BY uty_name';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

echo '<option value="0"><span class="text">all</span></option>';

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<option value="' . $zeile['uty_id'] . '"><span class="text">' . $zeile['uty_name'] . '</span></option>';
    }

echo '</select>';

echo '</td></tr>';

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

echo '<option value="0"><span class="text">alle</span></option>';

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<option value="' . $zeile['upr_nummer'] . '"><span class="text">' . $zeile['upr_name'] . '</span></option>';
    }

echo '</select>';

echo '</td></tr>';

echo '<tr>';

echo '<td class="text_klein">Bearbeiter: </td><td>';

echo '<select size="1" name="uau_hmaid">';

echo '<option><span class="text"></span></option>';

$sql='SELECT hma_id, hma_name, hma_vorname FROM mitarbeiter WHERE hma_level > 1 AND hma_level < 99 AND hma_aktiv = 1 ' .
    'ORDER BY hma_name';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<option value="' . $zeile['hma_id'] . '"><span class="text">' . $zeile['hma_name'] . ', '
        . $zeile['hma_vorname'] . '</span></option>';
    }

echo '</select>';

echo '</td></tr>';

echo
    '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="suchen" value="Suche" class="formularbutton" /></td></tr>';

echo '</table>';

echo '</form>';
?>