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

echo '<span class="box">These Sectors are available:</span>';

echo '<br><br>';

$sql='SELECT * FROM bereich ' .
    'ORDER BY ule_name';

$anzeigefelder=array('Name' => 'ule_name');
$iconzahl=2;
$icons=array(array
    (
    "inhalt" => "change",
    "bild" => "icon_aendern.gif",
    "link" => "verwaltung_bereich_aendern.php"
    ));

$icons[]=(array
    (
    "inhalt" => "delete",
    "bild" => "icon_loeschen.gif",
    "link" => "verwaltung_bereich_loeschen.php"
    ));

$link_id='ule_id';
$link_neu='verwaltung_bereich_neu.php';

include('segment_liste_verwaltung.php');

echo '</tr></table>';

include('segment_fuss.php');
?>
