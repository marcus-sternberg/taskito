<?php
###### Editnotes ####
#$LastChangedDate: 2011-09-20 16:56:44 +0200 (Di, 20 Sep 2011) $
#$Author: msternberg $ 
#####################

$teampage = 0;
$anzeige_zeichen = 'icon_quad_gruen.gif'; 

# define connection parameters for TOM

$rechnername="localhost";
$datenbankname="taskscout24";
$benutzername="root";
$passwort="Krenov";

#$rechnername="bersql03";
#$datenbankname="taskscout24";
#$benutzername="taskscout24";
#$passwort="taskscout24";

// Verbindung zum Host oeffnen    
    if(!$verbindung = mysql_connect($rechnername, $benutzername, $passwort)) 
          die ("Konnte keine Verbindung herstellen !</p>\n");
      
// Datenbank auswaehlen
    if (!(mysql_select_db($datenbankname, $verbindung)))
        fehler();

   mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', 
   character_set_database = 'utf8', character_set_server = 'utf8'", $verbindung);  
       
# Mailpath for the segment sending the mails

$mailpath = '';

function array_diff_ORG_NEW(&$org, &$new, $type='VALUES'){
    switch($type){
        case 'VALUES':
            $int = array_values(array_intersect($org, $new)); //C = A ^ B
            $org = array_values(array_diff($org, $int)); //A' = A - C
            $new= array_values(array_diff($new, $int)); //B' = B - C
            break;
        case 'KEYS':
            $int = array_values(array_intersect_key($org, $new)); //C = A ^ B
            $org = array_values(array_diff_key($org, $int)); //A' = A - C
            $new= array_values(array_diff_key($new, $int)); //B' = B - C
            break;
    }
}

function firstkw($jahr) {
$erster = mktime(0,0,0,1,1,$jahr);
$wtag = date('w',$erster);
if ($wtag <= 4) {
/**
* Donnerstag oder kleiner: auf den Montag zurueckrechnen.
*/
$montag = mktime(0,0,0,1,1-($wtag-1),$jahr);
} else {
/**
* auf den Montag nach vorne rechnen.
*/
$montag = mktime(0,0,0,1,1+(7-$wtag+1),$jahr);
}
return $montag;
}

function mondaykw($kw,$jahr) {
$firstmonday = firstkw($jahr);
$mon_monat = date('m',$firstmonday);
$mon_jahr = date('Y',$firstmonday);
$mon_tage = date('d',$firstmonday);
$tage = ($kw-1)*7;
$mondaykw = mktime(0,0,0,$mon_monat,$mon_tage+$tage,$mon_jahr);
return $mondaykw;
}

if(!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}

$heute = date("d.m.Y");   
$heute_usformat = date("Y-m-d"); 

function int2time($time) {
    $temp=$time;
    if(date("L")==1) {
        $schalt=366;
    } else {
        $schalt=365;
    }
    
    // Jahresberechnung
    $jahre=floor($temp/(60*60*24*$schalt));
    $temp=$temp-($jahre*60*60*24*$schalt);
    
    // Monate
    $monate=floor($temp/(60*60*24*30.5));
    $temp=$temp-($monate*60*60*24*30.5);
    $temp=round($temp);
    
    // Tage
    $tage=floor($temp/(60*60*24));
    $temp=$temp-($tage*60*60*24);
    
    // Stunden
    $stunden=floor($temp/(60*60));
    $temp=$temp-($stunden*60*60);
    
    // Minuten
    $minuten=floor($temp/60);
    $temp=$temp-($minuten*60);
    
    // Sekunden
    $sekunden=$temp;
    
    return array($jahre, $monate, $tage, $stunden, $minuten, $sekunden);
}

$Jahr = array("2009", "2010", "2011", "2012");
$WoTa = array ("mo" => "1", "tu"=>"2", "we"=>"3", "th"=>"4", "fr"=>"5");
$Stunden = array ("00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24");

// Fehler abfangen

function fehler()
{
	die("Fehler " . mysql_errno() . " : " . mysql_error());
}

function zeitstempel_anzeigen($var)
{
  
        if (preg_match('/-/', $var))
        {
            $fDatum = explode("-",substr($var,0,10)); 
            $fZeit = explode(":",substr($var,11,5));
            $fDatum = date("d.m.y H:i", mktime($fZeit[0], $fZeit[1], 0, $fDatum[1], $fDatum[2], $fDatum[0]));
            $var = $fDatum;
        }
    return $var;
}   
   

