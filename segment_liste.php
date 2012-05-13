<?php
###### Editnotes ####
#$LastChangedDate: 2012-02-03 13:37:38 +0100 (Fr, 03 Feb 2012) $
#$Author: msternberg $ 
#####################
if (isset($auto))
    {
    $refresh='&auto=' . $auto;
    }
else
    {
    $refresh='';
    }

if (!isset($i))
    {
    $i=0;
    }

if (isset($_GET['option']))
    {
    $option_string=$_GET['option'];
    }
else
    {
    $option_string=1;
    }

if (isset($_GET['sortierschluessel']))
    {                                      
    
            switch($_GET['sortierschluessel'])
        {
            case 'letzte_aktualisierung':
            $_GET['sortierschluessel'] = 'ulo_zeitstempel';
            break;
        }
             
    if ($_SESSION['z'] == 1)
        {

        $sql=substr($sql, 0, my_strrpos($sql, "ORDER BY") + 9) . $_GET['sortierschluessel'] . ' DESC,'
            . substr($sql, my_strrpos($sql, "ORDER BY") + 8);
        $sort_bild='<img src="bilder/sort_desc.gif" width=9 height=9 border=0 alt="abwärts">';
        }

    if ($_SESSION['z'] > 1)
        {
        $sql=substr($sql, 0, my_strrpos($sql, "ORDER BY") + 9) . $_GET['sortierschluessel'] . ' ASC,'
            . substr($sql, my_strrpos($sql, "ORDER BY") + 8);
        $sort_bild='<img src="bilder/sort_asc.gif" width=9 height=9 border=0 alt="aufwärts">';
        }
    }

   
// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

// Beginne mit Tabellenausgabe

echo '<table class="matrix" cellspacing="1" cellpadding="3" width="1200" border="0">';

echo '<form action="segment_filter_string.php" method="post">';

if (isset($_SESSION['suchstring']))
    {
    echo "<input type='hidden' name='suchstring' value='" . $_SESSION['suchstring'] . "'>";
    }

