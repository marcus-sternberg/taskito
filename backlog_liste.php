<?php
###### Editnotes ####
#$LastChangedDate: 2012-02-23 12:07:13 +0100 (Do, 23 Feb 2012) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

########################  Definiere Variablen ################################

if(!isset($_REQUEST['xProjekt'])) {$xProjekt = '1';} else {$xProjekt = $_REQUEST['xProjekt'];} 
if(!isset($_REQUEST['xGruppe'])) {$xGruppe = $_SESSION['hma_level'];} else {$xGruppe = $_REQUEST['xGruppe'];} 
if(ISSET($_REQUEST['check']) AND !ISSET($_REQUEST['filter'])) {$_SESSION['xFilter'] = 'off';} else
if(ISSET($_REQUEST['check']) AND $_REQUEST['filter']=='on') {$_SESSION['xFilter'] = 'on';} 

$zaehler=0;
$summe_horizontal=0;
$aktueller_monat = date("m");
$ruecksprung = 'bericht_rufbereitschaft_belegung.php';
$bereitschaftstage = array();

#####################################################################################
############################ Ausgabe Werte ##########################################

// Gebe Überschrift aus

echo '</table>';  // Kommt aus dem Kopf

echo '<table><tr><td width="15">&nbsp;</td><td>';    

echo '<br><table class="matrix" cellpadding = "5">';

# Definiere Ruecksprung

echo '<form action="backlog_liste.php" method="post">';

# Frage Zeitraum ab

echo '<tr>';

echo '<td class="text_klein" valign="top">Bitte gewünschtes Projekt wählen:</td>';
echo '<td class="text_klein" valign="top">Bitte gewünschte Gruppe wählen:</td>';
echo '<td class="text_klein" valign="top">Störer einblenden ';

if($_SESSION['xFilter']=='on')
{
    echo ' <input type="checkbox" checked name="filter">';
} else
{
    echo ' <input type="checkbox" name="filter">';    
}
echo '</td>';

echo '</tr>';

echo '<tr>';

echo '<td align="right">';

$sql_projekt = 'SELECT * FROM projekte LEFT JOIN mitarbeiter ON hma_id = hpr_inhaber WHERE hma_level = '.$xGruppe.' AND hpr_aktiv = 1 AND hpr_fertig = 0 AND (hpr_id = 1 OR hpr_id > 10) GROUP BY hpr_id ORDER BY hpr_titel';

echo '<select size="1" name="xProjekt">'; 

if (!$ergebnis_projekt=mysql_query($sql_projekt, $verbindung))
    {
    fehler();
    }
while ($zeile_projekt=mysql_fetch_array($ergebnis_projekt))
    {
     
    if($zeile_projekt['hpr_id']==1) {$zeile_projekt['hpr_titel']='Allgemeines Backlog';}   
    
    if ($zeile_projekt['hpr_id'] == $xProjekt)
        {
        echo '<option value="' . $zeile_projekt['hpr_id'] . '" selected><span class="text">' . $zeile_projekt['hpr_titel'] . '</span></option>';
        }
    else
        {
        echo '<option value="' . $zeile_projekt['hpr_id'] . '"><span class="text">' . $zeile_projekt['hpr_titel'] . '</span></option>';  }   
    }  
    

echo '</select> ';

echo '</td>';

echo '<td>';

echo '<select size="1" name="xGruppe">';

$sql_gruppe = 'SELECT * FROM level WHERE ule_aktiv = 1 ORDER BY ule_name';

if (!$ergebnis_gruppe=mysql_query($sql_gruppe, $verbindung))
    {
    fehler();
    }
while ($zeile_gruppe=mysql_fetch_array($ergebnis_gruppe))
    {
    if ($zeile_gruppe['ule_id'] == $xGruppe)
        {
        echo '<option value="' . $zeile_gruppe['ule_id'] . '" selected><span class="text">' . $zeile_gruppe['ule_name'] . '</span></option>';
        }
    else
        {
        echo '<option value="' . $zeile_gruppe['ule_id'] . '"><span class="text">' . $zeile_gruppe['ule_name'] . '</span></option>';
        }   
    }

echo '</select> ';

echo '</td>';

echo '<td align="right">';

echo '<input type="submit" value="Zeige Ansicht" class="formularbutton" name="check"/>';

