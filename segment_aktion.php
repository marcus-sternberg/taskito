<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$delete='';

$toggle=1;

foreach ($aktionen as $schluessel => $aktion)
    {
    switch ($aktion['inhalt'])
        {
        case 'Aufgabe übernehmen':

            $delete='';
            $sql_tl='SELECT uau_hauid FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $zeile['hau_id'];

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_tl=mysql_query($sql_tl, $verbindung))
                {
                fehler();
                }
                
                if (mysql_num_rows($ergebnis_tl)==0)
                    {

                    $bild='<img src="bilder/' . $aktion['bild'] . '" border="0" alt="' . $aktion['inhalt'] . '" title="'
                        . $aktion['inhalt'] . '">';
                    }
                else
                    {
                    $bild='';
                    }
                
            break;

          case 'remove staff on STOPP only':
            $delete='';
            $sql_gruppe=
                'SELECT COUNT(uau_id) as Anzahl FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $zeile['hau_id'];

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_gruppe=mysql_query($sql_gruppe, $verbindung))
                {
                fehler();
                }
            // Inhalt Tabelle

            while ($zeile_gruppe=mysql_fetch_array($ergebnis_gruppe))
                {
                if ($zeile_gruppe['Anzahl'] > 1)
                    {
                    $bild='<img src="bilder/' . $aktion['bild'] . '" border="0" alt="' . $aktion['inhalt'] . '" title="'
                        . $aktion['inhalt'] . '">';
                    $toggle=2;
                    }
                else
                    {
                    $bild='';
                    }
                }

            break;

        case 'confirm changed date':
            $delete='';
            $sql_gruppe='SELECT hau_id, hau_terminaendern FROM aufgaben WHERE hau_id = ' . $zeile['hau_id'];

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_gruppe=mysql_query($sql_gruppe, $verbindung))
                {
                fehler();
                }
            // Inhalt Tabelle

            while ($zeile_gruppe=mysql_fetch_array($ergebnis_gruppe))
                {
                if ($zeile_gruppe['hau_terminaendern'] == 1)
                    {
                    $bild='<img src="bilder/' . $aktion['bild'] . '" border="0" alt="' . $aktion['inhalt'] . '" title="'
                        . $aktion['inhalt'] . '">';
                    $toggle=2;
                    }
                else
                    {
                    $bild='';
                    }
                }

            break;

        case 'delete task':

            $delete=' onclick="return window.confirm(\'Delete Record?\');"';
            $bild='<img src="bilder/' . $aktion['bild'] . '" border="0" alt="' . $aktion['inhalt'] . '" title="'
                . $aktion['inhalt'] . '">';
            break;

        case 'Ticket löschen':
            if ($zeile['hau_ticketnr'] != '')
                {
                $delete=' onclick="return window.confirm(\'Ticket löschen?\');"';
                $bild='<img src="bilder/' . $aktion['bild'] . '" border="0" alt="' . $aktion['inhalt'] . '" title="'
                    . $aktion['inhalt'] . '">';
                }
            else
                {
                $bild='';
                }
            break;

            default:
            $bild='<img src="bilder/'.$aktion['bild'].'" border="0" alt="'.$aktion['inhalt'].'" title="'.$aktion['inhalt'].'">';
            break;
        }

    echo '<td align="center" style="border-left:1px solid grey;" ><a href="' . $aktion['link'] . '?hau_id='
        . $zeile['hau_id'] . '&toggle=' . $toggle . '"' . $delete . '>' . $bild . '</a></td>';
    }
?>