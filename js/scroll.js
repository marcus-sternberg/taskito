/***********************************************
* IFRAME Scroller script- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

//Specify speed of scroll. Larger=faster (ie: 5)
var scrollspeed=cache=1

//Specify intial delay before scroller starts scrolling (in miliseconds):
var initialdelay=500

function initializeScroller(){
dataobj=document.all? document.all.datacontainer : document.getElementById("datacontainer")
dataobj.style.top="400px"
setTimeout("getdataheight()", initialdelay)
}

function getdataheight(){
thelength=dataobj.offsetHeight
if (thelength==0)
setTimeout("getdataheight()",10)
else
scrollDiv()
}

function scrollDiv(){
dataobj.style.top=parseInt(dataobj.style.top)-scrollspeed+"px"
if (parseInt(dataobj.style.top)<thelength*(-1))
dataobj.style.top="400px"
setTimeout("scrollDiv()",40)
}

if (window.addEventListener)
window.addEventListener("load", initializeScroller, false)
else if (window.attachEvent)
window.attachEvent("onload", initializeScroller)
else
window.onload=initializeScroller
