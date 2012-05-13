<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
$session_frei=1;

require_once('konfiguration.php');
include('segment_session_pruefung.php');

$Daten=array();

foreach ($_POST as $varname => $value)
    {
    $Daten[$varname]=$value;
    }

if (($_FILES["hau_datei"]["tmp_name"] != '') AND ($Daten['uir_eintrag']==''))
{
    $Daten['uir_eintrag']='Datei '.$_FILES["hau_datei"]["name"].' hochgeladen';
}
    
    
// Umwandlung des Datumsfeldes in DATETIME

$DatumZeit=explode(" ", $Daten['uir_datum']);
$Datum=explode(".", $DatumZeit[0]);
$Zeit=explode(":", $DatumZeit[1]);

if (count($Zeit) < 2)
    {
    $Zeit[0]='12';
    $Zeit[1]='00';
    }
else if ($Zeit[1] == '' OR $Zeit[0] == '')
    {
    $Zeit[0]='12';
    $Zeit[1]='00';
    }

if (count($Datum) < 3)
    {

    $heute=date("d.m.Y");
    $Datum=explode(".", $heute);
    }
else if (!checkdate($Datum[1], $Datum[0], $Datum[2]))
    {

    $heute=date("d.m.Y");
    $Datum=explode(".", $heute);
    }

$Daten['uir_datum']=date("Y-m-d H:i:s", mktime($Zeit[0], $Zeit[1], 0, $Datum[1], $Datum[0], $Datum[2]));


// Speichere den Datensatz

$sql='INSERT INTO ir_log (' .
    'uir_hirid, ' .
    'uir_hmaid, ' .
    'uir_eintrag, ' .
    'uir_zeitstempel, ' .
    'uir_datum, ' .
    'uir_aktiv) ' .
    'VALUES ( ' .
    '"' . $Daten['uir_hirid'] . '", ' .
    '"' . $Daten['uir_hmaid'] . '", ' .
    '"' . mysql_real_escape_string($Daten['uir_eintrag']) . '", ' .
    'NOW(), ' .
    '"' . $Daten['uir_datum'] . '", ' .
    '"1")';

if (!($ergebnis=mysql_query($sql, $verbindung)))
    {
    fehler();
    }
    
    # Aktualisiere Zeitstempel Stammdaten

    // Speichere den Datensatz

    $sql='Update ir_stammdaten SET hir_zeitstempel = NOW() where hir_id = '.$Daten['uir_hirid'] ;
    
     if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

if ($_FILES["hau_datei"]["tmp_name"] != '')
    {
    if (!is_dir("ir/" . $Daten['uir_hirid']))
        {

        mkdir("ir/" . $Daten['uir_hirid'], 0777);
        }

    if (($_FILES["hau_datei"]["error"] == 3) OR ($_FILES["hau_datei"]["error"] == 4))
        {
        echo "Fehler: Die Datei wurde nur teilweise oder gar nicht hochgeladen. <br />" . $_FILES["hau_datei"]["error"];
        }
    else
        {
        move_uploaded_file($_FILES["hau_datei"]["tmp_name"],
            "ir/" . $Daten['uir_hirid'] . "/" . $_FILES["hau_datei"]["name"]);
        }
    }


// Zurueck zur Liste

header('Location: ir_neu.php?hir_id=' . $Daten['uir_hirid']);
exit;
?>
