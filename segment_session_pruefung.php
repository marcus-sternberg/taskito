<?php
###### Editnotes ####
#$LastChangedDate: 2011-10-21 11:24:10 +0200 (Fr, 21 Okt 2011) $
#$Author: msternberg $ 
#####################
// Session-Handling - Pruefe anhand der Benutzernummer ob gueltige Session vorliegt

$a=session_id();

if ($a == "")
    session_start();
    

if (!isset($_SESSION['hma_id']))
    {
    $_SESSION['hma_id']=3;
    $_SESSION['hma_login']='Gast';
    $_SESSION['hma_level']=99;
    if($session_frei != 1)
    {
    header('Location: index.php ');
    exit;
    }
    }
  
  
# $erlaubte_seiten = array ('index.php', 'ticket_anzeigen.php', 'grabberliste.php', 'email_block_liste.php', 'verwaltung_urlaub_gesamt.php')
    
if($_SESSION['hma_id'] == 3 AND $session_frei != 1) #substr(strrchr ($_SERVER['PHP_SELF'], "/"), 1)!='index.php')
{
    header('Location: index.php ');
    exit;
}

?>