foreach ($anzeigefelder as $bezeichner => $inhalt)
    {
    if (isset($_GET['sortierschluessel']) && $_GET['sortierschluessel'] == $inhalt)
        {
        $anzeige='&nbsp;' . $sort_bild;
        }
    else
        {
        $anzeige='';
        }

    if ($inhalt != 'sum_fertig' AND $inhalt != 'angepingt' AND $inhalt!='log.ulo_datum')
        {
        echo '<td rowspan="2" class="tabellen_titel" valign="top"><a href="' . $_SERVER['PHP_SELF'] . '?option='
            . $option_string . '&aktuelle_seite=0&sortierschluessel=' . $inhalt . $refresh
            . '"><span class="xnormal_sort">' . $bezeichner . '</span></a>' . $anzeige;
        }
    else
        {
        echo '<td rowspan="2" class="tabellen_titel" valign="top"><span class="xnormal_sort">' . $bezeichner
            . '</span>';
        }

    switch ($bezeichner)
        {
        case 'Eigner':
            echo '<br>';

            echo
                '<span id="tooltip" style="position: absolute; display: none; padding: 3px; border: 1px solid black; background: white; color : black;"></span>';

            echo
                '<select size="1" name="hau_inhaber" class="liste" style="width:50px;" width=50 onmouseenter="showHideTooltip()" onmouseleave="showHideTooltip()">';
            $sql_filter='SELECT hma_id, hma_login FROM mitarbeiter WHERE hma_id > 3 AND hma_aktiv = 1 ' .
                'ORDER BY hma_login';

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_filter=mysql_query($sql_filter, $verbindung))
                {
                fehler();
                }

            echo '<option value="0"><span class="text_mitte">alle</span></option>';

            while ($zeile_filter=mysql_fetch_array($ergebnis_filter))
                {
                if ($_SESSION['hau_inhaber'] == $zeile_filter['hma_id'])
                    {
                    echo '<option value="' . $zeile_filter['hma_id']
                        . '" style="background-color:#E28B78;" selected><span class="text">'
                        . $zeile_filter['hma_login'] . '</span></option>';
                    }
                else
                    {
                    echo '<option value="' . $zeile_filter['hma_id'] . '"><span class="text_mitte">'
                        . $zeile_filter['hma_login'] . '</span></option>';
                    }
                }

            echo '</select>';
            break;

        case 'Projekt':
            echo '<br>';

            echo
                '<span id="tooltip" style="position: absolute; display: none; padding: 3px; border: 1px solid black; background: white; color : black;"></span>';

            echo
                '<select size="1" name="hau_hprid" class="liste" style="width:100px;" width=70 onmouseenter="showHideTooltip()" onmouseleave="showHideTooltip()">';

            $sql_filter='SELECT hpr_id, hpr_titel FROM projekte 
            WHERE hpr_aktiv="1" AND hpr_fertig = 0 ' .
                'ORDER BY hpr_sort, hpr_titel';

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_filter=mysql_query($sql_filter, $verbindung))
                {
                fehler();
                }

            echo '<option value="0"><span class="text_mitte">alle</span></option>';

            while ($zeile_filter=mysql_fetch_array($ergebnis_filter))
                {
                if ($_SESSION['hau_hprid'] == $zeile_filter['hpr_id'])
                    {
                    echo '<option value="' . $zeile_filter['hpr_id'] . '" selected><span class="text">'
                        . $zeile_filter['hpr_titel'] . '</span></option>';
                    }
                else
                    {
                    echo '<option value="' . $zeile_filter['hpr_id'] . '"><span class="text">'
                        . $zeile_filter['hpr_titel'] . '</span></option>';
                    }
                }

            echo '</select>';
            break;

        case 'Gruppe':
            echo '<br>';

            echo '<select size="1" name="uaz_pg" class="liste" style="width:50px;" width=50>';
            $sql_filter='SELECT ule_id, ule_name FROM level WHERE ule_id > 1 ' .
                'ORDER BY ule_name';

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_filter=mysql_query($sql_filter, $verbindung))
                {
                fehler();
                }

            echo '<option value="0"><span class="text_mitte">alle</span></option>';

            while ($zeile_filter=mysql_fetch_array($ergebnis_filter))
                {
                if ($_SESSION['uaz_pg'] == $zeile_filter['ule_id'])
                    {
                    echo '<option value="' . $zeile_filter['ule_id']
                        . '" selected style="background-color:#E28B78;"><span class="text">' . $zeile_filter['ule_name']
                        . '</span></option>';
                    }
                else
                    {
                    echo '<option value="' . $zeile_filter['ule_id'] . '"><span class="text">'
                        . $zeile_filter['ule_name'] . '</span></option>';
                    }
                }

            echo '</select>';
            break;

        case 'Mitarbeiter':
            echo '<br>';

            echo
                '<span id="tooltip" style="position: absolute; display: none; padding: 3px; border: 1px solid black; background: white; color : black;"></span>';

            echo
                '<select size="1" name="uau_hmaid" class="liste" style="width:50px;" width=60 onmouseenter="showHideTooltip()" onmouseleave="showHideTooltip()">';
             $sql_filter='SELECT hma_id, hma_login FROM mitarbeiter WHERE hma_id > 3 AND hma_aktiv = 1 ' .
                'ORDER BY hma_login';

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_filter=mysql_query($sql_filter, $verbindung))
                {
                fehler();
                }

            echo '<option value="0"><span class="text_mitte">alle</span></option>';

            while ($zeile_filter=mysql_fetch_array($ergebnis_filter))
                {
                if ($_SESSION['uau_hmaid'] == $zeile_filter['hma_id'])
                    {
                    echo '<option value="' . $zeile_filter['hma_id']
                        . '" style="background-color:#E28B78;" selected><span class="text">'
                        . $zeile_filter['hma_login'] . '</span></option>';
                    }
                else
                    {
                    echo '<option value="' . $zeile_filter['hma_id'] . '"><span class="text_mitte">'
                        . $zeile_filter['hma_login'] . '</span></option>';
                    }
                }

            echo '</select>';
            break;

        case 'P-Ende':
            if (isset($_SESSION['hau_pende']))
                {
                echo '<input type="text" name="hau_pende" value="' . datum_wandeln_useu($_SESSION['hau_pende'])
                    . '" style="width:60px;">';
                }
            else
                {
                echo '<input type="text" name="hau_pende" style="width:60px;">';
                }
            break;
               
        case 'Typ':
            echo '<br>';

            echo
                '<span id="tooltip" style="position: absolute; display: none; padding: 3px; border: 1px solid black; background: white; color : black;"></span>';

            echo
                '<select size="1" name="hau_typ" class="liste" style="width:40px;" width=40 onmouseenter="showHideTooltip()" onmouseleave="showHideTooltip()">';
            $sql_filter='SELECT uty_id, uty_name FROM typ ' .
                'ORDER BY uty_name';

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_filter=mysql_query($sql_filter, $verbindung))
                {
                fehler();
                }

            echo '<option value="0"><span class="text_mitte">alle</span></option>';

            while ($zeile_filter=mysql_fetch_array($ergebnis_filter))
                {
                if ($_SESSION['hau_typ'] == $zeile_filter['uty_id'])
                    {
                    echo '<option value="' . $zeile_filter['uty_id']
                        . '" style="background-color:#E28B78;" selected><span class="text">' . $zeile_filter['uty_name']
                        . '</span></option>';
                    }
                else
                    {
                    echo '<option value="' . $zeile_filter['uty_id'] . '"><span class="text_mitte">'
                        . $zeile_filter['uty_name'] . '</span></option>';
                    }
                }

            echo '</select>';
            break;

        case 'Prio':
            echo '<br>';

            echo
                '<span id="tooltip" style="position: absolute; display: none; padding: 3px; border: 1px solid black; background: white; color : black;"></span>';

            echo
                '<select size="1" name="hau_prio" class="liste" style="width:30px;" width=30 onmouseenter="showHideTooltip()" onmouseleave="showHideTooltip()">';
            $sql_filter='SELECT upr_nummer, upr_name FROM prioritaet ' .
                'ORDER BY upr_sort';

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_filter=mysql_query($sql_filter, $verbindung))
                {
                fehler();
                }

            echo '<option value="0"><span class="text_mitte">alle</span></option>';

            while ($zeile_filter=mysql_fetch_array($ergebnis_filter))
                {
                if ($_SESSION['hau_prio'] == $zeile_filter['upr_nummer'])
                    {
                    echo '<option value="' . $zeile_filter['upr_nummer']
                        . '" style="background-color:#E28B78;" selected><span class="text">' . $zeile_filter['upr_name']
                        . '</span></option>';
                    }
                else
                    {
                    echo '<option value="' . $zeile_filter['upr_nummer'] . '"><span class="text_mitte">'
                        . $zeile_filter['upr_name'] . '</span></option>';
                    }
                }

            echo '</select>';
            break;

        case 'Teamlead':
            echo '<br>';

            echo
                '<span id="tooltip" style="position: absolute; display: none; padding: 3px; border: 1px solid black; background: white; color : black;"></span>';

            echo
                '<select size="1" name="hau_teamleiter" class="liste" style="width:30px;" width=30 onmouseenter="showHideTooltip()" onmouseleave="showHideTooltip()">';
              $sql_filter='SELECT hma_id, hma_login FROM mitarbeiter WHERE hma_id > 3 AND hma_aktiv = 1 ' .
                'ORDER BY hma_login';

            // Frage Datenbank nach Suchbegriff
            if (!$ergebnis_tl=mysql_query($sql_tl, $verbindung))
                {
                fehler();
                }

            echo '<option value="0"><span class="text_mitte">alle</span></option>';

            while ($zeile_tl=mysql_fetch_array($ergebnis_tl))
                {
                if ($_SESSION['hau_teamleiter'] == $zeile_tl['hma_id'])
                    {
                    echo '<option value="' . $zeile_tl['hma_id']
                        . '" style="background-color:#E28B78;" selected><span class="text">' . $zeile_tl['hma_login']
                        . '</span></option>';
                    }
                else
                    {
                    echo '<option value="' . $zeile_tl['hma_id'] . '"><span class="text_mitte">'
                        . $zeile_tl['hma_login'] . '</span></option>';
                    }
                }

            echo '</select>';
            break;
        }

    echo '</td>';
    }

