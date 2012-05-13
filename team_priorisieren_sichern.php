<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');

include('segment_session_pruefung.php');
include('segment_init.php');

foreach ($_POST['uau_prio'] as $uau_hauid => $uau_prio)
    {

    $sql_prio = 'SELECT uau_prio FROM aufgaben_mitarbeiter WHERE  uau_hauid = "' . $uau_hauid . '"';

    if (!($ergebnis_prio=mysql_query($sql_prio, $verbindung)))
        {
        fehler();
        }

    while ($zeile_prio=mysql_fetch_array($ergebnis_prio))
        {
        if ($zeile_prio['uau_prio'] != $uau_prio)
            {

            $sql=
                'UPDATE aufgaben_mitarbeiter SET uau_prio = "' . $uau_prio . '" WHERE uau_hauid = "' . $uau_hauid . '"';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }


            ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

            $sql_check='SELECT uau_hmaid FROM aufgaben_mitarbeiter WHERE uau_hauid = ' . $uau_hauid;

            if (!($ergebnis_check=mysql_query($sql_check, $verbindung)))
                {
                fehler();
                }

            while ($zeile_check=mysql_fetch_array($ergebnis_check))
                {
                $pingling = $zeile_check['uau_hmaid'];

                if ($pingling != $_SESSION['hma_id'])
                    {
                    $hauid=$uau_hauid;
                    $initiator=$_SESSION['hma_id'];
                    $empfaenger=$pingling;
                    $info='Priority for this task was changed by Teamlead.';

                    include('segment_news.php');


                    ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

                    ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

                    $mailtag='ume_aufgabestatus';
                    $mail_hma_id=$pingling;
                    $mail_hau_id=$uau_hauid;
                    $text="\nPriority for this task was changed by Teamlead.\n";
                    $mail_info='New Priority';

                    include('segment_mail_senden.php');

                    ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

                    }
                }
            } // End IF Prio check
        }     // End While Check
    }


// Zurueck zur Liste

header('Location: team_priorisieren.php');
exit;
?>