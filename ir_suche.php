<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$session_frei = 1; 
require_once ('konfiguration.php');

include ('segment_session_pruefung.php');
include ('segment_init.php');
include ('segment_kopf.php');

if(!isset($_REQUEST['sortierung'])) {$sort_vorgabe='hir_zeitstempel DESC';} else {$sort_vorgabe=$_REQUEST['sortierung'];}

$Daten=array();

foreach ($_POST as $varname => $value)
    {
    $Daten[$varname]=trim($value);
    }

if(ISSET($_POST['ir_suche']))
{
 $_SESSION['ir_suche'] = $_POST['ir_suche'];
}                                                  
// Aufgaben in Warteschlange

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Such-Ergebnisse für ['.$_SESSION['ir_suche'].'] in aktuellen IR<br><br></span>'; 

 echo '<table style="border: solid, 1px, black;" cellspacing="1" cellpadding="3" width="900" class="element">';

  echo '<tr>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hir_id%20DESC">IR-Nummer</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hir_datum%20DESC">angelegt am</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hir_problem%20ASC">Problem</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hir_auswirkung%20ASC">Auswirkung</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hir_status%20ASC">Status</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hir_kategorie%20ASC">Kategorie</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hma_name%20ASC">Agent</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hir_zeitstempel%20DESC">Letzte Änderung</a></td>'; 

echo '</tr>';

    $sql=
    'SELECT * FROM ir_stammdaten 
        LEFT JOIN mitarbeiter ON hir_agent = hma_id 
        LEFT JOIN impact ON hir_auswirkung = uia_id  
        WHERE hir_status < 5 AND (hir_problem LIKE "%'.$_SESSION['ir_suche'].'%" OR hir_beschreibung LIKE "%'.$_SESSION['ir_suche'].'%") 
        ORDER BY '.$sort_vorgabe;

        if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$zaehler=0;

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {
    switch ($zeile['hir_status'])
        {
        case 1:
            $status='eröffnet';
            break;

        case 2:
            $status='Analyse';
            break;

        case 3:
            $status='Fixing';
            break;

        case 4:
            $status='Testen';
            break;
        }

    if (fmod($zaehler, 2) == 1 && $zaehler > 0)
        {
        $hintergrundfarbe='#ffffff';
        }
    else
        {
        $hintergrundfarbe='#CED1F0';
        }

    // Beginne Datenausgabe
    echo '<tr>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top"><a href="ir_ansicht.php?hir_id='
        . $zeile['hir_id'] . '">' . $zeile['hir_id'] . '</a></td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">'
        . zeitstempel_anzeigen($zeile['hir_datum']) . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top"><a href="ir_ansicht.php?hir_id='
        . $zeile['hir_id'] . '">' . $zeile['hir_problem'] . '</a></td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . $zeile['uia_name'] . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . $status . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . $zeile['hir_kategorie'] . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . $zeile['hma_name'] . '</td>';
    
    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">'
        . zeitstempel_anzeigen($zeile['hir_zeitstempel']) . '</td>';

    
    echo '</tr>';
    $zaehler++;
    }

echo '</table>';

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Such-Ergebnisse für ['.$_SESSION['ir_suche'].'] im Archiv<br><br></span>'; 

 echo '<table style="border: solid, 1px, black;" cellspacing="1" cellpadding="3" width="900" class="element">';

  echo '<tr>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hir_id%20DESC">IR-Nummer</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hir_datum%20DESC">angelegt am</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hir_problem%20ASC">Problem</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hir_auswirkung%20ASC">Auswirkung</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hir_status%20ASC">Status</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hir_kategorie%20ASC">Kategorie</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hma_name%20ASC">Agent</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_suche.php?sortierung=hir_zeitstempel%20DESC">geschlossen am</a></td>'; 

echo '</tr>';

    $sql=
    'SELECT * FROM ir_stammdaten 
        LEFT JOIN mitarbeiter ON hir_agent = hma_id 
        LEFT JOIN impact ON hir_auswirkung = uia_id  
        WHERE hir_status = 5 AND (hir_problem LIKE "%'.$_SESSION['ir_suche'].'%" OR hir_beschreibung LIKE "%'.$_SESSION['ir_suche'].'%") 
        ORDER BY '.$sort_vorgabe;  

        if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$zaehler=0;

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {
    switch ($zeile['hir_status'])
        {
        case 1:
            $status='eröffnet';
            break;

        case 2:
            $status='Analyse';
            break;

        case 3:
            $status='Fixing';
            break;

        case 4:
            $status='Testen';
            break;
            
        case 5:
            $status='archiviert';
            break;
        }

    if (fmod($zaehler, 2) == 1 && $zaehler > 0)
        {
        $hintergrundfarbe='#ffffff';
        }
    else
        {
        $hintergrundfarbe='#CED1F0';
        }

    // Beginne Datenausgabe
    echo '<tr>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top"><a href="ir_ansicht.php?hir_id='
        . $zeile['hir_id'] . '">' . $zeile['hir_id'] . '</a></td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">'
        . zeitstempel_anzeigen($zeile['hir_datum']) . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top"><a href="ir_ansicht.php?hir_id='
        . $zeile['hir_id'] . '">' . $zeile['hir_problem'] . '</a></td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . $zeile['uia_name'] . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . $status . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . $zeile['hir_kategorie'] . '</td>';

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">' . $zeile['hma_name'] . '</td>';
    
    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top">'
        . zeitstempel_anzeigen($zeile['hir_zeitstempel']) . '</td>';

    
    echo '</tr>';
    $zaehler++;
    }

echo '</table>';


?>