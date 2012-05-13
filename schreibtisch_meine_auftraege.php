<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

echo '<br><table class="element" cellpadding = "5">';

echo '<tr>';

echo '<td class="text_mitte">';

echo '<img src="bilder/block.gif">&nbsp;Meine Aufträge';

echo '</td>';

echo '<td class="text_mitte">';

echo ' | ';

echo '</td>';

$sql=$sql_schreibtisch_aktuelle_auftraege;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<td class="text_mitte">';

echo '<a href="#offen">Aktuelle Aufträge (' . $anzahl . ')</a>';

echo '</td>';

echo '<td class="text_mitte">';

echo ' | ';

echo '</td>';

$sql=$sql_schreibtisch_abgelehnte_auftraege;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<td class="text_mitte">';

echo '<a href="#abgelehnt">Abgelehnte Aufträge (' . $anzahl . ')</a>';

echo '</td>';

echo '<td class="text_mitte">';

echo ' | ';

echo '</td>';

$sql=$sql_schreibtisch_serienaufgaben;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);

echo '<td class="text_mitte">';

echo '<a href="#serie">Daueraufträge (' . $anzahl . ')</a>';

echo '</td>';

echo '</tr>';

echo '</table>';

echo '<br><br><span class="box"><a name="offen">Aktuelle Aufträge</a></span>';

$sql=$sql_schreibtisch_aktuelle_auftraege;

$anzeigefelder=$anzeige_jobs;

$aktionenzahl=4;
$aktionen=array(array
    (
    "inhalt" => "view task",
    "bild" => "icon_anschauen.gif",
    "link" => "aufgabe_ansehen.php"
    ));

$aktionen[]=array
    (
    "inhalt" => "change task",
    "bild" => "icon_aendern.gif",
    "link" => "schreibtisch_aufgabe_aendern.php"
    );

$aktionen[]=array
    (
    "inhalt" => "delete task",
    "bild" => "icon_loeschen.gif",
    "link" => "aufgabe_loeschen.php"
    );

$aktionen[]=array
    (
    "inhalt" => "confirm changed date",
    "bild" => "icon_erledigt.gif",
    "link" => "aufgabe_termin_aendern.php"
    );

$infozahl=4;
$infos=array
    (
    'Datum' => 'hau_datumstyp',
    'Zugewiesen' => 'uau_ma_status',
    'Status' => 'uau_stopp',
    'Termin ändern' => 'hau_terminaendern'
    );

include('segment_liste.php');

echo '<br><br><span class="box"><a name="serie">Daueraufträge</a></span>';

$sql=$sql_schreibtisch_serienaufgaben;

$anzeigefelder=$anzeige_serie;

$aktionenzahl=1;
//$aktionen=array(array("inhalt"=>"Aufgabedetails ändern","bild"=>"icon_aendern.gif","link"=>"serienaufgabe_ansehen.php"));
//$aktionen[]=array("inhalt"=>"Einstellungen ändern","bild"=>"icon_einstellungen.png","link"=>"schreibtisch_serienaufgabe_aendern.php");
$aktionen=array(array
    (
    "inhalt" => "delete task",
    "bild" => "icon_loeschen.gif",
    "link" => "serienaufgabe_loeschen.php"
    ));

$infozahl=0;
$infos=array();

include('segment_liste_serie.php');

echo '<br><br><span class="box"><a name="abgelehnt">Abgelehnte Aufträge</a></span>';

$sql=$sql_schreibtisch_abgelehnte_auftraege;

$anzeigefelder=$anzeige_jobs;

$aktionenzahl=4;
$aktionen=array(array
    (
    "inhalt" => "view task",
    "bild" => "icon_anschauen.gif",
    "link" => "aufgabe_ansehen.php"
    ));

$aktionen[]=array
    (
    "inhalt" => "change task",
    "bild" => "icon_aendern.gif",
    "link" => "schreibtisch_aufgabe_aendern.php"
    );

$aktionen[]=array
    (
    "inhalt" => "reactivate task",
    "bild" => "icon_erneut.gif",
    "link" => "aufgabe_reaktivieren.php"
    );

$aktionen[]=array
    (
    "inhalt" => "delete task",
    "bild" => "icon_loeschen.gif",
    "link" => "aufgabe_loeschen.php"
    );

$infozahl=1;
$infos=array('date' => 'hau_datumstyp');

include('segment_liste.php');

echo '<br><br><span class="box">Abgeschlossene Aufträge</span>';

$sql=$sql_schreibtisch_abgeschlossene_auftraege;

$anzeigefelder=$anzeige_jobende;

$aktionenzahl=2;
$aktionen=array(array
    (
    "inhalt" => "view task",
    "bild" => "icon_anschauen.gif",
    "link" => "aufgabe_ansehen.php"
    ));

$aktionen[]=array
    (
    "inhalt" => "delete task",
    "bild" => "icon_loeschen.gif",
    "link" => "aufgabe_loeschen.php"
    );

$infozahl=1;
$infos=array('date' => 'hau_datumstyp');

include('segment_liste.php');

include('segment_fuss.php');
?>