echo '</td></tr>';

echo '</table>';

echo '</form>';

echo '<br><br>';

# Werte Filter aus

if($_SESSION['xFilter'] == 'on')
{
  $filterstring = ' (hba_hprid = 1 OR hba_hprid = '.$xProjekt .') ';  
} else
{
  $filterstring = ' hba_hprid = '.$xProjekt .' ';     
}


// Starte Tabelle

# Baue 3-spaltige Layouttabelle

echo '<table class="is24" width="900">';

echo '<caption class="is24">';

echo 'BACKLOG&nbsp;&nbsp;&nbsp;<a href="backlog_neuer_eintrag.php"><img src="bilder/icon_neu.gif" width="16" height="16" border="0" alt="Neuen Backlogeintrag erzeugen" title="Neuen Backlogeintrag erzeugen"></a>';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';   

echo '<th class="is24">OFFEN</th>';

echo '<th class="is24">IN ARBEIT</th>';

echo '<th class="is24">ERLEDIGT</th>';

echo '</tr>';

echo '</thead>';

echo '<tr>';
echo '<td valign="top">'; // erste Spalte BACKLOG

echo '<table width="300" cellpadding = "5">';

$sql = 'SELECT * FROM backlog 
        LEFT JOIN prioritaet ON upr_nummer = hba_uprid
        LEFT JOIN projekte ON hpr_id = hba_hprid
        LEFT JOIN level ON ule_id = hba_gruppe
        LEFT JOIN mitarbeiter ON hma_id = hba_hmaid
        WHERE hba_gruppe = '.$xGruppe.' AND '.$filterstring.' AND hba_status = 1 
        ORDER BY upr_sort, hba_anlage';

       
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {

        echo '<tr>';
        switch($zeile['hba_uprid'])
        {
            case 3:
            $farbe = '#FFBFA0';
            break;
            
            case 2:
            $farbe = '#FFF8B3';
            break;            

            case 1:
            $farbe = '#C1E2A5';
            break;

            case 4:
            $farbe = '#CED9E7';
            break;
            
        }
        if($zeile['hba_hprid']==1) {
        echo '<td style="border: 5px solid red; background-color: ' . $farbe . ';">';
        } else
        {
        echo '<td style="border: 5px solid green; background-color: ' . $farbe . ';">';            
        }
        echo '<span style="font-size:14px;font-weight:bold;">'.$zeile['hba_titel'].'</span><br>';
        echo 'liegt bei: '.$zeile['hma_login'].'<br>';       
        echo 'vom '.datum_wandeln_useu($zeile['hba_anlage']).'<br>';
        echo '<hr>';
        if($zeile['hba_uprid']==4) 
        {
            $prio_down = 4;
            $prio_up = 1;
        } else {
            $prio_down = $zeile['hba_uprid']-1;
            $prio_up = $zeile['hba_uprid']+1;        
        }
        if($prio_down<1){$prio_down=4;}
        if($prio_up>3){$prio_up=3;}
        echo '<a href="backlog_prio.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'&hba_prio='.$prio_up.'"><img src="bilder/arrow_up.png" width="16" height="16" border="0" alt="Prio hochstufen" title="Prio hochstufen"></a>&nbsp;';
        echo '<a href="backlog_prio.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'&hba_prio='.$prio_down.'"><img src="bilder/arrow_down.png" width="16" height="16" border="0" alt="Prio runterstufen" title="Prio runterstufen"></a>&nbsp;';
        echo '<a href="backlog_transfer.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'"><img src="bilder/icon_erneut.gif" width="16" height="16" border="0" alt="Aufgabe als Ticket anlegen" title="Aufgabe als Ticket anlegen"></a>&nbsp;';
        echo '<a href="backlog_move.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'&hba_status=2"><img src="bilder/icon_arbeit.gif" width="16" height="16" border="0" alt="Aufgabe in Bearbeitung setzen" title="Aufgabe in Bearbeitung setzen"></a>&nbsp;';
        echo '<a href="backlog_move.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'&hba_status=3"><img src="bilder/icon_erledigt.gif" width="16" height="16" border="0" alt="Aufgabe als erledigt markieren" title="Aufgabe als erledigt markieren"></a>&nbsp;';        
        echo '<a href="backlog_move_delete.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'""><img src="bilder/icon_loeschen.gif" width="16" height="16" border="0" alt="Aufgabe löschen" title="Aufgabe löschen"></a>'; 
     
        
               echo '<form method="post" action="backlog_ma.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'">';
                echo '&nbsp;<select name="hba_hmaid" onChange="this.form.submit();">';

$sql_ma=
    'SELECT hma_id, hma_login, hma_level FROM mitarbeiter WHERE hma_level > 1 AND hma_level < 99 AND hma_aktiv = 1 ORDER BY hma_login';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_ma=mysql_query($sql_ma, $verbindung))
    {
    fehler();
    }

