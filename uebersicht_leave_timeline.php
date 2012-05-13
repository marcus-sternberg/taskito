<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
error_reporting(E_ALL);

ini_set('display_errors', '1');

require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');

echo
    '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Timeline for all scheduled staff in calendar<br>&nbsp;&nbsp;&nbsp;<span class="text_klein">[<a href="home.php">back to schedule</a>]</span><br>';

echo '<img src="seg_timeline_leave.php"/>';

echo '<br><span class="text_klein">red = overdue, green = on track, yellow = on track, but out of office time</a><br>';
?>