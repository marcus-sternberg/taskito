<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
if ($_SESSION['hma_id'] == 3)
    {
    echo '<form action="login.php" method="post">';

    echo '<table cellspacing=0 cellpadding=0 border=0 width="195" align="center">';

    echo '<input type="hidden" name="url" value="<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
##################### echo $url ?>">';

    echo '<tr>';

    echo '<td align="center" style="vertical-align: middle;">Login:&nbsp;&nbsp;</td><td>&nbsp;</td>';

    echo
        '<td align="center" style="vertical-align: middle;"><input type="text" name="hma_login" class="text" style="width:110px;"></td><td>&nbsp;</td>';

    echo
        '<td align="center" style="vertical-align: middle;"><input type="password" name="hma_pw" class="text" style="width:110px;"></td><td>&nbsp;</td>';

    echo
        '<td align="center" style="vertical-align: middle;"><input type="submit" value="Go" class="searchbutton" /></td>';

    echo '</tr>';

    echo '</table>';

    echo '</form>';
    }
else
    {
    if ($_SESSION['filterstring'] != '')
        {
        echo '<table border=0><tr><td>';

        echo '<span class="text" align="right">The following Filters are set!</span><br>';

        if (isset($_SESSION['ulk_id']) AND $_SESSION['ulk_id'] != 0)
            {
            echo '<span class="text">Kategorie | </span>';
            }

        if (isset($_SESSION['hau_pende']) AND $_SESSION['hau_pende'] != 0)
            {
            echo '<span class="text">Enddatum | </span>';
            }

        if (isset($_SESSION['queued']) AND $_SESSION['queued'] != 0)
            {
            echo '<span class="text">Aufgabenqueue | </span>';
            }

        if (isset($_SESSION['uli_id']) AND $_SESSION['uli_id'] != 0)
            {
            echo '<span class="text">Provider | </span>';
            }

        if (isset($_SESSION['uau_hmaid']) AND $_SESSION['uau_hmaid'] != 0)
            {
            echo '<span class="text">Mitarbeiter | </span>';
            }

        if (isset($_SESSION['hau_hprid']) AND $_SESSION['hau_hprid'] != 0)
            {
            echo '<span class="text">Projekt | </span>';
            }

        if (isset($_SESSION['hau_prio']) AND $_SESSION['hau_prio'] != 0)
            {
            echo '<span class="text">Priorität| </span>';
            }

        if (isset($_SESSION['hau_typ']) AND $_SESSION['hau_typ'] != 0)
            {
            echo '<span class="text">Typ | </span>';
            }

        if (isset($_SESSION['uaz_pg']) AND $_SESSION['uaz_pg'] != 0)
            {
            echo '<span class="text">Gruppe | </span>';
            }

        if (isset($_SESSION['hau_inhaber']) AND $_SESSION['hau_inhaber'] != 0)
            {
            echo '<span class="text">Eigner</span>';
            }

        if (isset($_SESSION['hau_teamleiter']) AND $_SESSION['hau_teamleiter'] != 0)
            {
            echo '<span class="text">Teamlead</span>';
            }

        echo '</td><td style="vertical-align: bottom;">';

        echo '<form action="segment_filter_string.php">';

        echo '   <input type="submit" name="filter" value="Filter entfernen" class="formularbutton">';

        echo '</form>';

        echo '</td></tr></table>';
        }
    }
?>