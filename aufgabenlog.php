<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_init.php');

// nur anzeigen wenn Seite via link aufgeführt wird
if (!isset($_GET['pagecalltime']))
{
  include('segment_kopf_einfach.php');
  $task_id=$_GET['task_id'];
  include('segment_aufgabenlog.php');
  echo '</td></tr></table>';
  include('segment_fuss.php');
}
else
{
	$pagecalltime=$_GET['pagecalltime'];
  $task_id=$_GET['task_id'];
  include('segment_aufgabenlog.php');
}
?>
