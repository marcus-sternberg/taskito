<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (!isset($_POST['speichern']))
    {

    include('segment_kopf.php');

    $task_id=$_GET['ticket'];

    echo '<br><br><br>';

    echo '<span class="box">Hinzufügen einer Mailadresse zum Ticket:</span><br><br>';

    echo '<form action="mail_add.php" method="post">';

    echo '<table border=0 width="600">';

    echo '<tr>';

    echo '<td>&nbsp;&nbsp;';

    echo '</td>';

    echo '<td valign="top">Bitte die neue Mailadresse eingeben</td>';

    echo '<td>&nbsp;&nbsp;</td>';

    echo '<td>';

    echo '<input type="text" name="uti_mail" style="width:300px;">';

    echo '</td></tr>';

    echo '<tr>';

    echo '<td>&nbsp;&nbsp;';

    echo '</td>';

    echo '<td valign="top">Bitte Versandform festlegen</td>';

    echo '<td>&nbsp;&nbsp;</td>';

    echo '<td>';
    
    echo '<select size="1" name="uti_status">';
    
    echo '<option value="1"><span class="text">Empfänger</span></option>';

    echo '<option value="0"><span class="text">nur in Kopie (CC)</span></option>';

    echo '</select>';
    
    echo '</td></tr>';
    
    echo
        '<tr><td colspan="4" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Save Mail" class="formularbutton" /></td></tr>';

    echo '<input type="hidden" name="hau_id" value="' . $task_id . '">';

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

    $sql='INSERT INTO ticket_info (uti_hauid, uti_mail, uti_status, uti_aktiv) VALUES ("' . $Daten['hau_id'] . '", "' . $Daten['uti_mail']
        . '", "' . $Daten['uti_status'] . '", "1")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    header('Location: aufgabe_ansehen.php?hau_id=' . $Daten['hau_id']);
    exit;
    }

include('segment_fuss.php');
?>