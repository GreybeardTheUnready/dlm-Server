<?php
// EMail error log
require_once ("generalUtils.php");

$ErrorLog = "";
$EmailAddress = "jonbmail@gmail.com";

// First we get hold of the Host name and App Root.
$Hostname = $_SERVER['SERVER_NAME'];

$AppHost = $_SERVER['REQUEST_URI'];
$AppHost = substr($AppHost,1,strlen($AppHost)-16);

echo $Hostname . "<br>";
echo $AppHost . "<br>";
 
if ($Hostname == "45y.co.uk") {
    $ErrorLog = "php_errorlog";
} else {
    $ErrorLog = "error_log";
}

$message = file_get_contents($ErrorLog);
echo $message . "<br>";
$headers = 'From: webmaster@' . $Hostname . '\r\n' .
    'Reply-To: webmaster@' . $Hostname . '\r\n' .
    'X-Mailer: PHP/' . phpversion();

if (!sendEmail($EmailAddress, "ycouk@45y.co.uk", $AppHost . " Error Log", "Today's Error Log from " . $Hostname . " " . $AppHost, $ErrorLog)) {
//if (!mail($EmailAddress, $AppHost . " Error Log", $message, $headers)) {
    error_log("Failed to send " . $AppHost . " Error Log email");
} else {
    error_log ("Email sent to " . $EmailAddress . "(" . $AppHost . " Error Log)");
}
?>