<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

if (!isset($_POST['loeschen']))
    {
    require_once('segment_kopf.php');

    echo '<form name="nachrichten" action="' . $_SERVER['PHP_SELF'] . '" method="post">';   
    echo '<br><span class="text_mitte"><img src="bilder/block.gif">&nbsp;News<br><br>';
    echo '<span class="text"><input type="radio" name="checkall" onclick="checkedall(true)" /> alle markieren <input type="radio" name="checkall" onclick="checkedall(false)" /> alle zurücksetzen </span>&nbsp;&nbsp;<input type="submit" name="loeschen" value="Delete checked News" class="formularbutton" /><br><br>';

    $sql='SELECT * FROM news ' .
        'LEFT JOIN aufgaben ON hau_id = una_hauid ' .
        'LEFT JOIN mitarbeiter ON hma_id = una_initiator ' .
        'WHERE una_empfaenger = "' . $_SESSION['hma_id'] . '" AND una_geloescht = 0 ' .
        'ORDER BY una_zeitstempel DESC';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }




    // Beginne mit Tabellenausgabe
    echo '<table style="border: solid, 1px, black;" class="element" cellspacing="1" cellpadding="3" width="900">';

    echo '<tr>';

    echo '<td class="tabellen_titel">&nbsp;</td>';

    echo '<td class="tabellen_titel">Datum</td>';

    echo '<td class="tabellen_titel">von</td>';

    echo '<td class="tabellen_titel">TNR</td>';

    echo '<td class="tabellen_titel">Aufgabe</td>';

    echo '<td class="tabellen_titel">Info</td>';

    echo '</tr>';

    // Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
    while ($zeile=mysql_fetch_array($ergebnis))
        {
        // Beginne Datenausgabe
        echo '<tr>';

        switch ($zeile['una_gelesen'])
            {
            case 0:
                $xFont='xnormal_sort';
                break;

            case 1:
                $xFont='xnormal';
                break;
            }

        echo '<td width="10"><input type="checkbox" name="news[' . $zeile['una_id'] . ']"></td>'; 

        echo '<td class="' . $xFont . '">' . zeitstempel_anzeigen($zeile['una_zeitstempel']) . '</td>';

        echo '<td class="' . $xFont . '">' . $zeile['hma_login'] . '</td>';

        echo '<td class="' . $xFont . '"><a href="aufgabe_ansehen.php?hau_id=' . $zeile['hau_id'] . '">'
            . $zeile['hau_id'] . '</td>';

        echo '<td class="' . $xFont . '"><a href="aufgabe_ansehen.php?hau_id=' . $zeile['hau_id'] . '">'
            . ($zeile['hau_titel']) . '</td>';

        echo '<td class="' . $xFont . '">' . $zeile['una_info'] . '</td>';

        echo '</tr>';

        $sql_gelesen='UPDATE news SET una_gelesen = 1 WHERE una_id = ' . $zeile['una_id'];

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_gelesen=mysql_query($sql_gelesen, $verbindung))
            {
            fehler();
            }
        }

    echo '</table>';

     echo '<br><span class="text"><input type="radio" name="checkall" onclick="checkedall(true)" /> alle markieren <input type="radio" name="checkall" onclick="checkedall(false)" /> alle zurücksetzen </span>&nbsp;&nbsp;<input type="submit" name="loeschen" value="Delete checked News" class="formularbutton" /><br><br>';


    echo '</form>';
    // Ende Formular

    }
else
    {
    foreach ($_POST['news'] as $una_id => $value)
        {

        $sql = 'UPDATE news SET una_geloescht = 1 WHERE una_id = ' . $una_id;

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis=mysql_query($sql, $verbindung))
            {
            fehler();
            }
        }

    header('Location: schreibtisch_news.php');
    exit;
    }

?>   