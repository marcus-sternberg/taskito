<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

$Daten=array();

$check=array();
$check_ma=array();
$box=array();
$Anzahl_wota=0;

for ($i=0; $i < 5; $i++)
    {
    $check[$i]='';
    }

for ($i=1; $i < 4; $i++)
    {
    $check_ma[$i]='';
    }

foreach ($WoTa as $nr => $wt)
    {
    $box[$wt]='';
    }

if (isset($_POST['xIntervallwotag']))
    {
    $Anzahl_wota=count($_POST['xIntervallwotag']);

    foreach ($_POST['xIntervallwotag'] as $nr => $wt)
        {
        $box[$nr] = 'checked';
        $Intervall_wochentag=$nr;
        }
    }

if (!isset($_POST['speichern']))
    {
    require_once('segment_kopf.php');

    echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Erzeuge Dauerauftrag<br><br>';

    echo '<form action="schreibtisch_serienaufgabe.php" method="post">';

    echo '<table border="0" cellspacing="5" cellpadding="0">';

    echo '<tr>';

    echo '<td class="text" colspan="2" bgcolor="#c9c9c9">Anzahl Wiederholungen: </td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein"><input type="radio" name="xWiederholung" value="1" checked></td><td class="text_klein">endlos</td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein"><input type="radio" name="xWiederholung" value="2"></td><td class="text_klein"><input type="text" name="xWiederholung_eingabe" style="width:30px;"> Mal</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text" colspan="2" bgcolor="#c9c9c9">Startdatum der Aufgabe: </td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein"><input type="radio" name="xStartdatum" value="1" checked></td><td class="text_klein">Ab sofort</td>';

    echo '</tr>';

    echo '<tr>';

    echo
        "<td class='text_klein'><input type='radio' name='xStartdatum' value='2'></td><td class='text_klein'>Starte zum <input type='text' name='xStartdatum_eingabe' style='width:100px;'  id='xStartdatum_eingabe'><img src='bilder/date_go.gif' alt='Anklicken für Kalenderansicht' onclick='kalender(document.getElementById(\"xStartdatum_eingabe\"));'/></td>";

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text" colspan="2" bgcolor="#c9c9c9">Fällig: </td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein"><input type="radio" name="xPlanende" value="1" checked></td><td class="text_klein">Am gleichen Tag</td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein"><input type="radio" name="xPlanende" value="2"></td><td class="text_klein">fällig nach <input type="text" name="xPlanende_eingabe" style="width:30px;"> Kalendertagen nach Einstellung</td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein"><input type="radio" name="xPlanende" value="3"></td><td class="text_klein">offenes Ende</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text" colspan="2" bgcolor="#c9c9c9">Intervall: </td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein"><input type="radio" name="xIntervall" value="0" checked></td><td class="text_klein">täglich</td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein"><input type="radio" name="xIntervall" value="1"></td><td class="text_klein">Jeden <input type="text" name="xIntervallwert1" style="width:30px;">. Tag</td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein"><input type="radio" name="xIntervall" value="2"></td><td class="text_klein">Jede <input type="text" name="xIntervallwert2" style="width:30px;">. Woche</td></tr>';

    echo '<tr><td class="text_klein">&nbsp;</td><td class="text_klein">';

    echo '<input type="checkbox" name="xIntervallwotag[1]" value="mo">Mo ';

    echo '<input type="checkbox" name="xIntervallwotag[2]" value="tu"> Di ';

    echo '<input type="checkbox" name="xIntervallwotag[3]" value="we"> Mi ';

    echo '<input type="checkbox" name="xIntervallwotag[4]" value="Th"> Do ';

    echo '<input type="checkbox" name="xIntervallwotag[5]" value="fr"> Fr ';

    echo '</td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein"><input type="radio" name="xIntervall" value="3"></td><td class="text_klein">Jeden <input type="text" name="xIntervalltag3" style="width:30px;">. Tag des Monats</td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein"><input type="radio" name="xIntervall" value="4"></td><td class="text_klein">Jedes <input type="text" name="xIntervallwert4" style="width:30px;">. Jahr am <input type="text" name="xIntervallday4" style="width:30px;">. Tag des <input type="text" name="xIntervallmonth4" style="width:30px;">. Monats</td>';

    echo '</tr>';

    echo '<tr>';

    echo '<td class="text" colspan="2" bgcolor="#c9c9c9">Ausführung durch: </td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein"><input type="radio" name="xZuordnung" value="1" checked></td><td class="text_klein">In den Pool geben</td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein"><input type="radio" name="xZuordnung" value="2"></td><td class="text_klein">Selbst übernehmen</td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein" colspan="2"><br><br><input type="submit" name="speichern" value="Erzeuge Dauerauftrag" class="formularbutton" /></td>';

    echo '</tr>';

    echo '</table';

    echo '</form>';
    }
