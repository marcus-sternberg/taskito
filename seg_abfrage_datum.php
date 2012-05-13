<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################

# Definiere Variablen

$tage_des_monats=date('t');

$aktueller_tag_des_monats=date('d');
$aktueller_monat=date('m');
$aktuelles_jahr=date('Y');

if (isset($_REQUEST['xDay']))
    {
    $xDay=$_REQUEST['xDay'];
    }
else
    {
    $xDay=date('d');
    }

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

if ($xDay > $aktueller_tag_des_monats && $xMonth == date('m'))
    {
    $xDay=$aktueller_tag_des_monats;
    }

if ($xMonth != date('m'))
    {
    $aktueller_tag_des_monats=date('t', mktime(0, 0, 0, $xMonth, 1, $xYear));
    }


# Definiere Rücksprung

echo '<form action="checkliste_neu.php" method="post">';

# Frage Zeitraum ab

echo '<table border=0>';

echo '<tr>';

echo '<td class="text_klein" valign="top" colspan="3">Bitte das gewünschte Datum wählen:</td></tr>';

echo '<tr>';

echo '<td>';

echo '<select size="1" name="xDay">';

for ($i=1; $i <= $aktueller_tag_des_monats; $i++)
    {
    if ($xDay == $i)
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

echo '<td>';

echo '<select size="1" name="xMonth">';

for ($i=1; $i <= $aktueller_monat; $i++)
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

echo '<td>';

echo '<select size="1" name="xYear">';

for ($i=2010; $i <= $aktuelles_jahr; $i++)
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

echo '<tr><td align="right" colspan="3">';

echo '<input type="submit" value="Zeige Liste" class="formularbutton" name="check"/>';

echo '</td></tr>';

echo '</table>';

echo '</form>';

echo '<br>';
?>