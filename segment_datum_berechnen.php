<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
switch ($Daten['xIntervall'])
    {
    case 0: // Täglich

        # Dazu addieren wir zum Startdatum einen Tag

        if ($Startdatum_neue_aufgabe <= date("Y-m-d"))
            {
            $Startdatum_neue_aufgabe=strftime("%Y-%m-%d", strtotime($Startdatum_neue_aufgabe . '+1 day'));
            }
        $Daten['xIntervallwert']=1;
        $Daten['xIntervalltag']=0;

        break;

    case 1: // Tageweise

        # Dazu addieren wir zum Startdatum das Intervall
        if ($Startdatum_neue_aufgabe <= date("Y-m-d"))
            {

            $Startdatum_neue_aufgabe=strftime("%Y-%m-%d",
                strtotime($Startdatum_neue_aufgabe . '+' . $Daten['xIntervalltag'] . ' day'));
            }
        $Daten['xIntervalltag']=0;

        break;

    case 2: // Wochenweise
        //echo $Startdatum_neue_aufgabe.date("w", strtotime($Startdatum_neue_aufgabe)).$Intervall_wochentag;

        if ($Intervall_wochentag < date("w", strtotime($Startdatum_neue_aufgabe)))
            {

            # Berechne zunächst den nächst folgenden Wochentag
            switch ($Intervall_wochentag)
                {
                case 1:
                    $Startdatum_neue_aufgabe=strftime("%Y-%m-%d", strtotime('next monday'));
                    break;

                case 2:
                    $Startdatum_neue_aufgabe=strftime("%Y-%m-%d", strtotime('next tuesday'));
                    break;

                case 3:
                    $Startdatum_neue_aufgabe=strftime("%Y-%m-%d", strtotime('next wednesday'));
                    break;

                case 4:
                    $Startdatum_neue_aufgabe=strftime("%Y-%m-%d", strtotime('next thursday'));
                    break;

                case 5:
                    $Startdatum_neue_aufgabe=strftime("%Y-%m-%d", strtotime('next friday'));
                    break;
                }

            if ($Startdatum_neue_aufgabe <= date("Y-m-d"))
                {
                # Addiere noch die n-1 Wochen
                $Startdatum_neue_aufgabe=strftime("%Y-%m-%d",
                    strtotime($Startdatum_neue_aufgabe . '+' . ($Daten['xIntervallwert'] - 1) . ' week'));
                }
            }
        else if (date("w", strtotime($Startdatum_neue_aufgabe)) == $Intervall_wochentag)
            {
            if ($Startdatum_neue_aufgabe <= date("Y-m-d"))
                {

                # Addiere noch die Wochen
                $Startdatum_neue_aufgabe=strftime("%Y-%m-%d",
                    strtotime($Startdatum_neue_aufgabe . '+' . ($Daten['xIntervallwert']) . ' week'));
                }
            }
        else
            {

            # Berechne zunächst den nächst folgenden Wochentag
            switch ($Intervall_wochentag)
                {
                case 1:
                    $Startdatum_neue_aufgabe=strftime("%Y-%m-%d", strtotime('next monday'));
                    break;

                case 2:
                    $Startdatum_neue_aufgabe=strftime("%Y-%m-%d", strtotime('next tuesday'));
                    break;

                case 3:
                    $Startdatum_neue_aufgabe=strftime("%Y-%m-%d", strtotime('next wednesday'));
                    break;

                case 4:
                    $Startdatum_neue_aufgabe=strftime("%Y-%m-%d", strtotime('next thursday'));
                    break;

                case 5:
                    $Startdatum_neue_aufgabe=strftime("%Y-%m-%d", strtotime('next friday'));
                    break;
                }
            }

        $Daten['xIntervalltag']=$Intervall_wochentag;

        break;

    case 3: // Monatsweise

        # Wir addieren einen Monat zum Startdatum

        if (abs(date("d")) >= abs($Daten['xIntervalltag']))
            {
            $Startdatum_neue_aufgabe=strftime("%Y-%m-%d", strtotime($Startdatum_neue_aufgabe . '+1 month'));
            }

        $monat=abs(strftime("%m", strtotime($Startdatum_neue_aufgabe)));
        $jahr=abs(strftime("%Y", strtotime($Startdatum_neue_aufgabe)));

        $Startdatum_neue_aufgabe=date('Y-m-d', (mktime(0, 0, 0, $monat, 1, $jahr)));


        # Zunächst ermitteln wir, wieviel Tage der Monat des Startdatums hat

        $Monatstage=date("t", strtotime($Startdatum_neue_aufgabe));

        # Jetzt prüfen wir, ob der gewünschte Tag im Monat ggf. über der Anzahl der Tage des Monats liegt

        $Daten['xIntervalltag']=$Daten['xIntervalltag'];
        $Daten['xIntervallwert']=0;

        if ($Daten['xIntervalltag'] > $Monatstage)
            {
            $Daten['xIntervalltag']=$Monatstage;
            }

        # Jetzt prüfen wir, ob der gewünschte Tag vorliegt

        $Startdatum_array=getdate(strtotime($Startdatum_neue_aufgabe));

        while ($Startdatum_array['mday'] != $Daten['xIntervalltag'])
            {
            # Anscheinend haben wir nicht den gewünschten Tag erwischt, addiere einen Tag dazu und prüfe nochmal

            $Startdatum_neue_aufgabe = strftime("%Y-%m-%d", strtotime($Startdatum_neue_aufgabe . '+1 day'));
            $Startdatum_array=getdate(strtotime($Startdatum_neue_aufgabe));
            }

        break;

    case 4: //jährlich

        #  Bauen wir uns mal ein Datum aus den Eingaben

        $Jahresdatum=date("Y") . '-' . $Daten['xIntervallwert'] . '-' . $Daten['xIntervalltag'];

        # Mal sehen, ob das Datum in der Zukunft liegt - dazu in Timestamps wandeln und vergleichen

        $Datum_jahr=strtotime($Jahresdatum);
        $Datum_jetzt=time();

        if ($Datum_jahr < $Datum_jetzt)
            {
            $Startdatum_neue_aufgabe=strftime("%Y-%m-%d", strtotime($Startdatum_neue_aufgabe . '+1 year'));

            # Baue neues Datum mit dem vorgebenen Tag und Monat

            $Startdatum_array=explode('-', $Startdatum_neue_aufgabe);
            $Startdatum_neue_aufgabe=date('Y-m-d', (mktime(0, 0, 0, $Daten['xIntervallwert'], $Daten['xIntervalltag'],
                $Startdatum_array[0])));
            }
        else
            {
            $Startdatum_neue_aufgabe=$Jahresdatum;
            }

        break;
    }
?>