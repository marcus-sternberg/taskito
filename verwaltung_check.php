<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

echo '<br><br><br>';

echo '<table border=0 width=300>';

echo '<tr>';

echo '<td valign="top"></td>';

echo '<td>&nbsp;&nbsp;</td>';

echo '<td>';

echo '<span class="box">Diese Checks sind hinterlegt:</span>';

echo '<br><br>';

$sql='SELECT * FROM checks WHERE hck_aktiv=1 ORDER BY hck_name';

$anzeigefelder=array('Name' => 'hck_name');
$iconzahl=2;
$icons=array(array
    (
    "inhalt" => "change",
    "bild" => "icon_aendern.gif",
    "link" => "verwaltung_check_aendern.php"
    ));

$icons[]=(array
    (
    "inhalt" => "delete",
    "bild" => "icon_loeschen.gif",
    "link" => "verwaltung_check_loeschen.php"
    ));

$link_id='hck_id';
$link_neu='verwaltung_check_neu.php';

include('segment_liste_checks.php');

echo '</tr></table>';

include('segment_fuss.php');
?>