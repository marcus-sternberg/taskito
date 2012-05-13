<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$session_frei=1;

require_once('konfiguration.php');
include('segment_session_pruefung.php');

$name=$_GET['name'];
$ir=$_GET['ir'];
$pfad=$_GET['pfad'];

if ($handle=opendir($pfad))
    {
    if (is_file($pfad . $name))
        {
        unlink($pfad . $name);
        }
    }

// Zurueck zur Liste

header('Location: ir_neu.php?hir_id=' . $ir);
exit;
?>