<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Aufgabenüberblick<br><br>';

echo '<span class="box">in Gruppen (nicht zugewiesen)</span>';

$sql=$sql_uebersicht_in_gruppe;

$anzeigefelder=$anzeige_gruppe;

$aktionenzahl=2;
$aktionen=array(array
    (
    "inhalt" => "add activity",
    "bild" => "icon_arbeit.gif",
    "link" => "aufgabe_ansehen.php"
    ));

$aktionen[]=array
    (
    "inhalt" => "view task",
    "bild" => "icon_anschauen.gif",
    "link" => "aufgabe_ansehen.php"
    );

$infozahl=4;
$infos=array
    (
    'Datum' => 'hau_datumstyp',
    'Status' => 'uau_stopp',
    'Gruppe' => 'hau_id',
    'Ändern' => 'hau_terminaendern'
    );

include('segment_liste.php');

echo '<br><br><span class="box">Aufgaben in Warteschlange bei Bearbeitern</span>';

$sql=$sql_uebersicht_ma_im_pool;

$anzeigefelder=$anzeige_delegiert;

$aktionenzahl=2;
$aktionen=array(array
    (
    "inhalt" => "add activity",
    "bild" => "icon_arbeit.gif",
    "link" => "aufgabe_ansehen.php"
    ));

$aktionen[]=array
    (
    "inhalt" => "view task",
    "bild" => "icon_anschauen.gif",
    "link" => "aufgabe_ansehen.php"
    );

$infozahl=4;
$infos=array
    (
    'Datum' => 'hau_datumstyp',
    'Status' => 'uau_stopp',
    'Gruppe' => 'hau_id',
    'Ändern' => 'hau_terminaendern'
    );

include('segment_liste.php');

echo '<br><br><span class="box">Aufgaben in Bearbeitung</span>';

$sql=$sql_uebersicht_ma_in_arbeit;

$anzeigefelder=$anzeige_delegiert;

$aktionenzahl=2;
$aktionen=array(array
    (
    "inhalt" => "add activity",
    "bild" => "icon_arbeit.gif",
    "link" => "aufgabe_ansehen.php"
    ));

$aktionen[]=array
    (
    "inhalt" => "view task",
    "bild" => "icon_anschauen.gif",
    "link" => "aufgabe_ansehen.php"
    );

$infozahl=4;
$infos=array
    (
    'Datum' => 'hau_datumstyp',
    'Status' => 'uau_stopp',
    'Gruppe' => 'hau_id',
    'Ändern' => 'hau_terminaendern'
    );

include('segment_liste.php');

include('segment_fuss.php');
?>