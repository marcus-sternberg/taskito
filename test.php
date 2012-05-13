<?php

/* Include the class */
require_once("parser.class.php");

/* Load the file with the raw email content */
$handle = fopen("gmail.txt", "r");
while (!feof($handle)) {
    $gmail .= fread($handle, 1024);
}
fclose($handle);

/* Create a new instance of Parser */
$parse = new Parser($gmail);

/* Display the boundary used, as well as the header
   of the raw email and everything below it (content) */
echo "Boundary: " . $parse->boundary;
echo "<br><br><br>Header:<br><br><br>" . $parse->header;
echo "<br><br><br>Content:<br><br><br>" . $parse->content . "<br><br><br>";

/* Display the To, From, Subject, and plain text message
   that was parsed. An alternative html formatted message
   is availible if one was included in the raw email */
echo "To: " . $parse->to;
echo "<br>From: " . $parse->from;
echo "<br>Subject: " . $parse->subject;
echo "<br>Message: " . $parse->message['plain'] . "<br><br><br>";

/* Display information on any parsed attachments that
   may have been included with the raw email */
foreach ($parse->files as $file)
{
    echo "File Name: " . $file['name'];
    echo "<br>File Base Name: " . $file['base_name'];
    echo "<br>File Extension: " . $file['ext'];
    echo "<br>File Content: <br><br>" . $file['content'];
}

?>