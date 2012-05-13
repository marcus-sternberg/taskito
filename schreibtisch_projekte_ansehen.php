<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');


//if(isset($_POST['hpr_id'])) {$hpr_id=$_POST['hpr_id'];}
if (isset($_REQUEST['hpr_id']))
    {
    $hpr_id=$_REQUEST['hpr_id'];
    }

if (!isset($_POST['speichern']))
    {
    require_once('segment_kopf.php');

    echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Projektdetails ?ndern<br><br>';

    $sql='SELECT * FROM projekte ' .
        'LEFT JOIN mitarbeiter ON hpr_inhaber = hma_id ' .
        'WHERE hpr_id = ' . $hpr_id;


    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        echo '<form action="schreibtisch_projekte_ansehen.php" method="post">';

        echo '<table border="0" cellspacing="5" cellpadding="0">';

        echo '<input type="hidden" name="hpr_id" value="' . $hpr_id . '">';

        echo '<tr>';

        echo '<td class="text_klein">Titel: </td><td><input type="text" name="hpr_titel" value="' . $zeile['hpr_titel']
            . '" style="width:340px;"></td>';

        echo '</tr>';

        echo '<tr>';

        echo
            '<td class="text_klein" valign="top">Beschreibung:&nbsp;&nbsp;</td><td><textarea cols="40" rows="5" name="hpr_beschreibung">'
            . htmlspecialchars($zeile['hpr_beschreibung']) . '</textarea></td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein">Plan-Start: </td><td><input type="text" name="hpr_start"  value="'
            . datum_anzeigen($zeile['hpr_start']) . '" style="width:100px;"></td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein">Plan-Ende: </td><td><input type="text" name="hpr_pende" value="'
            . datum_anzeigen($zeile['hpr_pende']) . '" style="width:100px;"></td>';

        echo '</tr>';

        echo
            '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Änderungen speichern" class="formularbutton" /></td></tr>';

        echo '</table>';

        echo '</form>';
        }


    /////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////

    }
else
    { // Formular wurde abgeschickt - beginne Pr?fung Eingaben
    foreach ($_POST as $varname => $value)
        {

        $Daten[$varname]=$value;
        }
    $anzahl_fehler=0;

    //print_r ($_POST);

    if (empty($Daten['hpr_start']))
        {
        $anzahl_fehler++;
        $fehlermeldung1['hpr_start']='Bitte ein Datum eingeben!';
        }
    else
        {

        list($anzahl_fehler,
            $fehlermeldung1['hpr_start'])=datum_check($Daten['hpr_start'], 'hpr_start', $anzahl_fehler);
        }

    if (empty($Daten['hpr_pende']))
        {
        $anzahl_fehler++;
        $fehlermeldung1['hpr_pende']='Bitte ein Datum eingeben!';
        }
    else
        {

        list($anzahl_fehler,
            $fehlermeldung1['hpr_pende'])=datum_check($Daten['hpr_pende'], 'hpr_pende', $anzahl_fehler);
        }

    if ($Daten['hpr_titel'] == '')
        {
        $anzahl_fehler++;
        $fehlermeldung1['hpr_titel']='Bitte einen Titel f?r das Projekt vergeben!';
        }
    else
        {
        $fehlermeldung1['hpr_titel']='';
        }

    if ($anzahl_fehler > 0)
        { // Ein Datum war falsch, fordere Eingabe neu an
        require_once('segment_kopf.php');

        echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Projektdetails ?ndern<br><br>';

        echo '<form action="schreibtisch_projekte_ansehen.php" method="post">';

        echo '<table border="0" cellspacing="5" cellpadding="0">';

        echo '<input type="hidden" name="hpr_id" value="' . $hpr_id . '">';

        echo '<tr>';

        echo '<td class="text_klein">';

        echo '<span class="text_rot">' . $fehlermeldung1['hpr_titel'] . '</span><br>';

        echo 'Titel: </td><td><input type="text" name="hpr_titel" style="width:340px;" value="' . $_POST['hpr_titel']
            . '"></td>';

        echo '</tr>';

        echo '<tr>';

        echo
            '<td class="text_klein" valign="top">Beschreibung:&nbsp;&nbsp;</td><td><textarea cols="40" rows="5" name="hpr_beschreibung">'
            . htmlspecialchars($_POST['hpr_beschreibung']) . '</textarea></td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein">';

        echo '<span class="text_rot">' . $fehlermeldung1['hpr_start'] . '</span><br>';

        echo 'Plan-Start: </td><td><input type="text" name="hpr_start" value="' . $_POST['hpr_start']
            . '" style="width:100px;"></td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein">';

        echo '<span class="text_rot">' . $fehlermeldung1['hpr_pende'] . '</span><br>';

        echo 'Plan-Ende: </td><td><input type="text" name="hpr_pende" value="' . $_POST['hpr_pende']
            . '" style="width:100px;"></td>';

        echo '</tr>';

        echo
            '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Änderungen speichern" class="formularbutton" /></td></tr>';

        echo '</table>';

        echo '</form>';

        exit;
        }

    //} // Ende Check, ob Aufgaben vorhanden

    $Daten['hpr_start']=pruefe_datum($Daten['hpr_start']);
    $Daten['hpr_pende']=pruefe_datum($Daten['hpr_pende']);

    // Lege Projekt an

    $sql='UPDATE projekte SET ' .
        'hpr_titel = "' . mysql_real_escape_string($Daten['hpr_titel']) . '", ' .
        'hpr_beschreibung = "' . mysql_real_escape_string($Daten['hpr_beschreibung']) . '", ' .
        'hpr_start = "' . $Daten['hpr_start'] . '", ' .
        'hpr_pende = "' . $Daten['hpr_pende'] . '", ' .
        'hpr_techpm = "1", ' .
        'hpr_zeitstempel = NOW() ' .
        'WHERE hpr_id = ' . $Daten['hpr_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }


    // Zurueck zur Liste

    header('Location: schreibtisch_projekte.php');
    exit;
    }
?>