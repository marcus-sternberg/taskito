<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require ("bytecal_class.php");
$byte = new Kalender();
$byte->init_calendar(); 
echo($byte->show_kalender());
?>