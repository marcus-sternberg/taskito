<?php
###### Editnotes ####
#$LastChangedDate: 2011-09-02 11:39:26 +0200 (Fr, 02 Sep 2011) $
#$Author: msternberg $ 
#####################
$sql_news='INSERT INTO news (
    una_id, 
    una_zeitstempel, 
    una_initiator, 
    una_empfaenger, 
    una_hauid, 
    una_info, 
    una_gelesen, 
    una_geloescht, 
    una_typ
    ) '
    .

'VALUES (
    NULL,
    NOW(),
    "' . $initiator . '",
    "' . $empfaenger . '",    
    "' . $hauid . '",
    "' . nl2br(mysql_real_escape_string($info)) . '",
    "0",
    "0",
    "0"
    )';

if (!($ergebnis_news=mysql_query($sql_news, $verbindung)))
    {
    fehler();
    }
?>