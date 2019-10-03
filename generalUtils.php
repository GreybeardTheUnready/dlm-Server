<?php
/******************************************************************************
 * datetimeStrToDate(s)
 * Convert Date Time string to simple SQL Date
 * 
 * @param {String} s
 */
function datetimeStrToDate($s) {
    $timestamp = strtotime($s);
    return date("Y-m-d", $timestamp);
}

function buildDropdown($name, $qry) {
	global $dbconn;
	// Builds HTML for a SELECT ($name) from a database query ($qry)
	// The query ($qry) should return two values, the first for the 'value' of the SELECT option
	// the second for the 'prompt' of the SELETC option.  
	$result = dbQuery($dbconn, $qry);
	$opt = "<select name='" . $name . "' id='" . $name . "'>";
	while ($row = mysql_fetch_array($result)) {
		$opt .= "<option value='" . $row[0] . "'>" . $row[1] . "</option>";
	}
	$opt .= "</select>";
	return $opt;
}

function getGETorPOST($arg) {  // Basically the same as $_REQUEST but doesn't include $_COOKIE

	$value = NULL;
	if (isset($_GET[$arg])) {
		if ($_GET[$arg] != "") { $value = $_GET[$arg]; }
	} elseif (isset($_POST[$arg])) {
		if ($_POST[$arg] != "") { $value = $_GET[$arg]; }
	}
	return $value;
}

function displayFile($fname) {
	$site =  array_shift(explode(".",$_SERVER['HTTP_HOST']));
	$target = $site .DIRECTORY_SEPARATOR . $fname;

	echo file_get_contents($target);
}


function sqlStr($instr) {	//  Escape unsafe characters for a safe SQL string
   return mysql_real_escape_string($instr);
}

function valueExists($table, $field, $value) {
global $dbconn;
	$qry = "SELECT id FROM " . $table . " WHERE " . $field . " = '" . $value . "'";
	$result = dbQuery($dbconn, $qry);
	return (mysql_num_rows($result));
} 

function lastFileUpdate() {
	$timestamp = filemtime(__FILE__);
	return (date('Y-m-d H:i:s', $timestamp));
}

/******************************************************************************
 * sendEmail - general purpose 'Send Email' function
 *
 * @param {string} to -  Addressee
 * @param {string} from - Sender
 * @param {string} subject - Subject line of email
 * @param {string} msg - mail message content
 * @returns {boolean} TRUE or FALSE
 */
//function sendEmail($to, $from, $subject, $msg) {
//    $to = str_replace("%40","@",$to);
////error_log("sendemail " . $to . " " . $msg);
//	$headers = "From: $from \r\n";
//	$headers .= "Reply-To: $from \r\n";
//	$headers .= "Return-Path: $from\r\n";
//	$headers .= "X-Mailer: PHP \r\n";
//	if (mail($to,$subject,$msg,$headers)) {
//        return '{"status":"OK", "msg":"Email Sent"}';
//    } else {
//        return '{"status":"Failed", "msg":"Email Failed"}';
//    }
//}
require_once 'phpMailer/PHPMailerAutoload.php';

function sendEmail($to, $from, $subject, $msg, $attachment) {
    $email = new PHPMailer();
    $email->From      = $from;
    $email->Subject   = $subject;
    $email->Body      = $msg;
    $email->AddAddress( $to );

    if ($attachment) {
        $email->AddAttachment( $attachment );
    }

    return $email->Send();
}


/******************************************************************************
 * sendSMS - general purpose 'Send SMS' function
 *
 * @param {string} to -  Addressee
 * @param {string} from - Sender
 * @param {string} subject - Subject line of email
 * @param {string} msg - mail message content
 * @returns {boolean} TRUE or FALSE
 */
require('includes/textlocal.class.php');

function sendSMS($to, $from, $subject, $msg) {
    $textlocal = new Textlocal('jonbmail@gmail.com', '57caa39c5a3490f8dbd43b24347c0f1714c97078');

    $msisdn = preg_replace('/\+/', '', $to); 
    $msisdn = preg_replace("/ /","", $msisdn);
    if (substr($msisdn, 0, 1) == "0") { $msisdn = substr($msisdn,1); };
    if (substr($msisdn, 0, 2) != "44") { $msisdn = "44" . $msisdn; }
    if ((substr($msisdn,2,1) != "7") || (strlen($msisdn) <> 12)) {
        error_log("Bad number for SMS (" . $msisdn . ") (" . $from . ") (" . $subject . ") (" . $msg . ")");
		return '{"status":"Not Sent", "msg":"SMS Error: Invalid Mobile Number (' . $msisdn . ')"}';
    };
    $numbers = array($msisdn);  
    $sender = $from;
    $message = $subject . "\n" . $msg;

    try {
        $result = $textlocal->sendSms($numbers, $message, $sender);
        error_log("SMS Sent (" . $msisdn . ") ( " . $from . ") (" . $subject . ") (" . $msg . ")");
        return '{"status":"OK", "msg":"SMS Sent"}';
    } catch (Exception $e) {
        error_log("SMS Failed (" . $msisdn . ") ( " . $from . ") (" . $subject . ") (" . $msg . ")");
        return '{"status":"Failed", "msg":"SMS Error: ' . $e->getMessage() . '"}';
    }
}

/******************************************************************************
 * getFileTimestamp - general purpose function to get date/time stamp of file
 *
 * @param   {string} fspec - File specification relative to Webroot
 * @returns {string} timestamp
 */
function getFileTimestamp($fspec) {
    return date("Y-m-d H:i:s", filemtime($fspec));
}
?>