while ($zeile_ma=mysql_fetch_array($ergebnis_ma))
    {

     if ($zeile_ma['hma_id'] == $zeile['hba_hmaid'])
        {
        echo '<option value="' . $zeile_ma['hma_id'] . '" selected><span class="text">' . $zeile_ma['hma_login'] . '</span></option>';
        }
    else
        {
        echo '<option value="' . $zeile_ma['hma_id'] . '"><span class="text">' . $zeile_ma['hma_login'] . '</span></option>';     } 

   }

echo '</select>';
echo '</form>';
        
        echo '</td></tr>';
    }

echo '</table>';
echo '</td><td valign="top">'; // 2. Spalte IN PROGRESS

echo '<table width="300" cellpadding = "5">';

$sql = 'SELECT * FROM backlog 
        LEFT JOIN prioritaet ON upr_nummer = hba_uprid
        LEFT JOIN projekte ON hpr_id = hba_hprid
        LEFT JOIN level ON ule_id = hba_gruppe
        LEFT JOIN mitarbeiter ON hma_id = hba_hmaid
        WHERE hba_gruppe = '.$xGruppe.' AND '.$filterstring.' AND hba_status = 2
        ORDER BY upr_sort, hba_anlage';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {

        echo '<tr>';
        switch($zeile['hba_uprid'])
        {
            case 3:
            $farbe = '#FFBFA0';
            break;
            
            case 2:
            $farbe = '#FFF8B3';
            break;            

            case 1:
            $farbe = '#C1E2A5';
            break;

            case 4:
            $farbe = '#CED9E7';
            break;
            
        }
        
        if($zeile['hba_hprid']==1) {
        echo '<td style="border: 5px solid red; background-color: ' . $farbe . ';">';
        } else
        {
        echo '<td style="border: 5px solid green; background-color: ' . $farbe . ';">';            
        }
        echo '<span style="font-size:14px;font-weight:bold;">'.$zeile['hba_titel'].'</span><br>';
        echo 'liegt bei: '.$zeile['hma_login'].'<br>';       
        echo 'vom '.datum_wandeln_useu($zeile['hba_anlage']).'<br>';
        echo '<hr>';
        if($zeile['hba_uprid']==4) 
        {
            $prio_down = 4;
            $prio_up = 1;
        } else {
            $prio_down = $zeile['hba_uprid']-1;
            $prio_up = $zeile['hba_uprid']+1;        
        }
        if($prio_down<1){$prio_down=4;}
        if($prio_up>3){$prio_up=3;}
        echo '<a href="backlog_prio.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'&hba_prio='.$prio_up.'"><img src="bilder/arrow_up.png" width="16" height="16" border="0" alt="Prio hochstufen" title="Prio hochstufen"></a>&nbsp;';
        echo '<a href="backlog_prio.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'&hba_prio='.$prio_down.'"><img src="bilder/arrow_down.png" width="16" height="16" border="0" alt="Prio runterstufen" title="Prio runterstufen"></a>&nbsp;';
        echo '<a href="backlog_transfer.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'"><img src="bilder/icon_erneut.gif" width="16" height="16" border="0" alt="Aufgabe als Ticket anlegen" title="Aufgabe als Ticket anlegen"></a>&nbsp;';
        echo '<a href="backlog_move.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'&hba_status=1"><img src="bilder/icon_offen.gif" width="16" height="16" border="0" alt="Aufgabe auf unbearbeitet setzen" title="Aufgabe auf unbearbeitet setzen"></a>&nbsp;';
        echo '<a href="backlog_move.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'&hba_status=3"><img src="bilder/icon_erledigt.gif" width="16" height="16" border="0" alt="Aufgabe als erledigt markieren" title="Aufgabe als erledigt markieren"></a>&nbsp;';       
      
        echo '<a href="backlog_move_delete.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'""><img src="bilder/icon_loeschen.gif" width="16" height="16" border="0" alt="Aufgabe löschen" title="Aufgabe löschen"></a>'; 

        
               echo '<form method="post" action="backlog_ma.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'">';
                echo '&nbsp;<select name="hba_hmaid" onChange="this.form.submit();">';

