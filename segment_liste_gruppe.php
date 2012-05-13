<?php
###### Editnotes ####
#$LastChangedDate: 2011-11-14 09:24:37 +0100 (Mo, 14 Nov 2011) $
#$Author: msternberg $ 
#####################
if (isset($_GET['sortierschluessel']))
    {
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

echo '<br>';
echo '<br>';

  echo '<form action="segment_filter_string.php" method="post">';
echo '<table class="matrix" cellspacing="1" cellpadding="3" width="1200" border="0">';
echo '<tr>';

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

        echo '<td class="tabellen_titel" valign="top"><a href="' . $_SERVER['PHP_SELF'] . '?aktuelle_seite=0&sortierschluessel=' . $inhalt . '"><span class="xnormal_sort">' . $bezeichner . '</span></a>' . $anzeige;



    switch ($bezeichner)
        {

        case 'Gruppe':
            echo '<br>';
            echo '<select size="1" name="uaz_pg" class="liste" style="width:50px;" width=50>';
            $sql_filter='SELECT ule_id, ule_name FROM level WHERE ule_id > 1 AND ule_id < 99 and ule_aktiv = 1 ' .
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

    }

  echo '</td>';
    }

echo '<td style="text-align:right;"><input type="submit" name="speichern" value="filtern" class="formularbutton" /></td>';

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

                       while ($zeile_update=mysql_fetch_array($ergebnis_update))
                        {
                        $zeile[$inhalt]=zeitstempel_anzeigen($zeile_update['letzte_bearbeitung']);
                        }
                   break;
                
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

 echo '<td> </td>';    

    echo '</tr>';
    } // Ende Alle-Anzeige fuer die Ausgabe

echo '</table>';
?>
