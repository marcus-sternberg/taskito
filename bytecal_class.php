<?php
###### Editnotes ####
#$LastChangedDate: 2011-08-25 13:44:06 +0200 (Do, 25 Aug 2011) $
#$Author: msternberg $ 
#####################
/******************************************************************************
 *         FILE: kalender_class.php
 *      VERSION: 1.00 (29-Oct-2006)
 *       AUTHOR: Andreas Petermann (c) http://www.Bytenation.de
 *      PURPOSE: This class is to create a Calendar for Time & Day
 * 				 This class helps you to create Day and Time in any Button
 * 				 or input fields 
 *  
 * 				 
 * REQUIREMENTS: nothing
 * 
 *      PACKAGE: /bytecal/bytecal.tpl
 *                       /bytecal_class.php
 *                       /date_go.gif        <-- This gif comes from http://www.famfamfam.com/
 *                         
 * 		CHANGES:	 
 * 
 * 
 *      WARNING: Please beware that you have W3C Valid HTML-Code else, 
 *               the mouseposition doesn't can locate perfect
 * 
 * 		   BUGS: no known Bugs this time
 *  
 *        NOTES: It is forbidden to change the Copyright or code in this Script
 *               suport and Download only from Bytenation.de or SportDates.de
 *******************************************************************************/
/*
 *  needed javascript Code in youre formular, you can place this between <head></head> or include this in a external
 *  Javascript file
 * 

<script type='text/javascript'>
var bn_who="";
function kalender(s)
{	document.getElementById('bn_frame').style.top=yPos + "px";
	document.getElementById('bn_frame').style.left=xPos + "px";
	document.getElementById('bn_frame').style.display='block';	
	bn_who=s;
}

init_mousemove();

var xPos="";
var yPos="";
var docEl = (   typeof document.compatMode != "undefined" && 
                 document.compatMode        != "BackCompat"
                )? "documentElement" : "body";

function init_mousemove() 
{    if(document.layers) document.captureEvents(Event.MOUSEMOVE);
    document.onmousemove =	dpl_mouse_pos;
}

function dpl_mouse_pos(e) 
{   xPos    =  e? e.pageX : window.event.x;
  	yPos    =  e? e.pageY : window.event.y;

	
	if (document.all && !document.captureEvents && docEl) 
	{   xPos    += document[docEl].scrollLeft;
	    yPos    += document[docEl].scrollTop;
	}
    
    if (document.layers) routeEvent(e);
}

</script>
 **********************************************************************************************************************/
/* 
 * needed html-code at the end of  your html site, it is important to place this at the end of your html document 
 * 
 * <!-- Here is the begin of the html code before </body> -->
 * <div id='bn_frame' style='position:absolute; display:none; height:198px; width:205px; background-color:#ced7d6; overflow:hidden;'>
 * 	<iframe src='./kalender/kalender.php'  style='width:208px; margin-left:-1px; border:0px; height:202px; background-color:#ced7d6; overflow:hidden; border='0'></iframe>
 * </div>
 * <!-- end of teh needed Code -->
 * </body>
 * 
 **********************************************************************************************************************/
/* 
 * Example to start this calendar in your formular
 * 
 * <input type='text' name='start_date' value='Termin von' id='start_date'/> <img src='kalender/date_go.gif' alt='ByteNations Calendar Input' onclick='kalender(document.getElementById("start_date"));'/> 
 * 
 **********************************************************************************************************************/
/* 
 * if the formular is send, you can open the class Kalender and format the Date/Time as unix-timestamp or
 * sql-timestamp to insert the input date valid in your sql-table
 * 
 * Example:
 * 
 * 	require ("./kalender_class.php");
 * 	$byte = new Kalender();
 * 	echo($byte->get_unixtime($_REQUEST['start_date']));	
 * 	echo($byte->get_sqltimestamp($_REQUEST['start_date']));	
 * 
 **********************************************************************************************************************/


