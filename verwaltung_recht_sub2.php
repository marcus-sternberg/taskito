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

$sql='SELECT * FROM menu_main ORDER BY xSort';

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

$sql='SELECT * FROM menu_sub2 ORDER BY xKey_main, xTitle';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {
    $menu_sub2[$zeile['ID_SUB2']]['xKey'] = $zeile['xKey'];
    $menu_sub2[$zeile['ID_SUB2']]['xTitle']=$zeile['xTitle'];
    $menu_sub2[$zeile['ID_SUB2']]['xKey_main']=$zeile['xKey_main'];
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

foreach ($menu_sub2 AS $ID => $Inhalt)
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

    echo '<a href="verwaltung_recht_sub1.php">&nbsp;1. Menüebene</a>';

    echo '</td>';

    echo '<td class="text_mitte">';

    echo ' | ';

    echo '</td>';

    echo '<td class="text_mitte">';

    echo '&nbsp;2. Menüebene';

    echo '</td>';

    echo '</tr>';

    echo '</table>';

    echo '<br>';

    echo '<form action="verwaltung_recht_sub2.php" method="post">';

    foreach ($menu AS $hm_id => $hm_name)
        {

        # Prüfe, ob es ein Untermenü SUB1 gibt

        $sql_menu = 'SELECT COUNT(*) AS anzahl FROM menu_main
INNER JOIN menu_sub1 ON menu_main.ID = menu_sub1.xKey_main
INNER JOIN menu_sub2 ON menu_sub2.xKey_main = menu_sub1.ID_SUB1 
WHERE menu_main.ID = '
            . $hm_id;

        #echo $sql_menu;

        if (!$ergebnis_menu=mysql_query($sql_menu, $verbindung))
            {
            fehler();
            }

        $row=mysql_fetch_array($ergebnis_menu);
        $treffer=$row['anzahl'];

        if ($treffer > 0)
            # Es gibt ein 2. Untermenü, schreibe Tabelle

            {
            echo '<span class="box">' . $hm_name['xTitle'] . '</span><br>';

            echo
                '<table style="border: solid, 1px, black;" cellspacing="1" cellpadding="3" width="800" class="element">';

            echo '<tr>';

            echo '<td>&nbsp;</td>';

            $sql_sub1='SELECT * FROM menu_sub1 WHERE xKey_main = ' . $hm_id;

            if (!$ergebnis_sub1=mysql_query($sql_sub1, $verbindung))
                {
                fehler();
                }

            // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
            while ($zeile_sub1=mysql_fetch_array($ergebnis_sub1))
                {

                $sql_sub2 = 'SELECT COUNT(*) AS anzahl FROM menu_sub2 WHERE xKey_main = ' . $zeile_sub1['ID_SUB1'];

                if (!$ergebnis_sub2=mysql_query($sql_sub2, $verbindung))
                    {
                    fehler();
                    }

                while ($zeile_sub2=mysql_fetch_array($ergebnis_sub2))
                    {
                    $spalten=$zeile_sub2['anzahl'];
                    }

                if ($spalten != 0)
                    {
                    echo '<td align="center" style="  border:solid 1px black;"colspan="' . $spalten . '">'
                        . $zeile_sub1['xTitle'] . '</td>';
                    }
                }

            echo '</tr>';

            $sql_sub1='SELECT * FROM menu_sub1 WHERE xKey_main = ' . $hm_id;

            if (!$ergebnis_sub1=mysql_query($sql_sub1, $verbindung))
                {
                fehler();
                }

            echo '<tr>';

            echo '<td>&nbsp;</td>';

            // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
            while ($zeile_sub1=mysql_fetch_array($ergebnis_sub1))
                {

                $sql_sub2 = 'SELECT * FROM menu_sub2 WHERE xKey_main = ' . $zeile_sub1['ID_SUB1'];

                if (!$ergebnis_sub2=mysql_query($sql_sub2, $verbindung))
                    {
                    fehler();
                    }

                // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank

                if (mysql_num_rows($ergebnis_sub2) > 0)
                    {
                    while ($zeile_sub2=mysql_fetch_array($ergebnis_sub2))
                        {
                        echo '<td>' . $zeile_sub2['xTitle'] . '</td>';
                        }
                    }
                }

            echo '</tr>';

            $zaehler=0;

            foreach ($level AS $ule_id => $name)
                {
                $sql_sub1 = 'SELECT * FROM menu_sub1 WHERE xKey_main = ' . $hm_id;

                if (!$ergebnis_sub1=mysql_query($sql_sub1, $verbindung))
                    {
                    fehler();
                    }

                echo '<tr>';

                if (fmod($zaehler, 2) == 1 && $zaehler > 0)
                    {
                    $hintergrundfarbe='#ffffff';
                    }
                else
                    {
                    $hintergrundfarbe='#CED1F0';
                    }

                echo '<tr><td align="left" bgcolor="' . $hintergrundfarbe . '">' . $name['ule_name'] . '</td>';

                // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
                while ($zeile_sub1=mysql_fetch_array($ergebnis_sub1))
                    {

                    $sql_sub2 = 'SELECT * FROM menu_sub2 WHERE xKey_main = ' . $zeile_sub1['ID_SUB1'];

                    if (!$ergebnis_sub2=mysql_query($sql_sub2, $verbindung))
                        {
                        fehler();
                        }

                    // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank

                    if (mysql_num_rows($ergebnis_sub2) > 0)
                        {
                        while ($zeile_sub2=mysql_fetch_array($ergebnis_sub2))
                            {
                            if ($menu_sub2[$zeile_sub2['ID_SUB2']]['xKey_main'] == $zeile_sub1['ID_SUB1'])
                                {
                                if ($matrix[$zeile_sub2['ID_SUB2']][$ule_id] == 1)
                                    {
                                    $checked='checked';
                                    }
                                else
                                    {
                                    $checked='';
                                    }

                                echo '<td align="center" bgcolor="' . $hintergrundfarbe
                                    . '"><input type="checkbox" name="matrix[' . $zeile_sub2['ID_SUB2'] . '][' . $ule_id
                                    . ']" ' . $checked . '></td>'; // '.$ID.'-'.$ule_id.':'.$matrix[$ID][$ule_id].'
                                }
                            }
                        }
                    }

                echo '</tr>';
                $zaehler++;
                }

            echo '</table>';

            echo '<br>';
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

    $sql='SELECT * FROM menu_sub2 ORDER BY xTitle';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
    while ($zeile=mysql_fetch_array($ergebnis))
        {
        $matrix[$zeile['ID_SUB2']]['1']='on';
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
        $sql='UPDATE menu_sub2 SET xKey= ' . $xKey . ' WHERE ID_SUB2 = ' . $ID;

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }
        }

    echo 'Die neuen Rechte wurden gespeichert.';

    echo '<meta http-equiv="refresh" content="1;url=verwaltung_recht_sub2.php">';
    }

include('segment_fuss.php');
?>