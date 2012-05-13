<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';

echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">';

echo '<html>';

echo '<head>';

echo '<title>TOM - Task Organisation Management</title>';

echo '<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">';

echo '<link rel="stylesheet" type="text/css" href="css/tom.css">';

echo '<link rel="shortcut icon" href="tom.ico" type="image/x-icon">';

echo '<link rel="icon" href="tom.ico" type="image/x-icon">';

echo '</head>';

if (isset($_GET['hck_id']))
    {
    $hck_id=$_GET['hck_id'];
    }

$sql='SELECT * FROM checks  
             WHERE hck_id = ' . $hck_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Infos Check<br><br>';

    echo '<table border="0" cellspacing="5" cellpadding="0" class="element">';

    echo '<tr>';

    echo '<td class="text_klein">Name: </td><td>' . ($zeile['hck_name']) . '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein">Beschreibung: </td><td>' . ($zeile['hck_beschreibung']) . '</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein">Ziel: </td><td>' . ($zeile['hck_ziel']) . '</td>';

    echo '</tr>';

    echo '</table>';
    }
?>