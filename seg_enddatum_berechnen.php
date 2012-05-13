<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
# Nun das Enddatum berechnen

if ($Daten['xPlanende'] == 1) // sofort beginnen
    {
    $Planende_neue_aufgabe=$Startdatum_neue_aufgabe;
    }
else
    {
    $Planende_neue_aufgabe=strftime("%Y-%m-%d",
        strtotime($Startdatum_neue_aufgabe . '+' . $Daten['xPlanende_eingabe'] . ' day'));
    }

#So, nun pruefen wir noch, ob es sich ums Wochenende handelt und addieren ggf. Tage
# Dazu wandeln wir zunächst das Datum in ein Feld um mit den einzelnen Komponenten des Datum
$Planende_array=getdate(strtotime($Planende_neue_aufgabe));

# Nun nehmen wir davon den Wochentag und prüfen auf Wochenende (6=Samstag, 0= Sonntag)

while ($Planende_array['wday'] == 0 OR $Planende_array['wday'] == 6)
    {
    # Anscheinend haben wir ein Wochenende erwischt, addiere einen Tag dazu und prüfe nochmal

    $Planende_neue_aufgabe = strftime("%Y-%m-%d", strtotime($Planende_neue_aufgabe . '+1 day'));
    $Planende_array=getdate(strtotime($Planende_neue_aufgabe));
    }

if ($Daten['xPlanende'] == 3)
    {
    $Planende_neue_aufgabe='9999-01-01';
    $Aufgabendaten['hau_datumstyp']=1;
    }
else
    {
    $Aufgabendaten['hau_datumstyp']=2;
    }
?>