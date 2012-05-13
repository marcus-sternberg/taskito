<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
include('segment_kopf.php');

if(isset($_GET['cogrup_id'])) {$cogrup_id = $_GET['cogrup_id'];}

$kategorie = 1; // Für das Seitenmenü (1=Organisation)

echo '<table>'; // Layout

echo '<tr><td width="100" valign="top">'; // Randnavigation

echo '<table width="100">';

$sql = 'SELECT * FROM menu_cmdb WHERE kategorie = '.$kategorie.' ORDER BY name';

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

while ($zeile=mysql_fetch_array($ergebnis))
    {
echo '<tr><td><a href="'.$zeile['link'].'">'.$zeile['name'].'</a></td></tr>';
    }
    
echo '</table>';
    
echo '</td><td>'; // Eingabebereich

echo '<table id="is24_vertikal" width="815">';

echo '<caption class="is24">';
echo 'Details für diese Gruppe';
echo '</caption>';

echo '<colgroup>';
echo '<col class="is24-first" />';
echo '</colgroup>';

echo '<tbody>';

$sql='  SELECT * FROM cmdb_obj_gruppe
        WHERE cogrup_id = '.$cogrup_id;

if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }
    
while ($zeile=mysql_fetch_array($ergebnis))
    {

    echo '<tr>';
    echo '<td valign="top" width="15%">Kurzname: </td><td class="text" align="left">'
    . $zeile['cogrup_kurzname'] . '</td></tr>';
    
    echo '<td valign="top" width="15%">Name: </td><td class="text" align="left">'
    . $zeile['cogrup_name'] . '</td></tr>';

    echo '<td valign="top" width="15%">angelegt am: </td><td class="text" align="left">'
    . datum_anzeigen($zeile['cogrup_anlage']) . '</td></tr>';

    }
    echo '</tbody';
    echo '</table>';
    
echo '<table class="is24">';

echo '<caption class="is24">';

echo 'Gruppenmitglieder</span>';

echo '</caption>';

echo '<thead class="is24">';

echo '<tr class="is24">';

echo '<th class="is24">Name</th>';

echo '<th class="is24">Rolle</th>';

echo '</tr>';

echo '</thead>';

echo '<tbody  class="is24">';

    $sql_person = 'SELECT * FROM cmdb_obj_person
                LEFT JOIN obj_person2obj_gruppe ON obpege_copersid = copers_id
                LEFT JOIN cmdb_obj_gruppe ON cogrup_id = obpege_cogrupid
                LEFT JOIN cmdb_sys_rollen ON coroll_id = obpege_corollid
                WHERE cogrup_id = '.$cogrup_id .'
                ORDER BY obpege_corollid, copers_name';
        
        if (!$ergebnis_person=mysql_query($sql_person, $verbindung))
        {
            fehler();
        }

        while ($zeile_person=mysql_fetch_array($ergebnis_person))
        {

        echo '<tr><td valign="top">';
        echo '<a href="cmdb_person_detail.php?copers_id='.$zeile_person['copers_id'].'">'.$zeile_person['copers_name'].'</a>';
        echo '</td><td>';
        echo $zeile_person['coroll_name'];
        echo '</td></tr>';
        }


    echo '</tbody';
    echo '</table>';
    echo '<br><br>';
    echo '</td></tr></table>';
   
    
include('segment_fuss.php');
?>