class Kalender
  {
	var $VER = "1.0";
	
  	var $my_month = array(	 "1"=>"January",
							 "2"=>"February",
							 "3"=>"March",
							 "4"=>"April",
							 "5"=>"Mai",
							 "6"=>"June",
							 "7"=>"July",
							 "8"=>"August",
							 "9"=>"September",
							"10"=>"October",
							"11"=>"November",
							"12"=>"December");
					

	var $hour=	0;
	var $minute=0;
	var $year=0;
	var $day=0;
	var $month=0;
	var $error="";
	var $show_hour="";
	var $show_help="";
	var $show_minute="";
	var $show_day="";
	var $show_month="";
	var $show_year="";
	
	  
	function help_me()
	{	$help =  " <p style='margin-top:5px;'>Mit dem Wählen des Tages wird die Datumseingabe abgeschlossen.<br/>
				Zuvor besteht die Möglichkeit, eine Uhrzeit, den Monat sowie das Jahr zu wählen.</p>";	

		return $help;	
	}

	function get_years($year = 0,$show_years=6,$break=2)
	{	$e=1;
		$str ="</tr>";
		if(!$year || $year<=0) $year 	= 	intval(date("Y",time()));
		for($i=1;$i<=$show_years;$i++)	
		{	$str .="<td>".$year++."</td>\n";
			if($e++ >= $break && $i<$show_years-1)
			{	$e=1;
				$str .="</tr><tr>";
			}
		}
		$str .="</tr>";
		return($str);
	}

	function get_month($break=2)
	{	$e=1;
		$i=1;
		
		$str ="</tr>";
		while(list($m,$n) = each($this->my_month))
		{	$str .="<td>".$n."</td>\n";
			$i++;
			if($e++ >= $break && $i<12)
			{	$e=1;	
				$str .="</tr><tr>";
			}
		}
		$str .="</tr>";
		return($str);
	}

	function get_minute($jump=5,$break=4)
	{	$e=1;
		$str = "<tr>\n";
		for($i=0;$i<60;$i +=$jump)
		{	$str .="<td>".sprintf("%02d",$i)."</td>\n";
			if($e++ >= $break && $i<59-$jump)
			{	$e=1;
				$str .="</tr><tr>\n";
			}
		}
		$str .="</tr>\n";
		return($str);
	}

	function get_hour($break=4)
	{	$e=1;
		$str = "<tr>";
		for($i=0;$i<24;$i++)
		{	$str .="<td>".sprintf("%02d",$i)."</td>\n";
			if($e++ >= $break && $i<23)
			{	$e=1;
				$str .="</tr><tr>";
			}
		}
		$str .="</tr>";
		return($str);
	}

	function get_days()
	{	$show_days ="</tr>\n";
		$i = intval(date("w",mktime(1,0,0,$this->month,$this->day++,$this->year))+1);
		for($e=1;$e<$i;$e++) $show_days .="<td></td>";
		while(@checkdate($this->month,$this->day,$this->year))
		{	$show_days .="<td>".date("d",mktime(1,0,0,$this->month,$this->day++,$this->year))."</td>\n";
			if($i++>=7)
			{	$show_days .="</tr>\n<tr>\n";
				$i=1;
			}
		}
		if($i!=1)	$show_days .="</tr>";
		return($show_days);
	}
	
	
	function init_calendar($hour=0,$minute=0,$day=0,$month=1,$year=-1)
	{	if(isset($_REQUEST['hour'])) $this->hour=sprintf("%02d",intval($_REQUEST['hour']));
		else $this->hour=$hour;
		if(isset($_REQUEST['minute'])) $this->minute=sprintf("%02d",intval($_REQUEST['minute']));
		else $this->minute=$minute;
		if(isset($_REQUEST['day'])) $this->day=sprintf("%02d",intval($_REQUEST['day']));
		else $this->day=$day;
		if(isset($_REQUEST['month'])) $this->month=intval($_REQUEST['month']);
		else $this->month=$month;
		if(isset($_REQUEST['year'])) $this->year=intval($_REQUEST['year']);
		else $this->year=$year;
	
	
		/* here I Check if you have enter a valid day */
		if(!@checkdate($this->month,$this->day+1,$this->year))
		{	$this->year 	= 	intval(date("Y",time()));
			$this->month 	=	intval(date("m",time()));
			$this->hour 	=	"00";
			$this->minute 	=	"00";
			$this->day	=	0;
		}

			$this->show_day=$this->get_days();
			$this->show_help=$this->help_me();
			$this->show_month=$this->get_month();
			$this->show_year=$this->get_years();
			$this->show_minute=$this->get_minute();
			$this->show_hour=$this->get_hour();
			
	}

	/* this function is to create a unix timestamp from requested day */
	function get_unixtime($s="")
	{	$found="";
		if(preg_match("/([\d]{1,2}).*?([\d]{1,2}).*?([\d]{2,4})[^0-9\.]{1,}([\d]{1,2}).*?[\:]{1}.*?([\d]{1,2})/",$s,$found))
		{	if(intval($found[3])>=70)	return(@mktime($found[4],$found[5],0,$found[2],$found[1],$found[3]));
		}
		elseif(preg_match("/([\d]{1,2}).*?([\d]{1,2}).*?([\d]{2,4})/",$s,$found))
		{	if(intval($found[3])>=70)	return(@mktime(0,0,0,$found[2],$found[1],$found[3]));	
		}
		return(0);
	}

	/* this function is to create a timestamp for the SQL Database */
	function get_sqltimestamp($s="")
	{	$found="";
		if(preg_match("/([\d]{1,2}).*?([\d]{1,2}).*?([\d]{2,4})[^0-9\.]{1,}([\d]{1,2}).*?[\:]{1}.*?([\d]{1,2})/",$s,$found))
		{	return(sprintf("%02d",$found[3])."-".sprintf("%02d",$found[2])."-".$found[1]." ".sprintf("%02d",$found[4]).":".sprintf("%02d",$found[5]).":00");
		}
		elseif(preg_match("/([\d]{1,2}).*?([\d]{1,2}).*?([\d]{2,4})/",$s,$found))
		{	return(sprintf("%02d",$found[3])."-".sprintf("%02d",$found[2])."-".$found[1]." 00:00:00");
		}
		return(0);
	}

	function show_kalender()
	{	eval("\$str = \"".addslashes(file_get_contents("./bytecal.tpl"))."\";");	
		return(stripslashes($str));
	}
}


?>