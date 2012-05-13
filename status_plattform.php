<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$session_frei = 1;
############## INCLUDES ################

require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_init.php');
include('segment_kopf.php');

############# Variablen ################

$xSystem_typ = array();
$xSystem_check = array();
$xFormulardaten=array();

############# Ermittle Plattform ###################

if(!isset($_REQUEST['xSystem'])) {$xSystem = 1;} else {$xSystem = $_REQUEST['xSystem'];}

###############################################

######## Prüfe, ob Formular abgeschickt wurde ###############
######## Falls ja, werte Daten aus, aktualisiere DB und starte eMail-Versand #############

echo '<form action="status_plattform.php" method="post">';
    
echo '<table>';

echo '<tr><td>';

echo '<select size="1" name="xSystem">';

$sql_filter='SELECT * FROM system_plattformen ORDER BY hpl_name';

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_filter=mysql_query($sql_filter, $verbindung))
    {
    fehler();
    }

while ($zeile_filter=mysql_fetch_array($ergebnis_filter))
    {
    if ($xSystem == $zeile_filter['hpl_id'])
        {
        echo '<option value="' . $zeile_filter['hpl_id']
            . '" selected style="background-color:#E28B78;"><span class="text">' . $zeile_filter['hpl_name']
            . '</span></option>';
        }
    else
        {
        echo '<option value="' . $zeile_filter['hpl_id'] . '"><span class="text">' . $zeile_filter['hpl_name']
            . '</span></option>';
        }
    }

echo '</select>';

echo '<span style="vertical-align:top;">&nbsp;&nbsp;<input type="submit" name="filter" value="Plattform wählen" class="formularbutton" />';

echo '</td></tr>';

echo '</table>';

echo '</form>';

echo '<br>';
    
############## Zeige die aktuellen Statusdaten an #########################
 
$sql = 'SELECT hpl_name, hpl_status, hpl_bemerkung, hpl_version FROM system_plattformen WHERE hpl_id = '.$xSystem;

if (!$ergebnis=mysql_query($sql, $verbindung))
   {
   fehler();
   }

while ($zeile=mysql_fetch_array($ergebnis))
   {
   $xSystem_typ[$xSystem]['hpl_name']=$zeile['hpl_name'];
   $xSystem_typ[$xSystem]['hpl_status']=$zeile['hpl_status'];
   $xSystem_typ[$xSystem]['hpl_version']=$zeile['hpl_version'];
   $xSystem_typ[$xSystem]['hpl_bemerkung']=$zeile['hpl_bemerkung'];
   }
    
echo '<table class="is24" cellpadding="5">';

echo '<caption class="is24">';

echo $xSystem_typ[$xSystem]['hpl_name'];

echo '</caption>';

echo '<tr><td>Version</td><td>'.$xSystem_typ[$xSystem]['hpl_version'].'</td></tr>';

echo '<tr><td>Status</td><td>';

switch ($xSystem_typ[$xSystem]['hpl_status'])
{
    case 0:
        echo '<img src="bilder/icon_quad_rot.gif" alt="Server down" title="Server down">';
        break;

    case 1:
        echo '<img src="bilder/icon_quad_gruen.gif" alt="Server up" title="Server up">';
        break;
}

echo '</td></tr>';

echo '<tr><td valign="top">Bemerkung</td><td>'.$xSystem_typ[$xSystem]['hpl_bemerkung'].'</td></tr>';

echo '</table>';

echo '<br>';

echo '<table class="matrix" >';

echo '<thead class="is24">';

echo '<tr class="is24">';   

echo '<th class="is24">Arbeitsschritt</th>';

echo '<th class="is24">Status</th>';

echo '<th class="is24">Bemerkung</th>';

echo '<th class="is24">Letzte Aktualisierung</th>';

echo '<th class="is24">durch</th>';  

echo '</tr>';

echo '</thead>';

echo '<tbody class="is24">'; 

$sql = 'SELECT * FROM system_schritte 
        LEFT JOIN system_checkliste ON hsc_hssid = hss_id
        LEFT JOIN mitarbeiter ON hma_id = hsc_hmaid 
        WHERE hsc_hplid = '.$xSystem.' ORDER BY hss_sort';

if (!$ergebnis=mysql_query($sql, $verbindung))
   {
   fehler();
   }

while ($zeile=mysql_fetch_array($ergebnis))
   {
   $xSystem_check[$zeile['hss_id']]['hsc_status']=$zeile['hsc_status'];
   $xSystem_check[$zeile['hss_id']]['hsc_bemerkung']=$zeile['hsc_bemerkung'];
   $xSystem_check[$zeile['hss_id']]['hss_name']=$zeile['hss_name'];
   $xSystem_check[$zeile['hss_id']]['hss_id']=$zeile['hss_id'];
   $xSystem_check[$zeile['hss_id']]['hsc_zeitstempel']=$zeile['hsc_zeitstempel'];
   $xSystem_check[$zeile['hss_id']]['hma_name']=$zeile['hma_name']; 
   }
   
foreach($xSystem_check AS $schritte)
{
  
    echo '<tr><td>'.$schritte['hss_name'].'</td>';

        switch ($schritte['hsc_status'])
            {
            case 0:

                echo '<td bgcolor="#EE775F" align="center">unbearbeitet</td>';
                 
                echo '<td align="center">' . $schritte['hsc_bemerkung'] . '</td>';
                break;

            case 1:
                echo '<td bgcolor="#FFF8B3" align="center">in Arbeit</td>';
                 
                echo '<td align="center">' . $schritte['hsc_bemerkung'] . '</td>';
                break;

             case 2:

                  echo '<td bgcolor="#C1E2A5" align="center">erledigt</td>';
                 
                echo '<td align="center">' . $schritte['hsc_bemerkung'] . '</td>';
                break;
               
              case 3:
                  echo '<td bgcolor="#c2c2c2" align="center">entfällt</td>';
                 
                echo '<td align="center">' . $schritte['hsc_bemerkung'] . '</td>';
                break;
            }

    echo '<td>'.zeitstempel_anzeigen($schritte['hsc_zeitstempel']).'</td>';    
    echo '<td>'.$schritte['hma_name'].'</td>';  
}
echo '</tr>';

echo '<td class="xnormal_sort" align="right"><a href="mail_add_status.php?xSystem=' . $xSystem
    . '" title="Updates im Status werden an die hinterlegte eMail geschickt.">Updates beim Status abonnieren</a></td>';

echo '</tbody>';

echo '</table>';

include('segment_fuss.php');
?>
