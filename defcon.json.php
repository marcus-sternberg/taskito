<?php

require_once('konfiguration.php');

$sql='SELECT ude_status, ude_zeitstempel FROM defcon ORDER BY ude_zeitstempel DESC LIMIT 1';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis = mysql_query($sql, $verbindung)) {
  dieWithError("No DB connection");
}

$row = mysql_fetch_row($ergebnis);

header('Content-type: application/json');
echo json_encode($row[0]);


function dieWithError($error_text) {
  header("Status: 500 " . $error_text);
  exit();
}

?>
