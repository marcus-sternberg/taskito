<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if(!ISSET($_POST['speichern']))
{
    include('segment_kopf.php');  
    
    echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;DEFCON Text hinterlegen<br><br>';

    echo '<form action="defcon_text_neu.php" method="post">';

      echo '<table border="0" cellspacing="5" cellpadding="0">';

    echo '<tr>';

    echo '<td class="text_klein">Von DEFCON (Ausgangsstatus): </td><td><input type="text" name="udm_von" style="width:30px;"></td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein">Nach DEFCON (Zielstatus): </td><td><input type="text" name="udm_nach" style="width:30px;"></td>';

    echo '</tr>';    

    echo '<tr>';

    echo '<td class="text_klein" valign="top">Text der Mail:&nbsp;&nbsp;</td><td><textarea cols="80" rows="15" name="udm_text"></textarea></td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein">Mailempfänger: </td><td>';

    echo '<input type="text" name="udm_mail" style="width:400px;" value="is24-it-pro-om@immobilienscout24.de"></td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein" valign="top">Mail in cc <br>(Bitte Adressen mit Semikolon trennen): </td><td>';

    echo '<textarea cols="80" rows="15" name="udm_mails_cc"></textarea></td>';  

    echo '</tr>';

    echo
        '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Speichere Text" class="formularbutton" /></td></tr>';

    echo '</table>';

    echo '</form>';
    }
    else // Es wurde Text geschickt
    {
    foreach ($_POST as $varname => $value)
        {
        $Daten[$varname]=$value;
        }

    $udm_bezug = $_POST['udm_von'].$_POST['udm_nach'];
        
    $sql = 'SELECT * FROM defcon_texte WHERE udm_bezug = '.$udm_bezug;
    
    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    
    if(mysql_num_rows($ergebnis)>0)
    
    {
        echo 'Für diese Statusänderung ist bereits ein Text hinterlegt.';
        echo '<form action="defcon_text_liste.php" method="post">';
        echo '&nbsp;&nbsp;<input type="submit" name="fehler" value="OK" class="formularbutton" />';
        echo '</form>';
        exit;
    }
      
      
   $sql='INSERT INTO defcon_texte (' .
        'udm_bezug, ' .
        'udm_text, ' .
        'udm_mail, ' .
        'udm_mails_cc) ' .
        'VALUES ( ' .
        '"' . $udm_bezug . '", ' .
        '"' . nl2br(mysql_real_escape_string($Daten['udm_text'])) . '", ' .
        '"' . mysql_real_escape_string($Daten['udm_mail']) . '", ' .
        '"' . mysql_real_escape_string($Daten['udm_mails_cc']) . '")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    
  
    // Zurueck zur Liste

    header('Location: defcon_text_liste.php');
    exit;
    }
?>