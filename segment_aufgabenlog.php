<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################

$sql_komm='SELECT uko_datum,uko_ma,uko_kommentar FROM kommentare WHERE uko_hau_id = "' . $task_id . '"' ;
if (isset ($pagecalltime))
	$sql_komm.=' and uko_datum >="'.$pagecalltime.' " 
		   UNION
		   SELECT ulo_datum as uko_datum,ulo_text as uko_kommentar,concat(hma_vorname," ",hma_name) as uko_ma  
		   FROM log JOIN mitarbeiter on (ulo_ma = hma_id)
		   WHERE ulo_aufgabe = "' . $task_id . '" AND ulo_datum >="'.$pagecalltime.'" order by uko_datum DESC';
//echo $sql_komm;
//echo date ("H:i:s");
// Frage Datenbank nach Suchbegriff
if (!$ergebnis_komm=mysql_query($sql_komm, $verbindung))
{
    fehler();
}

$tmp_num_rows=mysql_num_rows($ergebnis_komm);
if ($tmp_num_rows > 0)
{
	if (!isset ($pagecalltime))
		echo '<span class="text_mitte"><br>Task Log:<br><br>
			   <table class="matrix"><tr><th>Datum</th><th>Bearbeiter</th><th>Logeintrag</th></tr>';
	else
		echo '<span class="text_mitte"><br>Neu eingetroffene Aktivit&auml;ten f&uuml;r diesen Task: <br><br>
			  <table class="matrix" width="100%"><tr><td>Datum</td><td>Bearbeiter</td><td>Logeintrag</td></tr>';
	while ($zeile_komm=mysql_fetch_array($ergebnis_komm))
	    {
	    if ($zeile_komm['uko_kommentar'] != '')
	        {
	        echo '<tr><td class="text_klein" valign="top">' . zeitstempel_anzeigen($zeile_komm['uko_datum'])
	            . '</td><td class="text_klein" valign="top">' . $zeile_komm['uko_ma']
	            . '</td><td class="text_klein" valign="top">' . ($zeile_komm['uko_kommentar']) . '</td></tr>';
	        }
	    }
	
	echo '</table>';
}
?>