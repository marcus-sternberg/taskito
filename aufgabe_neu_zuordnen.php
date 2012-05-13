<?php
###### Editnotes ####
#$LastChangedDate: 2012-01-23 08:27:43 +0100 (Mo, 23 Jan 2012) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');

$task_id=$_GET['hau_id'];
$alte_zuordnung=array();

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Aufgabe neu zuordnen<br><br>';

echo '<table border=0>';

echo '<tr><td valign="top">';

include('segment_aufgabe_anzeigen.php');

echo '<table>';

echo '<form action="aufgabe_neuzuordnen_sichern.php" method="post">';

echo '<input type="hidden" name="hau_id" value="' . $task_id . '">';

echo
    '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="deleg" value="Zuordnen" class="formularbutton" /></td></tr>';

echo '</table>';

echo '</td><td valign="top">';

# Ermittle alle Mitarbeiter, die der Aufgabe zugeordnet sind

$sql_alt='SELECT uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $task_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql_alt, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    $alte_zuordnung[]=$zeile['uau_hmaid'];
    }

echo '<table>';

echo '<tr>';

echo '<td class="text_klein" valign="top" colspan="2">Bitte weisen Sie die Bearbeiter neu zu: <br>';

$sql='SELECT * FROM mitarbeiter ' .
    'INNER JOIN level ON ule_id = hma_level ' .
    'WHERE hma_level > 1 AND hma_level < 99 AND hma_aktiv = 1 ' .
    'ORDER BY ule_sort, hma_name';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$Gruppenname='';


// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {
    if ($Gruppenname != $zeile['ule_name'])
        {
        echo '<hr>';

        echo '<span class="text_mitte">' . $zeile['ule_name'] . '</span>';

        echo '<hr>';
        }

    if (in_array($zeile['hma_id'], $alte_zuordnung))
        {
        echo '<input type="checkbox" name="uau_ma_id[]" value="' . $zeile['hma_id']
            . '" checked><span style="color: red;">&nbsp;' . $zeile['hma_name'] . ', ' . $zeile['hma_vorname']
            . '&nbsp;&nbsp;[' . $zeile['hma_login'] . ']</span><br>';
        }
    else
        {
        echo '<input type="checkbox" name="uau_ma_id[]" value="' . $zeile['hma_id'] . '">&nbsp;' . $zeile['hma_name']
            . ', ' . $zeile['hma_vorname'] . '&nbsp;&nbsp;[' . $zeile['hma_login'] . ']<br>';
        }

    $Gruppenname=$zeile['ule_name'];
    }

echo '</td></tr>';

echo '</table>';

echo '</form>';

echo '</td></tr></table>';
?>