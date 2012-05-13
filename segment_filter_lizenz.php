<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
include('segment_session_pruefung.php');

$_SESSION['filterstring']='';

$_SESSION['ulk_id']='';

foreach ($_POST as $filter => $inhalt)
    {
    if ($_POST[$filter] == '0')
        {
        $_SESSION[$filter]='0';
        }
    else
        {
        if ($_POST[$filter] != 'filter' AND $filter != 'suchstring')
            {
            $_SESSION['filterstring'].=' AND ' . $filter . '=' . $inhalt . ' ';

            $_SESSION[$filter]=$inhalt;

            // echo 'Filter a) :'.$filter.' # '.$inhalt.'<br> -->'.$_SESSION['filterstring'].'<br><br>';
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