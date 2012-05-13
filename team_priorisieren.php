<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');

if (isset($_POST['ma_id']))
    {
    $ma_id=$_POST['ma_id'];
    $_SESSION['ma_prio']=$ma_id;
    }
else if (isset($_SESSION['ma_prio']))
    {
    $ma_id=$_SESSION['ma_prio'];
    }
else
    {
    $ma_id=8;
    $_SESSION['ma_prio']=$ma_id;
    }
$sql='SELECT hma_login, hma_id FROM mitarbeiter ' .
    'WHERE hma_id = ' . $ma_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    $prio_hma_login = $zeile['hma_login'];
    $prio_hma_id=$zeile['hma_id'];
    }

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Task Priorisation<br><br>';

echo '<form action="team_priorisieren.php" method="post">';

echo 'für Mitarbeiter: ';

echo '<select size="1" name="ma_id" style="width:140px;">';

$sql='SELECT hma_id, hma_login FROM mitarbeiter ' .
    'ORDER BY hma_login';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    if ($_SESSION['ma_prio'] == $zeile['hma_id'])
        {
        echo '<option value="' . $zeile['hma_id'] . '" selected><span class="text">' . $zeile['hma_login']
            . '</span></option>';
        }
    else
        {
        echo '<option value="' . $zeile['hma_id'] . '"><span class="text">' . $zeile['hma_login'] . '</span></option>';
        }
    }

echo '</select>';

echo '<input type="submit" value="choose Staff Member" class="formularbutton" />';

echo '</form>';

echo '<br><br><span class="box">Tasks for ' . $prio_hma_login . '</span>';

$sql='SELECT *, m1.hma_login AS inhaber, m2.hma_login AS mitarbeiter, m3.hma_login AS teamleiter FROM aufgaben ' .
    'LEFT JOIN aufgaben_mitarbeiter ON hau_id = uau_hauid ' .
    'LEFT JOIN mitarbeiter m1 ON hau_inhaber = m1.hma_id ' .
    'LEFT JOIN mitarbeiter m2 ON uau_hmaid = m2.hma_id ' .
    'LEFT JOIN mitarbeiter m3 ON hau_teamleiter = m3.hma_id ' .
    'INNER JOIN typ ON hau_typ = uty_id ' .
    'LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id ' .
    'LEFT JOIN level ON uaz_pg = ule_id ' .
    'LEFT JOIN projekte ON hau_hprid = hpr_id  ' .
    'INNER JOIN prioritaet ON hau_prio = upr_nummer ' .
    'WHERE hau_id = ANY(SELECT DISTINCT hau_id FROM aufgaben LEFT JOIN aufgaben_mitarbeiter ON hau_id=uau_hauid ' .
    'WHERE hau_aktiv = "1" AND hau_tl_status = 1 AND uau_ma_status <2 AND uau_hmaid = ' . $_SESSION['ma_prio'] . ' ' .
    'AND uau_status = 0) ' .
    'GROUP BY hau_id ' .
    'ORDER BY uau_prio, hau_prio, hau_pende';

if (!isset($i))
    {
    $i=0;
    }

if (isset($_GET['sortierschluessel']))
    {
    if ($_SESSION['z'] == 1)
        {

        $sql=substr($sql, 0, my_strrpos($sql, "ORDER BY") + 9) . $_GET['sortierschluessel'] . ' DESC,'
            . substr($sql, my_strrpos($sql, "ORDER BY") + 8);
        $sort_bild='<img src="bilder/sort_desc.gif" width=9 height=9 border=0 alt="abwärts">';
        }

    if ($_SESSION['z'] > 1)
        {
        $sql=substr($sql, 0, my_strrpos($sql, "ORDER BY") + 9) . $_GET['sortierschluessel'] . ' ASC,'
            . substr($sql, my_strrpos($sql, "ORDER BY") + 8);
        $sort_bild='<img src="bilder/sort_asc.gif" width=9 height=9 border=0 alt="aufwärts">';
        }
    }

$anzeigefelder=array
    (
    'TNR' => 'hau_id',
    'prio' => 'upr_name',
    'P-End' => 'hau_pende',
    'R-End' => 'uau_tende',
    'title' => 'hau_titel',
    'owner' => 'inhaber',
    'teamlead' => 'teamleiter',
    'created' => 'hau_anlage',
    'progress [%]' => 'sum_fertig',
    'preference' => 'uau_prio'
    );

$iconzahl=0;
$icons=array();

// Bestimme Anzahl der Spalten der Tabelle
$col=count($anzeigefelder) + $iconzahl;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

echo '<form action="team_priorisieren_sichern.php" method="post">';

// Beginne mit Tabellenausgabe
echo '<table class="element" cellspacing="1" cellpadding="3" width="900">';

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
            case 'created':
                $zeile[$inhalt]=datum_anzeigen($zeile[$inhalt]);
                break;

            case 'P-End':
                $zeile[$inhalt]=datum_anzeigen($zeile[$inhalt]);
                break;

            case 'R-End':
                $zeile[$inhalt]=datum_anzeigen($zeile[$inhalt]);
                break;

            case 'progress [%]':

                // Fertigstellungsgrad

                $sql_menge='SELECT ulo_id,SUM(ulo_fertig) as Menge FROM log ' .
                    'WHERE ulo_aufgabe = ' . $zeile['hau_id'] . ' AND ulo_ma = "' . $prio_hma_id . '" ' .
                    'GROUP BY ulo_aufgabe';

                // Frage Datenbank nach Suchbegriff
                if (!$ergebnis_menge=mysql_query($sql_menge, $verbindung))
                    {
                    fehler();
                    }

                if (mysql_num_rows($ergebnis_menge) != 0)
                    {
                    while ($zeile_menge=mysql_fetch_array($ergebnis_menge))
                        {
                        $zeile[$inhalt]=$zeile_menge['Menge'];
                        }
                    }
                else
                    {
                    $zeile[$inhalt]=0;
                    }

                break;
            }

        $task_id=$zeile['hau_id'];
        include('segment_zeilenfarbe.php');

        if ($bezeichner == 'TNR' OR $bezeichner == 'title')
            {
            echo '<td bgcolor="' . $color . '" class="text_klein"><a href="aufgabe_ansehen.php?hau_id='
                . $zeile['hau_id'] . '">' . $zeile[$inhalt] . '</a></td>';
            }
        else if ($bezeichner == 'preference')
            {
            echo '<td bgcolor="' . $color . '" class="text_klein"><input type="text" name="uau_prio[' . $zeile['hau_id']
                . ']" value="' . $zeile['uau_prio'] . '" width="100px"></td>';
            }
        else
            {
            echo '<td bgcolor="' . $color . '" class="text_klein">' . $zeile[$inhalt] . '</td>';
            }
        }

    foreach ($icons as $icon)
        {
        echo '<td align="center" ><a href="' . $icon['link'] . '?hau_id=' . $zeile['hau_id'] . '"><img src="bilder/'
            . $icon['bild'] . '" border="0" alt="' . $icon['inhalt'] . '" title="' . $icon['inhalt'] . '"></a></td>';
        }

    echo '</tr>';
    }

echo '</tr>';

echo '<tr><td align="right" colspan="' . $col
    . '"><input type="submit" value="Change Preference" class="formularbutton" /></td></tr>';

echo '</table>';

echo '</form>';

include('segment_fuss.php');
?>