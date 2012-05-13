<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
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
    
echo
    '<a href="http://berffs01.be.ber.is24.loc:8182/jmx-console/HtmlAdaptor">http://berffs01.be.ber.is24.loc:8182/jmx-console/HtmlAdaptor</a>
    <form method="post" action="http://berffs01.be.ber.is24.loc:8182/jmx-console/HtmlAdaptor">
      <input type="hidden" name="action" value="updateAttributes">
      <input type="hidden" name="name" value="de.is24:service=Mailer">
       <input type="hidden" name="FraudContentPattern" value="'.$fraud_string.'" >
       <input type="submit" value="Apply Changes"> <i>click, to update JMX settings and use backbutton to return</i>
    </form>';

echo '<br/>';

echo
    '<a href="http://berffs02.be.ber.is24.loc:8182/jmx-console/HtmlAdaptor">http://berffs02.be.ber.is24.loc:8182/jmx-console/HtmlAdaptor</a>
    <form method="post" action="http://berffs02.be.ber.is24.loc:8182/jmx-console/HtmlAdaptor">
      <input type="hidden" name="action" value="updateAttributes">
      <input type="hidden" name="name" value="de.is24:service=Mailer">
       <input type="hidden" name="FraudContentPattern" value="'.$fraud_string.'" >
       <input type="submit" value="Apply Changes">
    </form>';
    

echo '<br/>';

echo
    '<a href="http://hamffs01.be.ham.is24.loc:8182/jmx-console/HtmlAdaptor">http://hamffs01.be.ham.is24.loc:8182/jmx-console/HtmlAdaptor</a>
    <form method="post" action="http://hamffs01.be.ham.is24.loc:8182/jmx-console/HtmlAdaptor">
      <input type="hidden" name="action" value="updateAttributes">
      <input type="hidden" name="name" value="de.is24:service=Mailer">
       <input type="hidden" name="FraudContentPattern" value="'.$fraud_string.'" >
       <input type="submit" value="Apply Changes">
    </form>';
    

echo '<br/>';

echo
    '<a href="http://hamffs02.be.ham.is24.loc:8182/jmx-console/HtmlAdaptor">http://hamffs02.be.ham.is24.loc:8182/jmx-console/HtmlAdaptor</a>
    <form method="post" action="http://hamffs02.be.ber.is24.loc:8182/jmx-console/HtmlAdaptor">
      <input type="hidden" name="action" value="updateAttributes">
      <input type="hidden" name="name" value="de.is24:service=Mailer">
       <input type="hidden" name="FraudContentPattern" value="'.$fraud_string.'" >
       <input type="submit" value="Apply Changes">
    </form>';
    

echo '<br/>';


//echo '<table bgcolor="#c2c2c2" width="600" border=1><tr><td width="600">';

//$fraud_string = wordwrap( $fraud_string, 200,"\n", true );
echo '<br> <br>';

echo 'Ohne Prefix<br><br>';
echo $fraud_string;
echo '<br> <br>';
echo 'Mit Prefix<br><br>';  
echo $fraud_string_prefix;
echo '<br> <br>';

//echo '</td></tr></table>';

echo '<br><br><a href="email_block_uebersicht.php">zurück zur Liste</a>';

include('segment_fuss.php');
?>