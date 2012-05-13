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
    include('seg_abfrage_datum.php');

    $zaehler=0;
    $check_input=array();
    $check_attribute=array();
    $status_check=array();
    $status_exist=array();
    $ergebnis_check=array();

    # Lies die ID's der aktiven Checks ein

    $sql='SELECT hck_id, hck_name, hck_url FROM checks WHERE hck_aktiv = 1';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        $check_attribute[$zeile['hck_id']]=array
            (
            $zeile['hck_name'],
            $zeile['hck_url']
            );
        }

    # Lese alle Werte fuer den Status ein oder setze Standardwert
    # Schau nach, ob es eine Checkliste zum gewaehlten Datum gibt

    $sql='SELECT hcl_id FROM checklists WHERE hcl_datum = "' . $xYear . '-' . $xMonth . '-' . $xDay . '"';


    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }


    # >0 heisst, es gibt eine Checkliste, also lese vorhandene Werte aus

    if (mysql_num_rows($ergebnis) > 0)
        {
        $hcl_id=mysql_result($ergebnis, 0);
        $sql_status='SELECT * FROM check_matrix WHERE hcm_hclid = ' . mysql_result($ergebnis, 0);

        // Frage Datenbank nach Suchbegriff
        if (!$ergebnis_status=mysql_query($sql_status, $verbindung))
            {
            fehler();
            }

        while ($zeile_status=mysql_fetch_array($ergebnis_status))
            {

            $status_exist[$zeile_status['hcm_hckid']][0] = $zeile_status['hcm_status'];
            $status_exist[$zeile_status['hcm_hckid']][1]=($zeile_status['hcm_bemerkung']);
            }


        # Nun schreibe die vorhandenen Werte ins Feld status_check

        foreach ($check_attribute AS $nummer_des_checks => $beschreibung_des_checks)
            {
            if (isset($status_exist[$nummer_des_checks][0]))
                {
                $status_check[$nummer_des_checks][0]=$status_exist[$nummer_des_checks][0];
                $status_check[$nummer_des_checks][1]=$status_exist[$nummer_des_checks][1];
                }
            else
                {
                $status_check[$nummer_des_checks][0]=0;
                $status_check[$nummer_des_checks][1]='';
                }
            }
        }
    else
        {
        $hcl_id=0;

        foreach ($check_attribute AS $nummer_des_checks => $beschreibung_des_checks)
            {
            $status_check[$nummer_des_checks][0] = 0;
            $status_check[$nummer_des_checks][1]='';
            }
        }

    $sql='SELECT hcl_id FROM checklists WHERE hcl_datum = "' . $xYear . '-' . $xMonth . '-' . $xDay . '"';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        $hcl_id=$zeile['hcl_id'];
        }

    if (!isset($hcl_id))
        {
        $hcl_id=0;
        }

    $sql='SELECT * FROM check_matrix WHERE hcm_hclid = "' . $hcl_id . '"';

    // Frage Datenbank nach Suchbegriff
    if (!$ergebnis=mysql_query($sql, $verbindung))
        {
        fehler();
        }

    if (mysql_num_rows($ergebnis) == 0)
        {
        $ergebnis_check[]=0;
        }

    while ($zeile=mysql_fetch_array($ergebnis))
        {
        $ergebnis_check[]=$zeile['hcm_status'];
        }

    asort($ergebnis_check);

    foreach ($ergebnis_check AS $feld_id => $resultat)
        {
        if ($resultat == 0)
            {
            $color='#c2c2c2';
            $status='ungeprüft';
            break;
            }

        if ($resultat == 1)
            {
            $color='#EE775F';
            $status='KRITISCH';
            break;
            }

        if ($resultat == 2)
            {
            $color='#FFF8B3';
            $status='WARNUNG';
            break;
            }

        if ($resultat == 3)
            {
            $color='#C1E2A5';
            $status='OK';
            break;
            }

        if ($resultat == 4)
            {
            $color='#CED1F0';
            $status='SKIP';
            break;
            }
        }

    echo '<br><table class="matrix" cellpadding = "5">';

    echo '<tr>';

    echo '<td class="text_mitte">';

    echo '<img src="bilder/block.gif">&nbsp;Daily Check für den ' . $xDay . '.' . $xMonth . '.' . $xYear . ' | </td>';

    echo '<td bgcolor="' . $color . '">' . $status . '</td>';

    echo '</tr>';

    echo '</table>';

    echo '<br><span class="text_klein"><a href="checkliste_uebersicht.php?xMonth=' . $xMonth
        . '">Monatsübersicht Checkliste</a></span><br>';

    echo '<br>';

    echo '<form action="checkliste_neu.php" method="post">';

    echo '<table border="0" cellspacing="3" cellpadding="0" class="matrix">';

    echo '<tr>';

    echo
        '<td class="text_mitte" colspan="2" width="32">&nbsp;<td class="text_mitte">Check</td><td  class="text_mitte"colspan="5">Status</td><td class="text_mitte">Bemerkung</td>';

    echo '</tr>';

    foreach ($check_attribute AS $nummer_des_checks => $beschreibung_des_checks)
        {
        if (fmod($zaehler, 2) == 1 && $zaehler > 0)
            {
            $hintergrundfarbe='#ffffff';
            }
        else
            {
            $hintergrundfarbe='#CED1F0';
            }

        echo '<tr>';

        if ($beschreibung_des_checks['1'] != '')
            {
            echo '<td width="16" bgcolor="' . $hintergrundfarbe . '"><a href="' . $beschreibung_des_checks['1']
                . '" target="_blank" title="Link" alt="Link"><img src="bilder/icon_pool.gif" border="0"></a></td>';
            }
        else
            {
            echo '<td width="16" bgcolor="' . $hintergrundfarbe . '">&nbsp;</td>';
            }

        echo '<td width="16" bgcolor="' . $hintergrundfarbe
            . '"><a href="#" OnClick="javascript: fenster(\'checklist_info.php?hck_id=' . $nummer_des_checks
            . '\',\'Checkbeschreibung\',800,600)" title="Info" alt="Info"><img src="bilder/icon_anschauen.gif" border=0></td>';

        echo '<td bgcolor="' . $hintergrundfarbe . '" class="text_klein">' . $beschreibung_des_checks[0] . '</td>';

        $check_id=$status_check[$nummer_des_checks][0];

        switch ($check_id)
            {
            case 0:
                echo '<td bgcolor="#c2c2c2"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="0" checked="checked"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#C1E2A5"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="3" />&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#FFF8B3"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="2"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#EE775F"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="1"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#CED1F0"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="4"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td align="center"><input type="text" size="42" name="check_input_bemerkung[' . $nummer_des_checks
                    . ']" value="' . $status_check[$nummer_des_checks][1] . '" /></td>';
                break;

            case 1:
                echo '<td bgcolor="#c2c2c2"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="0"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#C1E2A5"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="3"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#FFF8B3"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="2"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#EE775F"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="1" checked="checked"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#CED1F0"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="4"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td align="center"><input type="text" size="42" name="check_input_bemerkung[' . $nummer_des_checks
                    . ']" value="' . $status_check[$nummer_des_checks][1] . '" /></td>';
                break;

            case 2:
                echo '<td bgcolor="#c2c2c2"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="0"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#C1E2A5"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="3" />&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#FFF8B3"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="2" checked="checked"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#EE775F"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="1"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#CED1F0"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="4"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td align="center"><input type="text" size="42" name="check_input_bemerkung[' . $nummer_des_checks
                    . ']" value="' . $status_check[$nummer_des_checks][1] . '" /></td>';
                break;

            case 3:
                echo '<td bgcolor="#c2c2c2"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="0"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#C1E2A5"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="3" checked="checked"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#FFF8B3"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="2"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#EE775F"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="1"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#CED1F0"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="4"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td align="center"><input type="text" size="42" name="check_input_bemerkung[' . $nummer_des_checks
                    . ']" value="' . $status_check[$nummer_des_checks][1] . '" /></td>';
                break;

            case 4:
                echo '<td bgcolor="#c2c2c2"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="0"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#C1E2A5"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="3" />&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#FFF8B3"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="2"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#EE775F"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="1"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td bgcolor="#CED1F0"><input type="radio" name="check_input_status[' . $nummer_des_checks
                    . ']" value="4" checked="checked"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>';

                echo '<td align="center"><input type="text" size="42" name="check_input_bemerkung[' . $nummer_des_checks
                    . ']" value="' . $status_check[$nummer_des_checks][1] . '" /></td>';
                break;
            }

        echo '</tr>';

        $zaehler++;
        }

    echo
        '<tr><td colspan="9" align="right"><input type="submit" name="speichern" value="Checkliste sichern" class="formularbutton" /></td></tr>';

    echo '<input type="hidden" name="hcl_datum" value="' . $xYear . '-' . $xMonth . '-' . $xDay . '">';

    echo '<input type="hidden" name="hcl_id" value="' . $hcl_id . '">';

    echo '</table>';

    echo '</form>';

    echo
        '<br><span class="text_klein">Status: grau = ungeprüft, grün = ok, gelb = Warnung, rot = kritisch, blau = Skip (nicht relevant für diesen Tag)</span>';
    }
