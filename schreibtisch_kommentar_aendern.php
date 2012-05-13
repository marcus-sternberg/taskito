<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (isset($_GET['ulo_id']))
    {
    $ulo_id=$_GET['ulo_id'];
    }

if (!isset($_POST['speichern']))
    {
    include('segment_kopf.php');

    $sql_aufgabe='SELECT * FROM log WHERE ulo_id = ' . $ulo_id;

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_aufgabe=mysql_query($sql_aufgabe, $verbindung))
        {
        fehler();
        }

    while ($Daten=mysql_fetch_array($ergebnis_aufgabe))
        {

        echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Change Activity<br><br>';

        echo '<form action="schreibtisch_kommentar_aendern.php" method="post">';

        echo '<input type="hidden" name="hau_id" value="' . $ulo_id . '">';

        echo '<table border="0" cellspacing="5" cellpadding="0">';

        // ulo_id übergeben

        echo '<input type="hidden" name="ulo_id" value="' . $ulo_id . '">';

        echo '<input type="hidden" name="ulo_aufgabe" value="' . $Daten['ulo_aufgabe'] . '">';


        // Datum Kommentar

        echo '<tr>';

        echo '<td class="text_klein">Date: </td><td><input type="text" name="ulo_datum" value="'
            . zeitstempel_anzeigen($Daten['ulo_datum']) . '" style="width:340px;"></td>';

        echo '</tr>';

        // Text des Kommentars

        echo '<tr>';

        echo
            '<td class="text_klein" valign="top">Comment:&nbsp;&nbsp;</td><td><textarea cols="80" rows="10" name="ulo_text">'
            . htmlspecialchars($Daten['ulo_text']) . '</textarea></td>';

        echo '</tr>';

        // Aufwand

        echo '<tr>';

        echo
            '<td class="text_klein">Effort [min]: </td><td><input type="text" name="ulo_aufwand" style="width:100px;" value="'
            . $Daten['ulo_aufwand'] . '">&nbsp;&nbsp;&nbsp;';

        if ($Daten['ulo_extra'] == 1)
            {
            echo 'Extra: <input type="checkbox" name="ulo_extra" checked></td>';
            }
        else
            {
            echo 'Extra: <input type="checkbox" name="ulo_extra"></td>';
            }

        echo '</tr>';


        // Fertigstellung

        echo '<tr>';

        echo
            '<td class="text_klein">% from total Progress: </td><td><input type="text" name="ulo_fertig" style="width:100px;"  value="'
            . $Daten['ulo_fertig'] . '"></td>';

        echo '</tr>';

        echo
            '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Save Changes" class="formularbutton" /></td></tr>';

        echo '</table>';

        echo '</form>';
        }
    }
else
    {
    $fehlermeldung=array();
    $anzahl_fehler=0;

    foreach ($_POST as $varname => $value)
        {

        $Daten[$varname]=$value;
        }

    $ulo_id=$_POST['hau_id'];

    if (isset($Daten['ulo_extra']) AND $Daten['ulo_extra'] == 'on')
        {
        $Daten['ulo_extra']=1;
        }
    else
        {
        $Daten['ulo_extra']=0;
        }

    // Umwandlung des Datumsfeldes in DATETIME

    $DatumZeit=explode(" ", $Daten['ulo_datum']);
    $Datum=explode(".", $DatumZeit[0]);
    $Zeit=explode(":", $DatumZeit[1]);

    if (count($Zeit) < 2)
        {
        $Zeit[0]='12';
        $Zeit[1]='00';
        }
    else if ($Zeit[1] == '' OR $Zeit[0] == '')
        {
        $Zeit[0]='12';
        $Zeit[1]='00';
        }

    if (count($Datum) < 3)
        {

        $heute=date("d.m.Y");
        $Datum=explode(".", $heute);
        }
    else if (!checkdate($Datum[1], $Datum[0], $Datum[2]))
        {
        $heute=date("d.m.Y");
        $Datum=explode(".", $heute);
        }

    $Daten['ulo_datum']=date("Y-m-d H:i:s", mktime($Zeit[0], $Zeit[1], 0, $Datum[1], $Datum[0], $Datum[2]));


    // Speichere den Datensatz

    $sql='UPDATE log SET ' .

    'ulo_text = "' . mysql_real_escape_string($Daten['ulo_text']) . '",' .
        'ulo_datum = "' . $Daten['ulo_datum'] . '", ' .
        'ulo_aufwand = "' . $Daten['ulo_aufwand'] . '", ' .
        'ulo_fertig = "' . $Daten['ulo_fertig'] . '", ' .
        'ulo_extra = "' . $Daten['ulo_extra'] . '", ' .
        'ulo_zeitstempel =NOW() ' .
        'WHERE ulo_id = ' . $ulo_id;

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    // Zurueck zur Liste

    header('Location: aufgabe_ansehen.php?hau_id=' . $Daten['ulo_aufgabe']);
    exit;
    }
?>
