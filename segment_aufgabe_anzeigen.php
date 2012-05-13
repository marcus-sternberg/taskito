<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
 $sql='SELECT * FROM aufgaben ' .
    'LEFT JOIN typ ON hau_typ = uty_id ' .
    'LEFT JOIN projekte ON hau_hprid = hpr_id ' .
    'LEFT JOIN prioritaet ON hau_prio = upr_nummer 
    LEFT JOIN typ_change ON utc_id = hau_utcid
      LEFT JOIN mitarbeiter ON hau_inhaber = hma_id ' .
    'WHERE hau_aktiv = 1 AND hau_id = ' . $task_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {

    echo '<table id="is24_vertikal" width="815">';

    echo '<caption class="is24">';
    echo 'Details für diese Aufgabe';
    echo '</caption>';
    
    echo '<colgroup>';
    echo '<col class="is24-first" />';
    echo '</colgroup>';
    
    echo '<tbody>';
    
    // Aufgabennummer

    echo '<tr>';

    echo '<td valign="top" width="15%">Aufgabennumer: </td><td class="text" align="left">'
        . $zeile['hau_id'] . '</td></tr>';

    // Ticketnummer

    echo '<tr>';

    echo '<td class="text_klein" valign="top" width="15%">Referenz: </td><td class="text" align="left">'
        . $zeile['hau_ticketnr'];
        
    if($zeile['hau_otrsnr']!='')
    {
        echo ' || OTRS ' .$zeile['hau_otrsnr']. '</td></tr>';
    }

    // Anlagedatum

    echo '<tr>';

    echo '<td class="text_klein" valign="top" width="15%">angelegt: </td><td class="text" align="left">'
        . zeitstempel_anzeigen($zeile['hau_anlage']) . '</td></tr>';

    // Aufgabeninhaber

    echo '<tr>';

    echo '<td class="text_klein width="15%"">von: </td><td class="text" align="left">' . $zeile['hma_vorname'] . ' '
        . $zeile['hma_name'] . '</td>';

    echo '</tr>';

    // Aufgabentitel

    echo '<tr>';

    echo '<td class="text_klein" width="15%">Titel: </td><td class="text" align="left">' . ($zeile['hau_titel'])
        . '</td>';

    echo '</tr>';

    // Beschreibung
    
    echo '<tr>';

    echo '<td class="text_klein" valign="top" align="left" width="15%">Beschreibung:&nbsp;&nbsp;</td><td class="text">'
        . nl2br(htmlspecialchars($zeile['hau_beschreibung'])) . '</td>';

    echo '</tr>';

    // Aufgabentyp

    echo '<tr>';

    echo '<td class="text_klein" width="15%">Typ: </td><td class="text" align="left">' . $zeile['uty_name']
        . '</td></tr>';


    // Aufgabenpriorität

    echo '<tr>';

    echo '<td class="text_klein" width="15%">Priorität: </td><td class="text" align="left">' . $zeile['upr_name']
        . '</td></tr>';


    // Projekt

    echo '<tr>';

    echo '<td class="text_klein" valign="top" width="15%">Projekt: </td>';

    if (($zeile['hpr_titel'] != 'Release' AND $zeile['hpr_titel'] != 'Tagesgeschäft' AND $zeile['hpr_titel'] != 'Change' AND $zeile['hpr_titel'] != 'Ticket'))
        {
        echo '<td class="text" align="left">';

        echo '<a href="uebersicht_projekt.php?hpr_id=' . $zeile['hau_hprid'] . '" target="_blank">'
            . $zeile['hpr_titel'] . '</a></td>';
        }
    else
        {
            if($zeile['hau_utcid']>0)
            {
                echo '<td class="text" align="left">' . $zeile['utc_name'] . '-Change</td>';                  
            } else
            {
                echo '<td class="text" align="left">' . $zeile['hpr_titel'] . '</td>';
            }
        }

    echo '</tr>';

    // Dauer

    echo '<tr>';

    echo '<td class="text_klein" width="15%">Dauer [d]: </td><td class="text" align="left">' . $zeile['hau_dauer']
        . '</td></tr>';

    // Out Of Office Time

    echo '<tr>';

    if ($zeile['hau_nonofficetime'] == 0)
        {
        echo
            '<td class="text_klein" valign="top" width="15%">Normalschicht? </td><td class="text" align="left">Ja</td>';
        }
    else
        {
        echo
            '<td class="text_klein" valign="top" width="15%">Normalschicht? </td><td class="text" align="left">Nein</td>';
        }

    echo '</tr>';

    // Plandatum & Typ

    echo '<tr>';

    if ($zeile['hau_datumstyp'] == 1)
        {
        echo '<td class="text_klein" width="15%">Plan-Ende: </td><td class="text" align="left">offen</td>';
        }
    else if ($zeile['hau_datumstyp'] == 2)
        {
        echo '<td class="text_klein" width="15%">Plan-Ende: </td><td class="text" align="left">'
            . datum_anzeigen($zeile['hau_pende']) . ' (fällig bis)</td>';
        }
    else
        {
        echo '<td class="text_klein" width="15%">Plan-Ende: </td><td class="text" align="left">'
            . datum_anzeigen($zeile['hau_pende']) . ' (exakt an dem Tag)</td>';
        }

    echo '</tr>';

    $hau_pende=$zeile['hau_pende'];

    if ($hau_pende == '9999-01-01')
        {
        $hau_pende='open';
        }

    $hau_dauer=$zeile['hau_dauer'];

    // Info teamlead

    echo '<tr>';

    echo
        '<td class="text_klein" valign="top" width="15%">Teamlead-Infos:&nbsp;&nbsp;</td><td class="text" align="left">'
        . htmlspecialchars($zeile['hau_tl_info']) . '</td>';

    echo '</tr>';

    // Referenz auf die Originalmail
    
    if($zeile['hau_referenz']!='')
    {
    echo '<tr>';
    echo
        '<td class="text_klein" valign="top" width="15%">Referenz Originalmail:&nbsp;&nbsp;</td><td class="text" align="left"><a href="http://taskscout24.rz.is24.loc/mailer_dir/'.$zeile['hau_referenz'].'" target="_blank">'.$zeile['hau_referenz'] . '</a></td>';

    echo '</tr>';
    }
    
    echo '</tbody>';
    }

echo '</tr></table>';
?>
