<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$delete=' onclick="return window.confirm(\'Delete Record?\');"';

foreach ($aktionen as $schluessel => $aktion)
    {
    $bild = '<img src="bilder/' . $aktion['bild'] . '" border="0" alt="' . $aktion['inhalt'] . '" title="'
        . $aktion['inhalt'] . '">';

    echo '<td align="center" style="border-left:1px solid grey;" ><a href="' . $aktion['link'] . '?utr_id='
        . $zeile['utr_id'] . '" ' . $delete . '>' . $bild . '</a></td>';
    }
?>