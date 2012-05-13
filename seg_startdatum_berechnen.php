<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
switch ($xIntervalltyp)
    {
    case 0: // Täglich

        # Dazu addieren wir zum Startdatum einen Tag

        $xStarttag=strftime("%Y-%m-%d", strtotime($xStarttag . '+1 day'));
        break;

    case 1: // Tageweise

        # Dazu addieren wir zum Startdatum das Intervall

        $xStarttag=strftime("%Y-%m-%d", strtotime($xStarttag . '+' . $xIntervallwert . ' day'));
        break;

    case 2: // Wochenweise

        $fDatum=explode("-", $xStarttag);
        $xStarttag=date("Y-m-d", mktime(0, 0, 0, $fDatum[1], $fDatum[2] + $xIntervallwert * 7, $fDatum[0]));
        break;

    case 3: // Monatsweise

        # Wir addieren einen Monat zum Startdatum

        $fDatum=explode("-", $xStarttag);
        $xStarttag=date("Y-m-d", mktime(0, 0, 0, $fDatum[1] + $xIntervallwert, $xIntervalltag, $fDatum[0]));
        break;

    case 4: //jährlich

        $fDatum=explode("-", $xStarttag);
        $xStarttag=date("Y-m-d", mktime(0, 0, 0, $xIntervallmonat, $xIntervalltag, $fDatum[0] + $xIntervallwert));
        break;

        break;
    }

$Startdatum_array=getdate(strtotime($xStarttag));

# Nun nehmen wir davon den Wochentag und prüfen auf Wochenende (6=Samstag, 0= Sonntag)

while ($Startdatum_array['wday'] == 0 OR $Startdatum_array['wday'] == 6)
    {
    # Anscheinend haben wir ein Wochenende erwischt, addiere einen Tag dazu und prüfe nochmal

    $xStarttag = strftime("%Y-%m-%d", strtotime($xStarttag . '+1 day'));
    $Startdatum_array=getdate(strtotime($xStarttag));
    }
?>