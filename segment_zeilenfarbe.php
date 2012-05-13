<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
if (isset($zeile['hau_prio']))
    {
    if ($zeile['hau_prio'] == 2 OR $zeile['hau_prio'] == 3)
        {
        $font='text_prio';
        }
    else
        {
        $font='text_klein';
        }
    }

// Differenz der Aufgaben zum aktuellen Datum ermitteln

$sql_time=
    'SELECT hau_abschluss, hau_pende, DATEDIFF(hau_pende,curdate()) as Zeitdifferenz FROM aufgaben WHERE hau_id = '
    . $task_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_time=mysql_query($sql_time, $verbindung))
    {
    fehler();
    }

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile_time=mysql_fetch_array($ergebnis_time))
    {
    $Zeitdifferenz = $zeile_time['Zeitdifferenz'];
    $Endzeit=$zeile_time['hau_pende'];
    $ende_aufgabe=$zeile_time['hau_abschluss'];
    }


//   if ($zeile[$inhalt]=='0' and $bezeichner=='Ausführer') {$zeile[$inhalt]='n.n.';}

if ($Zeitdifferenz != NULL)
    {
    if ($Zeitdifferenz > 10)
        {
        $color='#C1E2A5';
        }
    else if ($Zeitdifferenz < 0)
        {
        $color='#FFBFA0';
        }
    else
        {
        $color='#FFF8B3';
        }
    } 

if ($Endzeit == '9999-01-01')
    {
    $color='#CED9E7';
    }

if (isset($ende_aufgabe))
    {
    if ($ende_aufgabe == 1)
        {
        $color='#E3E3E3';
        }
    }
  
// Pruefe, ob es fuer den Alarm eine Abschlussmeldung gibt?

$sql_time= 'SELECT hau_recovery FROM aufgaben WHERE hau_id = ' . $task_id;

// Frage Datenbank nach Suchbegriff
if (!$ergebnis_time=mysql_query($sql_time, $verbindung))
    {
    fehler();
    }

// Beginne Datenausgabe aus dem Abfrageergebnis der Datenbank
while ($zeile_time=mysql_fetch_array($ergebnis_time))
    {
    if ($zeile_time['hau_recovery'] == 1)
        {
        $color='#FFFFFF';
        }
    }  
    
?>