else
    {

    // Code Anlage

    $fehlermeldung=array();
    $anzahl_fehler=0;
    $Daten=array();


    // Lese das uebergebene Feld aus

    foreach ($_POST as $varname => $value)
        {
        $Daten[$varname]=$value;
        }

    $Daten['xWiederholung_eingabe']=abs($Daten['xWiederholung_eingabe']);
    $Daten['xPlanende_eingabe']=abs($Daten['xPlanende_eingabe']);


    // Pruefe die Eingaben

    if ($Daten['xWiederholung'] == 2 AND ($Daten['xWiederholung_eingabe']) == 0)
        {
        $anzahl_fehler++;
        $fehlermeldung['xWiederholung']='Bitte gib die Nummer der Wiederholungen an!';
        }
    else
        {
        $fehlermeldung['xWiederholung']='';
        }

    if ($Daten['xPlanende'] == 2 AND ($Daten['xPlanende_eingabe']) == 0)
        {
        $anzahl_fehler++;
        $fehlermeldung['xPlanende']='Bitte gib die Tage bis zum Fälligwerden an!';
        }
    else
        {
        $fehlermeldung['xPlanende']='';
        }

    if ($Daten['xStartdatum'] == 2 AND $Daten['xStartdatum_eingabe'] == '')
        {
        $anzahl_fehler++;
        $fehlermeldung['xStartdatum']='Bitte gib ein Startdatum an!';
        }
    else
        {

        list($anzahl_fehler,
            $fehlermeldung['xStartdatum'])=datum_check($Daten['xStartdatum_eingabe'], 'xStartdatum_eingabe',
            $anzahl_fehler);
        }

    if ($Daten['xZuordnung'] == 3 AND $Daten['xhma_id'] == '-1')
        {
        $anzahl_fehler++;
        $fehlermeldung['xZuordnung']='Bitte ordne die Aufgabe zu!';
        }
    else
        {
        $fehlermeldung['xZuordnung']='';
        }

    switch ($Daten['xIntervall'])
        {
        case 1:
            if ($Daten['xIntervallwert1'] == 0)
                {
                $anzahl_fehler++;
                $fehlermeldung['xIntervall']='Bitte gib an, nach wieviel Tagen die Aufgabe erneut erzeugt werden soll!';
                }
            else
                {
                $fehlermeldung['xIntervall']='';
                }

            $Daten['xIntervallwert']=$Daten['xIntervallwert1'];

            break;

        case 2:
            if ($Daten['xIntervallwert2'] == 0)
                {
                $anzahl_fehler++;
                $fehlermeldung['xIntervall']=
                    'Bitte gib an, nach wievielen Wochen die Aufgabe erneut erzeugt werden soll!';
                }
            else if (!isset($Daten['xIntervallwotag']))
                {
                $anzahl_fehler++;
                $fehlermeldung['xIntervall']='Bitte kreuze einen Wochentag an, zu dem die Aufgabe erzeugt werden soll!';
                }
            else if ($Anzahl_wota > 1)
                {
                $anzahl_fehler++;
                $fehlermeldung['xIntervall']='Bitte kreuze nur einen Wochentag an!';
                }
            else
                {
                $fehlermeldung['xIntervall']='';
                }

            $Daten['xIntervallwert']=$Daten['xIntervallwert2'];

            break;

        case 3:
            if ($Daten['xIntervalltag3'] == 0 OR $Daten['xIntervalltag3'] > 31)
                {
                $anzahl_fehler++;
                $fehlermeldung['xIntervall']='Bitte gib den Tag des Monats an, zu dem die Aufgabe erzeugt werden soll!';
                }
            else
                {
                $fehlermeldung['xIntervall']='';
                }

            $Daten['xIntervalltag']=$Daten['xIntervalltag3'];
            $Daten['xIntervallwert']=1;
            break;

        case 4:
            if ($Daten['xIntervallday4'] == 0 OR $Daten['xIntervallday4']
                > 31 OR $Daten['xIntervallmonth4'] == 0 OR $Daten['xIntervallmonth4'] > 12)
                {
                $anzahl_fehler++;
                $fehlermeldung['xIntervall']=
                    'Bitte gib den Tag des Monats und den Monat an, zu dem die Aufgabe erneut erzeugt werden soll!';
                }
            else
                {
                $fehlermeldung['xIntervall']='';
                }

            $Daten['xIntervallwert']=$Daten['xIntervallwert4'];
            $Daten['xIntervalltag']=$Daten['xIntervallday4'] . '-' . $Daten['xIntervallmonth4'];

            break;

            default:
    $fehlermeldung['xIntervall']='';
        }

    if (isset($Daten['xIntervalltag']))
        {
        if ($Daten['xIntervall'] != 4)
            {
            $Daten['xIntervalltag']=abs($Daten['xIntervalltag']);
            }
        }
    else
        {
        $Daten['xIntervalltag']=0;
        }

    if (isset($Daten['xIntervallwert']))
        {
        $Daten['xIntervallwert']=abs($Daten['xIntervallwert']);
        }
    else
        {
        $Daten['xIntervallwert']=0;
        }

    if ($anzahl_fehler > 0)
        {

        require_once('segment_kopf.php');

        echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Erzeuge Dauerauftrag<br><br>';

        echo '<form action="schreibtisch_serienaufgabe.php" method="post">';

        echo '<table border="0" cellspacing="5" cellpadding="0">';

        echo '<tr>';

        echo '<td class="text" colspan="2" bgcolor="#c9c9c9">Anzahl der Wiederholungen: </td>';

        echo '</tr>';

        switch ($Daten['xWiederholung'])
            {
            case 1:
                echo '<tr>';

                echo
                    '<td class="text_klein"><input type="radio" name="xWiederholung" value="1" checked></td><td class="text_klein">endlos</td>';

                echo '</tr>';

                echo '<tr>';

                echo
                    '<td class="text_klein"><input type="radio" name="xWiederholung" value="2"></td><td class="text_klein"><input type="text" name="xWiederholung_eingabe" style="width:30px;" value="'
                    . $Daten['xWiederholung_eingabe'] . '"> Mal</td>';

                echo '</tr>';

                break;

            case 2:
                echo '<tr>';

                echo
                    '<td class="text_klein"><input type="radio" name="xWiederholung" value="1"></td><td class="text_klein">endlos</td>';

                echo '</tr>';

                echo '<tr>';

                echo
                    '<td class="text_klein"><input type="radio" name="xWiederholung" value="2" checked></td><td class="text_klein"><input type="text" name="xWiederholung_eingabe" style="width:30px;"  value="'
                    . $Daten['xWiederholung_eingabe'] . '"> Mal</td>';

                echo '</tr>';
                break;
            }

        echo '<tr><td colspan="2" class="text_rot">&nbsp;&nbsp;' . $fehlermeldung['xWiederholung'] . '</td></tr>';

        echo '<tr>';

        echo '<td class="text" colspan="2" bgcolor="#c9c9c9">Startdatum der Aufgabe: </td>';

        echo '</tr>';

        switch ($Daten['xStartdatum'])
            {
            case 1:
                echo '<tr>';

                echo
                    '<td class="text_klein"><input type="radio" name="xStartdatum" value="1" checked></td><td class="text_klein">Ab sofort!</td>';

                echo '</tr>';

                echo '<tr>';

                echo
                    "<td class='text_klein'><input type='radio' name='xStartdatum' value='2'></td><td class='text_klein'>Starte am <input type='text' name='xStartdatum_eingabe' style='width:100px;'  value='"
                    . $Daten['xStartdatum_eingabe']
                        . "' id='xStartdatum_eingabe'><img src='bilder/date_go.gif' alt='Anklicken für Kalenderansicht' onclick='kalender(document.getElementById(\"xStartdatum_eingabe\"));'/></td>";

                echo '</tr>';
                break;

            case 2:
                echo '<tr>';

                echo
                    '<td class="text_klein"><input type="radio" name="xStartdatum" value="1"></td><td class="text_klein">Ab sofort!</td>';

                echo '</tr>';

                echo '<tr>';

                echo
                    "<td class='text_klein'><input type='radio' name='xStartdatum' value='2' checked></td><td class='text_klein'>Starte am <input type='text' name='xStartdatum_eingabe' style='width:100px;'  value='"
                    . $Daten['xStartdatum_eingabe']
                        . "' id='xStartdatum_eingabe'><img src='bilder/date_go.gif' alt='Anklicken für Kalenderansicht' onclick='kalender(document.getElementById(\"xStartdatum_eingabe\"));'/></td>";

                echo '</tr>';
                break;
            }

        echo '<tr><td colspan="2" class="text_rot">&nbsp;&nbsp;' . $fehlermeldung['xStartdatum'] . '</td></tr>';

        echo '<tr>';

        echo '<td class="text" colspan="2" bgcolor="#c9c9c9">Fälligkeit der Aufgabe: </td>';

        echo '</tr>';

        switch ($Daten['xPlanende'])
            {
            case 1:
                echo '<tr>';

                echo
                    '<td class="text_klein"><input type="radio" name="xPlanende" value="1" checked></td><td class="text_klein">Am gleichen Tag</td>';

                echo '</tr>';

                echo '<tr>';

                echo
                    '<td class="text_klein"><input type="radio" name="xPlanende" value="2"></td><td class="text_klein">fällig <input type="text" name="xPlanende_eingabe" style="width:30px;" value="'
                    . $Daten['xPlanende_eingabe'] . '">  Kalendertage nach Einstellen</td>';

                echo '</tr>';

                echo '<tr>';

                echo
                    '<td class="text_klein"><input type="radio" name="xPlanende" value="3"></td><td class="text_klein">offenes Ende</td>';

                echo '</tr>';
                break;

            case 2:
                echo '<tr>';

                echo
                    '<td class="text_klein"><input type="radio" name="xPlanende" value="1"></td><td class="text_klein">Am gleichen Tag</td>';

                echo '</tr>';

                echo '<tr>';

                echo
                    '<td class="text_klein"><input type="radio" name="xPlanende" value="2" checked></td><td class="text_klein">fällig <input type="text" name="xPlanende_eingabe" style="width:30px;" value="'
                    . $Daten['xPlanende_eingabe'] . '">  Kalendertage nach Einstellen</td>';

                echo '</tr>';

                echo '<tr>';

                echo
                    '<td class="text_klein"><input type="radio" name="xPlanende" value="3"></td><td class="text_klein">offenes Ende</td>';

                echo '</tr>';
                break;

            case 3:
                echo '<tr>';

                echo
                    '<td class="text_klein"><input type="radio" name="xPlanende" value="1"></td><td class="text_klein">Am gleichen Tag</td>';

                echo '</tr>';

                echo '<tr>';

                echo
                    '<td class="text_klein"><input type="radio" name="xPlanende" value="2"></td><td class="text_klein">fällig <input type="text" name="xPlanende_eingabe" style="width:30px;" value="'
                    . $Daten['xPlanende_eingabe'] . '">  Kalendertage nach Einstellen</td>';

                echo '</tr>';

                echo '<tr>';

                echo
                    '<td class="text_klein"><input type="radio" name="xPlanende" value="3" checked></td><td class="text_klein">offenes Ende</td>';

                echo '</tr>';
                break;
            }

        echo '<tr><td colspan="2" class="text_rot">&nbsp;&nbsp;' . $fehlermeldung['xPlanende'] . '</td></tr>';

        echo '<tr>';

        echo '<td class="text" colspan="2" bgcolor="#c9c9c9">Intervall: </td>';

        echo '</tr>';

        $check[$Daten['xIntervall']]='checked';

        echo '<tr>';

        echo '<td class="text_klein"><input type="radio" name="xIntervall" value="0" ' . $check[0]
            . '></td><td class="text_klein">täglich</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein"><input type="radio" name="xIntervall" value="1" ' . $check[1]
            . '></td><td class="text_klein">Jeden <input type="text" name="xIntervallwert1" style="width:30px;" value="'
            . $Daten['xIntervallwert1'] . '">. Tag</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein"><input type="radio" name="xIntervall" value="2" ' . $check[2]
            . '></td><td class="text_klein">Jede <input type="text" name="xIntervallwert2" style="width:30px;" value="'
            . $Daten['xIntervallwert2'] . '">. Woche</td></tr>';

        echo '<tr><td class="text_klein">&nbsp;</td><td class="text_klein">';

        echo '<input type="checkbox" name="xIntervallwotag[1]" value="mo" ' . $box['1'] . '>Mo ';

        echo '<input type="checkbox" name="xIntervallwotag[2]" value="tu" ' . $box['2'] . '> Di ';

        echo '<input type="checkbox" name="xIntervallwotag[3]" value="we" ' . $box['3'] . '> Mi ';

        echo '<input type="checkbox" name="xIntervallwotag[4]" value="th" ' . $box['4'] . '> Do ';

        echo '<input type="checkbox" name="xIntervallwotag[5]" value="fr" ' . $box['5'] . '> Fr ';

        echo '</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein"><input type="radio" name="xIntervall" value="3" ' . $check[3]
            . '></td><td class="text_klein">Jeden <input type="text" name="xIntervalltag3" style="width:30px;" value="'
            . $Daten['xIntervalltag3'] . '">. Tag im Monat</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein"><input type="radio" name="xIntervall" value="4" ' . $check[4]
            . '></td><td class="text_klein">Jedes  <input type="text" name="xIntervallwert4" style="width:30px;">. Jahr am <input type="text" name="xIntervallday4" style="width:30px;" value="'
            . $Daten['xIntervallday4']
            . '">. Tag des <input type="text" name="xIntervallmonth4" style="width:30px;" value="'
            . $Daten['xIntervallmonth4'] . '">. Monats</td>';

        echo '</tr>';

        echo '<tr><td colspan="2" class="text_rot">&nbsp;&nbsp;' . $fehlermeldung['xIntervall'] . '</td></tr>';

        $check_ma[$Daten['xZuordnung']]='checked';

        echo '<tr>';

        echo '<td class="text" colspan="2" bgcolor="#c9c9c9">Mapping: </td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein"><input type="radio" name="xZuordnung" value="1" ' . $check_ma[1]
            . '></td><td class="text_klein">In den Pool legen</td>';

        echo '</tr>';

        echo '<tr>';

        echo '<td class="text_klein"><input type="radio" name="xZuordnung" value="2" ' . $check_ma[2]
            . '></td><td class="text_klein">Selbst übernehmen</td>';

        echo '</tr>';

        echo '<tr>';

        echo
            '<td class="text_klein" colspan="2"><br><br><input type="submit" name="speichern" value="Erzeuge Dauerauftrag" class="formularbutton" /></td>';

        echo '</tr>';

        echo '</table';

        echo '</form>';
        }
    else
        {

        $Aufgabendaten=array();

        # Lese die Daten wieder aus

        foreach ($_SESSION['Daten'] AS $schluessel => $inhalt)
            {
            $Aufgabendaten[$schluessel]=$inhalt;
            }


        # First find out the start-date of the recurring task
        # Check, what option was choosed
        # if NOW was choosen the date is current date
        # if a date was given then we need to check if the format is ok

        if ($Daten['xStartdatum'] == 1)
            {
            $Startdatum_neue_aufgabe=date("Y-m-d");
            }
        else
            {
            list($anzahl_fehler, $fehlermeldung['xStartdatum_eingabe'])=
                datum_check($Daten['xStartdatum_eingabe'], 'xStartdatum_eingabe', 0);

            if ($anzahl_fehler == 0)
                {
                $Startdatum_neue_aufgabe=$Daten['xStartdatum_eingabe'];
                }
            $Startdatum_neue_aufgabe=datum_wandeln_euus($Startdatum_neue_aufgabe);
            }

        # OK, we have now a valid date from where to begin
        # Next is to check depending on the choice if the startdate meets the requirements
        # We will check this for each kind of interval

        switch ($Daten['xIntervall'])
            {
            case 0: // Daily creation
                # If the Startdate is in the past set it to current date
                if ($Startdatum_neue_aufgabe < date("Y-m-d"))
                    {
                    $Startdatum_neue_aufgabe=date("Y-m-d");
                    }
                break;

            case 1: // Every n. day

                # As long as the startdate is in the past add the interval n to it

                while ($Startdatum_neue_aufgabe < date("Y-m-d"))
                    {
                    $fDatum = explode("-", $Startdatum_neue_aufgabe);

                    echo $Startdatum_neue_aufgabe . '#' . $fDatum[2] . '#' . $Daten['xIntervallwert1'] . '<br>';

                    $Startdatum_neue_aufgabe=
                        date("Y-m-d", mktime(0, 0, 0, $fDatum[1], $fDatum[2] + $Daten['xIntervallwert1'], $fDatum[0]));
                    }
                break;

            case 2: // Weekly

                # First we change the date into an array to examine the components

                $Startdatum_array=getdate(strtotime($Startdatum_neue_aufgabe));

                # Grab the weekday from the form-array (userchoice)

                foreach ($Daten['xIntervallwotag'] AS $key => $content)
                    {
                    $xIntervallwota=$key;
                    }

                // echo 'Benutzer: '.$key.' # System: '.$Startdatum_array['wday'];

                # Now we check if we have the right weekday compared to the choice of the user (6=Samstag, 0= Sonntag)

                while ($Startdatum_array['wday'] != $key)
                    {
                    $Startdatum_neue_aufgabe = strftime("%Y-%m-%d", strtotime($Startdatum_neue_aufgabe . '+1 day'));
                    $Startdatum_array=getdate(strtotime($Startdatum_neue_aufgabe));
                    }

                # Now we have the correct date of the next weekday that is equal to the choice since the startdate
                # Next is to check, if the date is in the past - if so we have to add an interval and check again

                while ($Startdatum_neue_aufgabe < date("Y-m-d"))
                    {
                    $fDatum = explode("-", $Startdatum_neue_aufgabe);
                    $Startdatum_neue_aufgabe=date("Y-m-d",
                        mktime(0, 0, 0, $fDatum[1], $fDatum[2] + $Daten['xIntervallwert2'] * 7, $fDatum[0]));
                    }
                $Daten['xIntervalltag']=$key;
                break;

            case 3: // monthly

                # Lets set the startdate to the desired day

                $fDatum=explode("-", $Startdatum_neue_aufgabe);
                $Startdatum_neue_aufgabe=
                    date("Y-m-d", mktime(0, 0, 0, $fDatum[1], $Daten['xIntervalltag3'], $fDatum[0]));


                # Now lets check if the date is past, if so, jump on a month

                while ($Startdatum_neue_aufgabe < date("Y-m-d"))
                    {
                    $fDatum = explode("-", $Startdatum_neue_aufgabe);
                    $Startdatum_neue_aufgabe=date("Y-m-d", mktime(0, 0, 0, $fDatum[1] + 1, $fDatum[2], $fDatum[0]));
                    }

                # Now lets check if the date is appropriate if a special start date is fixed

                if ($Daten['xStartdatum'] == 2)
                    {
                    while ($Startdatum_neue_aufgabe < datum_wandeln_euus($Daten['xStartdatum_eingabe']))
                        {
                        $fDatum = explode("-", $Startdatum_neue_aufgabe);
                        $Startdatum_neue_aufgabe=date("Y-m-d", mktime(0, 0, 0, $fDatum[1] + 1, $fDatum[2], $fDatum[0]));
                        }
                    }

                break;

            case 4: //yearly

                # First lets build a date from the input

                if ($Daten['xStartdatum'] == 2)
                    {
                    $fDatum=explode(".", $Daten['xStartdatum_eingabe']);
                    $Startdatum_neue_aufgabe=
                        $fDatum[2] . '-' . $Daten['xIntervallmonth4'] . '-' . $Daten['xIntervallday4'];
                    }
                else
                    {
                    $Startdatum_neue_aufgabe=date("Y") . '-' . $Daten['xIntervallmonth4'] . '-'
                        . $Daten['xIntervallday4'];
                    }
                # We change the date from now and the built date into timestamps to compare them

                $Datum_jahr=strtotime($Startdatum_neue_aufgabe);
                $Datum_jetzt=time();

                # Lets check, if the date is past. If so, add n years

                if ($Datum_jahr < $Datum_jetzt)
                    {
                    $Startdatum_neue_aufgabe=strftime("%Y-%m-%d",
                        strtotime($Startdatum_neue_aufgabe . '+' . $Daten['xIntervallwert4'] . ' year'));
                    }

                $Datum_jahr=strtotime($Startdatum_neue_aufgabe);
                $Datum_jetzt=strtotime(($Daten['xStartdatum_eingabe']));

                if ($Daten['xStartdatum'] == 2)
                    {
                    if ($Datum_jahr < $Datum_jetzt)
                        {
                        $Startdatum_neue_aufgabe=strftime("%Y-%m-%d",
                            strtotime($Startdatum_neue_aufgabe . '+' . $Daten['xIntervallwert4'] . ' year'));
                        }
                    }
            }
        #So, nun pruefen wir noch, ob es sich ums Wochenende handelt und addieren ggf. Tage
        # Dazu wandeln wir zunächst das Datum in ein Feld um mit den einzelnen Komponenten des Datum
        # Vorher heben wir das originale Startdatum auf

        $xStartdatum_original=$Startdatum_neue_aufgabe;

        $Startdatum_array=getdate(strtotime($Startdatum_neue_aufgabe));

        # Nun nehmen wir davon den Wochentag und prüfen auf Wochenende (6=Samstag, 0= Sonntag)

        while ($Startdatum_array['wday'] == 0 OR $Startdatum_array['wday'] == 6)
            {
            # Anscheinend haben wir ein Wochenende erwischt, addiere einen Tag dazu und prüfe nochmal

            $Startdatum_neue_aufgabe = strftime("%Y-%m-%d", strtotime($Startdatum_neue_aufgabe . '+1 day'));
            $Startdatum_array=getdate(strtotime($Startdatum_neue_aufgabe));
            }

        include('seg_enddatum_berechnen.php');


        # Prüfen, ob die Aufgabe heute angelegt werden muß

        if ($Startdatum_neue_aufgabe == date("Y-m-d"))
            {


            // Aufgabe anlegen - Teamleiter und Status definieren (selbst / Pool)

            switch ($Daten['xZuordnung'])
                {
                case 1:
                    $sql='INSERT INTO aufgaben (' .
                        'hau_titel, ' .
                        'hau_beschreibung, ' .
                        'hau_anlage, ' .
                        'hau_inhaber, ' .
                        'hau_prio, ' .
                        'hau_pende, ' .
                        'hau_zeitstempel, ' .
                        'hau_aktiv, ' .
                        'hau_terminaendern, ' .
                        'hau_teamleiter, ' .
                        'hau_datumstyp, ' .
                        'hau_hprid, ' .
                        'hau_typ, ' .
                        'hau_tl_status, ' .
                        'hau_ticketnr) ' .
                        'VALUES ( ' .
                        '"' . mysql_real_escape_string($Aufgabendaten['hau_titel']) . '", ' .
                        '"' . mysql_real_escape_string($Aufgabendaten['hau_beschreibung']) . '", ' .
                        'NOW(), ' .
                        '"' . $_SESSION['hma_id'] . '", ' .
                        '"' . $Aufgabendaten['hau_prio'] . '", ' .
                        '"' . $Planende_neue_aufgabe . '", ' .
                        'NOW(), ' .
                        '"1", ' .
                        '"0", ' .
                        '"0", ' .
                        '"' . $Aufgabendaten['hau_datumstyp'] . '", ' .
                        '"' . $Aufgabendaten['hau_hprid'] . '", ' .
                        '"' . $Aufgabendaten['hau_typ'] . '", ' .
                        '"0", ' .
                        '"' . $Aufgabendaten['hau_ticketnr'] . '")';

                    if (!($ergebnis=mysql_query($sql, $verbindung)))
                        {
                        fehler();
                        }
      
                        
                    $hau_id=mysql_insert_id();

                    $sql='INSERT INTO aufgaben_zuordnung
                            (uaz_hauid, uaz_pg) ' .
                        'VALUES ("' . $hau_id . '", "4")';

                    if (!($ergebnis=mysql_query($sql, $verbindung)))
                        {
                        fehler();
                        }

                    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                        'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                        . '", "Aufgabe automatisch erzeugt", NOW() )';

                    if (!($ergebnis=mysql_query($sql, $verbindung)))
                        {
                        fehler();
                        }

                    break;

                case 2:
                    $sql='INSERT INTO aufgaben (' .
                        'hau_titel, ' .
                        'hau_beschreibung, ' .
                        'hau_anlage, ' .
                        'hau_inhaber, ' .
                        'hau_prio, ' .
                        'hau_pende, ' .
                        'hau_zeitstempel, ' .
                        'hau_aktiv, ' .
                        'hau_terminaendern, ' .
                        'hau_teamleiter, ' .
                        'hau_datumstyp, ' .
                        'hau_hprid, ' .
                        'hau_typ, ' .
                        'hau_tl_status, ' .
                        'hau_ticketnr) ' .
                        'VALUES ( ' .
                        '"' . mysql_real_escape_string($Aufgabendaten['hau_titel']) . '", ' .
                        '"' . mysql_real_escape_string($Aufgabendaten['hau_beschreibung']) . '", ' .
                        'NOW(), ' .
                        '"' . $_SESSION['hma_id'] . '", ' .
                        '"' . $Aufgabendaten['hau_prio'] . '", ' .
                        '"' . $Planende_neue_aufgabe . '", ' .
                        'NOW(), ' .
                        '"1", ' .
                        '"0", ' .
                        '"999", ' .
                        '"' . $Aufgabendaten['hau_datumstyp'] . '", ' .
                        '"' . $Aufgabendaten['hau_hprid'] . '", ' .
                        '"' . $Aufgabendaten['hau_typ'] . '", ' .
                        '"1", ' .
                        '"' . $Aufgabendaten['hau_ticketnr'] . '")';

                    if (!($ergebnis=mysql_query($sql, $verbindung)))
                        {
                        fehler();
                        }


                    $hau_id=mysql_insert_id();

                    $sql='INSERT INTO aufgaben_mitarbeiter (' .
                        'uau_id, ' .
                        'uau_hmaid, ' .
                        'uau_hauid, ' .
                        'uau_status, ' .
                        'uau_prio, ' .
                        'uau_stopp, ' .
                        'uau_tende, ' .
                        'uau_zeitstempel, ' .
                        'uau_ma_status) ' .
                        'VALUES ( ' .
                        'NULL, ' .
                        '"' . $_SESSION['hma_id'] . '", ' .
                        '"' . $hau_id . '", ' .
                        '"0", ' .
                        '"99", ' .
                        '"0", ' .
                        '"' . $Planende_neue_aufgabe . '", ' .
                        'NOW(), ' .
                        '"1")';

                    if (!($ergebnis=mysql_query($sql, $verbindung)))
                        {
                        fehler();
                        }


                    $sql='INSERT INTO aufgaben_zuordnung
                            (uaz_hauid, uaz_pg, uaz_pba) ' .
                        'VALUES ("' . $hau_id . '", "' . $Aufgabendaten['uaz_pg'] . '", "' . $_SESSION['hma_id']
                        . '" )';

                    if (!($ergebnis=mysql_query($sql, $verbindung)))
                        {
                        fehler();
                        }

                    $sql='INSERT INTO kommentare (uko_hau_id, uko_datum, uko_ma, uko_kommentar, uko_zeitstempel) ' .
                        'VALUES ("' . $hau_id . '", "' . date("Y-m-d H:i") . '", "' . $_SESSION['hma_login']
                        . '", "Aufgabe automatisch erzeugt und dem Ersteller zugewiesen", NOW() )';

                    if (!($ergebnis=mysql_query($sql, $verbindung)))
                        {
                        fehler();
                        }

                    break;
                }
            }

        if ($xStartdatum_original <= date("Y-m-d"))
            {
            # So, Aufgabe wurde angelegt, also muß das nächste Startdatum berechnet werden

            $xIntervalltyp=$Daten['xIntervall'];
            $xStarttag=$xStartdatum_original;

            switch ($xIntervalltyp)
                {
                case 0: break;

                case 1:
                    $xIntervallwert=$Daten['xIntervallwert1'];
                    break;

                case 2:
                    $xIntervallwert=$Daten['xIntervallwert2'];
                    break;

                case 3:
                    $xIntervallwert=1;
                    $xIntervalltag=$Daten['xIntervalltag3'];
                    break;

                case 4:
                    $xIntervallmonat=$Daten['xIntervallmonth4'];
                    $xIntervallwert=$Daten['xIntervallwert4'];
                    $xIntervalltag=$Daten['xIntervallday4'];
                    break;
                }

            include('seg_startdatum_berechnen.php');

            $Startdatum_neue_aufgabe=$xStarttag;
            }
        else
            {
            $Startdatum_neue_aufgabe=$xStartdatum_original;
            }

        # Kein $Daten['xhma_id'] gesetzt = kein Mitarbeiter

        if (!isset($Daten['xhma_id']))
            {
            $Daten['xhma_id']=0;
            }

        # Berechne mit neuem Startdatum das Enddatum

        include('seg_enddatum_berechnen.php');

        # Und wir nehmen Umbrüche aus dem Text und ersetzen sie durch HTML-Code

        $sql='INSERT INTO tracker (' .
            'utr_inhaber, ' .
            'utr_next_date, ' .
            'utr_prio, ' .
            'utr_bereich, ' .
            'utr_typ, ' .
            'utr_titel, ' .
            'utr_beschreibung, ' .
            'utr_zuordnung, ' .
            'utr_datumstyp, ' .
            'utr_wiederholung, ' .
            'utr_intervalltyp, ' .
            'utr_intervallwert, ' .
            'utr_intervalltag, ' .
            'utr_hmaid, ' .
            'utr_sid, ' .
            'utr_ticketnr, ' .
            'utr_pende_wert, ' .
            'utr_pende, ' .
            'utr_planende, ' .
            'utr_wiederholungwert, ' .
            'utr_zeitstempel) ' .
            'VALUES ( ' .
            '"' . $_SESSION['hma_id'] . '", ' .
            '"' . $Startdatum_neue_aufgabe . '", ' .
            '"' . $Aufgabendaten['hau_prio'] . '", ' .
            '"' . $Aufgabendaten['uaz_pg'] . '", ' .
            '"' . $Aufgabendaten['hau_typ'] . '", ' .
            '"' . mysql_real_escape_string($Aufgabendaten['hau_titel']) . '", ' .
            '"' . mysql_real_escape_string($Aufgabendaten['hau_beschreibung']) . '", ' .
            '"' . $Daten['xZuordnung'] . '", ' .
            '"' . $Aufgabendaten['hau_datumstyp'] . '", ' .
            '"' . $Daten['xWiederholung'] . '", ' .
            '"' . $Daten['xIntervall'] . '", ' .
            '"' . $Daten['xIntervallwert'] . '", ' .
            '"' . $Daten['xIntervalltag'] . '", ' .
            '"' . $Daten['xhma_id'] . '", ' .
            '"' . $Aufgabendaten['hau_hprid'] . '", ' .
            '"' . $Aufgabendaten['hau_ticketnr'] . '", ' .
            '"' . $Daten['xPlanende_eingabe'] . '", ' .
            '"' . $Planende_neue_aufgabe . '", ' .
            '"' . $Daten['xPlanende'] . '", ' .
            '"' . $Daten['xWiederholung_eingabe'] . '", ' .
            'NOW())';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        switch ($Daten['xZuordnung'])
            {
            case '1':

                header('Location: schreibtisch_meine_auftraege.php');
                exit;
                break;

            case '2':

                header('Location: schreibtisch_meine_aufgaben.php');
                exit;
                break;

            case '3':

                header('Location: schreibtisch_meine_auftraege.php');
                exit;
                break;
            }
        }
    }

echo
    '<div id="bn_frame" style="position:absolute; display:none; height:198px; width:205px; background-color:#ced7d6; overflow:hidden;">';

echo
    '<iframe src="bytecal.php" style="width:208px; margin-left:-1px; border:0px; height:202px; background-color:#ced7d6; overflow:hidden;" border="0"></iframe>';

echo '</div>';

//include('segment_fuss.php');

echo '</body>';

echo '</html>';
?>