<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################

# Kommentar Ä Ä Ö Ü

# ß ö ü ä
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
$session_frei=0;

$task_id=$_POST['hau_id'];

echo '<table width="400" border=0><tr><td>';

if (isset($_POST['print_details']))
    {
    include('segment_aufgabe_anzeigen.php');
    }

if (isset($_POST['print_comments']))
    {
    echo '<hr>';
    include('segment_liste_aktiv.php');
    }

echo '</td></tr></table>';
?>