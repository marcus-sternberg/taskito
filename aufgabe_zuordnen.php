<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');

$task_id=$_GET['hau_id'];

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Aufgabe zuordnen<br><br>';

echo '<table border=0>';

echo '<tr><td valign="top">';

include('segment_aufgabe_anzeigen.php');

echo '<table>';

echo '<form action="aufgabe_zuordnen_sichern.php" method="post">';

echo '<input type="hidden" name="hau_id" value="' . $task_id . '">';

echo '<tr><td colspan="2" class="text_klein"><br>Falls nötig, bitte das Enddatum anpassen&nbsp;<br>';

if ($hau_pende > date("Y-m-d"))
    {
    echo 'Fällig zum: <input type="text" name="hau_pende" value ="' . datum_anzeigen($hau_pende)
        . '" style="background-color: #C1E2A5; color: #000000;"></td></tr>';
    }
else
    {
    echo 'Fällig zum: <input type="text" name="hau_pende" value ="' . datum_anzeigen($hau_pende)
        . '" style="background-color: #FFBFA0; color: #000000;"></td></tr>';
    }

echo '<tr><td colspan="2" class="text_klein">Dauer:&nbsp;<input type="text" name="hau_dauer" value ="' . $hau_dauer
    . '" style="width:30px";>&nbsp;day(s)</td></tr>';

echo
    '<tr><td colspan="2" class="text_klein"><br>Hier kann zusätzliche Info für den Bearbeiter eingegeben werden:</td></tr>';

echo '<tr><td colspan="2"><textarea cols="50" rows="5" name="hau_tl_info"></textarea></td></tr>';

echo
    '<tr><td colspan="2" class="text_klein"><br>Falls die Aufgabe abgelehnt wird, bitte eine kurze Begründung eingeben:</td></tr>';

echo '<tr><td colspan="2"><textarea cols="50" rows="5" name="uko_kommentar"></textarea></td></tr>';

echo
    '<tr><td colspan="2" style="text-align:center; padding-top:10px;"><input type="submit" name="deleg" value="Zuweisen" class="formularbutton" />&nbsp;&nbsp;<input type="submit" name="deleg" value="Ablehnen" class="formularbutton" /></td></tr>';

echo '</table>';

echo '</td><td valign="top">';

echo '<table>';

echo '<tr>';

echo '<td class="text_klein" valign="top" colspan="2">Bitte entweder eine Gruppe wählen: <br>';

echo '<select size="1" name="uau_gruppe" style="width:140px;">';
$sql='SELECT ule_id, ule_name FROM level WHERE ule_id > 1 ' .
    'ORDER BY ule_sort';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

echo '<option value="0">none</option>';

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<option value="' . $zeile['ule_id'] . '"><span class="text">' . $zeile['ule_name'] . '</span></option>';
    }

echo '</select>';

echo '</td></tr>';

echo '<tr>';

echo '<td class="text_klein" valign="top" colspan="2"><br>oder einen oder mehrere Mitarbeiter: <br>';

$sql='SELECT * FROM mitarbeiter ' .
    'INNER JOIN level ON ule_id = hma_level ' .
    'WHERE  ule_id > 1 AND ule_id <99  AND ule_aktiv = 1 AND hma_aktiv = 1 ' .
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

    echo
        '<table border=0 cellspacing="0" cellpadding="0"><tr><td width="150"><input type="checkbox" name="uau_ma_id[]" value="'
        . $zeile['hma_id'] . '">&nbsp;' . $zeile['hma_name'] . ', ' . $zeile['hma_vorname'] . '</td><td width="100">['
        . $zeile['hma_login'] . ']</td><td  width="150" nowrap><a href="uebersicht_ressource_ma.php?hma_id='
        . $zeile['hma_id'] . '" target="_blank">Zeige Arbeitsauslastung</a></td></tr></table>';
    $Gruppenname=$zeile['ule_name'];
    }

echo '</td></tr>';

echo '</table>';

echo '</form>';

echo '</td></tr></table>';
?>
