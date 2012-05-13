<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################

# Definiere Variablen

$aktueller_monat=date('m');

$aktuelles_jahr=date('Y');

if (isset($_REQUEST['xMonth']))
    {
    $xMonth=$_REQUEST['xMonth'];
    }
else
    {
    $xMonth=date('m');
    }

if (isset($_REQUEST['xYear']))
    {
    $xYear=$_REQUEST['xYear'];
    }
else
    {
    $xYear=date('Y');
    }

# Definiere Ruecksprung

echo '<form action="' . $ruecksprung . '" method="post">';

# Frage Zeitraum ab

echo '<table border=0>';

echo '<tr>';

echo '<td class="text_klein" valign="top" colspan="2">Bitte den gewünschten Monat wählen:</td></tr>';

echo '<tr>';

echo '<td align="right">';

echo '<select size="1" name="xMonth">';

for ($i=1; $i <= 12; $i++)
    {
    if ($xMonth == $i)
        {
        echo '<option value="' . $i . '" selected><span class="text">' . $i . '</span></option>';
        }
    else
        {
        echo '<option value="' . $i . '"><span class="text">' . $i . '</span></option>';
        }
    }

echo '</select> ';

echo '</td>';

$start_year=$Jahr[0];
$end_year=$Jahr[count($Jahr) - 1];

echo '<td>';

echo '<select size="1" name="xYear">';

for ($i=$start_year; $i <= $end_year; $i++)
    {
    if ($xYear == $i)
        {
        echo '<option value="' . $i . '" selected><span class="text">' . $i . '</span></option>';
        }
    else
        {
        echo '<option value="' . $i . '"><span class="text">' . $i . '</span></option>';
        }
    }

echo '</select> ';

echo '</td>';

echo '<td align="right">';

echo '<input type="submit" value="Zeige Ansicht" class="formularbutton" name="check"/>';

echo '</td></tr>';

echo '</table>';

echo '</form>';

echo '<br>';
?>
