<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
  echo '<span class="text_mitte"><br>Task Log:<br><br>';
echo '<table bgcolor="#ffffff" border=1><tr><td class="text_klein" valign="top">Date</td><td class="text_klein" valign="top">Staff Member</td><td class="text_klein" valign="top">Comment</td></tr>';

$sql_komm = 'SELECT * FROM kommentare WHERE uko_hau_id = "'.$task_id.'"';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_komm = mysql_query($sql_komm, $verbindung))
    { fehler(); }
    
while ($zeile_komm = mysql_fetch_array($ergebnis_komm)) 
{ 
    if ($zeile_komm['uko_kommentar']!='')
    {
        echo '<tr><td class="text_klein" valign="top">'.zeitstempel_anzeigen($zeile_komm['uko_datum']).'</td><td class="text_klein" valign="top">'.$zeile_komm['uko_ma'].'</td><td class="text_klein" valign="top">'.html_entity_decode($zeile_komm['uko_kommentar']).'</td></tr>';
    }
}
echo '</table>';
?>
