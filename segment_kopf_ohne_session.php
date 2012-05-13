<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';

echo '<html>';

echo '<head>';

echo '<title>TASKITO - Task Organisation Management</title>';

echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8">';

echo '<link rel="stylesheet" type="text/css" href="css/tom.css">';

echo '<link rel="shortcut icon" href="tom.ico" type="image/x-icon">';

echo '<link rel="icon" href="tom.ico" type="image/x-icon">';

echo '<meta http-equiv="refresh" content="60" >';

echo '<script type="text/javascript">';

echo
    '
<!--
function fenster(winname,wintitel,breite,hoehe) {
    var links=screen.width/2-breite/2;
    var oben=screen.height/2-hoehe/2;
    NewWin = window.open(winname, wintitel, "width="+breite+",height="+hoehe+",top="+oben+",left="+links+",status=0, scrollbars=1, toolbar=0,location=0,resizable=1");
    }
-->
';

echo '</script>';

echo '</head>';

echo '<body>';

echo '<table border=0><tr><td width="10">&nbsp;</td><td>';

echo '<table border=0 width=900>';

echo '<tr>';

echo '<td width="150"><a href="index.php"><img src="bilder/tom_small.jpg" width="155" height="80" border=0></a>';  

echo '<br><span class="text_klein">logged in as: ' . $_SESSION['hma_login'] . '</span></td>';

echo '<td width="550">';

include('segment_status.php');

echo '</td>';

echo '</tr>';

echo '<tr>';

echo '<td colspan=2  width="700">';

echo '</td>';

echo '<td width="200">&nbsp;';

echo '</td></tr>';

echo '</table>';

echo '</td></tr></table>';

echo '<table width=100%><tr><td width="10">&nbsp;</td><td>';
?>