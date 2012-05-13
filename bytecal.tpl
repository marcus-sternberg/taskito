<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
<meta name="DC.Creator" content="Andreas Petermann" />
<meta name="DC.Rights" content="Andreas Petermann / ByteNation.de" />
<meta name="DC.Language" content="de" />
<meta http-equiv="cache-control" content="no-cache" />
<title>ByteCal {$this->VER}</title>
<style type='text/css'>
body
{	background-color:#ced7d6;
}

*
{	padding:0px;
	margin:0px;
}

#box
{	width:200px;
	height:194px;
	border:2px outset #ced7d6;
	border-width:1px 2px 2px 1px;
	background-color:#ced7d6;
	margin:auto;
	overflow:hidden;
}

#table_box
{	width:100%;
	height:100%;
	overflow:hidden;
}
#table_box td
{	vertical-align:top;
}

#first_button_box
{	width:100%;
	height:40px;
	font-weight:bold;	
	color:#779D98;
}



#last_button_box
{	width:100%;
	height:20px;
	font-weight:bold;	
	color:#779D98;
}

#first_button_box .minute, #first_button_box .hour, #first_button_box .point 
{	text-align:center;
	border:2px outset #ffffff;
	vertical-align:middle;
}

#first_button_box .first_cell,#last_button_box .first_cell
{	text-align:center;
	border:2px outset #ffffff;
	vertical-align:middle;
	width:50%;
}


#day_box td.head_line
{	border-bottom:1px solid #232323;
	font-weight:bold;
	cursor:auto;
}


#inhaltsbox
{	height:120px;
	overflow:hidden;
	cursor:pointer;
}

#year_box, #month_box, #hour_box, #minute_box,#day_box,#help_box
{	width:98.5%; 
	height:100%;
	font-size:12px; 
	text-align:center;
	background-color:#e8e9eb;
	border:2px outset #ced7d6;
	border-width:0px 1px 0px 2px;
	display:none;
}

#year_box table, #month_box table, #hour_box table, #minute_box table,#day_box table
{	width:100%;
	height:100%;
}

#year_box td,#month_box td,#hour_box td,#minute_box td
{
	font-weight:normal;
	vertical-align:middle;
	border:1px outset #ced7d6;
	border:1px solid #cecece;
		border-width:1px 1px 0px 0px;
	cursor:pointer;
}

#help_box td
{	text-align:left;
	font-size:13px;	
	padding:3px;
}

.my_pointer
{	cursor:pointer;
}
</style>

</head>
<body>
<div id='box'>
	<table id='table_box' cellpadding='0' cellspacing='0'> 
		<tr>
			<td id='headtable'>
			 <table id='first_button_box' cellpadding='0' cellspacing='0'>
		 	<tr><td class='first_cell' id='Uhrzeit'>Time</td><td id='open_hour' class='hour my_pointer'>{$this->hour}</td><td class='point' id='no_want'>:</td><td id='open_minute' class='minute my_pointer'>{$this->minute}</td></tr>
		 	<tr><td class='first_cell my_pointer' id='open_month' >{$this->my_month[$this->month]}</td><td id='open_year' class='first_cell my_pointer' colspan='3'>{$this->year}</td></tr>
			 </table>
			</td>
		</tr> 
		<tr>

			<td id='inhaltsbox'>
