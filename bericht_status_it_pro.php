<?php
###### Editnotes ####
#$LastChangedDate: 2012-01-10 14:14:54 +0100 (Di, 10 Jan 2012) $
#$Author: msternberg $ 
#####################
$session_frei = 1;

require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_kopf.php');

# Definiere Rücksprung

echo '<form action="bericht_status_it_pro.php" method="post">';

# Baue Tabelle

echo '<table border=0>';

echo '<tr>';

echo '<td>';

echo '<table border=0';

echo '<tr>';

echo '<td>';

if (isset($_REQUEST['xKw']))
    {
    $xKw=$_REQUEST['xKw'];
    }
else
    {
    $xKw=date('W');
    }

echo '<select size="1" name="xKw">';

for ($i=1; $i < 54; $i++)
    {
    if ($xKw == $i)
        {
        echo '<option value="' . $i . '" selected><span class="text">' . $i . '. KW</span></option>';
        }
    else
        {
        echo '<option value="' . $i . '"><span class="text">' . $i . '. KW</span></option>';
        }
    }

echo '</select> ';

echo '</td>';

echo '<td>';

if (isset($_REQUEST['xJahr']))
    {
    $xJahr=$_REQUEST['xJahr'];
    }
else
    {
    $xJahr=date('Y');
    }

echo '<select size="1" name="xJahr">';

for ($i=2011; $i < 2014; $i++)
    {
    if ($xJahr == $i)
        {
        echo '<option value="' . $i . '" selected><span class="text">' . $i . '</span></option>';
        }
    else
        {
        echo '<option value="' . $i . '"><span class="text">' . $i . '</span></option>';
        }
    }

echo '</select> ';

echo '</td>';
 
echo '<td align="right">';

echo '<input type="submit" value="Zeige KW" class="formularbutton" name="range"/>';

echo '</td></tr>';

echo '</table>';  

echo '</form>';

### Initialisiere Variablen ###

$defcon_feld = array();
$ir_feld = array();
$deployment_feld = array();
$ticket_feld = array();
$feld_index = 0;
$ticket_fertig_gesamt = 0;
$ticket_neu_gesamt = 0;
$ticket_offen_gesamt = 0;
$ticket_changes_gesamt = 0;

### Lege Zeitraum des Berichts fest 

$xMontag=date("Y-m-d 00:00:00", mondaykw($xKw, $xJahr));
$xSonntag = date ('Y-m-d 23:59:59' , strtotime("$xMontag +6 days"));

#$xMontag = '2011-07-11 00:00:00';
#$xSonntag = '2011-07-17 00:00:00';

###  Lese die DEFCON-Status des Zeitraumes aus

$feld_index = 0; 

$sql = 'SELECT * FROM defcon
        LEFT JOIN mitarbeiter ON hma_id = ude_hmaid 
        WHERE (ude_zeitstempel BETWEEN "'.$xMontag.'" 
        AND "'.$xSonntag.'") 
        ORDER BY ude_zeitstempel DESC';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
      $defcon_feld[$feld_index]['zeit'] = $zeile['ude_zeitstempel'];
      $defcon_feld[$feld_index]['status'] = $zeile['ude_status'];
      $defcon_feld[$feld_index]['ir'] = $zeile['ude_irid'];  
      $feld_index++;     
    }
    
### Lese die Incident-Reports ein

$feld_index = 0; 

$sql=  'SELECT * FROM ir_stammdaten 
        LEFT JOIN impact ON uia_id = hir_auswirkung 
        WHERE (hir_datum BETWEEN "'.$xMontag.'" 
        AND "'.$xSonntag.'") ORDER BY hir_auswirkung'; 

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
      $ir_feld[$feld_index]['datum'] = $zeile['hir_datum'];
      $ir_feld[$feld_index]['nummer'] = $zeile['hir_id'];
      $ir_feld[$feld_index]['incident'] = $zeile['hir_problem'];  
      $ir_feld[$feld_index]['impact'] = $zeile['uia_name'];  
      $ir_feld[$feld_index]['release'] = $zeile['hir_release'];  
      $feld_index++;     
    }

### Lese die Deployments aus (Typ = 11)

$feld_index = 0; 

$sql=  'SELECT * FROM aufgaben 
        WHERE (hau_abschlussdatum BETWEEN "'.$xMontag.'" 
        AND "'.$xSonntag.'") AND hau_aktiv =1 AND hau_abschluss = 1 AND hau_typ = 11 ORDER BY hau_abschlussdatum DESC'; 

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
      $deployment_feld[$feld_index]['datum'] = $zeile['hau_abschlussdatum'];
      $deployment_feld[$feld_index]['inhalt'] = $zeile['hau_titel'];
      $deployment_feld[$feld_index]['ticket'] = $zeile['hau_id'];
      $feld_index++;     
    } 
 
### Lese die Tickets aus
## Lese dazu zunächst die Gruppen ein

$feld_index = 0; 

