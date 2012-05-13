<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
error_reporting(E_ALL);

ini_set('display_errors', '1');

require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');

$hpr_id=$_GET['hpr_id'];

$sql_projekt='SELECT hpr_titel FROM projekte 
                WHERE hpr_id = ' . $hpr_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_projekt=mysql_query($sql_projekt, $verbindung))
    {
    fehler();
    }

while ($zeile_projekt=mysql_fetch_array($ergebnis_projekt))
    {
    $titel=$zeile_projekt['hpr_titel'];
    }

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Timeline Projectstasks for ' . $titel
    . '<br>&nbsp;&nbsp;&nbsp;<span class="text_klein">[<a href="uebersicht_projekt.php?hpr_id=' . $hpr_id
    . '">back to list</a>]</span><br>';

echo '<img src="seg_timeline_projectdetails.php?hpr_id=' . $hpr_id . '"/>';

echo '<br><span class="text_klein">red = overdue, green = on track, blue = task is closed</a><br>';
?>