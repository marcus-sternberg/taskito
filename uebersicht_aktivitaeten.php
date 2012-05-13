<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################

# Definiere Grundlagen

require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

$Bereichssumme=0;
$Zwischensumme=0;
$Gesamtsumme=0;

# Gib Auswahlfeld für Monat und KW aus

$ruecksprung='uebersicht_aktivitaeten.php';
$Anzeige='none';

# Definiere Rücksprung

echo '<form action="' . $ruecksprung . '" method="post">';

# Baue Tabelle

echo '<table border="0">';

# Frage Zeitraum ab

echo '<tr>';

echo '<td class="text_klein" valign="top">Bitte KW und Jahr wählen:</td>';

echo '<td>';

if (isset($_REQUEST['xKw']))
    {
    $xPruefkw=$_REQUEST['xKw'];
    }
else
    {
    $xPruefkw=date('W');
    }

echo '<select size="1" name="xKw">';

for ($i=1; $i < 54; $i++)
    {
    if ($xPruefkw == $i)
        {
        echo '<option value="' . $i . '" selected><span class="text">' . $i . '. CW</span></option>';
        }
    else
        {
        echo '<option value="' . $i . '"><span class="text">' . $i . '. CW</span></option>';
        }
    }

echo '</select> ';

echo '</td>';

echo '<td>';

if (isset($_REQUEST['xJahr']))
    {
    $xPruefjahr=$_REQUEST['xJahr'];
    }
else
    {
    $xPruefjahr=date('Y');
    }

echo '<select size="1" name="xJahr">';

foreach ($Jahr as $Anzeigejahr)
    {
    if ($xPruefjahr == $Anzeigejahr)
        {
        echo '<option value="' . $Anzeigejahr . '" selected><span class="text">' . $Anzeigejahr . '</span></option>';
        }
    else
        {
        echo '<option value="' . $Anzeigejahr . '"><span class="text">' . $Anzeigejahr . '</span></option>';
        }
    }

echo '</select> ';

echo '</td>';

echo '</tr>';

echo '<td class="text_klein" valign="top">KW geht von Fr bis Fr?</td>';   

echo '<td><input type="checkbox" name="zeitraum"></td>';

echo '</td></tr>';

echo '<td class="text_klein" valign="top">Zeige nur Rufbereitschaft?</td>';   

echo '<td><input type="checkbox" name="rufbereitschaft"></td>';

echo '</td></tr>';

echo '<tr><td colspan = "3" align="right">';

echo '<input type="submit" value="Zeige Aktivitäten" class="formularbutton" name="cw"/>';

echo '</td></tr>';

echo '</table>';   

echo '</form>';

echo '<br><br>';

if (isset($_REQUEST['xJahr']))
    {
    if (isset($_REQUEST['cw']))
        {
        $kw=$_REQUEST['xKw'];
        $anzeigestring=$_REQUEST['xKw'] . '.CW ' . $_REQUEST['xJahr'];
        $filterstring=' AND week(hau_anlage) = ' . $kw;
        $filterstring_ua=' AND week(ulo_datum,3) = ' . $kw;
        $cw_string='&cw=1';
        }
    else
        {
        $anzeigestring=$_REQUEST['xMonat'] . '.' . $_REQUEST['xJahr'];
        $filterstring=' AND month(hau_anlage) = ' . $_REQUEST['xMonat'];
        $filterstring_ua=' AND month(ulo_datum) = ' . $_REQUEST['xMonat'];
        $cw_string='';
        }
    }


////////////////////////////////////////////////////////////////

if (!isset($_REQUEST['xJahr']))
    {

    $wochendatum=date("Y-m-d", mondaykw(date('W'), date('Y')));
    }
else

    # Ermittle den ersten Montag der KW

    {
    $wochendatum=date("Y-m-d", mondaykw($_REQUEST['xKw'], $_REQUEST['xJahr']));
    }
 
if(ISSET($_POST['zeitraum']))
{
$wochendatum = date("Y-m-d",strtotime("-3 days",strtotime($wochendatum)));     
}

$ruf = '';

if(ISSET($_POST['rufbereitschaft']))
{
$ruf = 'hau_typ = 17 AND ';     
}
    
$o=0;

################################### Beginne Ausgabe der Aktivitäten ###################################

echo '<span class="box">Folgende Jobs sind gespeichert für diese Woche ['.date("d.m.Y",strtotime($wochendatum)).' - '.date("d.m.Y",strtotime("+6 days",strtotime($wochendatum))).']:</span><br><br>';

for ($i=1; $i < 8; $i++)
    {
    $zwischensumme = 0;
    $wochendatum=strftime("%Y-%m-%d", strtotime($wochendatum . '+' . $o . ' day'));

    echo '<br>';

    echo '<table class="element" width="700">';

    echo '<tr>';

    echo '<td colspan="3" bgcolor="#c2c2c2">';

    echo datum_anzeigen($wochendatum);

    echo '</td></tr>';

    echo '<td bgcolor="#c2c2c2">Aufgabe</td><td bgcolor="#c2c2c2">Aufwand [h]</td></tr>';

    $sql_aufgaben='SELECT DISTINCT hau_id, hau_titel FROM aufgaben ' .
        'LEFT JOIN log ON hau_id = ulo_aufgabe ' .
        'WHERE '.$ruf.'hau_aktiv = 1 AND  ulo_ma = ' . $_SESSION['hma_id'] . ' AND date_format(ulo_datum, "%Y-%m-%d") = "'
        . $wochendatum . '" ORDER BY hau_titel';


    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_aufgaben=mysql_query($sql_aufgaben, $verbindung))
        {
        fehler();
        }

    while ($zeile_aufgaben=mysql_fetch_array($ergebnis_aufgaben))
        {

        $sql_aufwand = 'SELECT SUM(ulo_aufwand) AS menge FROM log ' .
            'WHERE date_format(ulo_datum, "%Y-%m-%d") = "' . $wochendatum . '" AND ulo_ma = ' . $_SESSION['hma_id']
            . ' AND ulo_aufgabe = ' . $zeile_aufgaben['hau_id'] . ' GROUP BY ulo_aufgabe';

        if (!$ergebnis_aufwand=mysql_query($sql_aufwand, $verbindung))
            {
            fehler();
            }

        while ($zeile_aufwand=mysql_fetch_array($ergebnis_aufwand))
            {
            echo '<td width="400"><a href="aufgabe_ansehen.php?hau_id=' . $zeile_aufgaben['hau_id']
                . '" target="_blank">' . $zeile_aufgaben['hau_titel'] . '</td>';

            echo '<td width="50" align="right">' . round($zeile_aufwand['menge'] / 60, 2) . '</td>';
            $zwischensumme=$zwischensumme + round($zeile_aufwand['menge'] / 60, 2);
            $Gesamtsumme=$Gesamtsumme + round($zeile_aufwand['menge'] / 60, 2);
            }

        echo '</tr>';
        }

    if ((int)$zwischensumme < 6)
        {
        $color='#FFBFA0';
        }
    else
        {
        $color='#ffffff';
        }

    echo '<tr><td colspan="3" align="right" bgcolor="' . $color . '">Summe: ' . $zwischensumme . '</td></tr>';

    echo '</table>';
    $o=1;
    }

if ((int)$Gesamtsumme < 30)
    {
    $color='#FFBFA0';
    }
else
    {
    $color='#ffffff';
    }

echo '<br>';

echo '<table class="element" width="700">';

echo '<tr>';

echo '<tr><td colspan="3" align="right" bgcolor="' . $color . '">Summe: ' . $Gesamtsumme . '</td></tr>';

echo '</table>';
?>