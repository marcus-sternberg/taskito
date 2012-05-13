<?php
$session_frei = 1;
require_once('konfiguration.php');


$sql='SELECT ude_status, ude_zeitstempel FROM defcon
         ORDER BY ude_zeitstempel DESC LIMIT 1';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo 'DEFCON ';
    echo $zeile['ude_status'];
    }

$sql='SELECT * FROM system_plattformen ORDER BY hpl_id';
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '#Preview';
    echo $zeile['hpl_id'];
    echo ' ';
    if ($zeile['hpl_status'] == 1)
      echo 'UP ';
    else
      echo 'DOWN ';
    echo 'Version ';
    echo $zeile['hpl_version'];
    }


?>
