<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

########################  Definiere Variablen ################################


#####################################################################################
############################ Ausgabe Werte ##########################################

# Lese letzten Status

$sql='SELECT ude_status, ude_zeitstempel FROM defcon
         ORDER BY ude_zeitstempel DESC LIMIT 1';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    if ($zeile['ude_status'] != 4)
        {
        $peace=0;
        $Tage=0;
        }
    else
        {
        $datum=explode('-', substr($zeile['ude_zeitstempel'], 0, 10));
        $Alt_Wert=mktime(0, 0, 0, $datum[1], $datum[2], $datum[0]);
        $DIFF=time() - $Alt_Wert;
        $Tage=floor($DIFF / 86400);

        if ($Tage < 1)
            {
            $Tage=0;
            }
        }
    }


// Gebe Überschrift aus

echo '<br><table class="element" cellpadding = "5">';

echo '<tr>';

echo '<td class="text_mitte">';

echo '<img src="bilder/block.gif">&nbsp;DefCon | Friedenszeit seit ' . $Tage . ' Tag(en)';

echo '</td>';

echo '</tr>';

echo '</table>';


################### Acitivty Log ######################

// Starte Tabelle

echo '<br>';

echo '<table border="0"><tr>';

echo '<td>';

echo
    '<span class="text_klein"><a href="http://wiki-ite.iscout.local/index.php/DefCons" target="_blank">Infos zu Defcon im WIKI</a></span>';

echo '<table class="matrix" width="500">';

echo '<br>';

echo '<tr><th colspan="9" align="center">Aktueller DEFCON-Status:</th></tr>';

echo '<tr>';

echo '<th align="center" width="200">Status</th>';

echo '<th align="center">seit</th>';

echo '<th align="center" colspan="4">Status ändern</th>';

echo '</tr>';

$sql='SELECT ude_status, ude_zeitstempel FROM defcon
         ORDER BY ude_zeitstempel DESC LIMIT 1';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    switch ($zeile['ude_status'])
        {
        case 1:
            $color='#EE775F';
            $status='KRITISCH';
            break;

        case 2:
            $color='#F3C39B';
            $status='PROBLEM';
            break;

        case 3:
            $color='#FFF8B3';
            $status='WARNUNG';
            break;

        case 4:
            $color='#C1E2A5';
            $status='OK';
            break;
        }

    echo '<tr>';

    echo '<td class="text_mitte" align="center" bgcolor="' . $color . '" width="200"><strong>' . $zeile['ude_status']
        . '</strong>&nbsp;<br>(' . $status . ')</td>';

    echo '<td align="left">' . zeitstempel_anzeigen($zeile['ude_zeitstempel']) . '</td>';

    echo '<td width="30" align="center"><a href="defcon_change.php?status=1"><h1>1</h1></a></td>';

    echo '<td align="center" width="30"><a href="defcon_change.php?status=2"><h1>2</h1></a></td>';

    echo '<td align="center" width="30"><a href="defcon_change.php?status=3"><h1>3</h1></a></td>';

    echo '<td align="center" width="30"><a href="defcon_change.php?status=4"><h1>4</h1></a></td>';

    echo '</tr>';
    }

echo '</table>';

echo '</td><td valign="top">';

echo '</td></tr>';

echo '</table>';

echo '</td></tr><tr><td colspan="2">';

echo
    '<br><span class="text_rot">Bei Änderungen auf DEFCON 2 oder 1 bitte neben der Mail unbedingt anrufen:
<ul>
<li>Oliver Schmitz unter 0173 890 03 19</li>
<li>Andreas Hankel unter 0172 394 46 81 (wenn er nicht im Büro ist).</li>
</ul></span>';

echo '</td></tr><tr><td colspan="2">';

// Starte Tabelle

echo '<table class="matrix" width="500">';

echo '<tr><th colspan = "9" align="center">Rückschau [20 letzte Einträge]:</th></tr>';

echo '<tr>';

echo '<th align="center" width="200">Status</th>';

echo '<th align="center">seit</th>';

echo '<th align="center">geändert durch</th>';

echo '</tr>';

$sql='SELECT ude_status, ude_zeitstempel, hma_login FROM defcon
        LEFT JOIN mitarbeiter ON hma_id = ude_hmaid 
         ORDER BY ude_zeitstempel DESC LIMIT 20';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    switch ($zeile['ude_status'])
        {
        case 1:
            $color='#EE775F';
            $status='KRITISCH';
            break;

        case 2:
            $color='#F3C39B';
            $status='PROBLEM';
            break;

        case 3:
            $color='#FFF8B3';
            $status='WARNUNG';
            break;

        case 4:
            $color='#C1E2A5';
            $status='OK';
            break;
        }

    echo '<tr>';

    echo '<td align="center" bgcolor="' . $color . '" width="200"><strong>' . $zeile['ude_status'] . '</strong>&nbsp;('
        . $status . ')</td>';

    echo '<td align="left">' . zeitstempel_anzeigen($zeile['ude_zeitstempel']) . '</td>';

    echo '<td align="left">' . $zeile['hma_login'] . '</td>';

    echo '</tr>';
    }

echo '</table>';

echo '</td></tr></table>';

include('segment_fuss.php');
?>