else
    {
    foreach ($_POST as $varname => $value)
        {

        $Daten[$varname]=$value;
        }
    $merge_entries=0;
    if ($Daten['hcl_id'] == 0) // Es gibt keine Checkliste
        {

          // Sicherheitsueberprüfung - Multiple Bearbeiter - 2011-01-14, dmeisner
          $chk_q="SELECT hcl_datum,hcl_id FROM checklists WHERE hcl_datum='".$Daten['hcl_datum']."'";
          if (!($chk_ergebnis=mysql_query($chk_q, $verbindung)))
          {
            fehler();
          }
          $num_chk_ergebnis=mysql_num_rows($chk_ergebnis);
          if ($num_chk_ergebnis == 1)  // ohje, es gibt wohl doch schon einen Checklisteneintrag fuer das Datum
          {
            // Eintrag auslesen
            $chk_val=mysql_fetch_array($chk_ergebnis);
            $hcl_id=$chk_val['hcl_id'];
            $get_existing_query='SELECT * FROM check_matrix WHERE hcm_hclid = "' . $hcl_id . '"';
            if (!($get_existing_entry=mysql_query($get_existing_query, $verbindung)))
            {
              fehler();
            }
            $existing_entry=array();
            While ($row_existing_entry=mysql_fetch_array($get_existing_entry))
            {
              if ($row_existing_entry['hcm_status'] > 0)
                $existing_entry[$row_existing_entry['hcm_hckid']]=1;
            }
            $merge_entries=1;
          }
          if ($num_chk_ergebnis > 1)
          {
            echo "<br><br><h3 style='color:#990000;'>Attention: due to some inconsistences in the checklist table it is not possible to store / update the checklist for the selected day.<br>
                  Please consult the TOM database administrator for assistence.</h3>sorry for any caused inconveniences and thank you for your understanding.";
            exit;
          }
          if ($num_chk_ergebnis == 0)
          {
            // Speichere den Datensatz
            $sql='INSERT INTO checklists (hcl_datum) VALUES ( "' . $Daten['hcl_datum'] . '")';
            if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
              fehler();
            }
            $hcl_id=mysql_insert_id();
          }

        foreach ($Daten['check_input_status'] AS $check_id => $check_status)
            {
              $Daten['check_input_bemerkung'][$check_id] =
                  mysql_real_escape_string(htmlspecialchars($Daten['check_input_bemerkung'][$check_id],
                      $quote_style=ENT_COMPAT, 'UTF-8', $double_encode=false));
              if ($merge_entries == 0 || ($merge_entries == 1 && $existing_entry[$check_id] != 1))
              {
                $sql='INSERT INTO check_matrix (hcm_hclid, hcm_hckid, hcm_status, hcm_bemerkung)  
                    VALUES ( "' . $hcl_id . '", "' . $check_id . '", "' . $check_status . '", "'
                    . $Daten['check_input_bemerkung'][$check_id] . '")';
    
                if (!($ergebnis=mysql_query($sql, $verbindung)))
                    {
                    fehler();
                    }
              }
            }

        $sql='INSERT INTO eventlog (hel_area, hel_type, hel_referer, hel_text, hel_timestamp) ' .
            'VALUES ("Checkliste", "Eintrag", "' . $_SESSION['hma_login'] . '", "Checkliste ' . $hcl_id
            . ' erzeugt.", NOW() )';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }
        }
    else
        {


        // Ändere den Datensatz

        foreach ($Daten['check_input_status'] AS $check_id => $check_status)
            {
            $bemerkung = mysql_real_escape_string(htmlspecialchars($Daten['check_input_bemerkung'][$check_id],
                $quote_style=ENT_COMPAT, 'UTF-8', $double_encode=false));
           
            $sql_check='SELECT hcm_id FROM check_matrix WHERE hcm_hclid = "' . $Daten['hcl_id'] . '" AND hcm_hckid = "'
                . $check_id . '"';

            if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
                {
                fehler();
                }

            if (mysql_num_rows($ergebnis_check) != 0)
                {

                $sql='UPDATE check_matrix SET 
               hcm_status = "' . $check_status . '",
               hcm_bemerkung = "' . $bemerkung . '"
               WHERE hcm_hclid = "' . $Daten['hcl_id'] . '" AND hcm_hckid = "' . $check_id . '"';

                if (!($ergebnis=mysql_query($sql, $verbindung)))
                    {
                    fehler();
                    }
                }
            else
                {
                $sql='INSERT INTO check_matrix 
                (hcm_hclid, hcm_hckid, hcm_status, hcm_bemerkung) VALUES ("' . $Daten['hcl_id'] . '", "' . $check_id
                    . '", "' . $check_status . '", "' . $bemerkung . '")';

                if (!($ergebnis=mysql_query($sql, $verbindung)))
                    {
                    fehler();
                    }
                }
            }

        $sql='INSERT INTO eventlog (hel_area, hel_type, hel_referer, hel_text, hel_timestamp) ' .
            'VALUES ("Checkliste", "Eintrag", "' . $_SESSION['hma_login'] . '", "Checkliste ' . $Daten['hcl_id']
            . ' wurde geaendert.", NOW() )';

        if (!($ergebnis=mysql_query($sql, $verbindung)))
            {
            fehler();
            }
        }

    $save=1;


    // Zurueck zur Liste

    $datum=explode('-', $Daten['hcl_datum']);

    require_once('segment_kopf.php');

    echo '<br>';

    echo '<img src="bilder/block.gif">&nbsp;Checkliste wurde gespeichert.</td>';

    echo '<meta http-equiv="refresh" content="1;url=checkliste_neu.php?xDay=' . $datum[2] . '&xMonth=' . $datum[1]
        . '&xYear=' . $datum[0] . '">';
    exit;
    break;
    }
?>
