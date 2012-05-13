var req;
var hau_id;
function tomajax_request()
{
     if (window.ActiveXObject) 
     { 
          try 
          { 
             // IE 6 and higher
             req = new ActiveXObject("MSXML2.XMLHTTP");
          } 
          catch (e) 
          {
              try 
              {
                  // IE 5
                  req = new ActiveXObject("Microsoft.XMLHTTP");
              }
              catch (e) 
              {
                  req=false;
              }
          }
      }
      else if (window.XMLHttpRequest) 
      {
          try 
          {
              // Mozilla, Opera, Safari ...
              req = new XMLHttpRequest();
          } 
          catch (e) 
          {
              req=false;
          }
      }
}

function task_gethappenings(debuggen,hau_id)
{
	if (debuggen == 1)
		alert("task_gethappenings: sending request: pagecalltime:"+pagecalltime);
	tomajax_request();
	req.open("GET", 'aufgabe_zugriffscheck.php?get_taskhappenings=1&hau_id='+hau_id+'&pagecalltime='+pagecalltime+'&rand='+Math.random(), true);
	req.onreadystatechange = function()
	{            
		if (debuggen == 1)
			alert("task_checkeditor - readystate: "+req.readyState+" req.status: "+req.status);
		// Auskommentiert wegen fehlender Kenntnis, warum der Fehler kommt
        // if (req.readyState == 4 && req.status != 200)
		  //  alert("task_checkeditor - Fehler:"+req.status);
		if (req.readyState == 4 && req.status == 200)
		{
			if (debuggen == 1)
				alert("task_checkeditor - Antwort: *"+req.responseText+"*");
			if (req.responseText.length > 3)
			{
				document.getElementById("taskhappenings").style.display="inline";
				document.getElementById("taskhappenings").innerHTML=req.responseText;
			}
			else
			{
				document.getElementById("taskhappenings").style.display="none";
			}
		}
	};
	req.send(null);  
	
}


function task_checkeditor(debuggen,hau_id)
{
	if (debuggen == 1)
		alert("task_checkeditor: sending request");
	tomajax_request();
	req.open("GET", 'aufgabe_zugriffscheck.php?edit_allowance=1&hau_id='+hau_id+'&rand='+Math.random(), true);
	req.onreadystatechange = function()
	{            
		if (debuggen == 1)
			alert("task_checkeditor - readystate: "+req.readyState+" req.status: "+req.status);
		if (req.readyState == 4 && req.status != 200)
		    alert("task_checkeditor - Fehler:"+req.status);
		if (req.readyState == 4 && req.status == 200)
		{
			if (debuggen == 1)
				alert("task_checkeditor - Antwort: *"+req.responseText+"*");
			eval(req.responseText);
            if (tmp_status == 0)
            {
            	if (tmp_user)
            		deny_pageaccess(tmp_user);
            	else
            		deny_pageaccess(0);
            }
            if (tmp_status == 1)
            {
            	setInterval ("task_sendeditor(0,hau_id)", 60000 );task_sendeditor(debuggen,hau_id);
            }
		}
	};
	req.send(null);  
}
function task_sendeditor(debuggen,received_hau_id)
{
	hau_id=received_hau_id;
	if (debuggen == 1)
		alert("task_sendeditor: sending request with hau_id: "+hau_id);
	tomajax_request();
	req.open("GET", 'aufgabe_zugriffscheck.php?current_user=1&hau_id='+hau_id+'&rand='+Math.random(), true);
	req.onreadystatechange = function()
	{            
		if (debuggen == 1)
			alert("task_sendeditor - readystate: "+req.readyState+" req.status: "+req.status);
		if (req.readyState == 4 && req.status != 200)
		    alert("task_sendeditor - Fehler:"+req.status);
		if (req.readyState == 4 && req.status == 200)
		{
			if (debuggen == 1)
				alert("task_sendeditor - Antwort: *"+req.responseText+"*");
            if (req.responseText == 0)
            {
            	deny_pageaccess();
            }
		}
	};
	req.send(null);  
}

function deny_pageaccess(tmp_user)
{
	if (tmp_user == 0)
		tmp_user='';
	document.getElementById('is24_content').innerHTML="<h3>Zugriff nicht m&ouml;glich</h3>Diese Aufgabe wird gerade von einem anderen User ("+tmp_user+") bearbeitet und kann daher von Dir nicht ge&ouml;ffnet werden.<br><br><input type='button' name='retry' value='erneut versuchen' class='formularbutton' style='cursor:hand;' onclick='window.location.reload();'/>&nbsp;<input type='button' name='retry' value='zur&uuml;ck' class='formularbutton' style='cursor:hand;' onclick='window.history.back();'/>";
}