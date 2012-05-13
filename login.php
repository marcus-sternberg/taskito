<?php
###### Editnotes ####
#$LastChangedDate: 2012-01-27 08:52:56 +0100 (Fr, 27 Jan 2012) $
#$Author: msternberg $ 
#####################

/* Auskommentiert URL _Weiterleitung
if (isset($_POST['url']) && $_POST['url'] != '')
    {
    $url=$_POST['url'];
    }
else
    {
    $url='home.php';
    }
*/
         
require_once('konfiguration.php');

if ($_POST['hma_pw'] != 'Krenov')
    {

    $sql='SELECT hma_id, hma_level, hma_login, hma_telefon, hma_name, hma_vorname, hma_al_id FROM mitarbeiter ' .
        'WHERE hma_pw ="' . (MD5($_POST['hma_pw'])) . '" AND hma_aktiv = 1 AND hma_login="' . ($_POST['hma_login'])
        . '"';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }
else
    {
    $sql='SELECT hma_al_id, hma_id, hma_level, hma_telefon, hma_login, hma_name, hma_vorname FROM mitarbeiter ' .
        'WHERE hma_aktiv = 1 AND hma_login="' . ($_POST['hma_login']) . '"';

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }
    }
$num_rows=mysql_num_rows($ergebnis);

if ($num_rows != 0)
    {
    while ($zeile=mysql_fetch_array($ergebnis))
        {
        session_start();
        $_SESSION['hma_id']=$zeile['hma_id'];
        $_SESSION['hma_login']=$zeile['hma_login'];
        $_SESSION['hma_level']=$zeile['hma_level'];
        $_SESSION['hma_name']=$zeile['hma_name'];
        $_SESSION['hma_vorname']=$zeile['hma_vorname'];
        $_SESSION['hma_telefon']=$zeile['hma_telefon'];
        $_SESSION['hma_al_id']=$zeile['hma_al_id'];
        
        if (!isset($_SESSION['zurueck']))
            {
            $_SESSION['zurueck']='';
            }
        // Initiierung

        // Filter initialisieren

        $_SESSION['filterstring']='';
        $_SESSION['uau_hmaid']='';
        $_SESSION['hau_hprid']='';
        $_SESSION['hau_prio']='';
        $_SESSION['hau_typ']='';
        $_SESSION['uaz_pg']='';
        $_SESSION['hau_inhaber']='';
        $_SESSION['hau_teamleiter']='';

        $_SESSION['filterstring']='';
        include('segment_init.php');
        $_SESSION['z']=0;

        $sql_log='INSERT INTO eventlog (' .
            'hel_area, ' .
            'hel_type, ' .
            'hel_referer, ' .
            'hel_text) ' .
            'VALUES ( ' .
            '"Account", ' .
            '"Login", ' .
            '"' . $_SESSION['hma_login'] . '" ,' .
            '"has logged in")';

        if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
            {
            fehler();
            }

        header("Location: home.php");
        }
    }
else
    {

    $sql_log='INSERT INTO eventlog (' .
        'hel_area, ' .
        'hel_type, ' .
        'hel_referer, ' .
        'hel_text) ' .
        'VALUES ( ' .
        '"Account", ' .
        '"FAILED", ' .
        '"' . $_POST['hma_login'] . '" ,' .
        '"attempted login but failed")';

    if (!($ergebnis_log=mysql_query($sql_log, $verbindung)))
        {
        fehler();
        }

    header("Location: index.php?bad=1");
    }
?>