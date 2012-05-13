<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$session_frei = 1;
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_kopf.php'); 

if(!ISSET($_REQUEST['sortierung'])) {$sort_vorgabe='hir_zeitstempel DESC';} else {$sort_vorgabe=$_REQUEST['sortierung'];}

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Liste der offenen Incident Reports<br><br></span>';

// Beginne mit Tabellenausgabe
echo '<table style="border: solid, 1px, black;" cellspacing="1" cellpadding="3" width="900" class="element">';

echo '<tr>';

echo '<form action="ir_suche.php" method="post">';  

echo '<td colspan="2"><input type="text" name="ir_suche" style="width:160px;">&nbsp;&nbsp;'; 

echo '<input type="submit" name="ir_suchen" value="Suche" class="formularbutton" /></td>';

echo '</form>';

echo '<td class="text_mitte_normal" colspan="5" align="right">';

if($_SESSION['hma_id']!=3)
{
echo
    'Neuen IR erzeugen</td><td align="right"><a href="ir_anlegen.php"><img src="bilder/icon_neu.gif" border="0" alt="Neuen IR erzeugen" title="Neuen IR erzeugen"></a>';
} else
{
    echo '&nbsp;';
}
echo '</tr>';

echo '<tr>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_liste.php?sortierung=hir_id%20DESC">IR-Nummer</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_liste.php?sortierung=hir_datum%20DESC">angelegt am</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_liste.php?sortierung=hir_problem%20ASC">Problem</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_liste.php?sortierung=hir_auswirkung%20ASC">Auswirkung</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_liste.php?sortierung=hir_status%20ASC">Status</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_liste.php?sortierung=hir_kategorie%20ASC">Kategorie</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_liste.php?sortierung=hma_name%20ASC">Agent</a></td>';

echo '<td class="tabellen_titel" valign="top"><a href="ir_liste.php?sortierung=hir_zeitstempel%20DESC">Letzte Änderung</a></td>'; 

echo '<td class="tabellen_titel" valign="top">&nbsp;</td>';  

echo '<td class="tabellen_titel" valign="top">&nbsp;</td>';  

echo '</tr>';

$sql=
    'SELECT * FROM ir_stammdaten 
        LEFT JOIN mitarbeiter ON hir_agent = hma_id 
        LEFT JOIN impact ON hir_auswirkung = uia_id  
        WHERE hir_status < 5 
        ORDER BY '.$sort_vorgabe;

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

$zaehler=0;

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {
        $hir_zeitstempel = 0;
        $uir_zeitstempel = 0;
        # Suche letzte Zeitstempel für die Anzeige
        
        $sql_time= 'SELECT uir_zeitstempel FROM ir_log
        LEFT JOIN ir_stammdaten ON uir_hirid = hir_id 
        WHERE uir_hirid = '.$zeile['hir_id'].' 
        ORDER BY uir_zeitstempel DESC LIMIT 1';

if (!($ergebnis_time =mysql_query($sql_time, $verbindung)))
    {
    fehler();
    }
    
    while ($zeile_time=mysql_fetch_array($ergebnis_time))
    {
      $uir_zeitstempel = $zeile_time['uir_zeitstempel'];  
    }
        
     
        $sql_time= 'SELECT hir_zeitstempel FROM ir_stammdaten  
        WHERE hir_id = '.$zeile['hir_id'].'  
        ORDER BY hir_zeitstempel DESC LIMIT 1';

if (!($ergebnis_time =mysql_query($sql_time, $verbindung)))
    {
    fehler();
    }
    
    while ($zeile_time=mysql_fetch_array($ergebnis_time))
    {
      $hir_zeitstempel = $zeile_time['hir_zeitstempel'];  
    }

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
       
    if(strtotime($uir_zeitstempel)>strtotime($hir_zeitstempel)) {$zeitstempel = $uir_zeitstempel; } else {$zeitstempel = $hir_zeitstempel;}
        
  
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
        . zeitstempel_anzeigen($zeitstempel) . '</td>';

    $sql_sperre = 'SELECT * FROM ir_sperre LEFT JOIN mitarbeiter ON uisp_hmaid = hma_id WHERE uisp_hirid = '.$zeile['hir_id'];
    
    if (!($ergebnis_sperre=mysql_query($sql_sperre, $verbindung)))
    {
    fehler();
    }
    
    if(mysql_num_rows($ergebnis_sperre)>0) 
    {
        while ($zeile_sperre=mysql_fetch_array($ergebnis_sperre))
        { 
            if($zeile_sperre['uisp_hmaid']!=$_SESSION['hma_id'])
            {
            echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top"><img src="bilder/icon_zurueck.gif" border="0" title="Incident Report gesperrt durch '.$zeile_sperre['hma_login'].' seit '.$zeile_sperre['uisp_zeit'].'" alt="Incident Report gesperrt durch '.$zeile_sperre['hma_login'].' seit '.$zeile_sperre['uisp_zeit'].'"></td>';              
            } 
            else if($zeile_sperre['uisp_hmaid']==$_SESSION['hma_id'])
            {
            echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top"><a href="ir_neu.php?hir_id='.$zeile['hir_id'].'"><img src="bilder/icon_lock.gif" border="0" title="Incident Report ist durch Dich gesperrt" alt="Incident Report ist durch Dich gesperrt"></a></td>';             
            }
            else if($_SESSION['hma_id']!=3) 
            {
            echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top"><a href="ir_neu.php?hir_id='.$zeile['hir_id'].'"><img src="bilder/icon_aendern.gif" border="0" title="Incident Report editieren" alt="Incident Report editieren"></a></td>'; 
            }
        }
    } else if($_SESSION['hma_id']!=3) 
    {
        echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top"><a href="ir_neu.php?hir_id='.$zeile['hir_id'].'"><img src="bilder/icon_aendern.gif" border="0" title="Incident Report editieren" alt="Incident Report editieren"></a></td>'; 
    }
    
    echo '</tr>';
    $zaehler++;
    }

echo '</table>';

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Letzte 30 archivierten IR<br><br></span>';

// Beginne mit Tabellenausgabe
echo '<table style="border: solid, 1px, black;" cellspacing="1" cellpadding="3" width="900" class="element">';

echo '<tr>';

echo '<td class="tabellen_titel" valign="top">IR-Nummer</td>';

echo '<td class="tabellen_titel" valign="top">angelegt am</td>'; 

echo '<td class="tabellen_titel" valign="top">Problem</td>';

echo '<td class="tabellen_titel" valign="top">Auswirkung</td>';

echo '<td class="tabellen_titel" valign="top">Status</td>';

echo '<td class="tabellen_titel" valign="top">Kategorie</td>';

echo '<td class="tabellen_titel" valign="top">Agent</td>';

echo '<td class="tabellen_titel" valign="top">geschlossen am</td>'; 

echo '</tr>';

$sql=
    'SELECT * FROM ir_stammdaten 
        LEFT JOIN mitarbeiter ON hir_agent = hma_id 
        LEFT JOIN impact ON hir_auswirkung = uia_id  
        WHERE hir_status = 5 
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

    echo '<td class="text_klein" bgcolor="' . $hintergrundfarbe . '" valign="top"><a href="ir_neu.php?hir_id='
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