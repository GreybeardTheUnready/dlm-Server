<?php
require_once ("dlmUtils.php");

$dbErrorMsg = "";
//error_log("[".$_SERVER['HTTP_HOST']."]");

    if ($_SERVER['HTTP_HOST'] == "localhost") {
        $db = "dlm";
        $dbUser = "root";
        $dbPW = "";
    }
    elseif (stripos($_SERVER['HTTP_HOST'], "45y") === false) {
        $db = "somerand_dlm";
        $dbUser = "somerand_dlm";
        $dbPW = "ARTyMNK90gdH";
    } else {
        $db = "ycouk_dlm";
        $dbUser = "ycouk_dlm";
        $dbPW = "S*?VpJR#Ip[*";
    }

    $dbconn = selectDB("localhost", $db, $dbUser, $dbPW);

	// Database Stuff
function selectDB($h, $d, $u, $p) { // Host, Database, Username, Password
	global $dbErrorMsg;
	$hconn = @mysql_connect($h, $u, $p);
	if (!$hconn) {
		error_log("Unable to connect to database '.$d.'\n" . mysql_error());
        $dbErrorMsg = '{"status":"DB_Error","msg":"Unable to connect to Database '.$d.'"}';
	} else if (!mysql_select_db($d, $hconn)) {
        error_log("Failed to access database '.$d.'\n" . mysql_error());
		$dbErrorMsg = '{"status":"DB_Error","msg":"Failed to access database '.$d.'"}';
	}
	return $hconn;
}

function dbQuery($conn, $q) {
	global $dbErrorMsg;	

    if (!$conn) { return false; }
	$r = mysql_query($q,$conn);
    $dbErrorMsg = "";      // Clear any previous error
	if (!$r) {
        error_log("Database query failed:\n" . $q . "\n" . mysql_error());
        $dbErrorMsg = '{"status":"DB_Error","msg":"Database request failed, please try again later.<br>If this problem persists please report to technical support."}';
	}
    return $r;
}
?>