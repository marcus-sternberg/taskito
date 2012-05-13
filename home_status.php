<?php
###### Editnotes ####
#$LastChangedDate: 2012-02-24 15:13:06 +0100 (Fr, 24 Feb 2012) $
#$Author: bpetersen $ 
#####################
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';

echo '<html>';

echo '<head>';

echo '<title>TaskScout24 - Task Organisation Management</title>';

echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8">';

echo '<meta http-equiv="refresh" content="60" >';


echo '<link rel="stylesheet" type="text/css" href="css/tom.css">';

echo '<link rel="shortcut icon" href="tom.ico" type="image/x-icon">';

echo '<link rel="icon" href="tom.ico" type="image/x-icon">';

echo '<style type="text/css">';

echo 'body { background-color:#000;}';

echo 'table.element { background-color:#000;}';

echo '</style>';

echo '</head>';

$auto='on';
$_SESSION['filterstring']='';
$_SESSION['hma_id']=1;
require_once('konfiguration.php');
include('segment_init.php');

$jahr=date("Y");
$month=date("m");
$day=date("d");
$ergebnis_check=array();

########################  Definiere Variablen ################################


#####################################################################################
############################ Ausgabe Werte ##########################################
// Gebe Überschrift aus
echo '<table border=0 cellspacing = 5 style="background-color:#000"><tr><td>';

echo '<table class="element" border=1 cellpadding = "5">';

echo '<tr>';

echo '<td class="text_anzeige" align="right">Status ';

$sql='SELECT ude_status, ude_zeitstempel FROM defcon
         ORDER BY ude_zeitstempel DESC LIMIT 1';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    switch ($zeile['ude_status'])
        {
        case 1:
            $color='#EE775F';
            $status='KRITISCH';
            break;

        case 2:
            $color='#F3C39B';
            $status='PROBLEM';
            break;

        case 3:
            $color='#FFF8B3';
            $status='WARNUNG';
            break;

        case 4:
            $color='#C1E2A5';
            $status='OK';
            break;
        }

    echo ' </td><td align="center" bgcolor="' . $color . '"class="text_anzeige3">DefCon';

    echo '&nbsp;' . $zeile['ude_status'] . ' : ' . $status . '&nbsp;</td>';
    }

$sql='SELECT hcl_id FROM checklists WHERE hcl_datum = "' . $jahr . '-' . $month . '-' . $day . '"';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    $hcl_id=$zeile['hcl_id'];
    }

if (!isset($hcl_id))
    {
    $hcl_id=0;
    }

$sql='SELECT * FROM check_matrix WHERE hcm_hclid = "' . $hcl_id . '"';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis) == 0)
    {
    $ergebnis_check[]=0;
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    $ergebnis_check[]=$zeile['hcm_status'];
    }

asort($ergebnis_check);

foreach ($ergebnis_check AS $feld_id => $resultat)
    {
    if ($resultat == 0)
        {
        $color='#c2c2c2';
        $status='ungeprüft';
        break;
        }

    if ($resultat == 1)
        {
        $color='#EE775F';
        $status='KRITISCH';
        break;
        }

    if ($resultat == 2)
        {
        $color='#FFF8B3';
        $status='WARNUNG';
        break;
        }

    if ($resultat == 3)
        {
        $color='#C1E2A5';
        $status='OK';
        break;
        }

    if ($resultat == 4)
        {
        $color='#CED1F0';
        $status='SKIP';
        break;
        }
    }

echo '<td class="text_anzeige" align="right"> Checklist</td>';

echo '<td align="center" class="text_anzeige3" bgcolor="' . $color . '">&nbsp;' . $status
    . '&nbsp;</td><td class="text_anzeige">&nbsp;' . date('d.m.Y') . ' - ' . date('H:i');

if (date("w") == 5)
    {
    echo ' T-G-I-F ';
    }

echo '</td>';

   $sql='SELECT * FROM system_plattformen ORDER BY hpl_name';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
    echo '<td class="text_anzeige">' . $zeile['hpl_name'] . ' ';

    if ($zeile['hpl_status'] == 1)
        {
        $bild='<img src="bilder/icon_quad_gruen.gif">';
        }
    else
        {
        $bild='<img src="bilder/icon_quad_rot.gif">';
        }

    echo $bild;

    echo ' (' . $zeile['hpl_version'] . ')</td>';
    }


echo '</tr>';

echo '</table>';

echo '<br>';

echo '<table width="1800" border=0><tr><td valign="top">';

  $sql =  'SELECT ulok_zeitstempel, hma_name, ulok_text FROM log_kunde 
         LEFT JOIN mitarbeiter ON ulok_ma = hma_id 
         WHERE ulok_gruppe = 4   
         ORDER BY ulok_zeitstempel DESC';


// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

echo '<table border="1" class="element" width="900" cellpadding="5">';

    echo '<tr>';
 
    echo '<td align="left" class="text_anzeige" colspan="2">Notizen für OM</td>';

    echo '</tr>';

while ($zeile=mysql_fetch_array($ergebnis))
    {

    echo '<tr>';

    echo '<td align="left" class="text_anzeige">' . datum_anzeigen($zeile['ulok_zeitstempel']) . '&nbsp;</td>';

    echo '<td align="left" class="text_anzeige">' . $zeile['ulok_text'] . '&nbsp;</td>';

    echo '</tr>';
    }

echo '</table>';

echo '</td><td>&nbsp;&nbsp;&nbsp;</td><td valign="top">';

echo '<iframe src="anzeige_change_liste.php" frameborder="0" scrolling="no" style="margin: 0px; padding:0; border-style: none;width: 950px; height: 500px;" name="Alle freigegebenen Changes anzeigen">';

echo '</iframe>';

echo '</td></tr></table>';

include('segment_fuss.php');
?>
