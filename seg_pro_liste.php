<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
if (isset($_GET['sortierschluessel']))
    {
    $sql=substr($sql, 0, strpos($sql, 'ORDER BY') + 9) . $_GET['sortierschluessel'] . ','
        . substr($sql, strpos($sql, 'ORDER BY') + 8);
    }


// Bestimme Anzahl der Spalten der Tabelle
$col=count($anzeigefelder) + $iconzahl;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

// Beginne mit Tabellenausgabe
echo '<table class="matrix" cellspacing="1" cellpadding="3" width="900">';

foreach ($anzeigefelder as $bezeichner => $inhalt)
    {
    if (isset($_GET['sortierschluessel']) && $_GET['sortierschluessel'] == $inhalt)
        {
        $anzeige='&nbsp;<img src="bilder/sort.gif" width=9 height=9 border=0 alt="">';
        }
    else
        {
        $anzeige='';
        }

    echo '<td class="tabellen_titel"><a href="' . $_SERVER['PHP_SELF'] . '?aktuelle_seite=0&sortierschluessel='
        . $inhalt . '"><span class="xnormal_sort">' . $bezeichner . '</span></a>' . $anzeige . '</td>';
    }

for ($count=1; $count < ($iconzahl + 1); $count++)
    {
    echo '<td class="tabellen_titel">&nbsp;</td>';
    }

echo '</tr>';

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {

    // Beginne Datenausgabe
    echo '<tr>';

    foreach ($anzeigefelder as $bezeichner => $inhalt)
        {
        switch ($bezeichner)
            {
            case 'Start':
                $zeile[$inhalt]=substr(datum_anzeigen($zeile[$inhalt]), 0, 8);
                break;

            case 'Ende':
                $zeile[$inhalt]=substr(datum_anzeigen($zeile[$inhalt]), 0, 8);
                break;
            }

        if ($zeile['diff'] != NULL)
            {
            if ($zeile['diff'] > 10)
                {
                $color='#C1E2A5';
                }
            else if ($zeile['diff'] < 0)
                {
                $color='#FFBFA0';
                }
            else
                {
                $color='#FFF8B3';
                }
            }
        else
            {
            {
            $color='#CED9E7';
                }
            }

        if ($zeile['hpr_fertig'] == 1)
            {
            $color='#A1A4A8';
            }

        if ($bezeichner == 'Nr')
            {
            echo '<td bgcolor="' . $color . '" class="text_klein"><a href="uebersicht_projekt.php?hpr_id='
                . $zeile['hpr_id'] . '" target="_blank">' . $zeile[$inhalt] . '</a></td>';
            }
        else if ($bezeichner == 'Titel')
            {
            echo '<td bgcolor="' . $color . '" class="text_klein"><a href="uebersicht_projekt.php?hpr_id='
                . $zeile['hpr_id'] . '" target="_blank">' . $zeile[$inhalt] . '</a></td>';
            }
        else
            {
            echo '<td bgcolor="' . $color . '" class="text_klein">' . $zeile[$inhalt] . '</td>';
            }
        }

    foreach ($icons as $icon)
        {
        echo '<td align="center" ><a href="' . $icon['link'] . '?hpr_id=' . $zeile['hpr_id'] . '"><img src="bilder/'
            . $icon['bild'] . '" border="0" alt="' . $icon['inhalt'] . '" title="' . $icon['inhalt'] . '"></a></td>';
        }

    echo '</tr>';
    }

echo '</tr>';

echo '</table>';
?>