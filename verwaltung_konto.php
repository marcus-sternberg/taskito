<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (!isset($_POST['speichern']))
    {
    require_once('segment_kopf.php');

    echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Create a new Password<br><br>';

    echo '<form action="verwaltung_konto.php" method="post">';

    echo '<table border="0" cellspacing="5" cellpadding="0">';

    echo '<tr>';

    echo
        '<td class="text_klein">New Password: </td><td><input type="password" name="hma_pw" style="width:340px;"></td>';

    echo '</tr>';

    echo '<tr>';

    echo
        '<td class="text_klein">Repeat Password: </td><td><input type="password" name="hma_pw1" style="width:340px;"></td>';

    echo '</tr>';

    echo
        '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="speichern" value="Change Password" class="formularbutton" /></td></tr>';

    echo '</table>';

    echo '</form>';
    }
else
    {
    foreach ($_POST as $varname => $value)
        {
        $Daten[$varname]=$value;
        }

    if ($Daten['hma_pw'] != $Daten['hma_pw1'] or $Daten['hma_pw'] == '')
        {
        require_once('segment_kopf.php');

        echo '<form action="verwaltung_konto.php" method="post">';

        echo '<br><br>The Passwords did not match - please try again.<br><br>';

        echo '<input type="submit" name="neues_pw" value="OK" class="formularbutton" /></td></tr>';

        echo '</form>';

        exit;
        }
    else
        {

        // Speichere den Datensatz

        $sql='UPDATE mitarbeiter SET hma_pw = "' . md5($Daten['hma_pw']) . '" WHERE hma_id = ' . $_SESSION['hma_id'];

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }

        header('Location: logout.php');
        exit;
        }
    }

////////////////////// MAIL Einstellungen ///////////////

if (!isset($_POST['mail']))
    {
    require_once('segment_kopf.php');

    $sql_mail='SELECT * FROM mitarbeiter INNER JOIN maileinstellungen ON ume_hmaid = hma_id ' .
        'WHERE hma_id = ' . $_SESSION['hma_id'];

    if (!($ergebnis_mail=mysql_query($sql_mail, $verbindung)))
        {
        fehler();
        }

    while ($zeile_mail=mysql_fetch_array($ergebnis_mail))
        {
        echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;Mail Settings<br><br>';

        echo '<form action="verwaltung_konto.php" method="post">';

        echo '<table border="0" cellspacing="5" cellpadding="0">';

        if ($zeile_mail['ume_kommentar_erhalten'] == 1)
            {
            $check='checked="checked"';
            }
        else
            {
            $check='';
            }

        echo '<tr>';

        echo '<td><input type="checkbox" name = "ume_kommentar_erhalten" ' . $check . '></td>';

        echo '<td class="text_klein">Sende Mail bei neuen Kommentaren.</td>';

        echo '</tr>';

        if ($zeile_mail['ume_kommentar_gelesen'] == 1)
            {
            $check='checked="checked"';
            }
        else
            {
            $check='';
            }

        echo '<tr>';

        echo '<td><input type="checkbox" name = "ume_kommentar_gelesen" ' . $check . '></td>';

        echo '<td class="text_klein">Sende Mail, wenn mein Kommentar gelesen wurde.</td>';

        echo '</tr>';

        if ($zeile_mail['ume_ping'] == 1)
            {
            $check='checked="checked"';
            }
        else
            {
            $check='';
            }

        echo '<tr>';

        echo '<td><input type="checkbox" name = "ume_ping" ' . $check . '></td>';

        echo '<td class="text_klein">Sende Mail wenn ich angepingt (PING) wurde.</td>';

        echo '</tr>';

        if ($zeile_mail['ume_aufgabestatus'] == 1)
            {
            $check='checked="checked"';
            }
        else
            {
            $check='';
            }

        echo '<tr>';

        echo '<td><input type="checkbox" name = "ume_aufgabestatus" ' . $check . '></td>';

        echo '<td class="text_klein">Sende Mail wenn sich der Status einer Aufgabe ändert.</td>';

        echo '</tr>';

        if ($zeile_mail['ume_termin'] == 1)
            {
            $check='checked="checked"';
            }
        else
            {
            $check='';
            }

        echo '<tr>';

        echo '<td><input type="checkbox" name = "ume_termin" ' . $check . '></td>';

        echo '<td class="text_klein">Sende Mail bei Terminänderungen.</td>';

        echo '</tr>';

                if ($zeile_mail['ume_gruppe'] == 1)
            {
            $check='checked="checked"';
            }
        else
            {
            $check='';
            }

        echo '<tr>';

        echo '<td><input type="checkbox" name = "ume_gruppe" ' . $check . '></td>';

        echo '<td class="text_klein">Sende Mails für meine Gruppe.</td>';

        echo '</tr>';
        
        if ($zeile_mail['ume_format'] == 1)
            {
            $check='checked="checked"';
            }
        else
            {
            $check='';
            }

        echo '<tr>';

        echo '<td><input type="checkbox" name = "ume_format" ' . $check . '></td>';

        echo '<td class="text_klein">Sende Mails als HTML (für Plain Text Haken entfernen).</td>';

        echo '</tr>';
        
        echo
            '<tr><td colspan="2" style="text-align:right; padding-top:10px;"><input type="submit" name="mail" value="Save Settings" class="formularbutton" /></td></tr>';

        echo '</table>';

        echo '</form>';
        }
    }
else
    {

    $mailfelder=array
        (
        "ume_ping",
        "ume_aufgabestatus",
        "ume_kommentar_erhalten",
        "ume_kommentar_gelesen",
        "ume_termin",
        "ume_format",    
        "ume_gruppe"
        );

    foreach ($mailfelder as $feld)
        {
        if (!isset($_POST[$feld]))
            {
            $Daten[$feld]='0';
            }
        else
            {
            $Daten[$feld]='1';
            }
        }


    // Speichere den Datensatz

    $sql='UPDATE maileinstellungen SET ' .
        'ume_kommentar_erhalten = ' . $Daten['ume_kommentar_erhalten'] . ', ' .
        'ume_kommentar_gelesen = ' . $Daten['ume_kommentar_gelesen'] . ', ' .
        'ume_ping = ' . $Daten['ume_ping'] . ', ' .
        'ume_termin = ' . $Daten['ume_termin'] . ', ' .
        'ume_format = ' . $Daten['ume_format'] . ', ' . 
        'ume_gruppe = ' . $Daten['ume_gruppe'] . ', ' .
        'ume_aufgabestatus = ' . $Daten['ume_aufgabestatus'] .
        ' WHERE ume_hmaid = ' . $_SESSION['hma_id'];

    if (!($ergebnis=mysql_query($sql, $verbindung)))
        {
        fehler();
        }

    echo '<meta http-equiv="refresh" content="0;url="verwaltung_konto.php">';
    }
?>