$sql_ma=
    'SELECT hma_id, hma_login, hma_level FROM mitarbeiter WHERE hma_level > 1 AND hma_level < 99 AND hma_aktiv = 1 ORDER BY hma_login';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_ma=mysql_query($sql_ma, $verbindung))
    {
    fehler();
    }

while ($zeile_ma=mysql_fetch_array($ergebnis_ma))
    {

     if ($zeile_ma['hma_id'] == $zeile['hba_hmaid'])
        {
        echo '<option value="' . $zeile_ma['hma_id'] . '" selected><span class="text">' . $zeile_ma['hma_login'] . '</span></option>';
        }
    else
        {
        echo '<option value="' . $zeile_ma['hma_id'] . '"><span class="text">' . $zeile_ma['hma_login'] . '</span></option>';     } 

   }

echo '</select>';
echo '</form>';
        
        
        echo '</td></tr>';
    }

echo '</table>';
echo '</td><td valign="top">'; // 3. Spalte DONE

echo '<table width="300" cellpadding = "5">';

$sql = 'SELECT * FROM backlog 
        LEFT JOIN prioritaet ON upr_nummer = hba_uprid
        LEFT JOIN projekte ON hpr_id = hba_hprid
        LEFT JOIN level ON ule_id = hba_gruppe
        LEFT JOIN mitarbeiter ON hma_id = hba_hmaid
        WHERE hba_gruppe = '.$xGruppe.' AND '.$filterstring.' AND hba_status = 3
        ORDER BY upr_sort, hba_anlage';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {

        echo '<tr>';
        if($zeile['hba_hprid']==1) {
        echo '<td style="border: 5px solid red; background-color: #E3E3E3;">';
        } else
        {
        echo '<td style="border: 5px solid green; background-color: #E3E3E3;">';            
        }
        echo '<span style="font-size:14px;font-weight:bold;">'.$zeile['hba_titel'].'</span><br>';
        echo 'lag bei: '.$zeile['hma_login'].'<br>';        
        echo 'vom '.datum_wandeln_useu($zeile['hba_anlage']).'<br>';
        echo '<hr>';
        if($zeile['hba_uprid']==4) 
        {
            $prio_down = 4;
            $prio_up = 1;
        } else {
            $prio_down = $zeile['hba_uprid']-1;
            $prio_up = $zeile['hba_uprid']+1;        
        }
        if($prio_down<1){$prio_down=4;}
        if($prio_up>3){$prio_up=3;}
        echo '<a href="backlog_move.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'&hba_status=1"><img src="bilder/icon_offen.gif" width="16" height="16" border="0" alt="Aufgabe auf unbearbeitet setzen" title="Aufgabe auf unbearbeitet setzen"></a>&nbsp;';
        echo '<a href="backlog_move.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'&hba_status=2"><img src="bilder/icon_arbeit.gif" width="16" height="16" border="0" alt="Aufgabe in Bearbeitung setzen" title="Aufgabe in Bearbeitung setzen"></a>&nbsp;';       
  
        echo '<a href="backlog_move_delete.php?hba_id='.$zeile['hba_id'].'&xGruppe='.$xGruppe.'&xProjekt='.$xProjekt.'""><img src="bilder/icon_loeschen.gif" width="16" height="16" border="0" alt="Aufgabe löschen" title="Aufgabe löschen"></a>'; 
        echo '</td></tr>';
    }

echo '</table>';
echo '</td></tr>';

echo '<tr><td colspan="3">';
echo '<br><br>roter Rand = Störaufgabe / grüner Rand = Aufgabe des Projektes<br>';
echo 'Priorität: Rot = kritisch, gelb = wichtig, grün = normal, blau = keine Priorität';

echo '</td></tr></table>';

echo '</td></tr></table>'; // Layout Abstand




include('segment_fuss.php');
?>
