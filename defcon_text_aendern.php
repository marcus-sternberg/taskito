<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_init.php');

$udm_id = $_REQUEST['udm_id'];

if(!ISSET($_POST['speichern']))
{
    include('segment_kopf.php');  
    
    
    $sql='SELECT udm_bezug, udm_text, udm_mail, udm_mails_cc FROM defcon_texte WHERE udm_id = '.$udm_id; 

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
    
    $udm_von = substr($zeile['udm_bezug'],0,1);
    $udm_nach = substr($zeile['udm_bezug'],-1,1);       
            
    
    echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;DEFCON Text ändern<br><br>';

    echo '<form action="defcon_text_aendern.php" method="post">';
    
    echo '<input type="hidden" name="udm_id" value="'.$udm_id.'">';
    echo '<input type="hidden" name="udm_von" value="'.$udm_von.'">'; 
    echo '<input type="hidden" name="udm_nach" value="'.$udm_nach.'">'; 
    
      echo '<table border="0" cellspacing="5" cellpadding="0">';

    echo '<tr>';

    echo '<td class="text_klein">Von DEFCON (Ausgangsstatus): </td><td>'.$udm_von.'</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein">Nach DEFCON (Zielstatus): </td><td>'.$udm_nach.'</td>';

    echo '</tr>';    

    echo '<tr>';

    echo '<td class="text_klein" valign="top">Text der Mail:&nbsp;&nbsp;</td><td><textarea cols="80" rows="15" name="udm_text">'.htmlspecialchars($zeile['udm_text']).'</textarea></td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein">Mailempfänger: </td><td>';

    echo '<input type="text" name="udm_mail" style="width:400px;" value="'.htmlspecialchars($zeile['udm_mail']).'"></td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein" valign="top">Mail in cc <br>(Bitte Adressen mit Semikolon trennen): </td><td>';

    echo '<textarea cols="80" rows="15" name="udm_mails_cc">'.htmlspecialchars($zeile['udm_mails_cc']).'</textarea></td>';  

    echo '</tr>';

    echo
        '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Speichere Text" class="formularbutton" /></td></tr>';

    echo '</table>';

    echo '</form>';
        }
    }
    else // Es wurde Text geschickt
    {
    foreach ($_POST as $varname => $value)
        {
        $Daten[$varname]=$value;
        }

    $udm_bezug = $_POST['udm_von'].$_POST['udm_nach'];
        
      
   $sql='UPDATE defcon_texte SET 
        udm_bezug =  "' . $udm_bezug . '",
        udm_text = "' . mysql_real_escape_string($Daten['udm_text']) . '", 
        udm_mail = "' . mysql_real_escape_string($Daten['udm_mail']) . '",  
        udm_mails_cc = "' . mysql_real_escape_string($Daten['udm_mails_cc']) . '" 
        WHERE udm_id = '.$Daten['udm_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    
  
    // Zurueck zur Liste

    header('Location: defcon_text_liste.php');
    exit;
    }
?>
