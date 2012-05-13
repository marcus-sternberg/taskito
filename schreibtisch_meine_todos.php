<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');

    if (isset($_GET['sort_todo'])) {
       $sort_todo=$_GET['sort_todo']; 
    } else {$sort_todo='uto_prio DESC, uto_enddatum DESC';}

echo '<div id="header">';

echo '<ul>';

############# GRUPPEN ######################

$sql=$sql_schreibtisch_offene_gruppenjobs;

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<li><a href="schreibtisch_meine_gruppenaufgaben.php">Gruppenaufgaben ('.$anzahl.')</a></li>';

############ AUFGABEN ######################

$sql=$sql_schreibtisch_aktuelle_aufgaben;

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl_queue=mysql_num_rows($ergebnis);

$sql=$sql_schreibtisch_aufgaben_angenommen;

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl_working=mysql_num_rows($ergebnis);

$anzahl = $anzahl_working + $anzahl_queue;

echo '<li><a href="schreibtisch_meine_aufgaben.php">Aufgaben ('.$anzahl.')</a></li>';

############ PROJEKTE ######################

$sql='SELECT *, DATEDIFF(hpr_pende,curdate()) as diff FROM projekte 
        LEFT JOIN mitarbeiter ON hpr_inhaber = hma_id    
        WHERE hpr_inhaber = "' . $_SESSION['hma_id'] . '" AND hpr_fertig = "0" AND hpr_aktiv = "1" 
        ORDER BY hpr_prio, hpr_pende';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<li><a href="schreibtisch_meine_projekte.php">Projekte ('.$anzahl.')</a></li>';

############ PING ######################

$sql=$sql_schreibtisch_aufgaben_mit_PING;

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<li><a href="schreibtisch_meine_pings.php">Pings ('.$anzahl.')</a></li>';

############ TODO ######################

$sql='SELECT uto_id FROM todo WHERE uto_status = 0 AND uto_hmaid = ' . $_SESSION['hma_id'];

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<li id="current"><a href="#">ToDos ('.$anzahl.')</a></li>';

echo '</ul>';

echo '</div>';

echo '<br>';
echo '<br>';
echo '<br>';

echo '<div id="header">';

echo '<ul>';

$sql='SELECT uto_id FROM todo WHERE uto_status = 0 AND uto_hmaid = ' . $_SESSION['hma_id'];

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<li><a href="#">Meine ToDos ('.$anzahl.')</a></li>';

echo '</ul>';

echo '</div>';

echo '<table class="matrix" width="900">';   

echo '<form action="schreibtisch_todo_speichern.php?toggle=3" method="post">'; 
echo '<tr>';
echo '<td class="text">done</td>';
echo '<td class="text"><a href="'.$_SERVER['PHP_SELF'].'?sort_todo=uto_text">ToDo</a></td>';
echo '<td class="text"><a href="'.$_SERVER['PHP_SELF'].'?sort_todo=uto_enddatum">due until</a></td>';
echo '<td class="text"><a href="'.$_SERVER['PHP_SELF'].'?sort_todo=upr_sort">Prio</td>';
echo '</tr>';


        
$sql =  'SELECT * FROM todo '. 
        'LEFT JOIN prioritaet ON upr_nummer = uto_prio '.
        'WHERE uto_hmaid = '.$_SESSION['hma_id'].' AND uto_status = 0 ORDER BY '.$sort_todo;

  // Frage Datenbank nach Suchbegriff
  if (!$ergebnis = mysql_query($sql, $verbindung))
     { fehler(); }

  while ($zeile = mysql_fetch_array($ergebnis)) 
  {     
   
   echo '<tr>';
   echo '<td class="text" valign="top"><input type="checkbox" name="done['.$zeile['uto_id'].']"></td>';
   echo '<td class="text" valign="top">'.html_entity_decode($zeile['uto_text']).'</td>';    
   echo '<td class="text" valign="top">'.datum_anzeigen($zeile['uto_enddatum']).'</td>'; 
   echo '<td class="text" valign="top">'.$zeile['upr_name'].'</td>'; 

  echo '<td class="text_klein" valign="top"><a href="schreibtisch_todo_aendern.php?uto_id='.$zeile['uto_id'].'"><img src="bilder/icon_aendern.gif" border="0" alt="change ToDo" title="change ToDo"></a></td>';                    
   
  echo '<td class="text_klein" valign="top"><a href="schreibtisch_todo_loeschen.php?uto_id='.$zeile['uto_id'].'" onclick="return window.confirm(\'Delete ToDo?\');"><img src="bilder/icon_loeschen.gif" border="0" alt="delete ToDo" title="delete ToDo"></a></td>';                                                                        
  echo '<td class="text_klein" valign="top"><a href="schreibtisch_todo_transfer.php?uto_id='.$zeile['uto_id'].'"><img src="bilder/icon_todo_transfer.png" border="0" alt="Make Task from ToDo" title="Make Task from ToDo"></a></td>';    

   echo '</tr>';
       
  }
   echo '<tr>';
echo '<td colspan="7" style="text-align:left; padding-top:10px;"><input type="submit" name="speichern" value="Mark ToDo as Done" class="formularbutton" /></td></tr>';      
 echo '</table>'; 
 
    echo '<input type="hidden" name="uto_id" value="'.$zeile['uto_id'].'">'; 
    echo '</form>'; 

include ('segment_fuss.php');
?>
