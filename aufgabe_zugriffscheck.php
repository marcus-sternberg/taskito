<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
require_once('konfiguration.php');
include('segment_session_pruefung.php');
// überprüft ob eine Aufgabe bearbeitet werden darf.
if (isset($_GET['edit_allowance']) && $_GET['edit_allowance'] == 1)
{
	$check_value=time()-(60*$taskedit_allowance_timevalue);
	//echo date ("Y-m-d H:i:s",$check_value);
	$sql_editor="SELECT hau_editor,hau_editor_response, CONCAT(hma_name,', ',hma_vorname) as username   
				 FROM aufgaben
				 LEFT JOIN mitarbeiter on (hau_editor=hma_id) 
				 WHERE hau_id=".$_GET['hau_id'];
	if (!($check_editor=mysql_query($sql_editor, $verbindung)))
	{
	    fehler();
	}
	$row_check_editor=mysql_fetch_array($check_editor);
	if ($row_check_editor['hau_editor'] == $_SESSION['hma_id'] || ($row_check_editor['hau_editor_response'] < date("Y-m-d H:i:s",$check_value)))
		echo "var tmp_status=1";
	else
		echo "var tmp_status=0; var tmp_user='".$row_check_editor['username']."'";
	exit;
}
// trägt den aktuellen Bearbeiter einer Aufgabe in die Datenbank ein
if (isset($_GET['current_user']) && $_GET['current_user'] == 1)
{
	$act_value=date("Y-m-d H:i:s");
	$check_value=time()-(60*$taskedit_allowance_timevalue);
	$sql_editor="UPDATE aufgaben 
				 set hau_editor='".$_SESSION['hma_id']."'
				 ,hau_editor_response='".$act_value."' 
				 WHERE 
				 	hau_id=".$_GET['hau_id']."
				 	AND (hau_editor='".$_SESSION['hma_id']."' 
				 			OR (hau_editor != '".$_SESSION['hma_id']."' AND hau_editor_response<'".date("Y-m-d H:i:s",$check_value)."')
				 	     )";
	if (!($check_editor=mysql_query($sql_editor, $verbindung)))
	{
	    fehler();
	}
	if (mysql_affected_rows() == 1)
		echo "1";
	else
		echo "0";
	exit;
}
// informiert, was gerade in einem Task noch so passiert
if (isset($_GET['get_taskhappenings']) && $_GET['get_taskhappenings'] == 1)
{
	$_GET['task_id']=$_GET['hau_id'];
	include ('aufgabenlog.php');
	
}
