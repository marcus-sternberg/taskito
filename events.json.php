<?php

require_once('konfiguration.php');

$sql = 'SELECT hau_dauer, hau_id, hau_titel, hau_pende, hau_nonofficetime FROM aufgaben 
         WHERE hau_aktiv =1 AND hau_kalender = 1 AND hau_pende >= CURDATE() AND hau_abschluss = 0 
         ORDER BY hau_pende';


// Frage Datenbank nach Suchbegriff
if (!$ergebnis = mysql_query($sql, $verbindung)) {
  dieWithError("No DB connection");
}

$events = array();
while ($zeile = mysql_fetch_array($ergebnis)) {

  $event_id = $zeile['hau_id'];

  $events[$event_id]["name"] = $zeile['hau_titel'];
  $events[$event_id]["datum"] = $zeile['hau_pende'];

  $sql_owner = 'SELECT hma_vorname as vorname, hma_name as nachname FROM mitarbeiter
                    LEFT JOIN aufgaben_mitarbeiter ON hma_id = uau_hmaid
                    WHERE uau_hauid = ' . $zeile['hau_id'] . ' 
                    ORDER BY hma_name';

  if (!$ergebnis_owner = mysql_query($sql_owner, $verbindung)) {
    dieWithError("No DB connection");
  }

  $events[$event_id]["contacts"] = array();
  while ($person = mysql_fetch_array($ergebnis_owner)) {
    array_push($events[$event_id]["contacts"], $person["vorname"] . " " . $person["nachname"]);
  }  
}

if (count($events > 0)) {
  header('Content-type: application/json');
  echo json_encode($events);
} else {
  header('Status: 404 No events found');
}

function dieWithError($error_text) {
  header("Status: 500 " . $error_text);
  exit();
}

?>