<!-- hier drinnen sind die einzelnen boxen verpackt -->

				<div id='day_box'  style='display:block;'>
					<table  cellpadding='0' cellspacing='0'>
						<tr>	
							<td class='head_line' id='day_headline'>Mo</td><td class='head_line'>Tu</td><td class='head_line'>We</td><td class='head_line'>Th</td><td class='head_line'>Fr</td><td class='head_line'>Sa</td><td class='head_line'>Su</td>
						</tr>
						{$this->show_day}
					</table>
				</div>

				<div id='year_box'>
					<table cellpadding='0' cellspacing='0'>
					{$this->show_year}
					</table>
				</div>

				<div id='month_box'>
					<table cellpadding='0' cellspacing='0'>
						{$this->show_month}
					</table>
				</div>

				<div id='minute_box'>
					<table cellpadding='0' cellspacing='0'>
						{$this->show_minute}
					</table>
				</div>

				<div id='hour_box'>
					<table cellpadding='0' cellspacing='0'  >
						{$this->show_hour}
					</table>
				</div>
				
				<div id='help_box'>
					<table cellpadding='0' cellspacing='0'  >
						<tr><td>	{$this->show_help} </td></tr>
					</table>
				</div>

			</td>
<!-- hier drinnen sind die einzelnen boxen verpackt -->

		</tr>
		<tr>
			<td id='bottomtable'>
			 <table id='last_button_box' cellpadding='0' cellspacing='0'>
			 	<tr><td class='first_cell my_pointer' id='open_help'>Hilfe</td><td class='first_cell my_pointer' id='open_close'>close</td></tr>
			 </table>
			</td>
		</tr> 
</table>
</div>
<script type='text/javascript'>
var bn_i=0;
// var bn_t ="";
var open_now='day_box';
var my_opener = "";

var my_month = new Array();

my_month['January']="01";
my_month['February']="02";
my_month['March']="03";
my_month['April']="04";
my_month['Mai']="05";
my_month['June']="06";
my_month['July']="07";
my_month['August']="08";
my_month['September']="09";
my_month['October']="10";
my_month['November']="11";
my_month['December']="12";

var hour,minute,day,year,month;

no_focus();

function no_focus()
{	var s=document.getElementsByTagName('td');
	var search = /open\_/;
	while(s[bn_i])
	{	if(search.test(s[bn_i].id)) s[bn_i].onclick=new Function("open_box(this.id)");
		if(!s[bn_i].id)		s[bn_i].onclick=new Function("make_it(this.innerHTML); return false");
		bn_i++;
	}
}

function open_box(s)
{ document.getElementById(open_now).style.display='none';
	my_opener=s;
	switch(s)
  {	case 'open_hour': open_now='hour_box'; break;
		case 'open_minute': open_now='minute_box'; break;
		case 'open_month': open_now='month_box'; break;
		case 'open_year': open_now='year_box'; break;
		case 'open_help': open_now='help_box'; break;
		case 'open_day'	: 
		case 'open_close' :	my_opener="";
												if(top.bn_who) top.document.getElementById('bn_frame').style.display='none';
	}	
	document.getElementById(open_now).style.display='block';
}

function make_it(s)
{	
	if(my_opener && my_opener!='open_help' && my_opener!='open_close' ) document.getElementById(my_opener).innerHTML=s;

	if(document.getElementById('open_hour')) var hour=document.getElementById('open_hour').innerHTML;
	else var hour="00";
	if(document.getElementById('open_minute')) var minute=document.getElementById('open_minute').innerHTML;
	else var minute="00";
	var year=document.getElementById('open_year').innerHTML;
	var month=document.getElementById('open_month').innerHTML;

	if(open_now == 'day_box' && top.bn_who ) 
	{		var day=s;
			if(hour!='00' || minute!='00')	top.document.getElementById(top.bn_who.id).value=day + "." + my_month[month] + "." + year + "/" + hour + ":" + minute;
			else	top.document.getElementById(top.bn_who.id).value=day + "." + my_month[month] + "." + year;
			top.document.getElementById('bn_frame').style.display='none';
			return;
	}
	if(my_opener=='open_year' || my_opener=='open_month')
	{ 		document.location.href="./bytecal.php?hour=" + hour + "&minute=" + minute + "&year=" + year + "&month=" + my_month[month];
	}
	else
	{	document.getElementById(open_now).style.display='none';
		document.getElementById('day_box').style.display='block';
		open_now='day_box';
		my_opener="";
	}
}

</script>
</body>
</html>

