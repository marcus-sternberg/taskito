<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (isset($_GET['ulok_id']))
    {
    $ulok_id=$_GET['ulok_id'];
    }

if (!isset($_POST['speichern']))
    {
    include('segment_kopf.php');

    $sql_aufgabe='SELECT * FROM log_kunde WHERE ulok_id = ' . $ulok_id;

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_aufgabe=mysql_query($sql_aufgabe, $verbindung))
        {
        fehler();
        }

    while ($Daten=mysql_fetch_array($ergebnis_aufgabe))
        {

        $Daten['ulok_text'] = ($Daten['ulok_text']);

        echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Change Activity Log Entry<br><br>';

        echo '<form action="schreibtisch_log_aendern.php" method="post">';

        echo '<input type="hidden" name="hau_id" value="' . $ulok_id . '">';

        echo '<table border="0" cellspacing="5" cellpadding="0">';

        // ulok_id übergeben

        echo '<input type="hidden" name="ulok_id" value="' . $ulok_id . '">';

        // Datum Kommentar

        echo '<tr>';

        echo '<td class="text_klein">Date: </td><td><input type="text" name="ulok_zeitstempel" value="'
            . zeitstempel_anzeigen($Daten['ulok_zeitstempel']) . '" style="width:340px;"></td>';

        echo '</tr>';

        // Text des Kommentars

        echo '<tr>';

        echo
            '<td class="text_klein" valign="top">Comment:&nbsp;&nbsp;</td><td><textarea cols="80" rows="10" name="ulok_text">'
            . $Daten['ulok_text'] . '</textarea></td>';

        echo '</tr>';

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

    $ulok_id=$_POST['ulok_id'];

    $sql_aufgabe='SELECT * FROM log_kunde WHERE ulok_id = ' . $ulok_id;

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_aufgabe=mysql_query($sql_aufgabe, $verbindung))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis_aufgabe))
        {

        $Text=($zeile['ulok_text']);
        }

    // Umwandlung des Datumsfeldes in DATETIME

    $DatumZeit=explode(" ", $Daten['ulok_zeitstempel']);
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

    $Daten['ulok_zeitstempel']=date("Y-m-d H:i:s", mktime($Zeit[0], $Zeit[1], 0, $Datum[1], $Datum[0], $Datum[2]));

    $Daten['ulok_text']=strip_tags($Daten['ulok_text']);

    // Speichere den Datensatz

    $sql='UPDATE log_kunde SET ' .

    'ulok_text = "' . mysql_real_escape_string($Daten['ulok_text']) . '",' .
        'ulok_zeitstempel = "' . $Daten['ulok_zeitstempel'] . '" ' .
        'WHERE ulok_id = ' . $ulok_id;

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    $sql_log='INSERT INTO eventlog (' .
        'hel_area, ' .
        'hel_type, ' .
        'hel_referer, ' .
        'hel_text) ' .
        'VALUES ( ' .
        '"Whiteboard", ' .
        '"Edit", ' .
        '"' . $_SESSION['hma_login'] . '" ,' .
        '"hat folgenden Eintrag geaendert: ' . mysql_real_escape_string($Text) . '")';

    if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
        {
        fehler();
        }

    // Zurueck zur Liste

    header('Location: kunden_logfile.php');
    exit;
    }
?>