<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

    $sql='UPDATE fans SET fan = fan +1';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

// Zurueck zur Liste

header('Location: index.php');
exit;
?>