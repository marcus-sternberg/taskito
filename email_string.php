<?php
###### Editnotes ####
#$LastChangedDate: 2011-10-12 18:32:16 +0200 (Mi, 12 Okt 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

$block_mails=array();

echo '<br><table class="element" cellpadding = "5">';

echo '<tr>';

echo '<td class="text_mitte">';

echo '<img src="bilder/block.gif">&nbsp;Fraud-String ausgeben';

echo '</td>';

echo '</tr></table>';

echo '<br><br>';

$sql='SELECT * FROM spam_block';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }


// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {
    $block_mails[]=$zeile['usb_email'];
    }

foreach ($block_mails AS $mail)
    {
    $fraud_string.=trim($mail) . '|';
    }

$fraud_string=substr($fraud_string, 0, (strlen($fraud_string) - 1)); 
$fraud_string_prefix='email.fraud.content.pattern='.$fraud_string;       
    

//echo '<table bgcolor="#c2c2c2" width="600" border=1><tr><td width="600">';

//$fraud_string = wordwrap( $fraud_string, 200,"\n", true );
echo '<br> <br>';

echo 'Ohne Prefix<br><br>';
echo $fraud_string;

//echo '</td></tr></table>';

echo '<br><br><a href="email_block_uebersicht.php">zurück zur Liste</a>';

include('segment_fuss.php');
?>
