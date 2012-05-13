<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');
require_once('segment_kopf.php');

echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Übersicht Projekte';

$sql='SELECT *, DATEDIFF(hpr_pende,curdate()) as diff FROM projekte 
        LEFT JOIN mitarbeiter ON hpr_inhaber = hma_id    
        WHERE hpr_inhaber = "' . $_SESSION['hma_id'] . '" AND hpr_fertig = "0" AND hpr_aktiv = "1" 
        ORDER BY hpr_prio, hpr_pende';
        // Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

$anzahl=mysql_num_rows($ergebnis);


echo '<br><br><span class="box">Aktuelle Projekte '.$anzahl.'</span>';

$sql='SELECT *, DATEDIFF(hpr_pende,curdate()) as diff FROM projekte 
        LEFT JOIN mitarbeiter ON hpr_inhaber = hma_id    
        WHERE hpr_inhaber = "' . $_SESSION['hma_id'] . '" AND hpr_fertig = "0" AND hpr_aktiv = "1" 
        ORDER BY hpr_prio, hpr_pende';

$anzeigefelder=array
    (
    'Nr' => 'hpr_id',
    'Titel' => 'hpr_titel',
    'Start' => 'hpr_start',
    'Ende' => 'hpr_pende'
    );

    $iconzahl=4;

$icons=array(array
    (
    "inhalt" => "delete",
    "bild" => "icon_loeschen.gif",
    "link" => "schreibtisch_projekte_loeschen.php"
    ));

$icons[]=(array
    (
    "inhalt" => "Change Project",
    "bild" => "icon_aendern.gif",
    "link" => "schreibtisch_projekte_ansehen.php"
    ));

$icons[]=(array
    (
    "inhalt" => "Special Projectinfo",
    "bild" => "icon_projectnote.png",
    "link" => "schreibtisch_neue_projektinfo.php"
    ));

$icons[]=(array
    (
    "inhalt" => "File Project",
    "bild" => "icon_erledigt.gif",
    "link" => "schreibtisch_projekte_archivieren.php"
    ));

include('seg_pro_liste.php');

echo '<br><br><span class="box">Abgeschlossene Projekte</span>';

$sql='SELECT *, DATEDIFF(hpr_pende,curdate()) as diff FROM projekte 
        LEFT JOIN mitarbeiter ON hpr_inhaber = hma_id    
        WHERE hpr_inhaber = "' . $_SESSION['hma_id'] . '" AND hpr_fertig = "1" AND hpr_aktiv = "1" 
        ORDER BY hpr_prio, hpr_pende';

$anzeigefelder=array
    (
    'Nr' => 'hpr_id',
    'Titel' => 'hpr_titel',
    'Start' => 'hpr_start',
    'Ende' => 'hpr_pende'
    );

    $iconzahl=3;

$icons=array(array
    (
    "inhalt" => "delete",
    "bild" => "icon_loeschen.gif",
    "link" => "schreibtisch_projekte_loeschen.php"
    ));

$icons[]=(array
    (
    "inhalt" => "Change Project",
    "bild" => "icon_aendern.gif",
    "link" => "schreibtisch_projekte_ansehen.php"
    ));

$icons[]=(array
    (
    "inhalt" => "Special Projectinfo",
    "bild" => "icon_projectnote.png",
    "link" => "schreibtisch_neue_projektinfo.php"
    ));

include('seg_pro_liste.php');
?>