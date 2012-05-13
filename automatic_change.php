<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
# Integriere Module

require_once('konfiguration.php');
                
$hau_id = $_REQUEST['hau_id'];

            # Informiere die Changemanager

            $sql='SELECT * FROM mitarbeiter LEFT JOIN rollen_matrix ON urm_hmaid = hma_id WHERE hma_aktiv = 1 AND urm_uroid = 1';

            if (!($ergebnis=mysql_query($sql, $verbindung)))
                {
                fehler();
                }

            while ($zeile=mysql_fetch_array($ergebnis))
                {
                ///////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

                $hauid = $hau_id;
                $initiator='1';
                $empfaenger=$zeile['hma_id'];
                $info='Ein neuer Change wurde zur Freigabe eingereicht.';

                include('segment_news.php');

                //////////////////////////  EINTRAG NEWS ///////////////////////////////////////////

                ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////

                $mailtag='ume_aufgabestatus';
                $mail_hma_id=$empfaenger;                 
                $mail_hau_id=$hau_id;
                $text="\nEin neuer Change wurde eingereicht:\n";
                $mail_info='Neuer Change';
                $kommentator = 'Change kam von CC per Mail';
                $telefon = $_SESSION['hma_telefon'];

                include('segment_mail_senden.php');

                ///////////////////////////  MAIL SENDEN ///////////////////////////////////////////
                }                
        
?>