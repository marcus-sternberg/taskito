<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
# week mode 3: Montag  1-53     mit mehr als drei Tagen innerhalb dieses Jahres

# Formular zum Ermitteln der Zeit

if (!isset($_REQUEST['ugr_id']))
    {
    $_REQUEST['ugr_id']=0;
    }

# Definiere Rücksprung

echo '<form action="' . $ruecksprung . '" method="post">';

# Baue Tabelle

echo '<table border=0>';

# Entscheide, ob nach Kunde oder Mitarbeiter gefragt wird

if ($Anzeige != 'none')
    {
    if ($Anzeige == 'Kunde')
        {

        # Zeige Dialog Mitarbeiter

        # Zeige Dialog Mitarbeiter

        }
    else
        {
        if ($Anzeige == 'MA')
            {
            echo '<tr>';

            echo '<td class="text_klein">Staff Member: </td><td>';

            echo '<select size="1" name="hma_id">';

            echo '<option value="-1"><span class="text">Choose Staff Member</span></option>';

            $sql='SELECT hma_id, hma_name, hma_vorname FROM mitarbeiter WHERE hma_level >1 ' .
                'ORDER BY hma_name';

            if (!$ergebnis=mysql_query($sql, $verbindung))
                {
                fehler();
                }

            while ($zeile=mysql_fetch_array($ergebnis))
                {
                if (isset($_REQUEST['hma_id']) && $_REQUEST['hma_id'] == $zeile['hma_id'])
                    {
                    echo '<option value="' . $zeile['hma_id'] . '" selected><span class="text">' . $zeile['hma_name']
                        . ', ' . $zeile['hma_vorname'] . '</span></option>';
                    }
                else
                    {
                    echo '<option value="' . $zeile['hma_id'] . '"><span class="text">' . $zeile['hma_name'] . ', '
                        . $zeile['hma_vorname'] . '</span></option>';
                    }
                }

            echo '</select>';

            echo '</td></tr>';
            }
        }
    }

# Frage Zeitraum ab

echo '<tr>';

echo '<td class="text_klein" valign="top">Please choose the desired CW or Month and Year:</td><td>';

echo '<table border=0>';

echo '<tr>';

echo '<td>';

echo '<table border=0';

echo '<tr>';

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

if (isset($_REQUEST['xMonat']))
    {
    $xPruefmonat=$_REQUEST['xMonat'];
    }
else
    {
    $xPruefmonat=date('m');
    }

echo '<select size="1" name="xMonat">';

for ($counter=1; $counter <= 12; $counter++)
    {
    if ($xPruefmonat == $counter)
        {
        echo '<option value="' . $counter . '" selected><span class="text">' . $counter . '</span></option>';
        }
    else
        {
        echo '<option value="' . $counter . '"><span class="text">' . $counter . '</span></option>';
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

echo '</td></tr>';

echo '<tr><td align="right">';

echo '<input type="submit" value="Show CW" class="formularbutton" name="range"/>';

echo '<td colspan="2"><input type="submit" value="Show Month" class="formularbutton" name="range"/>';

echo '</td></tr>';

echo '</table>';

echo '</td></tr>';

echo '</table>';

echo '</form>';

echo '</td></tr>';

echo '</table><br><br>';

if (isset($_REQUEST['xJahr']))
    {
    if ($_REQUEST['range'] == 'Show CW')
        {
        $kw=$_REQUEST['xKw'];
        $anzeigestring=$_REQUEST['xKw'] . '.CW ' . $_REQUEST['xJahr'];
        $filterstring=' AND week(hau_anlage) = ' . $kw;
        $filterstring_change=' AND week(hau_anlage) = ' . $kw;
        $filterstring_ua=' AND week(ulo_datum,3) = ' . $kw;
        $cw_string='&cw=1';
        $group_id=$_REQUEST['ugr_id'];
        }
    else
        {
        $anzeigestring=$_REQUEST['xMonat'] . '.' . $_REQUEST['xJahr'];
        $filterstring=' AND month(hau_anlage) = ' . $_REQUEST['xMonat'];
        $filterstring_ua=' AND month(ulo_datum) = ' . $_REQUEST['xMonat'];
        $filterstring_change=' AND month(hau_anlage) = ' . $_REQUEST['xMonat'];
        $cw_string='';
        $group_id=$_REQUEST['ugr_id'];
        }
    }
?>