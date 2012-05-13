<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

/*
if($_SESSION['hma_level']!=1)
{
    echo 'Sie haben nicht die erforderlichen Rechte zum Ändern.';
} else
{ */

$menu=array();
$menu_sub1=array();
$level=array();
$matrix=array();

####### Lese alle menüpunkte ein #########################

$sql='SELECT * FROM menu_main ORDER BY xTitle';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {
    $menu[$zeile['ID']]['xKey'] = $zeile['xKey'];
    $menu[$zeile['ID']]['xTitle']=$zeile['xTitle'];
    }

$sql='SELECT * FROM menu_sub1 ORDER BY xKey_main, xTitle';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {
    $menu_sub1[$zeile['ID_SUB1']]['xKey'] = $zeile['xKey'];
    $menu_sub1[$zeile['ID_SUB1']]['xTitle']=$zeile['xTitle'];
    $menu_sub1[$zeile['ID_SUB1']]['xKey_main']=$zeile['xKey_main'];
    }

########## Lese alle Gruppen ein #############################

$sql='SELECT * FROM level WHERE ule_id > 1 ORDER BY ule_name';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {
    $level[$zeile['ule_id']]['ule_name']=$zeile['ule_name'];
    }

########### Baue Rechtematrix auf ##############################

foreach ($menu_sub1 AS $ID => $Inhalt)
    {
    foreach ($level AS $ule_id => $name)
        {
        if (($Inhalt['xKey'] & (1 << $ule_id)) > 0)
            {
            $matrix[$ID][$ule_id]=1;
            }
        else
            {
            $matrix[$ID][$ule_id]=0;
            }
        }
    }

############### Gebe Matrix aus #################################

if (!isset($_POST['speichern']))
    {
    echo '<table style="border: solid, 1px, black;" cellspacing="1" cellpadding="3" width="700" class="element">';

    echo '<tr>';

    echo '<td class="text_mitte">';

    echo '<img src="bilder/block.gif">&nbsp;Rechte setzen';

    echo '</td>';

    echo '<td class="text_mitte">';

    echo ' | ';

    echo '</td>';

    echo '<td class="text_mitte">';

    echo '<a href="verwaltung_recht.php">&nbsp;Hauptmen?</a>';

    echo '</td>';

    echo '<td class="text_mitte">';

    echo ' | ';

    echo '</td>';

    echo '<td class="text_mitte">';

    echo '&nbsp;1. Menüebene';

    echo '</td>';

    echo '<td class="text_mitte">';

    echo ' | ';

    echo '</td>';

    echo '<td class="text_mitte">';

    echo '<a href="verwaltung_recht_sub2.php">&nbsp;2. Menüebene</a>';

    echo '</td>';

    echo '</tr>';

    echo '</table>';

    echo '<br>';

    echo '<form action="verwaltung_recht_sub1.php" method="post">';

    foreach ($menu AS $menu_id => $menu_name)
        {
        foreach ($menu_sub1 AS $ID => $Inhalt)
            {
            if ($menu_sub1[$ID]['xKey_main'] != $menu_id)
                {
                $kein_submenu=1;
                }
            else
                {
                $kein_submenu=0;
                break;
                }
            }

        if ($kein_submenu == 0)
            {
            echo '<span class="box">' . $menu_name['xTitle'] . '</span><br>';

            echo
                '<table style="border: solid, 1px, black;" cellspacing="1" cellpadding="3" width="700" class="element">';

            echo '<tr>';

            echo '<td>&nbsp;</td>';

            foreach ($menu_sub1 AS $ID => $Inhalt)
                {
                if ($menu_sub1[$ID]['xKey_main'] == $menu_id)
                    {
                    echo '<td align="center">' . $Inhalt['xTitle'] . '</td>';
                    }
                }

            echo '</tr>';
            $zaehler=0;

            foreach ($level AS $ule_id => $name)
                {
                if (fmod($zaehler, 2) == 1 && $zaehler > 0)
                    {
                    $hintergrundfarbe='#ffffff';
                    }
                else
                    {
                    $hintergrundfarbe='#CED1F0';
                    }

                echo '<tr><td align="left" bgcolor="' . $hintergrundfarbe . '">' . $name['ule_name'] . '</td>';

                foreach ($menu_sub1 AS $ID => $Inhalt)
                    {
                    if ($menu_sub1[$ID]['xKey_main'] == $menu_id)
                        {
                        if ($matrix[$ID][$ule_id] == 1)
                            {
                            $checked='checked';
                            }
                        else
                            {
                            $checked='';
                            }

                        echo '<td align="center" bgcolor="' . $hintergrundfarbe
                            . '"><input type="checkbox" name="matrix[' . $ID . '][' . $ule_id . ']" ' . $checked
                            . '></td>'; // '.$ID.'-'.$ule_id.':'.$matrix[$ID][$ule_id].'
                        }
                    }

                echo '</tr>';
                $zaehler++;
                }

            echo '</table>';

            echo '<br><hr><br>';
            }
        }

    echo '<tr><td colspan="' . (count($level) + 1)
        . '" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Speichere Rechte" class="formularbutton" /></td></tr>';

    echo '</form>';
    }

################### ENDE Eingabe ###########################

################### Beginne Auswertung Formular ############
else
    {

    ################### Lese übergebene daten aus ##############
    $matrix=$_POST['matrix'];

    ################## Setze für alle Menüpunkte Adminrecht #####

    $sql='SELECT * FROM menu_sub1 ORDER BY xTitle';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
    while ($zeile=mysql_fetch_array($ergebnis))
        {
        $matrix[$zeile['ID_SUB1']]['1']='on';
        }


    ########### Speichere sonstige rechte ########################

    foreach ($matrix AS $ID => $Inhalt)
        {
        $matrix[$ID]['1'] = 'on';
        $xKey=0;

        foreach ($Inhalt AS $xID => $trigger)
            {
            if ($matrix[$ID][$xID] == 'on')
                {
                $xKey=$xKey + bcpow(2, $xID);
                }
            }
        $sql='UPDATE menu_sub1 SET xKey= ' . $xKey . ' WHERE ID_SUB1 = ' . $ID;

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }
        }

    echo 'Die neuen Rechte wurden gespeichert.';

    echo '<meta http-equiv="refresh" content="1;url=verwaltung_recht_sub1.php">';
    }
#}
include('segment_fuss.php');
?>