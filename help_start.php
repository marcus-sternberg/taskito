<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

echo '<br><table class="element" cellpadding = "5">';

echo '<tr>';

echo '<td class="text_mitte">';

echo '<img src="bilder/block.gif">&nbsp;Help-Line';

echo '</td>';

echo '</tr>';

echo '</table>';

echo '<br><table cellpadding = "5">';

echo '<tr>';

echo '<td class="text_mitte">';

echo 'If you experience problems with TOM or miss features please feel free to contact the Developer: ';

echo '</td>';

echo '</tr>';

echo '<tr>';

echo '<td class="text">';

echo 'Marcus Sternberg';

echo '</td>';

echo '</tr>';

echo '<tr>';

echo '<td class="text">';

echo 'Mail: <a href="mailto:marcus@nyksund.de>tom@insolitus.de</a>';

echo '</td>';

echo '</tr>';

echo '</table>';
?>