$sql_gruppe = 'SELECT ule_id, ule_kurz FROM level WHERE ule_aktiv =1 AND ule_id < 10 ORDER BY ule_kurz';

if (!($ergebnis_gruppe=mysql_query($sql_gruppe, $verbindung)))
    {
    fehler();
    }

while ($zeile_gruppe=mysql_fetch_array($ergebnis_gruppe))
    {
     
$neue_tickets = 0;        
$fertige_tickets = 0;
$offene_tickets = 0;
$changes_ticket = 0;
$offene_tickets_ohne_ma = 0;
$offene_tickets_mit_ma = 0;  

# Lese zunächst die neu hereingekommen Tickets

$sql=  'SELECT COUNT(DISTINCT hau_id) AS anzahl FROM aufgaben 
        LEFT JOIN aufgaben_zuordnung ON hau_id = uaz_hauid 
        LEFT JOIN level ON uaz_pg = ule_id 
        WHERE (hau_anlage BETWEEN "'.$xMontag.'" AND "'.$xSonntag.'")  
        AND hau_aktiv =1 
        AND uaz_pg = '.$zeile_gruppe['ule_id'].'
        GROUP BY ule_kurz'; 
        
if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
       $neue_tickets = $zeile['anzahl'];
    }       
    
# Lese nun die abgeschlossenen Tickets

$sql=  'SELECT COUNT(DISTINCT hau_id) AS anzahl FROM aufgaben 
        LEFT JOIN aufgaben_zuordnung ON hau_id = uaz_hauid 
        LEFT JOIN level ON uaz_pg = ule_id 
        WHERE (hau_abschlussdatum BETWEEN "'.$xMontag.'" AND "'.$xSonntag.'")  
        AND hau_aktiv =1 
        AND hau_abschluss = 1
        AND uaz_pg = '.$zeile_gruppe['ule_id'].'
        GROUP BY ule_kurz'; 
        
if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
       $fertige_tickets = $zeile['anzahl'];
    }       
    
# Lese nun die noch offenen Tickets
# Zunächst die ohne Bearbeiter

$sql='SELECT COUNT(DISTINCT hau_id) AS anzahl FROM aufgaben 
    LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
    LEFT JOIN level ON uaz_pg = ule_id 
    WHERE uaz_pba = 0 AND hau_aktiv = 1 AND hau_abschluss = 0 
    AND uaz_pg = '.$zeile_gruppe['ule_id'].'
    AND hau_anlage < "'.$xSonntag.'" 
    GROUP BY ule_kurz'; 
                
if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
       $offene_tickets_ohne_ma = $zeile['anzahl'];
    }   
 
# Jetzt die mit Bearbeiter 
 
$sql='SELECT COUNT(DISTINCT hau_id) AS anzahl FROM aufgaben 
    LEFT JOIN aufgaben_mitarbeiter ON uau_hauid = hau_id    
    LEFT JOIN aufgaben_zuordnung ON uaz_hauid = hau_id 
    LEFT JOIN level ON uaz_pg = ule_id      
    WHERE uau_status = 0 AND hau_aktiv = 1 AND hau_abschluss = 0 
    AND uaz_pg = '.$zeile_gruppe['ule_id'].'
    AND hau_anlage < "'.$xSonntag.'"  
    GROUP BY ule_kurz'; 
                         
if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
       $offene_tickets_mit_ma = $zeile['anzahl'];
    }   
    
$offene_tickets = $offene_tickets_mit_ma + $offene_tickets_ohne_ma;
    
# Lese nun die Changes ein

$sql=  'SELECT COUNT(DISTINCT hau_id) AS anzahl FROM aufgaben 
        LEFT JOIN mitarbeiter ON hma_id = hau_inhaber 
        LEFT JOIN level ON hma_level = ule_id 
        WHERE (hau_abschlussdatum BETWEEN "'.$xMontag.'" AND "'.$xSonntag.'")  
        AND hau_aktiv =1 
        AND ule_id = '.$zeile_gruppe['ule_id'].'
        AND hau_hprid = 6  
        GROUP BY ule_kurz'; 
        
          
if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
       $changes_ticket = $zeile['anzahl'];
    }  
    
# Jetzt speichere die Werte für die Gruppe

if($offene_tickets > 0)
{
      $ticket_feld[$feld_index]['gruppe'] = $zeile_gruppe['ule_kurz'];
      $ticket_feld[$feld_index]['neu'] = $neue_tickets;
      $ticket_feld[$feld_index]['fertig'] = $fertige_tickets;
      $ticket_feld[$feld_index]['offen'] = $offene_tickets;
      $ticket_feld[$feld_index]['changes'] = $changes_ticket;  
      $feld_index++;   
}
} 

### Gebe Seitentiel aus        
        
echo '<br><table class="is24" cellpadding = "5">';

echo '<tr>';

echo '<td class="text_mitte">';

echo '<img src="bilder/block.gif">&nbsp;Bericht für den Zeitraum '.datum_anzeigen($xMontag).' bis '.datum_anzeigen($xSonntag);

