<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');


// Definiere Variable

$projektjobs=array();

// var_dump($_POST);

if (!isset($_POST['speichern']))
    {
    require_once('segment_kopf.php');

    echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Erstelle ein neues Projekt<br><br>';

    echo '<form action="schreibtisch_projekte_neu.php" method="post">';

    echo '<table border="0" cellspacing="5" cellpadding="0">';

    echo '<tr>';

    echo '<td class="text_klein">Titel: </td><td><input type="text" name="hpr_titel" style="width:340px;"></td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein" valign="top">Beschreibung:&nbsp;&nbsp;</td><td><textarea cols="40" rows="5" name="hpr_beschreibung"></textarea></td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein">Plan-Start: </td><td>';

    echo
        "<input type='text' name='hpr_start' style='width:100px;' id='hpr_start'><img src='bilder/date_go.gif' alt='Anklicken für Kalenderansicht' title='Anklicken für Kalenderansicht' onclick='kalender(document.getElementById(\"hpr_start\"));'/></td>";

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text_klein">Plan-Ende: </td><td>';

    echo
        "<input type='text' name='hpr_pende' style='width:100px;' id='hpr_pende'><img src='bilder/date_go.gif' alt='Anklicken für Kalenderansicht' title='Anklicken für Kalenderansicht' onclick='kalender(document.getElementById(\"hpr_pende\"));'/></td>";

    echo '</tr>';

    echo
        '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Projekt anlegen" class="formularbutton" /></td></tr>';

    echo '</table>';

    echo '</form>';

    /////////////////////////////////////////////////////////////////////////

    }
else
    { // Formular wurde abgeschickt - beginne Prüfung Eingaben

    /////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////
    foreach ($_POST as $varname => $value)
        {
        $Daten[$varname]=$value;
        }
    $anzahl_fehler=0;

    // print_r ($_POST);

    if (empty($Daten['hpr_start']))
        {
        $anzahl_fehler++;
        $fehlermeldung1['hpr_start']='Bitte ein Datum eintragen!';
        }
    else
        {

        list($anzahl_fehler,
            $fehlermeldung1['hpr_start'])=datum_check($Daten['hpr_start'], 'hpr_start', $anzahl_fehler);
        }

    if (empty($Daten['hpr_pende']))
        {
        $anzahl_fehler++;
        $fehlermeldung1['hpr_pende']='Bitte ein Datum eintragen!';
        }
    else
        {

        list($anzahl_fehler,
            $fehlermeldung1['hpr_pende'])=datum_check($Daten['hpr_pende'], 'hpr_pende', $anzahl_fehler);
        }

    if ($Daten['hpr_titel'] == '')
        {
        $anzahl_fehler++;
        $fehlermeldung1['hpr_titel']='Bitte einen Titel für das Projekt festlegen!';
        }
    else
        {
        $fehlermeldung1['hpr_titel']='';
        }

    // Prüfe, ob überhaupt Aufgaben angelegt wurden

    if (!empty($Daten['uprj_id']))
        {

        // Ja, es gab Aufgaben, überprüfe die Datumseingaben

        foreach ($Daten['uprj_id'] as $key => $content)
            {
            if (empty($Daten['hau_pende'][$key]))
                {
                $Daten['hau_pende'][$key]='9999-01-01';
                }
            else
                {
                list($anzahl_fehler, $fehlermeldung['hau_pende'][$key])=
                    datum_check($Daten['hau_pende'][$key], 'blabla', $anzahl_fehler);
                $Daten['hau_pende'][$key]=pruefe_datum($Daten['hau_pende'][$key]);
                }
            }
        }

    if ($anzahl_fehler > 0)
        { // Ein Datum war falsch, fordere Eingabe neu an
        require_once('segment_kopf.php');

        echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Erstelle ein neues Projekt<br><br>';

        echo '<form action="schreibtisch_projekte_neu.php" method="post">';

        echo '<table border="0" cellspacing="5" cellpadding="0">';

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
            '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Projekt anlegen" class="formularbutton" /></td></tr>';

        echo '</table>';

        echo '</form>';

        exit;
        }


    //} // Ende Check, ob Aufgaben vorhanden

    $Daten['hpr_start']=pruefe_datum($Daten['hpr_start']);
    $Daten['hpr_pende']=pruefe_datum($Daten['hpr_pende']);

    // Lege Projekt an

    $sql='INSERT INTO projekte (' .
        'hpr_id, ' .
        'hpr_titel, ' .
        'hpr_beschreibung, ' .
        'hpr_techpm, ' .
        'hpr_inhaber, ' .
        'hpr_start, ' .
        'hpr_pende, ' .
        'hpr_zeitstempel, ' .
        'hpr_aktiv,
        hpr_sort) ' .
        'VALUES ( ' .
        'NULL, ' .
        '"' . mysql_real_escape_string($Daten['hpr_titel']) . '", ' .
        '"' . mysql_real_escape_string($Daten['hpr_beschreibung']) . '", ' .
        '"' . $_SESSION['hma_id'] . '", ' .
        '"' . $_SESSION['hma_id'] . '", ' .
        '"' . $Daten['hpr_start'] . '", ' .
        '"' . $Daten['hpr_pende'] . '", ' .
        'NOW(), ' .
        '"1",
        "99")';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }


    // Zurueck zur Liste

    header('Location: schreibtisch_projekte.php');
    exit;
    }

echo
    '<div id="bn_frame" style="position:absolute; display:none; height:198px; width:205px; background-color:#ced7d6; overflow:hidden;">';

echo
    '<iframe src="bytecal.php" style="width:208px; margin-left:-1px; border:0px; height:202px; background-color:#ced7d6; overflow:hidden;" border="0"></iframe>';

echo '</div>';
?>
