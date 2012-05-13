<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$session_frei = 1;     
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_init.php');

if (!isset($_POST['speichern']))
    {

    include('segment_kopf.php');

    $xSystem=$_GET['xSystem'];

    echo '<br><br><br>';

    echo '<span class="box">Abonnement Plattformstatus</span><br><br>';

    echo '<form action="mail_add_status.php" method="post">';

    echo '<table border="0" width="500">';

    echo '<tr>';

    echo '</td>';

    echo '<td valign="top">Bitte an diese Mailadresse Updates zum Status senden:</td></tr>';

    echo '<tr><td>';

    echo '<input type="text" name="hsm_mail" style="width:500px;">';

    echo '</td></tr>';

    echo
        '<tr><td colspan="4" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Update abonnieren" class="formularbutton" /></td></tr>';

    echo '<input type="hidden" name="xSystem" value="' . $xSystem . '">';

    echo '</table>';

    echo '</form>';
    }
else
    {

    $Daten=array();

    foreach ($_POST as $varname => $value)
        {
        $Daten[$varname]=$value;
        }

    $sql='INSERT INTO system_mail (hsm_mail) VALUES ("' . $Daten['hsm_mail'] . '")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    include('segment_kopf.php');  
        
    echo '<br>';

    echo '<img src="bilder/block.gif">&nbsp;Die eMail wurde für Statusbenachrichtigung gespeichert.</td>';

    echo '<meta http-equiv="refresh" content="1;url=status_plattform.php?xSystem=' . $Daten['xSystem']. '">';
    exit;
    break;

    }

include('segment_fuss.php');
?>