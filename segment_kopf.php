<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';

echo '<html>';

echo '<head>';

echo '<title>TaskScout24 - Task Organisation Management</title>';

echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8">';

echo '<link rel="stylesheet" type="text/css" href="css/tom.css">';

echo '<link rel="shortcut icon" href="tom.ico" type="image/x-icon">';

echo '<link rel="icon" href="tom.ico" type="image/x-icon">';

echo '<script type="text/javascript" language="JavaScript">';
   
echo '
function checkedall(checked)
{
var form = document.nachrichten;
for (var i = 0, field; field = form.elements[i]; i++) {
    if(field.type == "checkbox") {
      field.checked = checked;
   }
}
}
';   

echo
    "
var bn_who=\"\";
function kalender(s)
{    document.getElementById('bn_frame').style.top=yPos + \"px\";
    document.getElementById('bn_frame').style.left=xPos + \"px\";
    document.getElementById('bn_frame').style.display='block';    
    bn_who=s;
}

init_mousemove();

var xPos=\"\";
var yPos=\"\";
var docEl = (   typeof document.compatMode != \"undefined\" && 
                 document.compatMode        != \"BackCompat\"
                )? \"documentElement\" : \"body\";

function init_mousemove() 
{    if(document.layers) document.captureEvents(Event.MOUSEMOVE);
    document.onmousemove =    dpl_mouse_pos;
}

function dpl_mouse_pos(e) 
{   xPos    =  e? e.pageX : window.event.x;
      yPos    =  e? e.pageY : window.event.y;

    
    if (document.all && !document.captureEvents && docEl) 
    {   xPos    += document[docEl].scrollLeft;
        yPos    += document[docEl].scrollTop;
    }
    
    if (document.layers) routeEvent(e);
}
";

echo
    '
<!--
showHideTooltip = function () {
var obj = event.srcElement;
with(document.getElementById("tooltip")) {
innerHTML = obj.options[obj.selectedIndex].text;
with(style) {
if(event.type == "mouseleave") {
display = "none";
} else {
display = "inline";
left = event.x;
top = event.y;
}
}
}
}   
//--> ';

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



$sql_level='SELECT ule_schluessel FROM mitarbeiter LEFT JOIN level ON ule_id = hma_level WHERE hma_id = ' . $_SESSION['hma_id'];

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_level=mysql_query($sql_level, $verbindung))
    {
    fehler();
    }

while ($zeile_level=mysql_fetch_array($ergebnis_level))
    {
    $level=$zeile_level['ule_schluessel'];
    }

echo '<body>';

echo '<script type="text/javascript" src="js/wz_tooltip.js"></script>
	  <script type="text/javascript" src="js/tom_ajax.js"></script>';

echo '<table border="0"><tr><td width="10">&nbsp;</td><td>';

echo '<table border="0" width=900>';

echo '<tr>';

echo '<td width="150" rowspan="2"><a href="index.php"><img src="bilder/tom_small.gif" width="112" height="56" border=0></a>';

echo '<br><span class="text_klein">logged in as: ' . $_SESSION['hma_login'] . '</span></td>';

echo '<td width="550" rowspan="2">';

include('segment_status.php');

echo '</td>';

echo '<td width="200">';

 if($_SESSION['hma_id']!=3)
{

$sql='SELECT * FROM news ' .
    'WHERE una_empfaenger = "' . $_SESSION['hma_id'] . '" AND una_gelesen = 0 AND una_geloescht = 0 LIMIT 1';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

if (mysql_num_rows($ergebnis) > 0)
    {
    echo
        '<a href="schreibtisch_news.php"><img src="bilder/icon_news_blink.gif" width="16" height="16" border=0 alt="News!" title="News!"></a>&nbsp';
    }
else
    {
    echo
        '<a href="schreibtisch_news.php"><img src="bilder/icon_news_alt.gif" width="16" height="16" border=0 alt="Newscenter" title="Newscenter"></a>&nbsp';
    }

echo
    '<a href="schreibtisch_meine_aufgaben.php"><img src="bilder/icon_meine_aufgaben.png" width="16" height="16" border=0 alt="My Tasks" title="My Tasks"></a>
     <a href="schreibtisch_meine_gruppenaufgaben.php"><img src="bilder/shield.png" width="16" height="16" border=0 alt="Group Tasks" title="Group Tasks"></a>
     <a href="schreibtisch_meine_auftraege.php"><img src="bilder/icon_request.png" width="16" height="16" border=0 alt="My Requests" title="My Requests"></a>
     <a href="uebersicht_ticker.php"><img src="bilder/icon_ticker.gif" width="16" height="16" border=0 alt="Ticker" title="Ticker"></a>
     <a href="schreibtisch_todo.php"><img src="bilder/icon_todo.png" width="16" height="16" border=0 alt="ToDo" title="ToDo"></a> 
     <a href="logout.php"><img src="bilder/icon_logout.gif" width="16" height="16" border=0 alt="Logout" title="Logout"></a>';
}

echo '</td></tr>';
echo '<tr><td width="200">'; 
 
if($_SESSION['hma_id']!=3)
{

echo '<form name="suche" action="archiv_suche.php" method="post">';
 
echo 'Suche Ticketnr: <input type="text" name="hau_id" style="width:60px;">';
echo '<input type="submit" value="Go" class="searchbutton" />';

echo '</form>';
}   
echo '</td></tr>';

echo '<tr>';

echo '<td colspan=3  width="900">';
  
include('segment_navigation.php');

echo '</td>';

// echo '<td width="200">&nbsp;</td>';
echo '</tr>';

echo '</table>';

echo '</td></tr></table>';

echo '<table border=0><tr><td width="10" rowspan="5">&nbsp;</td><td>';


?>