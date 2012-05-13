<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$session_frei=1;

require_once('konfiguration.php');
include('segment_session_pruefung.php');

if (isset($_GET['uir_id']))
    {
    $uir_id=$_GET['uir_id'];
    }

if (!isset($_POST['speichern']))
    {
    require_once('segment_kopf_einfach_no_reload.php');

    $sql_aufgabe='SELECT * FROM ir_log WHERE uir_id = ' . $uir_id;

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis_aufgabe=mysql_query($sql_aufgabe, $verbindung))
        {
        fehler();
        }

    while ($Daten=mysql_fetch_array($ergebnis_aufgabe))
        {

        echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Kommentar Ändern<br><br>';

        echo '<form action="ir_kommentar_aendern.php" method="post">';

        echo '<table border="0" cellspacing="5" cellpadding="0">';

        // ulo_id übergeben

        echo '<input type="hidden" name="uir_id" value="' . $uir_id . '">';

        echo '<input type="hidden" name="uir_hirid" value="' . $Daten['uir_hirid'] . '">';

        // Datum Kommentar

        echo '<tr>';

        echo '<td class="text_klein">Datum: </td><td><input type="text" name="uir_datum" value="'
            . zeitstempel_anzeigen($Daten['uir_datum']) . '" style="width:340px;"></td>';

        echo '</tr>';

        // Text des Kommentars

        echo '<tr>';

        echo
            '<td class="text_klein" valign="top">Kommentar:&nbsp;&nbsp;</td><td><textarea cols="80" rows="10" name="uir_eintrag">'
            . htmlspecialchars($Daten['uir_eintrag']) . '</textarea></td>';

        echo '</tr>';

        echo
            '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Speichern" class="formularbutton" /></td></tr>';

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

    $uir_id=$_POST['uir_id'];
    $uir_hirid=$_POST['uir_hirid'];

    // Umwandlung des Datumsfeldes in DATETIME

    $DatumZeit=explode(" ", $Daten['uir_datum']);
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

    $Daten['uir_datum']=date("Y-m-d H:i:s", mktime($Zeit[0], $Zeit[1], 0, $Datum[1], $Datum[0], $Datum[2]));


    // Speichere den Datensatz

    $sql='UPDATE ir_log SET ' .

    'uir_eintrag = "' . mysql_real_escape_string($Daten['uir_eintrag']) . '",' .
        'uir_datum = "' . $Daten['uir_datum'] . '", ' .
        'uir_zeitstempel =NOW() ' .
        'WHERE uir_id = ' . $uir_id;

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    # Aktualisiere Zeitstempel Stammdaten

    // Speichere den Datensatz

    $sql='Update ir_stammdaten SET hir_zeitstempel = NOW() where hir_id = '.$uir_id ;
    
     if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
        
    // Zurueck zur Liste

    header('Location: ir_neu.php?hir_id=' . $Daten['uir_hirid']);
    exit;
    }
?>