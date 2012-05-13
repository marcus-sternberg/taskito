<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once ('konfiguration.php'); 
  include ('segment_session_pruefung.php');  
  include ('segment_init.php'); 
 

  $hpr_id = $_REQUEST['hpr_id'];
 
   if(!isset($_POST['speichern'])){
  require_once ('segment_kopf.php');  
   echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Change Project-Status<br><br>';     

  $sql= 'SELECT * FROM projekte 
        LEFT JOIN mitarbeiter ON hpr_inhaber = hma_id '.   
        'WHERE hpr_id = '.$hpr_id;

  // Frage Datenbank nach Suchbegriff
  if (!$ergebnis = mysql_query($sql, $verbindung))
     { fehler(); }
     
  while ($zeile = mysql_fetch_array($ergebnis)) 
  { 
      
  echo '<form action="schreibtisch_projekte_uebernehmen.php" method="post">';
  echo '<table border="0" cellspacing="5" cellpadding="0">';
  echo '<input type="hidden" name="hpr_id" value="'.$hpr_id.'">';
  echo '<tr>';
  echo '<td class="text_klein">Title: </td><td>'.$zeile['hpr_titel'].'</td>';
  echo '</tr>';
  echo '<tr>';
  echo '<td class="text_klein" valign="top">Description:&nbsp;&nbsp;</td><td>'.nl2br(htmlspecialchars($zeile['hpr_beschreibung'])).'</td>';
  echo '</tr>';  
   echo '<tr>';
  echo '<td class="text_klein" valign="top">Project Type:</td><td>'.$zeile['upt_name'].'</td>';
  echo '</tr>'; 
  
  
  echo '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Set new Status" class="formularbutton" /></td></tr>';    
  
    echo '</table>';
  echo '</form>';

  } else
  {

         
    
    // Zurueck zur Liste

        header( 'Location: schreibtisch_projekte.php' );
        exit;
      
    
      
  }


  ?>

