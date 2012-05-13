<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
include('segment_session_pruefung.php');

include('konfiguration.php');

$_SESSION['queued']='';

$_SESSION['filterstring']='';

$_SESSION['uau_hmaid']='';

$_SESSION['hau_hprid']='';

$_SESSION['hau_prio']='';

$_SESSION['hau_typ']='';

$_SESSION['suchstring']='';

$_SESSION['uaz_pg']='';

$_SESSION['hau_inhaber']='';

$_SESSION['hau_teamleiter']='';

$_SESSION['hau_pende']='';

foreach ($_POST as $filter => $inhalt)
    {
    if ($_POST[$filter] == '0')
        {
        $_SESSION[$filter]='0';
        }
    else
        {
        if ($_POST[$filter] != 'filtern' AND $filter != 'suchstring')
            {
            if ($filter != 'hau_pende')
                {

                $_SESSION['filterstring'].=' AND ' . $filter . '=' . $inhalt . ' ';

                $_SESSION[$filter]=$inhalt;
                }
            else if ($filter == 'hau_pende')
                {
                if ($inhalt != '')
                    {
                    $inhalt=datum_wandeln_euus($inhalt);
                    $_SESSION[$filter]=$inhalt;
                    $_SESSION['filterstring'].=' AND ' . $filter . ' LIKE "%' . $inhalt . '%" ';
                    }
                }

            if ($filter == 'queued')
                {

                $_SESSION[$filter]=1;
                $_SESSION['filterstring'].=' AND uau_ma_status = 0 ';
                }

            //echo 'Filter a) :'.$filter.' # '.$inhalt.'<br> -->'.$_SESSION['filterstring'].'<br><br>';

            }
        else if ($filter == 'suchstring')
            {

            $_SESSION['suchstring']=stripslashes($inhalt);

            //echo 'Filter b):'.$filter.' # '.$inhalt.'<br> -->'.$_SESSION['filterstring'].'<br><br>';

            }
        }
    }

// $_SESSION['neu_gesetzt']=1;

header('Location: ' . $_SERVER['HTTP_REFERER']);

exit;
?>