function datum_anzeigen($var)
{
//    echo $var;
    
    if($var=='9999-01-01' or $var=='01.01.9999')
    {
        $var='open';
    } else
    {
    
        if (preg_match('/-/', $var))
        {
            $fDatum = explode("-",substr($var,0,10)); 
            $fDatum = date("d.m.y", mktime(0, 0, 0, $fDatum[1], $fDatum[2], $fDatum[0]));
            $var = $fDatum;
        }
    }
    return $var;
}

function datum_wandeln_euus($var)
{
       if (preg_match('/./', $var))
        {
            $fDatum = explode(".",substr($var,0,10)); 
            $fDatum = date("Y-m-d", mktime(0, 0, 0, $fDatum[1], $fDatum[0], $fDatum[2]));
            $var = $fDatum;
        }
    return $var;
}

function datum_wandeln_useu($var)
{
        if (preg_match('/-/', $var))
        {
            $fDatum = explode("-",substr($var,0,10)); 
            $fDatum = date("d.m.Y", mktime(0, 0, 0, $fDatum[1], $fDatum[2], $fDatum[0]));
            $var = $fDatum;
        }
    return $var;
}




function my_strrpos($haystack, $needle) {
   $index = strpos(strrev($haystack), strrev($needle));
   if($index === false) {
        return false;
   }
   $index = strlen($haystack) - strlen($needle) - $index;
   return $index;
}



// Pruefe Datumseingabe
function pruefe_datum($var_eingabe)
{
        if ($var_eingabe=='open' or $var_eingabe=='') {$Datum='9999-01-01';}
        else
        {
        $Datum = explode(".",$var_eingabe); 
        if (checkdate($Datum[1],$Datum[0],$Datum[2])) 
        { 
        $Datum = date("Y-m-d", mktime(0, 0, 0, $Datum[1], $Datum[0], $Datum[2]));
        }
        }
        return $Datum;
        

}

// Pruefe Datumseingabe
function pruefe_datum1($var_eingabe)
{
        if ($var_eingabe=='open' or $var_eingabe=='') {$Datum='9999-01-01';}
        else{
        $Datum = explode(".",$var_eingabe); 
        if (checkdate($Datum[1],$Datum[0],$Datum[2])) 
        { 
        $Datum = date("Y-m-d", mktime(0, 0, 0, $Datum[1], $Datum[0], $Datum[2]));
        }
        }
        return $Datum;
        

}


// Pruefe Datumseingabe
function datum_check($var_eingabe, $xIndex, $anzahl_fehler)
{

    
    if ($var_eingabe=='open' or $var_eingabe=='' or $var_eingabe == '9999-01-01') {$var_eingabe='01.01.9999';} 

    $regex = '/^\d{1}\.\d{1}\.(\d{2}){1,2}$/';
    if (preg_match($regex,$var_eingabe)!=0)
    {
        $var_eingabe = '0'.substr($var_eingabe,0,2).'0'.substr($var_eingabe,2);
    } 


    
    $regex = '/^\d{1}\.\d{2}\.(\d{2}){1,2}$/';      
    if (preg_match($regex,$var_eingabe)!=0)
    {
        $var_eingabe = '0'.substr($var_eingabe,0);
    }  


    $regex = '/^\d{2}\.\d{1}\.(\d{2}){1,2}$/';      
    if (preg_match($regex,$var_eingabe)!=0)
    {
        $var_eingabe = substr($var_eingabe,0,3).'0'.substr($var_eingabe,3);
    } 
    
 
    $regex = '/^\d{2}\.\d{2}\.(\d{4})$/';       
    if (preg_match($regex,$var_eingabe)!=0)
    {
        $var_eingabe = substr($var_eingabe,0,6).substr($var_eingabe,8,2);
    }   
 
    $regex = '/^\d{2}\.\d{2}\.(\d{2})$/';
    if (preg_match($regex,$var_eingabe)!=0)
    {

        $Datum = explode(".",$var_eingabe); 

        if (checkdate($Datum[1],$Datum[0],$Datum[2]) OR $var_eingabe=='01.01.99') 
        { 

        $Datum = date("y-m-d", mktime(0, 0, 0, $Datum[1], $Datum[0], $Datum[2]));

        } else {               

            $fehlermeldung = 
        "This Date is not valid - please use (tt.mm.jj)!";
          $anzahl_fehler++;
          $fehlerfeld = array($anzahl_fehler,$fehlermeldung);
          return $fehlerfeld;
      }
    } else {
         $fehlermeldung = 
        "This Date is not valid - please use (tt.mm.jj)!";
          $anzahl_fehler++;
          $fehlerfeld = array($anzahl_fehler,$fehlermeldung);
          return $fehlerfeld;
    } 
          $fehlerfeld = array($anzahl_fehler,'');
      return $fehlerfeld;
}

?>
