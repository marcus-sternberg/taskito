<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
// Frage Datenbank nach Suchbegriff
if (!$ergebnis=mysql_query($sql, $verbindung))
    {
    fehler();
    }

// Beginne mit Tabellenausgabe
echo '<table class="element" cellspacing="1" cellpadding="3" width="95%">';

echo '<tr>';

foreach ($anzeigefelder as $bezeichner => $inhalt)
    {
    echo '<td class="tabellen_titel" valign="top"><span class="xnormal_sort">' . $bezeichner . '</span>';
    }

echo '<td colspan="' . $aktionenzahl
    . '" class="xnormal_sort" style="border:1px solid grey; width:100px; text-align:center;">Action</td>';

echo '</tr>';

while ($zeile=mysql_fetch_array($ergebnis))
    {

    // Beginne Datenausgabe
    echo '<tr>';

    foreach ($anzeigefelder as $bezeichner => $inhalt)
        {

        # Ermittle die Anzahl der Wiederholungen

        if ($zeile['utr_wiederholung'] == 1)
            {
            $zeile['utr_wiederholungwert']='endless';
            }

        # Wandle Datum zur Anzeige

        if ($inhalt == 'utr_next_date')
            {
            $zeile[$inhalt]=datum_anzeigen($zeile[$inhalt]);
            }

        if ($inhalt == 'utr_pende_wert')
            {
            $zeile[$inhalt]=$zeile[$inhalt] . ' day(s)';
            }

        # Ermittle Intervallart

        if ($inhalt == 'utr_intervalltyp')
            {
            switch ($zeile['utr_intervalltyp'])
                {
                case 0:
                    $zeile[$inhalt]='Daily';
                    break;

                case 1:
                    $zeile[$inhalt]='Every ' . $zeile['utr_intervallwert'] . '. Day';
                    break;

                case 2:
                    switch ($zeile['utr_intervalltag'])
                        {
                        case 1:
                            $wota='Monday';
                            break;

                        case 2:
                            $wota='Tuesday';
                            break;

                        case 3:
                            $wota='Wednesday';
                            break;

                        case 4:
                            $wota='Thursday';
                            break;

                        case 5:
                            $wota='Friday';
                            break;
                        }

                    $zeile[$inhalt]='Every ' . $zeile['utr_intervallwert'] . '. week on ' . $wota;
                    break;

                case 3:
                    $zeile[$inhalt]='Every ' . $zeile['utr_intervalltag'] . '. Day in the Month';
                    break;

                case 4:
                    $fDatum=explode("-", $zeile['utr_intervalltag']);
                    $xIntervallmonat=$fDatum[1];
                    $xIntervalltag=$fDatum[0];
                    $zeile[$inhalt]=
                        'Every ' . $zeile['utr_intervallwert'] . '. Year at the ' . $xIntervalltag . '. Day of the '
                        . $xIntervallmonat . '. Month';
                    break;
                }
            }

        echo '<td bgcolor="#F3BE63" class="text_klein">' . ($zeile[$inhalt]) . '</td>';
        }

    $zeile['hau_id']=$zeile['utr_id'];
    include('segment_aktion_serie.php');

    echo '</tr>';
    }

echo '</table>';
?>