if($_SESSION['hma_id']==76)
{
echo '<a href="bericht_status_mailen.php?xKw='.$xKw.'&xJahr='.$xJahr.'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Bericht mailen]</a>';
}
echo '</td>';

echo '</tr>';

echo '</table>';

echo '<br><br>';

### Gebe DEFCON-Status aus

echo '<table class="is24" width="600">';

echo '<caption class="is24">';

echo 'DEFCON Statusänderungen';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Incidentreport</th>';

echo '<th class="is24">Zeit der Änderung</th>';

echo '<th class="is24">Status</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';

foreach($defcon_feld AS $defcon_detail)
{
    echo '<tr>';

    echo '<td class="is24"><a href="ir_ansicht.php?hir_id='.$defcon_detail['ir'].'">' . $defcon_detail['ir'] . '</a></td>';
    echo '<td class="is24">' . zeitstempel_anzeigen($defcon_detail['zeit']) . '</td>';
    echo '<td class="is24">' . $defcon_detail['status'] . '</td>';
    echo '</tr>';
}
echo '</tbody>'; 
echo '</table>';

echo '<br><br>';

### Gebe IR-Liste aus

echo '<table class="is24" width="600">';

echo '<caption class="is24">';

echo 'Incidents im Zeitraum';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Report-Nummer</th>';

echo '<th class="is24">Datum Incident</th>';

echo '<th class="is24">Problem</th>';

echo '<th class="is24">Einstufung</th>';

echo '<th class="is24">Grund Release</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';

foreach($ir_feld AS $ir_detail)
{
    echo '<tr>';

    echo '<td class="is24"><a href="ir_ansicht.php?hir_id='.$ir_detail['nummer'].'">' . $ir_detail['nummer'] . '</a></td>';
    echo '<td class="is24">' . datum_anzeigen($ir_detail['datum']) . '</td>';
    echo '<td class="is24">' . $ir_detail['incident'] . '</td>';
    echo '<td class="is24">' . $ir_detail['impact'] . '</td>';    
    echo '<td class="is24">' . $ir_detail['release'] . '</td>'; 
    
    echo '</tr>';
}
echo '</tbody>'; 
echo '</table>';

echo '<br><br>';

### Gebe Deployments aus


echo '<table class="is24" width="600">';

echo '<caption class="is24">';

echo 'Deployments im Zeitraum';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Ticket</th>';

echo '<th class="is24">ausgerollt am</th>';

echo '<th class="is24">Thema</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';

foreach($deployment_feld AS $deploy_detail)
{
    echo '<tr>';

    echo '<td class="is24"href="aufgabe_ansehen.php?hau_id='.$deploy_detail['ticket'].'">' . $deploy_detail['ticket'] . '</a></td>';
    echo '<td class="is24">' . datum_anzeigen($deploy_detail['datum']) . '</td>';
    echo '<td class="is24">' . $deploy_detail['inhalt'] . '</td>';
   
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';

echo '<br><br>';

### Gebe Tickets aus

echo '<table class="is24" width="600">';

echo '<caption class="is24">';

echo 'Ticketfluss im Zeitraum';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Gruppe</th>';

echo '<th class="is24">Offene Tickets*</th>';

echo '<th class="is24">Neue Tickets</th>';

echo '<th class="is24">Abgeschlossene Tickets</th>';

echo '<th class="is24">Abgeschlossene Changes</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';

foreach($ticket_feld AS $ticket_detail)
{
    echo '<tr>';

    echo '<td class="is24">' . $ticket_detail['gruppe'] . '</td>';
    echo '<td class="is24">' . $ticket_detail['offen'] . '</td>';
    echo '<td class="is24">' . $ticket_detail['neu'] . '</td>';
    echo '<td class="is24">' . $ticket_detail['fertig'] . '</td>';
    echo '<td class="is24">' . $ticket_detail['changes'] . '</td>';  
    
    $ticket_offen_gesamt = $ticket_offen_gesamt + $ticket_detail['offen'];     
    $ticket_neu_gesamt = $ticket_neu_gesamt + $ticket_detail['neu'];   
    $ticket_fertig_gesamt = $ticket_fertig_gesamt + $ticket_detail['fertig']; 
    $ticket_changes_gesamt = $ticket_changes_gesamt + $ticket_detail['changes'];  
    echo '</tr>';
}
echo '<thead class="is24">';

echo '<tr class="is24">';

    echo '<th class="is24">Summe</td>';
    echo '<th class="is24">' . $ticket_offen_gesamt . '</td>';
    echo '<th class="is24">' . $ticket_neu_gesamt . '</td>';
    echo '<th class="is24">' . $ticket_fertig_gesamt . '</td>';
    echo '<th class="is24">' . $ticket_changes_gesamt . '</td>';  
    echo '</tr>';

echo '</thead>';
echo '</tbody>';
echo '</table>';

echo '* Stand '.datum_anzeigen($xSonntag);

include('segment_fuss.php');
?>