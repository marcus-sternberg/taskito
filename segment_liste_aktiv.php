<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
if ($session_frei == 1)
    {
    $sql='SELECT * FROM log ' .
        'INNER JOIN mitarbeiter ON ulo_ma = hma_id ' .
        'WHERE ulo_aufgabe = ' . $task_id . ' AND (ulo_mail=1 OR ulo_requestor=1) ' .
        ' ORDER BY ulo_datum DESC';
    }
else
    {

    $sql='SELECT * FROM log ' .
        'INNER JOIN mitarbeiter ON ulo_ma = hma_id ' .
        'WHERE ulo_aufgabe = ' . $task_id .
        ' ORDER BY ulo_datum DESC';
    }

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

  echo '<table id="is24_vertikal" width="450">';

echo '<caption class="is24">';

echo 'Aktivitäten';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Datum</th>';

echo '<th class="is24">Bearbeiter</th>';

echo '<th class="is24">Kommentar</th>';

echo '<th class="is24" nowrap>Aufwand [min]</th>';

echo '<th class="is24" nowrap>Fortschritt [%]</th>';

echo '<th class="is24">&nbsp;</th>';  

echo '<th class="is24">&nbsp;</th>';  

echo '<th class="is24">&nbsp;</th>';  

echo '</tr>';

echo '</thead>';

echo '<tbody class="is24">';

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<tr class="is24">';

    echo '<td class="is24" valign="top">' . zeitstempel_anzeigen($zeile['ulo_datum']) . '</td>';

    echo '<td class="is24" valign="top">' . $zeile['hma_login'] . '</td>';

    echo '<td class="is24" valign="top">';

    if ($zeile['ulo_link'] != '' AND $session_frei != 1)
        {
        $infos=explode("|", $zeile['ulo_link']);

        foreach ($infos AS $link)
            {
            echo '<strong><u>Infolink: </u></strong><a href="' . $link . '" target="_blank">' . $link . '</a><br><br>';
            }

   #     echo '<strong><u>Hinweis: </u></strong><br>';
        }

   
    echo nl2br(htmlspecialchars($zeile['ulo_text'])) . '</td>';

    echo '<td class="is24" valign="top">' . $zeile['ulo_aufwand'] . '</td>';

    echo '<td class="is24" valign="top">' . $zeile['ulo_fertig'] . ' %</td>';

    if ($_SESSION['hma_id'] == $zeile['ulo_ma'])
        {
        echo '<td class="is24" valign="top"><a href="schreibtisch_kommentar_aendern.php?ulo_id='
            . $zeile['ulo_id'] . '"><img src="bilder/icon_aendern.gif" border="0" title="Kommentar ändern" alt="Kommentar ändern"></a></td>';

        echo '<td class="is24" valign="top"><a href="schreibtisch_kommentar_loeschen.php?ulo_id='
            . $zeile['ulo_id']
                . '" onclick="return window.confirm(\'Delete Datarecord?\');"><img src="bilder/icon_loeschen.gif" border="0" title="Kommentar löschen" alt="Kommentar löschen"></a></td>';
        }
    else
        {
        echo '<td class="is24">&nbsp;</td>';

        echo '<td class="is24">&nbsp;</td>';
        }

    if ($zeile['ulo_mail'] == 1)
        {
        echo '<td class="is24" valign="top"><img src="bilder/email_go.png" border="0" title="Mail versandt" alt="Mail versandt"></td>';
        }
    else
        {
        echo '<td class="is24">&nbsp;</td>';
        }
        
    echo '</tr>';
    }
    
    echo '</tbody>';

echo '</table>';
?>