echo '<td colspan="' . ($infozahl + $aktionenzahl)
    . '" style="text-align:right;"><input type="submit" name="speichern" value="filtern" class="formularbutton" /></td></tr>';

echo '<tr><td colspan="' . $infozahl
    . '" class="xnormal_sort" style="border:1px solid grey;text-align:center;">Info</td>';

echo '<td colspan="' . $aktionenzahl
    . '" class="xnormal_sort" style="border:1px solid grey;text-align:center;">Action</td>';

echo '</tr>';

echo '</form>';


// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile=mysql_fetch_array($ergebnis))
    {

    $task_id=$zeile['hau_id'];

   $color='#ffffff';
   $font='text_klein';

include('segment_zeilenfarbe.php');   
   
    // Beginne Datenausgabe
    echo '<tr>';

    foreach ($anzeigefelder as $bezeichner => $inhalt)
        {
        
    #$zeile[$inhalt] = htmlspecialchars($zeile[$inhalt],ENT_QUOTES);
     	
        switch ($bezeichner)
            {
            case 'angelegt':
                $zeile[$inhalt]=zeitstempel_anzeigen($zeile[$inhalt]);
                break;

            case 'Start':
                $zeile[$inhalt]=substr(datum_anzeigen($zeile[$inhalt]), 0, 8);
                break;
                     


            case 'P-Ende':
                if ($zeile[$inhalt] == '9999-01-01')
                    {
                    $zeile[$inhalt]='open';
                    }
                else
                    {
                    $zeile[$inhalt]=substr(datum_anzeigen($zeile[$inhalt]), 0, 8);
                    }
                break;

            case 'R-Ende':
                if ($zeile[$inhalt] == '9999-01-01' or $zeile[$inhalt] == '')
                    {
                    $zeile[$inhalt]='open';
                    }
                else
                    {
                    $zeile[$inhalt]=substr(datum_anzeigen($zeile[$inhalt]), 0, 8);
                    }
                break;

            case 'Status':
                switch ($zeile[$inhalt])
                    {
                    case '0':
                        $zeile[$inhalt]='queued';
                        break;

                    case '1':
                        $zeile[$inhalt]='in progress';
                        break;

                    case '2':
                        $zeile[$inhalt]='rejected';
                        break;
                    }

                break;

            case 'Zugewiesen':
                switch ($zeile[$inhalt])
                    {
                    case '0':
                        $zeile[$inhalt]='no';
                        break;

                    case '1':
                        $zeile[$inhalt]='yes';
                        break;
                    }

                break;

            case 'Mitarbeiter':

                // Ermittle, ob fuer die Aufgabe Bearbeiter existieren

                $sql_ma=
                    'SELECT uau_id FROM aufgaben_mitarbeiter WHERE uau_ma_status<2 AND uau_hauid = ' . $zeile['hau_id'];

                // Frage Datenbank nach Suchbegriff
                if (!$ergebnis_ma=mysql_query($sql_ma, $verbindung))
                    {
                    fehler();
                    }

                if (mysql_num_rows($ergebnis_ma) > 1)
                    {

                    $zeile[$inhalt]='more than one';
                    }
                break;

            case 'Fortschritt [%]':

                // Fertigstellungsgrad

                $sql_menge='SELECT ulo_id,SUM(ulo_fertig) as Menge FROM log ' .
                    'LEFT JOIN aufgaben_mitarbeiter ON uau_hauid = ulo_aufgabe ' .
                    'WHERE ulo_aufgabe = ' . $zeile['hau_id'] . ' AND ulo_ma = uau_hmaid ' .
                    ' GROUP BY ulo_aufgabe';


                // Frage Datenbank nach Suchbegriff
                if (!$ergebnis_menge=mysql_query($sql_menge, $verbindung))
                    {
                    fehler();
                    }

                if (mysql_num_rows($ergebnis_menge) != 0)
                    {
                    while ($zeile_menge=mysql_fetch_array($ergebnis_menge))
                        {
                        $zeile[$inhalt]=$zeile_menge['Menge'];
                        }
                    }
                else
                    {
                    $zeile[$inhalt]=0;
                    }
                $sql_ma='SELECT COUNT(*) AS Menge FROM aufgaben_mitarbeiter ' .
                    'WHERE uau_hauid = ' . $zeile['hau_id'];

                // Frage Datenbank nach Suchbegriff
                if (!$ergebnis_ma=mysql_query($sql_ma, $verbindung))
                    {
                    fehler();
                    }

                if (mysql_num_rows($ergebnis_ma) != 0)
                    {
                    while ($zeile_ma=mysql_fetch_array($ergebnis_ma))
                        {
                        if ($zeile_ma['Menge'] > 0)
                            {

                            $zeile[$inhalt]=round($zeile[$inhalt] / $zeile_ma['Menge'], 2);
                            }
                        }
                    }

                break;

            case 'abgeschlossen':

                    $zeile[$inhalt]=zeitstempel_anzeigen($zeile[$inhalt]);
                    break;
            }

        switch ($bezeichner)
            {
            case 'TNR':
                echo '<td bgcolor="' . $color . '" class="' . $font . '">';

                echo '<a href="aufgabe_ansehen.php?hau_id=' . $zeile['hau_id'] . '">' . $zeile[$inhalt] . '</a></td>';
                break;

            case 'Ticket':
                echo '<td bgcolor="' . $color . '" class="' . $font . '">';

                if(substr($zeile['hau_ticketnr'],0,2)=='IR')
                {
                    $reportnummer = substr($zeile['hau_ticketnr'], 3);
                    echo '<a href="ir_ansicht.php?hir_id=' . $reportnummer. '">'. $zeile[$inhalt] . '</a>';
                } 
                echo '</td>';
                
                break;

            case 'Aufgabe':
                echo '<td bgcolor="' . $color . '" class="' . $font . '">';

                echo '<a href="aufgabe_ansehen.php?hau_id=' . $zeile['hau_id'] . '" onmouseover="Tip(\''
                    . substr(preg_replace('/\r\n|\r|\n/', ' ', (htmlspecialchars($zeile["hau_beschreibung"],ENT_QUOTES))), 0, 300)
                    . '\')" onmouseout="UnTip()">' . $zeile[$inhalt] . '</a></td>';
                break;

            case 'Projekt':
                if (($zeile['hau_hprid'] > 10))
                    {
                    echo '<td bgcolor="' . $color . '" class="' . $font . '">';

                    echo '<a href="uebersicht_projekt.php?hpr_id=' . $zeile['hau_hprid'] . '">' . htmlspecialchars($zeile[$inhalt])
                        . '</a></td>';
                    }
                else
                    {
                    echo '<td bgcolor="' . $color . '" class="' . $font . '">' . htmlspecialchars($zeile[$inhalt]) . '</td>';
                    }
                break;

               case 'aktualisiert':

                $sql_update =  'SELECT ulo_datum AS letzte_bearbeitung FROM log
                                LEFT JOIN aufgaben ON hau_id = ulo_aufgabe
                                WHERE hau_id = '.$zeile['hau_id'].'
                                ORDER BY ulo_datum DESC
                                LIMIT 1';
                            
                            
                   // Frage Datenbank nach Suchbegriff
                if (!$ergebnis_update=mysql_query($sql_update, $verbindung))
                    {
                    fehler();
                    }

                    if(mysql_num_rows($ergebnis_update)>0)
                    {
                       while ($zeile_update=mysql_fetch_array($ergebnis_update))
                        {
                        $zeile[$inhalt]=zeitstempel_anzeigen($zeile_update['letzte_bearbeitung']);
                        }
                         echo '<td bgcolor="' . $color . '" class="' . $font . '">' . htmlspecialchars($zeile[$inhalt]) . '</td>';
                    } else
                    {
                         echo '<td bgcolor="' . $color . '" class="' . $font . '">&nbsp;</td>';                           
                    }
                break;
                
            default:
                echo '<td bgcolor="' . $color . '" class="' . $font . '">' . htmlspecialchars($zeile[$inhalt]) . '</td>';
                break;
            }

        }

    include('segment_infos.php');
    include('segment_aktion.php');

    echo '</tr>';

    echo '</tr>';
    } // Ende Alle-Anzeige fuer die Ausgabe

echo '</table>';
?>
