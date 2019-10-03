<?php
/*
 * Utility functions for Roger.
 */
require_once ("db.php");
require_once ("generalUtils.php");

if ($dbErrorMsg != "") {
	echo $dbErrorMsg;
	die();
}

function login() {
global $dbconn, $dbErrorMsg;
	$qry = "SELECT * FROM users WHERE name = '" . $_POST['username'] . "'";
	$result = dbQuery($dbconn, $qry);
    if (!$result) { return ($dbErrorMsg); }
	$row = mysql_fetch_array($result);
	if ($row['status'] == 0) {
		$json = '{"username":"' . $_POST['username'] . '", "userType": "newpassword"}';
	} elseif ($row['password'] == $_POST['password']) {
		$json = '{"userId":"' . $row['id'] . '", "username":"' . $_POST['username'] . '", "userType": "' . $row['type'] . '", "ip":"' . $_SERVER['REMOTE_ADDR'] . '"}';
		$qry = "UPDATE users SET consoleLoginCount=consoleLoginCount+1, lastConsoleLogin='" . date('Y-m-d H:i:s') ."'  WHERE users.name = '" . $_POST['username'] ."'";
		$result = dbQuery($dbconn, $qry);
        if (!$result) { return ($dbErrorMsg); }
	} else {
		$json = '{"username":"-", "type": "none"}';
	}
	return $json;
}

function updatePassword($u,$pw) {
global $dbconn, $dbErrorMsg;
		$qry = "SELECT id FROM users WHERE name = '" . $u . "'";
		$result = dbQuery($dbconn, $qry);
        if (!$result) { return ($dbErrorMsg); }
		if (mysql_num_rows($result) == 1) {	//set status 'Pending' and tell someone
			$row = mysql_fetch_array($result);
			$qry = "UPDATE users SET password = '" . $pw . "', status=0 WHERE id = " . $row['id'];
			$result = dbQuery($dbconn, $qry);
            if (!$result) { return ($dbErrorMsg); }
			$json = '{"status":"OK"}';
		} else {
			$json = '{"status":"Unrecognised user."}';
		}
		echo $json;
}

/******************************************************************************
 * SITES functions
 ******************************************************************************/
function getSitesList() {
global $dbconn, $dbErrorMsg;

	$qry = "SELECT * FROM sites ORDER BY name";
	$result = dbQuery($dbconn, $qry);
    if (!$result) { return ($dbErrorMsg); }
	$json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
	while ($row = mysql_fetch_assoc($result)) {
		$json .= '{';
			$qry = "SELECT * FROM boats WHERE siteId = " . $row['id'];
			$res1 = dbQuery($dbconn, $qry);
            if (!$res1) { return ($dbErrorMsg); }
            $bcount = mysql_num_rows($res1);
			$json .= '"id":"' . $row['id'] . '",';
			$json .= '"name":"' . $row['name'] . '",';
			$json .= '"contact":"' . $row['contact'] . '",';
			$json .= '"phone":"' . $row['phone1'] . '",';
			$json .= '"email":"<a href=\'mailto:' . $row['email'] . '\'>' . $row['email'] . '",';
			$json .= '"website":"<a target=\'_blank\' href=\'http://' . $row['website'] . '\'>' . $row['website'] . '</a>' . '",';
			$json .= '"map":"<a target=\'_blank\' href=\'http://google.co.uk/maps?q=' . $row['address'] . '\'><img src=\'images/map24x24.png\'></a>' . '",';
            if ($bcount > 0) {
                $json .= '"boats":"<a href=\'boats.php?s=' . $row['id'] . '\'>' . $bcount . '</a>' . '"';
            } else {
                $json .= '"boats":"' . $bcount . '"';
            }
		$json .= '},';
	}
	if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
	$json .= ']}';
	return $json;
}

function getSiteDetails($sid) {
global $dbconn,  $dbErrorMsg;

	$qry = "SELECT sites.* FROM sites ";
    $qry .= "WHERE sites.id = " . $sid;

	$result = dbQuery($dbconn, $qry);
    if (!$result) { return ($dbErrorMsg); }
	if (mysql_num_rows($result) == 0) {
		$json = '{"resultcount":"0"}';
	} else {
		$row = mysql_fetch_assoc($result);
		$json = '{"resultcount":"' . mysql_num_rows($result)  . '", ';
		$json .= '"id":"' . $row['id'] . '", ';
		$json .= '"name":"' . $row['name'] . '", ';
		$json .= '"address":"' . $row['address'] . '", ';
		$json .= '"contact":"' . $row['contact'] . '", ';
		$json .= '"phone1":"' . $row['phone1'] . '", ';
		$json .= '"phone2":"' . $row['phone2'] . '", ';
		$json .= '"email":"' . $row['email'] . '", ';
		$json .= '"website":"' . $row['website'] . '", ';
		$json .= '"notes":"' . $row['notes'] . '", ';
		$json .= '"created":"' . $row['created'] . '", ';
		$json .= '"updated":"' . $row['updated'] . '", ';
		$json .= '"updatedBy":"' . $row['updatedBy'] . '"}';
	}
	return $json;
}

function updateSite($id) {
global $dbconn,  $dbErrorMsg;
		$qry = "UPDATE sites SET ";
		$qry .= "address = '" . $_POST['sa'] . "', ";
		$qry .= "contact = '" . $_POST['sc'] . "', ";
		$qry .= "phone1 = '" . $_POST['sp1'] . "', ";
		$qry .= "phone2 = '" . $_POST['sp2'] . "', ";
		$qry .= "email = '" . $_POST['se'] . "', ";
		$qry .= "website = '" . $_POST['sw'] . "', ";
		$qry .= "notes = '" . $_POST['sn'] . "', ";
        $qry .= "updated = '" . date("Y-m-d h:i") . "', ";
        $qry .= "updatedBy = '" . $_POST['su'] . "' ";
		$qry .= "WHERE id = " . $id;
		$result = dbQuery($dbconn, $qry);
        if (!$result) { return ($dbErrorMsg); }
		return ('{"status":"OK","msg":"Site information updated."}');
}

function addSite() {
global $dbconn,  $dbErrorMsg;
	// First Check that this really is a new building...
	$qry = "SELECT id from sites WHERE name = '" . $_POST['sname'] . "'";
	$result = dbQuery($dbconn, $qry);
    if (!$result) { return ($dbErrorMsg); }
	if (mysql_num_rows($result) > 0) {
		$json .= '{"status":"Failed", "msg":"A site with this name is already registered!  (No action taken)"}';
	} else {
		$qry = "INSERT INTO sites (name, address, contact, phone1, phone2, email, website, notes, updatedBy)";
		$qry .= " VALUES ('" . $_POST['sname'] . "', '" . $_POST['sa'] . "','" . $_POST['sc'] . "','" . $_POST['sp1'] . "','" . $_POST['sp2'] . "','" . $_POST['se'] . "', '" . $_POST['sw'] . "', '" . $_POST['sn'] . "', '" . $_POST['su'] . "')";
		if (dbQuery($dbconn, $qry)) {
			$json = '{"status":"OK", "msg":"Site added to database"}';
		} else {
			$json = $dbErrorMsg;
		}
	};
	echo $json;
}

function deleteSite($sid) {
global $dbconn,  $dbErrorMsg;
	$qry = "DELETE FROM sites WHERE id = " . $sid ;
	if (!dbQuery($dbconn, $qry)) return ($dbErrorMsg);
	$qry = "SELECT id FROM boats WHERE siteId = " . $sid;
	if (!dbQuery($dbconn, $qry)) return ($dbErrorMsg);
	$qry = "DELETE FROM boats WHERE siteId = " . $sid;
	if (!dbQuery($dbconn, $qry)) return ($dbErrorMsg);
	return ('{"status":"OK"}');
}

function getSitesDropdown() {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT * FROM sites ORDER BY name";
    $result = dbQuery($dbconn, $qry);
    if (!$result) {return ($dbErrorMsg);}
    $json = '{"options":[';
    while ($row = mysql_fetch_assoc($result)) {
        $json .= '{';
        $json .= '"id":"' . $row['id'] . '", ';
        $json .= '"name":"' . $row['name'] . '"';
        $json .= '}, ';
    }
    if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
    $json .= ']}';
    return $json;
}

/******************************************************************************
 ************************ End of SITES functions ******************************
 ******************************************************************************/

/******************************************************************************
 * CUSTOMERS functions
 ******************************************************************************/
function getCustomerList($s=0) {
global $dbconn,  $dbErrorMsg;

    $qry = "SELECT customers.*, sites.name as siteName FROM customers, sites ";
    $qry .= "WHERE sites.id = customers.siteId ";
    if ($s > 0) {
        $qry .= " AND customers.siteId = " . $s . " ";
    }
    $qry .= "ORDER BY lastname";
	$result = dbQuery($dbconn, $qry);
    if (!$result) {return ($dbErrorMsg);}
	$json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
	while ($row = mysql_fetch_assoc($result)) {
		$json .= '{';
			$qry = "SELECT id as boatId, name as boatName FROM boats WHERE customerId = " . $row['id'];
			$res1 = dbQuery($dbconn, $qry);
            if (!$res1) {return ($dbErrorMsg);}
            if (mysql_num_rows($res1) < 1) {
                $boatId = 0;
                $boatName = "Unknown";
            } else {
                $row1 = mysql_fetch_assoc($res1);
                $boatId = $row1['boatId'];
                $boatName = $row1['boatName'];
            }
			$json .= '"id":"' . $row['id'] . '",';
            $json .= '"title":"' . $row['title'] . '",';
            $json .= '"firstname":"' . $row['firstname'] . '",';
            $json .= '"lastname":"' . $row['lastname'] . '",';
			$json .= '"name":"",';       // Blank to be filled by client (customers.php)
			$json .= '"address":"' . $row['address1'] .', '. $row['address2'] .', '. $row['address3'] .' ' . $row['postcode'] . '",';
			$json .= '"phone":"' . $row['phone'] . '",';
			$json .= '"email":"<a href=\'mailto:' . $row['email'] . '\'>' . $row['email'] . '",';
            $json .= '"site":"' . $row['siteName'] . '",';
            $json .= '"boatId":"' . $boatId . '",';
            $json .= '"boat":"' . $boatName . '"';
		$json .= '},';
	}
	if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
	$json .= ']}';
//error_log($json);
	return $json;
}

function getCustomerNames() {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT id, CONCAT(title, ' ', firstName, ' ', lastName) as name FROM customers ORDER BY lastname";
    $result = dbQuery($dbconn, $qry);
    if (!$result) {return ($dbErrorMsg);}
    $json = '{"options":[';
    while ($row = mysql_fetch_assoc($result)) {
        $json .= '{';
        $json .= '"id":"' . $row['id'] . '", ';
        $json .= '"name":"' . $row['name'] . '"';
        $json .= '}, ';
    }
    if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
    $json .= ']}';
    return $json;
}

function getCustomerDetails($id) {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT * FROM customers WHERE id = " . $id;
    $result = dbQuery($dbconn, $qry);
    if (!$result) {return ($dbErrorMsg);}
	if (mysql_num_rows($result) == 0) {
		$json = '{"resultcount":"0"}';
	} else {
		$row = mysql_fetch_assoc($result);
		$json = '{"resultcount":"' . mysql_num_rows($result)  . '", ';
		$json .= '"id":"' . $row['id'] . '", ';
		$json .= '"title":"' . $row['title'] . '", ';
		$json .= '"firstname":"' . $row['firstname'] . '", ';
		$json .= '"lastname":"' . $row['lastname'] . '", ';
		$json .= '"address1":"' . $row['address1'] . '", ';
		$json .= '"address2":"' . $row['address2'] . '", ';
		$json .= '"address3":"' . $row['address3'] . '", ';
		$json .= '"county":"' . $row['county'] . '", ';
		$json .= '"postcode":"' . $row['postcode'] . '", ';
		$json .= '"phone":"' . $row['phone'] . '", ';
		$json .= '"email":"' . $row['email'] . '", ';
		$json .= '"notes":"' . $row['notes'] . '", ';
        $qry = "SELECT boats.id as boatId, boats.name as boatName, sites.id as siteId, sites.name as siteName ";
        $qry .= "FROM boats, sites ";
        $qry .= "WHERE customerId = " . $id . " ";
        $qry .= "AND sites.id = boats.siteId";
//error_log($qry);
        $res1 = dbQuery($dbconn, $qry);
        if (!$res1) {return ($dbErrorMsg);}
        if (mysql_num_rows($res1) > 0) {
            $row1 = mysql_fetch_assoc($res1);
            $json .= '"boatId":"' . $row1['boatId'] . '", ';
            $json .= '"boatName":"' . $row1['boatName'] . '", ';
            $json .= '"siteId":"' . $row1['siteId'] . '", ';
            $json .= '"siteName":"' . $row1['siteName'] . '", ';
        } else {
            $json .= '"boatId":"0", ';
            $json .= '"boatName":"unknown", ';
            $json .= '"siteId":"0", ';
            $json .= '"siteName":"unknown", ';
        }
		$json .= '"created":"' . $row['created'] . '", ';
		$json .= '"updated":"' . $row['updated'] . '", ';
		$json .= '"updatedBy":"' . $row['updatedBy'] . '"}';
	}
//error_log($json);
	return $json;
}

function getCustomerAddresses($siteId) {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT concat(title, ' ', firstname, ' ', lastname) as name, ";
    $qry .= "address1, address2, address3, county, postcode, customers.email as email ";
    if ($siteId > -1) {
        $qry .= ", sites.name as site ";
        $qry .= "FROM customers, sites ";
        $qry .= "WHERE sites.id = " . $siteId . " ";
        $qry .= "AND customers.siteId = sites.id ";
        $qry .= "ORDER BY site, lastname";
    } else {
        $qry .= "FROM customers ";
        $qry .= "ORDER BY lastname";
    }
    
    $result = dbQuery($dbconn, $qry);
    if (!$result) {return ($dbErrorMsg);}
    $addressCSV = "Name;Address1;Address2;Address3;County;Postcode;Phone;Email\n";
    while ($row = mysql_fetch_assoc($result)) {
        $addressCSV .= urldecode($row['name']) . ";";
        $addressCSV .= urldecode($row['address1']) . ";"; 
        $addressCSV .= urldecode($row['address2']) . ";"; 
        $addressCSV .= urldecode($row['address3']) . ";";
        $addressCSV .= urldecode($row['county']) . ";"; 
        $addressCSV .= urldecode($row['postcode']) . ";"; 
        $addressCSV .= urldecode($row['phone']) . ";"; 
        $addressCSV .= urldecode($row['email']) . "\n"; 
    }
    
    $fp = fopen("addresses.csv","w");
    fwrite($fp,$addressCSV);
    fclose($fp);
    
    return ('{"status":"OK"}');
}

function updateCustomer($id) {
global $dbconn,  $dbErrorMsg;
		$qry = "UPDATE customers SET ";
		$qry .= "title = '" . $_POST['ct'] . "', ";
		$qry .= "firstname = '" . $_POST['cf'] . "', ";
		$qry .= "lastname = '" . $_POST['cl'] . "', ";
		$qry .= "address1 = '" . $_POST['ca1'] . "', ";
		$qry .= "address2 = '" . $_POST['ca2'] . "', ";
		$qry .= "address3 = '" . $_POST['ca3'] . "', ";
		$qry .= "county = '" . $_POST['cc'] . "', ";
		$qry .= "postcode = '" . $_POST['cpc'] . "', ";
		$qry .= "phone = '" . $_POST['cph'] . "', ";
		$qry .= "email = '" . $_POST['ce'] . "', ";
		$qry .= "notes = '" . $_POST['cn'] . "', ";
        $qry .= "updated = '" . date("Y-m-d h:i") . "', ";
        $qry .= "updatedBy = '" . $_POST['cu'] . "' ";
		$qry .= "WHERE id = " . $id;
		$result = dbQuery($dbconn, $qry);
        if (!$result) {
            return ($dbErrorMsg);
        } else {
            return ('{"status":"OK","msg":"Customer information updated."}');
        }
}

function addCustomer() {
global $dbconn,  $dbErrorMsg;
	// First Check that this really is a new customer...
	$qry = "SELECT * from customers WHERE firstname = '" . $_POST['cf'] . "' AND lastname = '" . $_POST['cl'] . "' AND postcode = '" . $_POST['cpc'] . "'";
	$result = dbQuery($dbconn, $qry);
    if (!$result) {return ($dbErrorMsg);}
	if (mysql_num_rows($result) > 0) {
		$json .= '{"status":"Failed", "msg":"A customer with this name and Postcode is already registered!  (No action taken)"}';
	} else {
		$qry = "INSERT INTO customers (title, firstname, lastname, address1, address2, address3, county, postcode, phone, email, notes, updatedBy)";
		$qry .= " VALUES ('" . $_POST['ct'] . "', '" . $_POST['cf'] . "','" . $_POST['cl'] . "','" . $_POST['ca1'] . "','" . $_POST['ca2'] . "','" . $_POST['ca3'] . "','" . $_POST['cc'] . "','" . $_POST['cpc'] . "', '" . $_POST['cph'] . "', '" . $_POST['ce'] . "', '" . $_POST['cn'] . "', '" . $_POST['cu'] . "')";
		if (dbQuery($dbconn, $qry)) {
            $id = mysql_insert_id();
			$json = '{"status":"OK", "msg":"Customer added to database.", "id":"' . $id . '"}';
		} else {
			$json = $dbErrorMsg;
		}
	};
	echo $json;
}

function deleteCustomer($cid) {
global $dbconn,  $dbErrorMsg;
	$qry = "DELETE FROM customers WHERE id = " . $cid ;
	if (!dbQuery($dbconn, $qry)) { return ($dbErrorMsg); }
	$qry = "DELETE FROM boats WHERE customerId = " . $cid;
	if (!dbQuery($dbconn, $qry)) { return ($dbErrorMsg); }
	return ('{"status":"OK"}');
}
/******************************************************************************
 *********************** End of CUSTOMERS functions ***************************
 ******************************************************************************/

/******************************************************************************
 * BOATS functions
 ******************************************************************************/
function getBoatTable() {	// Get full table (for mobile client)
global $dbconn,  $dbErrorMsg;
	$qry = "SELECT * FROM boats";
	$result = dbQuery($dbconn, $qry);
	$json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
	while ($row = mysql_fetch_assoc($result)) {
		$json .= '{';
			$json .= '"id":"' . $row['id'] . '",';
			$json .= '"name":"' . $row['name'] . '",';
			$json .= '"make":"' . $row['make'] . '",';
			$json .= '"model":"' . $row['model'] . '",';
			$json .= '"year":"' . $row['year'] . '",';
			$json .= '"loa":"' . $row['loa'] . '",';
			$json .= '"beam":"' . $row['beam'] . '",';
			$json .= '"regno":"' . $row['regno'] . '",';
			$json .= '"customerId":"' . $row['customerId'] . '",';
			$json .= '"siteId":"' . $row['siteId'] . '",';
			$json .= '"berth":"' . $row['berth'] . '",';
			$json .= '"notes":"' . $row['notes'] . '",';
			$json .= '"boatkeys":"' . $row['boatkeys'] . '",';
			$json .= '"inwater":"' . $row['inwater'] . '",';
			$json .= '"state":"' . $row['state`'] . '",';
			$json .= '"created":"' . $row['ceated'] . '",';
			$json .= '"updated":"' . $row['updated'] . '",';
			$json .= '"updatedBy":"' . $row['updatedBy'] . '"';
		$json .= '},';
	}
	if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
	$json .= ']}';
//error_log($json);
	return $json;
}


function getBoatList($s) {
global $dbconn,  $dbErrorMsg;

    $qry = "SELECT * FROM boats ";
error_log("s = [" . $s . "]");
    if ($s != "null") { $qry .= "WHERE siteId = " . $s . " "; }
    $qry .= "ORDER BY name";
	$result = dbQuery($dbconn, $qry);
    if (!$result) {return ($dbErrorMsg);}
	$json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
	while ($row = mysql_fetch_assoc($result)) {
		$json .= '{';
			$qry = "SELECT id as siteId, name as siteName FROM sites WHERE id = " . $row['siteId'];
			$res1 = dbQuery($dbconn, $qry);
            if (!$res1) {return ($dbErrorMsg);}
            $row1 = mysql_fetch_assoc($res1);
			$qry = "SELECT id as customerId, (CONCAT(title, ' ', firstname, ' ', lastname)) as customerName FROM customers WHERE id = " . $row['customerId'];
			$res2 = dbQuery($dbconn, $qry);
            if (!$res2) {return ($dbErrorMsg);}
            $row2 = mysql_fetch_assoc($res2);
			$json .= '"id":"' . $row['id'] . '",';
			$json .= '"name":"<a onclick=\'showBoat(' . $row['id'] . ', 0)\'>' . $row['name'] . '</a>' . '",';
			$json .= '"make":"' . $row['make'] . '",';
			$json .= '"model":"' . $row['model'] . '",';
			$json .= '"loa":"' . $row['loa'] . '",';
//			$json .= '"engine1":"<a target=\'_blank\' href=\'http://' . $row['engine1Id'] . '\'>' . $row['engine1Type'] . '</a>' . '",';
//			$json .= '"engine2":"<a target=\'_blank\' href=\'http://' . $row['engine2Id'] . '\'>' . $row['engine2Type'] . '</a>' . '",';
			$json .= '"customer":"<a onclick=\'showCustomer(' . $row2['customerId'] . ')\'>' . $row2['customerName'] . '</a>' . '",';
			$json .= '"site":"<a onclick=\'showSite(' . $row1['siteId'] . ')\'>' . $row1['siteName'] . '</a>' . '",';
            $json .= '"berth":"' . $row['berth'] . '",';
            $status = ($row['inwater'] + $row['state']);
            $qry = "SELECT * FROM jobsheets WHERE boatId = " . $row['id'] . " AND stage <> 'Closed'";
            $res1 = dbQuery($dbconn, $qry);
            if (!$res1) {return ($dbErrorMsg);}
            if (mysql_num_rows($res1) > 0) { $status = $status + 16;}
            $json .= '"status":"' . $status . '"';
		$json .= '},';
	}
	if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
	$json .= ']}';
//error_log($json);
	return $json;
}

function getBoatNames($custId = 0) {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT boats.id as boatId, boats.name as boatName, sites.id as siteId FROM boats, sites ";
    $qry .= "WHERE sites.id = boats.siteId ";
    if ($custId > 0) { $qry .= "AND customerId = " . $custId . " "; }
    $qry .= "ORDER BY boats.name";
    $result = dbQuery($dbconn, $qry);
    if (!$result) {return ($dbErrorMsg);}
    $json = '{"options":[';
    while ($row = mysql_fetch_assoc($result)) {
        $json .= '{';
        $json .= '"id":"' . $row['boatId'] . '", ';
        $json .= '"name":"' . $row['boatName'] . '", ';
        $json .= '"data":"' . $row['siteId'] . '"';
        $json .= '}, ';
    }
    if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
    $json .= ']}';
    return $json;
}

function addBoat() {
global $dbconn,  $dbErrorMsg;
	// First Check that this really is a new boat...
	$qry = "SELECT * from boats WHERE name = '" . $_POST['name'] . "'";
	$result = dbQuery($dbconn, $qry);
    if (!$result) {return ($dbErrorMsg);}
	if (mysql_num_rows($result) > 0) {
		$json .= '{"status":"Failed", "msg":"A boat with this name is already registered!  (No action taken)"}';
	} else {
		$qry = "INSERT INTO boats (name, make, model, year, loa, beam, regno, customerId, siteId, berth, boatkeys, inwater, state, notes, updatedBy)";
		$qry .= " VALUES ('" . $_POST['name'] . "', '" . $_POST['make'] . "','" . $_POST['model'] . "','" . $_POST['year'] . "','" . $_POST['loa'] . "','" . $_POST['beam'] . "','" . $_POST['regno'] . "','" . $_POST['owner'] . "', '" . $_POST['site'] . "', '" . $_POST['berth'] . "', '" . $_POST['keys'] . "', '" . $_POST['inwater'] . "', '" . $_POST['state'] . "', '" . $_POST['notes'] . "', '" . $_POST['cu'] . "')";
		if (dbQuery($dbconn, $qry)) {
            $id = mysql_insert_id();
            // Update customer record field 'siteId with the value for this boat
            $qry = "UPDATE customers SET siteId = '" . $_POST['site'] . "' WHERE id = " . $_POST['owner'];
            dbQuery($dbconn, $qry);
			$json = '{"status":"OK", "msg":"Boat added to database.", "id":"' . $id . '"}';
		} else {
			$json = $dbErrorMsg;
		}
	}
	echo $json;
}


function getBoatDetails($id) {
global $dbconn,  $dbErrorMsg;

//error_log("getBoatDetails id = " . $id);
    $qry = "SELECT boats.*, sites.name as siteName ";
    $qry .= "FROM boats, sites ";
    $qry .= "WHERE boats.id = " . $id . " ";
    $qry .= "AND sites.id = boats.siteId ";
//error_log($qry);
    $result = dbQuery($dbconn, $qry);
    if (!$result) {return ($dbErrorMsg);}
	if (mysql_num_rows($result) == 0) {
		$json = '{"resultcount":"0"}';
	} else {
		$row = mysql_fetch_assoc($result);
		$json = '{"resultcount":"' . mysql_num_rows($result)  . '", ';
		$json .= '"id":"' . $row['id'] . '", ';
		$json .= '"name":"' . $row['name'] . '", ';
		$json .= '"make":"' . $row['make'] . '", ';
		$json .= '"model":"' . $row['model'] . '", ';
		$json .= '"year":"' . $row['year'] . '", ';
		$json .= '"LOA":"' . $row['loa'] . '", ';
		$json .= '"beam":"' . $row['beam'] . '", ';
        $json .= '"Regno":"' . $row['regno'] . '", ';
        $json .= '"CustomerId":"' . $row['customerId'] . '", ';
//error_log($json);
        if ($row['customerId'] == -1) {  // Allow for unknown Customer
            $json .= '"CustomerName":"", ';
        } else {
            $qry = "SELECT (CONCAT(customers.title,' ',customers.firstname,' ', customers.lastname)) as customerName ";
            $qry .= "FROM customers WHERE id = " . $row['customerId'];
            $res2 = dbQuery($dbconn, $qry);
            if (!$res2) {return ($dbErrorMsg);}
            $row2 = mysql_fetch_assoc($res2);
            $json .= '"CustomerName":"' . $row2['customerName'] . '", ';
        }
//error_log($json);
        $json .= '"SiteId":"' . $row['siteId'] . '", ';
        $json .= '"SiteName":"' . $row['siteName'] . '", ';
        $json .= '"Berth":"' . $row['berth'] . '", ';
        $qry = "SELECT engines.* , engineTemplates.id as etId, engineTemplates.make, engineTemplates.model ";
        $qry .= "FROM engines, engineTemplates ";
        $qry .= "WHERE engines.boatId = " . $id . " ";
        $qry .= "AND engineTemplates.id = engines.engineTemplateId";
//error_log($qry);
        $res2 = dbQuery($dbconn, $qry);
        if (!$res2) {return ($dbErrorMsg);}
        $engineCount = mysql_num_rows($res2);
        $json .= '"engines":[';
        while ($row2 = mysql_fetch_assoc($res2)) {
            $json .= '{';
            $json .= '"EngineId":"' . $row2['id'] . '", ';
            $json .= '"EngineType":"' . $row2['type'] . '", ';
            $json .= '"EngineMake":"' . $row2['make'] . '", ';
            $json .= '"EngineModel":"' . $row2['model'] . '", ';
            $json .= '"EngineSerialno":"' . $row2['serialno'] . '"';
            $json .= '}, ';
        }
        if ($engineCount > 0) { $json = trim($json, ", "); }
        $json .= '], ';
		$json .= '"boatKeys":"' . $row['boatkeys'] . '", ';
        $json .= '"inwater":"' . $row['inwater'] . '",';
        $json .= '"state":"' . $row['state'] . '", ';
		$json .= '"notes":"' . $row['notes'] . '", ';
		$json .= '"created":"' . $row['created'] . '", ';
		$json .= '"updated":"' . $row['updated'] . '", ';
		$json .= '"updatedBy":"' . $row['updatedBy'] . '"}';
	}
//error_log($json);
	return $json;
}

function updateBoat($id) {
global $dbconn,  $dbErrorMsg;
    $qry = "UPDATE boats SET ";
    $qry .= "make='" . $_POST['make'] . "', ";
    $qry .= "model='" . $_POST['model'] . "', ";
    $qry .= "year='" . $_POST['year'] . "', ";
    $qry .= "loa='" . $_POST['loa'] . "', ";
    $qry .= "beam='" . $_POST['beam'] . "', ";
    $qry .= "regno='" . $_POST['regno'] . "', ";
    $qry .= "customerId='" . $_POST['owner'] . "', ";
    $qry .= "siteId=" . $_POST['site'] . ", ";
    $qry .= "berth='" . $_POST['berth'] . "', ";
    $qry .= "boatkeys='" . $_POST['keys'] . "', ";
    $qry .= "inwater=" . $_POST['inwater'] . ", ";
    $qry .= "state=" . $_POST['state'] . ", ";
    $qry .= "notes='" . $_POST['notes'] . "', ";
    $qry .= "updated='" . date("Y-m-d h:i") . "', ";
    $qry .= "updatedBy='" . $_POST['u'] . "'";
    $qry .= " WHERE id=" . $id;
//error_log("updateBoat " . $qry);
    if (dbQuery($dbconn, $qry)) {
        $qry = "UPDATE engines SET siteId = " . $_POST['site'] . " WHERE boatId = " . $id;
        if (dbQuery($dbconn, $qry)) {
            $json = '{"status":"OK", "msg":"Boat information updated.", "id":"' . $id . '"}';
        } else {
            $json = $dbErrorMsg;
        }
    } else {
        $json = $dbErrorMsg;
    }
    return $json;
}

function addEngineToBoat($boatId, $engineId) {
global $dbconn,  $dbErrorMsg;

    alert("TBD - Add Engine to Boat");
}

function deleteBoat($id) {
global $dbconn,  $dbErrorMsg;
	$qry = "DELETE FROM boats WHERE id = " . $id ;
	if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg;}
	$qry = "DELETE FROM engines WHERE boatId = " . $id;
	if (!dbQuery($dbconn, $qry)) { return $dbErrirMsg; }
	return ('{"status":"OK"}');
}

function getBoatsByET($id) {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT boats.id as boatId, boats.name as boatName, ";
	$qry .= "engines.type, engines.serialno, ";
	$qry .= "engineTemplates.make as etMake, engineTemplates.model as etModel ";
    $qry .= "FROM engineTemplates, engines, boats ";
    $qry .= "WHERE engineTemplates.id = " . $id . " ";
    $qry .= "AND engines.engineTemplateId = engineTemplates.id ";
    $qry .= "AND boats.id = engines.boatId ";
    $qry .= "ORDER BY boats.name ";
//error_log($qry);
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
	if (mysql_num_rows($result) == 0) {
		$json = '{"resultcount":"0"}';
	} else {
        $json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
        while ($row = mysql_fetch_assoc($result)) {
            $json .= '{';
            $json .= '"boatId":"' . $row['boatId'] . '", ';
            $json .= '"boatName":"' . $row['boatName'] . '", ';
            $json .= '"type":"' . $row['type'] . '", ';
            $json .= '"serialno":"' . $row['serialno'] . '", ';
            $json .= '"etMake":"' . $row['etMake'] . '", ';
            $json .= '"etModel":"' . $row['etModel'] . '"';
            $json .= '},';
        }
        if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
        $json .= ']}';
    }
//error_log($json);
	return $json;
}


function getBoatsSI($boatId) {
global $dbconn,  $dbErrorMsg;

    $qry = "SELECT id as eId FROM engines WHERE boatId = " . $boatId;
//error_log($qry);
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
	if (mysql_num_rows($result) == 0) {
		$json = '{"resultcount":"0"}';
	} else {
        $json = '{"resultcount":"' . mysql_num_rows($result)  . '", "engines":[';
        while ($row = mysql_fetch_assoc($result)) {
            $qry = "SELECT sites.name as site, sites.id as siteId, boats.name as boat, boats.id as boatId,  ";
                $qry .= "engines.type as engineType, engines.serialno as serialno, engines.notes as engineNotes, engines.created as engcreated, engines.updated as engupdated, engines.updatedBy as engupdatedBy, ";
                $qry .= "engineTemplates.id as etId, engineTemplates.make as etMake, engineTemplates.model as etModel, engineTemplates.cylinders, engineTemplates.capacity,engineTemplates.fuel, ";
                $qry .= "serviceItemNames.name as siName, ";
                $qry .= "serviceItems.id as siId, ";
                $qry .= "serviceItems.make as siMake, ";
                $qry .= "serviceItems.partno as siPartno, ";
                $qry .= "serviceItems.price as siPrice, ";
                $qry .= "serviceItems.notes as siNotes, ";
                $qry .= "siTObeMap.id as siMapId, ";
                $qry .= "siTObeMap.ismod as siIsmod, ";
                $qry .= "siTObeMap.qty as siQty ";
            $qry .= "FROM serviceItemNames, serviceItems, siTObeMap, engines, engineTemplates, boats, sites ";
            $qry .= "WHERE engines.id = " .$row['eId'] . " ";
            $qry .= "AND engineTemplates.id = engines.engineTemplateId ";
            $qry .= "AND engines.boatId = boats.id  ";
            $qry .= "AND sites.id = engines.siteId ";
            $qry .= "AND siTObeMap.eId = engines.id ";
            $qry .= "AND serviceItems.id = siTObeMap.siId ";
            $qry .= "AND serviceItemNames.id = serviceItems.siNameId ";
            $res2 = dbQuery($dbconn, $qry);
            if (!$res2) { return $dbErrorMsg; }
            $siCount = mysql_num_rows($res2);
            $json .= '{"serviceItems":[';
            while ($row2 = mysql_fetch_assoc($res2)) {
                $json .= '{';
                $json .= '"siMapId":"' . $row2['siMapId'] . '", ';
                $json .= '"siId":"' . $row2['siId'] . '", ';
                $json .= '"siName":"' . $row2['siName'] . '", ';
                $json .= '"siMake":"' . $row2['siMake'] . '", ';
                $json .= '"siPartno":"' . $row2['siPartno'] . '", ';
                $json .= '"siPrice":"' . $row2['siPrice'] . '", ';
                $json .= '"siQty":"' . $row2['siQty'] . '", ';
                $json .= '"siIsmod":"' . $row2['siIsmod'] . '",';
                $json .= '"siNotes":"' . $row2['siNotes'] . '"';
                $json .= '}, ';
            }
            if ($siCount > 0) { $json = trim($json, ", "); }
            $json .= ']},';              
            }
        if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
        $json .= ']}';
    }
//error_log($json);
    return $json;
}

/******************************************************************************
   *********************** End of BOATS functions ***************************
 ******************************************************************************/

/******************************************************************************
 * ENGINES functions
 ******************************************************************************/
function getEngineTable() {	// Get full table (for mobile client)
global $dbconn,  $dbErrorMsg;
	$qry = "SELECT * FROM engines";
	$result = dbQuery($dbconn, $qry);
	$json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
	while ($row = mysql_fetch_assoc($result)) {
		$json .= '{';
			$json .= '"id":"' . $row['id'] . '",';
			$json .= '"siteId":"' . $row['siteId'] . '",';
			$json .= '"boatId":"' . $row['boatId'] . '",';
			$json .= '"type":"' . $row['type'] . '",';
			$json .= '"serialno":"' . $row['serialno'] . '",';
			$json .= '"engineTemplateId":"' . $row['engineTemplateId'] . '",';
			$json .= '"notes":"' . $row['notes'] . '",';
			$json .= '"created":"' . $row['ceated'] . '",';
			$json .= '"updated":"' . $row['updated'] . '",';
			$json .= '"updatedBy":"' . $row['updatedBy'] . '"';
		$json .= '},';
	}
	if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
	$json .= ']}';
//error_log($json);
	return $json;
}
 
function getEngineList() {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT engines.id as engineId, engines.boatId as boatId, engines.siteId as siteId, engines.type as enginesType,";
	$qry .= "sites.name as siteName, ";
	$qry .= "engineTemplates.make, engineTemplates.model, engineTemplates.cylinders, engineTemplates.capacity, engineTemplates.fuel ";
	$qry .= "FROM engines, engineTemplates, sites ";
	$qry .= "WHERE engineTemplates.id = engines.engineTemplateId ";
    $qry .= "AND sites.id = engines.siteId";
	$result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
	$json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
	while ($row = mysql_fetch_assoc($result)) {
		$json .= '{';
			$json .= '"engineId":"' . $row['engineId'] . '",';
			$json .= '"boatId":"' . $row['boatId'] . '",';
            if ($row['boatId'] > 0) {
                $qry = "SELECT name FROM boats WHERE id = " . $row['boatId'];
                $res1 = dbQuery($dbconn, $qry);
                $row1 = mysql_fetch_assoc($res1);
                $json .= '"boatName":"' . $row1['name'] . '", ';
            } else {
                $json .= '"boatName":"", ';
            }
			$json .= '"siteId":"' . $row['siteId'] . '",';
            $json .= '"siteName":"' . $row['siteName'] . '", ';
			$json .= '"make":"<a onclick=\'showEngine(' . $row['engineId'] . ')\'>' . $row['make'] . '</a>' . '",';
			$json .= '"model":"' . $row['model'] . '",';
			$json .= '"cylinders":"' . $row['cylinders'] . '",';
			$json .= '"capacity":"' . $row['capacity'] . '",';
			$json .= '"fuel":"' . $row['fuel'] . '"';
		$json .= '},';
	}
	if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
	$json .= ']}';
//error_log($json);
	return $json;
}

function getEngineDetails($id) {  //
global $dbconn,  $dbErrorMsg;

    $qry = "SELECT sites.name as site, sites.id as siteId, boats.name as boat, boats.id as boatId, ";
	$qry .= "engines.type as engineType, engines.serialno as serialno, engines.notes as engineNotes, ";
	$qry .= "engines.created as engcreated, engines.updated as engupdated, engines.updatedBy as engupdatedBy, ";
	$qry .= "engineTemplates.id as etId, engineTemplates.make as etMake, engineTemplates.model as etModel, ";
	$qry .= "engineTemplates.cylinders, engineTemplates.capacity,engineTemplates.fuel, ";
	$qry .= "serviceItemNames.name as siName, serviceItems.make as siMake, serviceItems.partno as siPart, ";
	$qry .= "serviceItems.price as siPrice, serviceItems.notes as siNotes, ";
	$qry .= "siTObeMap.id as siMapId, siTObeMap.ismod as siIsmod, siTObeMap.qty as siQty ";
$qry .= "FROM serviceItemNames, serviceItems, siTObeMap, engines, engineTemplates, boats, sites ";
$qry .= "WHERE engines.id = " . $id . " ";
	$qry .= "AND engineTemplates.id = engines.engineTemplateId ";
	$qry .= "AND engines.boatId = boats.id ";
	$qry .= "AND sites.id = engines.siteId ";
	$qry .= "AND siTObeMap.eId = engines.id ";
	$qry .= "AND serviceItems.id = siTObeMap.siId ";
	$qry .= "AND serviceItemNames.id = serviceItems.siNameId ";
    $qry .= "ORDER BY siIsmod";
//error_log($qry);
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
    $siCount = mysql_num_rows($result);
	if ($siCount == 0) {
		$json = '{"resultcount":"0"}';
	} else {
        $first = true;
		while ($row = mysql_fetch_assoc($result)) {
            if ($first) {
                $json = '{"resultcount":"' . $siCount  . '", ';
                $json .= '"id":"' . $id . '", ';                    // Engine Id
                $json .= '"etId":"' . $row['etId'] . '", ';         // Engine Template Id
                $json .= '"boatId":"' . $row['boatId'] . '", ';
                $json .= '"boatName":"' . $row['boat'] . '",';
                $json .= '"siteId":"' . $row['siteId'] . '", ';
                $json .= '"siteName":"' . $row['site'] . '",';
                $json .= '"engineType":"' . $row['engineType'] . '", ';
                $json .= '"serialno":"' . $row['serialno'] . '", ';
                $json .= '"make":"' . $row['etMake'] . '", ';
                $json .= '"model":"' . $row['etModel'] . '", ';
                $json .= '"cylinders":"' . $row['cylinders'] . '",';
                $json .= '"capacity":"' . $row['capacity'] . '",';
                $json .= '"fuel":"' . $row['fuel'] . '",';
                $json .= '"notes":"' . $row['engineNotes'] . '",';
                $json .= '"created":"' . $row['engcreated'] . '", ';
                $json .= '"updated":"' . $row['engupdated'] . '", ';
                $json .= '"updatedBy":"' . $row['engupdatedBy'] . '",';
                $json .= '"serviceItems":[';
                $first = false;
            }
            $json .= '{';
            $json .= '"siMapId":"' . $row['siMapId'] . '", ';       // id of record in siTObeMap table
//            $json .= '"siId":"' . $row['siId'] . '", ';
            $json .= '"siName":"' . $row['siName'] . '", ';
            $json .= '"siMake":"' . $row['siMake'] . '", ';
            $json .= '"siPartno":"' . $row['siPart'] . '", ';
            $json .= '"siQty":"' . $row['siQty'] . '", ';
            $json .= '"siIsmod":"' . $row['siIsmod'] . '", ';
            $json .= '"siNotes":"' . $row['siNotes'] . '"';
            $json .= '}, ';
        }
        if ($siCount > 0) { $json = trim($json, ", "); }
        $json .= ']}';
//error_log($json);
        }
//error_log("Engine Details: " . $json);
	return $json;
}

function updateEngine($id) {
global $dbconn,  $dbErrorMsg;
//error_log("updateEngine");
    if ($id == 0) { // 0 means this is actually a new engine, so we INSERT not UPDATE
        $newEngine = true;
        $qry = "INSERT INTO engines  ";
        $qry .= "(siteId, boatId, type, serialno, engineTemplateId, notes, updated, updatedBy) ";
        $qry .= "VALUES (" . $_POST['siteId'] . ", " . $_POST['boatId'] . ",'" . $_POST['engineType'] . "',";
        $qry .= "'" . $_POST['serialno'] . "',";
        $qry .= $_POST['etId'] . ",";
        $qry .= "'" . $_POST['notes'] ."',";
        $qry .= "'" . date("Y-m-d h:i") . "', ";
        $qry .= "'" . $_POST['u'] . "' )";
        // Add engine and get id of new engine
 		if (dbQuery($dbconn, $qry)) {
            $id = mysql_insert_id();
        } else {
            return ($dbErrorMsg);
        }
    } else {
        $newEngine = false;
        $qry = "UPDATE engines SET ";
        $qry .= "siteId = " . $_POST['siteId'] . ", ";
        $qry .= "boatId = " . $_POST['boatId'] . ", ";
        $qry .= "type = '" . $_POST['engineType'] . "',";
        $qry .= "serialno = '" . $_POST['serialno'] . "',";
        $qry .= "engineTemplateId = " . $_POST['etId'] . ",";
        $qry .= "notes = '" . $_POST['notes'] ."',";
        $qry .= "updated = '" . date("Y-m-d h:i") . "', ";
        $qry .= "updatedBy = '" . $_POST['u'] . "' ";
        $qry .= "WHERE id = " . $id;
        if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
    }
//error_log($qry);
    // Now update quantities of Standard Serivce Items
    foreach ($_POST['siqty'] as $key => $value) {
// ===============================================
//        $qry = "SELECT * FROM siTObeMap WHERE eId = " . $id . " AND siId = " . $value['siId'];
        $qry = "SELECT * FROM siTObeMap WHERE eId = " . $id . " AND id = " . $value['siMapId'];
//error_log(" SI Get current QTY [" . $qry . "]");
        $result = dbQuery($dbconn, $qry);
 		if ($result) {
            if (mysql_num_rows($result) == 0) {
//                $qry = "INSERT INTO siTObeMap (eId, siId, qty) VALUES ( " . $id . ", " . $value['siId'] . ", '" . $value['siQty'] . "' )";
                $qry = "INSERT INTO siTObeMap (eId, siId, qty) VALUES ( " . $id . ", " . $value['siMapId'] . ", '" . $value['siQty'] . "' )";
            } else {
                $qry = "UPDATE siTObeMap SET qty = '" . $value['siQty'] . "' ";
//                $qry .= "WHERE eId = " . $id . " AND siId = " . $value['siId'];                
                $qry .= "WHERE id = " . $value['siMapId'];
            }
//error_log(" SI QTY UPDATE: - [" . $qry . "]");
            if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
        } else {
            return ($dbErrorMsg);
        }
// ===============================================
//        if ($newEngine) {
//            $qry = "INSERT INTO siTObeMap (eId, siId, qty) VALUES ( " . $id . ", " . $value['siId'] . ", '" . $value['siQty'] . "' )";
//        } else {
//            $qry = "UPDATE siTObeMap SET qty = '" . $value['siQty'] . "' ";
//            $qry .= "WHERE eId = " . $id . " AND siId = " . $value['siId'];
//        }
//error_log($qry);
//        if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
// ===============================================
    }
    // Now update modified Service Items
    foreach ($_POST['em'] as $key => $value) {
            $qry = "UPDATE engineMods SET ";
            $qry .= "qty = '" . $value['emQty'] . "' ";
            $qry .= "WHERE id = " . $value['mId'];
//        }
//error_log(" SI QTY UPDATE: - [" . $qry . "]");
        if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
    }
    echo '{"status":"OK","msg":"Engine information updated."}';
}

function deleteEngine($id) {
global $dbconn,  $dbErrorMsg;
	$qry = "DELETE FROM engines WHERE id = " . $id ;
	if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
	$qry = "DELETE FROM engineMods WHERE engineId = " . $id ;
	if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
	return ('{"status":"OK"}');
}

function getEngineMods($id, $dayaonly=false) {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT engineMods.*, serviceItemNames.name as siName, serviceItemNames.id as siNameId ";
    $qry .= "FROM engineMods, serviceItemNames ";
    $qry .= "WHERE engineMods.engineId = " . $id . " ";
    $qry .= "AND serviceItemNames.id = engineMods.siNameId";
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
    $modCount = mysql_num_rows($result);
    $json = '{"resultcount":"' . $modCount  . '", "engineMods":[';
    while ($row = mysql_fetch_assoc($result)) {
        $json .= '{';
        $json .= '"modId":"' . $row['id'] . '", ';
        $json .= '"siNameId":"' . $row['siNameId'] . '", ';
        $json .= '"name":"' . $row['siName'] . '", ';
        $json .= '"make":"' . $row['make'] . '", ';
        $json .= '"partno":"' . $row['partno'] . '", ';
        $json .= '"qty":"' . $row['qty'] . '", ';
        $json .= '"notes":"' . $row['notes'] . '"';
        $json .= '}, ';
    }
    if ($modCount > 0) { $json = trim($json, ", "); }
    $json .= ']}';
//error_log("Engine Mods: " . $json);
	return $json;
}

//function addMod($engineId) {
//global $dbconn,  $dbErrorMsg;
//    $qry = "INSERT INTO engineMods ";
//    $qry .= "(engineId, siNameId, make, partno, qty, notes, updated, updatedBy) ";
//    $qry .= "VALUES (" . $engineId . ",";
//    $qry .= $_POST['siNameId'] . ",";
//    $qry .= "'" . $_POST['make'] . "',";
//    $qry .= "'" . $_POST['partno'] . "',";
//    $qry .= "'" . $_POST['qty'] . "',";
//    $qry .= "'" . $_POST['notes'] . "',";
//    $qry .= "'" . date("Y-m-d h:i") . "',";
//    $qry .= "'" . $_POST['u'] . "' )";
////error_log($qry);
//    if (dbQuery($dbconn, $qry)) {
//        $json = '{"status":"OK", "msg":"Mod added to database."}';
//    } else {
//        $json = $dbErrorMsg;;
//    }
//    return $json;
//}

function deleteMod($modId) {
global $dbconn,  $dbErrorMsg;
    $qry = "DELETE FROM siTObeMap WHERE id = " . $modId;
	if (dbQuery($dbconn, $qry)) {
        return ('{"status":"OK"}');
    } else {
        return ($dbErrorMsg);
    }
}
/******************************************************************************
   ********************* End of ENGINES functions **************************
 ******************************************************************************/


/******************************************************************************
 * ENGINE TEMPLATES functions
 ******************************************************************************/
function getEngineTemplateTable() {	// Get full table (for mobile client)
global $dbconn,  $dbErrorMsg;
	$qry = "SELECT * FROM engineTemplates";
	$result = dbQuery($dbconn, $qry);
	$json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
	while ($row = mysql_fetch_assoc($result)) {
		$json .= '{';
			$json .= '"id":"' . $row['id'] . '",';
			$json .= '"make":"' . $row['make'] . '",';
			$json .= '"model":"' . $row['model'] . '",';
			$json .= '"cylinders":"' . $row['cylinders'] . '",';
			$json .= '"capacity":"' . $row['capacity'] . '",';
			$json .= '"fuel":"' . $row['fuel'] . '",';
			$json .= '"notes":"' . $row['notes'] . '",';
			$json .= '"created":"' . $row['ceated'] . '",';
			$json .= '"updated":"' . $row['updated'] . '",';
			$json .= '"updatedBy":"' . $row['updatedBy'] . '"';
		$json .= '},';
	}
	if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
	$json .= ']}';
//error_log($json);
	return $json;
}

function getEngineTemplateList() {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT * FROM engineTemplates ORDER BY make, model";
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
	if (mysql_num_rows($result) == 0) {
		$json = '{"resultcount":"0"}';
	} else {
        $json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
        while ($row = mysql_fetch_assoc($result)) {
            $json .= '{';
            $json .= '"id":"' . $row['id'] . '", ';
            $json .= '"make":"' . $row['make'] . '", ';
            $json .= '"model":"' . $row['model'] . '", ';
            $json .= '"cylinders":"' . $row['cylinders'] . '", ';
            $json .= '"capacity":"' . $row['capacity'] . '", ';
            $json .= '"fuel":"' . $row['fuel'] . '" ';
            $json .= "}, ";
        }
        if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
        $json .= ']}';
    }
//error_log("Engine Templates " . $json);
    return $json;
}

function getEngineTemplateDetails($id) {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT * ";
	$qry .= "FROM engineTemplates ";
	$qry .= "WHERE id = " . $id . " ";
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
	if (mysql_num_rows($result) == 0) {
		$json = '{"resultcount":"0"}';
	} else {
		$row = mysql_fetch_assoc($result);
		$json = '{"resultcount":"' . mysql_num_rows($result)  . '", ';
		$json .= '"id":"' . $id . '", ';          // Engine Id
        $json .= '"make":"' . $row['make'] . '", ';
        $json .= '"model":"' . $row['model'] . '", ';
        $json .= '"cylinders":"' . $row['cylinders'] . '",';
        $json .= '"capacity":"' . $row['capacity'] . '",';
        $json .= '"fuel":"' . $row['fuel'] . '",';
        $json .= '"notes":"' . $row['notes'] . '",';
        $json .= '"created":"' . $row['created'] . '",';
        $json .= '"updated":"' . $row['updated'] . '",';
        $json .= '"updatedBy":"' . $row['updatedBy'] . '",';
        $qry = "SELECT serviceItems.id as siId, serviceItems.make as siMake, serviceItems.partno as siPartno, serviceItems.notes as siNotes, ";
        $qry .= "serviceItemNames.name as siName ";
        $qry .= "FROM serviceItems, serviceItemNames, siTOetMap ";
        $qry .= "WHERE siTOetMap.etId = " . $id . " ";
        $qry .= "AND serviceItems.id = siTOetMap.siId ";
        $qry .= "AND serviceItemNames.id = serviceItems.siNameId ";
        $qry .= "ORDER BY siName";
//error_log($qry);
        $res2 = dbQuery($dbconn, $qry);
        if (!$res2) { return $dbErrorMsg; }
        $siCount = mysql_num_rows($res2);
        $json .= '"serviceItems":[';
        while ($row2 = mysql_fetch_assoc($res2)) {
            $json .= '{';
            $json .= '"siId":"' . $row2['siId'] . '", ';
            $json .= '"siName":"' . $row2['siName'] . '", ';
            $json .= '"siMake":"' . $row2['siMake'] . '", ';
            $json .= '"siPartno":"' . $row2['siPartno'] . '", ';
            $json .= '"siNotes":"' . $row2['siNotes'] . '"';
            $json .= '}, ';
        }
        if ($siCount > 0) { $json = trim($json, ", "); }
        $json .= ']}';
	}
//error_log("Engine Template Details: " . $json);
	return $json;
}

function addEngineTemplate() {
global $dbconn,  $dbErrorMsg;
	// First Check that this really is a new engine template...
	$qry = "SELECT id from engineTemplates WHERE make = '" . $_POST['make'] . "' AND model = '" . $_POST['model'] . "'";
	$result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
	if (mysql_num_rows($result) > 0) {
		$json .= '{"status":"Failed", "msg":"This Engine Template is already registered!"}';
	} else {
		$qry = "INSERT INTO engineTemplates ";
//        $qry .= "(make, model, cylinders, capacity, fuel, notes, updatedBy) ";
//		$qry .= " VALUES ('" . $_POST['make'] . "', '" . $_POST['model'] . "','" . $_POST['cylinders'] . "','" . $_POST['capacity'] . "','" . $_POST['fuel'] . "','" . $_POST['notes'] . "', '" . $_POST['u'] . "')";
        $qry .= "(make, model, cylinders, capacity, fuel, updatedBy) ";
		$qry .= " VALUES ('" . $_POST['make'] . "', '" . $_POST['model'] . "','" . $_POST['cylinders'] . "','" . $_POST['capacity'] . "','" . $_POST['fuel'] . "','" . $_POST['u'] . "')";
		if (dbQuery($dbconn, $qry)) {
			$json = '{"status":"OK", "msg":"Engine Template added to database"}';
		} else {
			$json = $dbErrorMsg;
		}
	};
	echo $json;
}

function updateEngineTemplate($id) {
global $dbconn,  $dbErrorMsg;
    $qry = "UPDATE engineTemplates SET ";
    $qry .= "make = '" . $_POST['make'] . "',";
    $qry .= "model = '" . $_POST['model'] . "',";
    $qry .= "cylinders = '" . $_POST['cylinders'] . "',";
    $qry .= "capacity = '" . $_POST['capacity'] . "',";
    $qry .= "fuel = '" . $_POST['fuel'] . "',";
    $qry .= "notes = '" . $_POST['notes'] . "',";
    $qry .= "updated = '" . date("Y-m-d h:i") . "',";
    $qry .= "updatedBy = '" . $_POST['u'] . "' ";
    $qry .= "WHERE id = " . $id;
    if (dbQuery($dbconn, $qry)) {
        $json = '{"status":"OK", "msg":"Engine Template details updated"}';
    } else {
        $json = '{"status":"Failed", "msg":"Failed to add Engine Template:<br>(' . mysql_error() . ')"}';
    }
	echo $json;
}

function deleteEngineTemplate($id) {
global $dbconn,  $dbErrorMsg;
	$qry = "DELETE FROM engineTemplates WHERE id = " . $id;
	if (dbQuery($dbconn, $qry)) {
	   return ('{"status":"OK"}');
    } else {
        return $dbErrorMsg;
    }
}

function getServiceItemsList() {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT serviceItems.id as siId, serviceItemNames.id as siNameId, serviceItemNames.name, serviceItems.make, serviceItems.partno, serviceItems.price, serviceItems.notes ";
    $qry .= "FROM serviceItems, serviceItemNames ";
    $qry .= "WHERE serviceItemNames.id = serviceItems.siNameId ";
    $qry .= "ORDER BY name";
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
	$json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
	while ($row = mysql_fetch_assoc($result)) {
		$json .= '{';
        $json .= '"siId":"' . $row['siId'] . '", ';
        $json .= '"siNameId":"' . $row['siNameId'] . '", ';
        $json .= '"name":"' . $row['name'] . '", ';
        $json .= '"make":"' . $row['make'] . '", ';
        $json .= '"partno":"' . $row['partno'] . '", ';
        $json .= '"price":"' . $row['price'] . '", ';
        $json .= '"notes":"' . $row['notes'] . '" ';
        $json .= "}, ";
    }
    if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
    $json .= ']}';
//error_log("si list: " . $json);
	return $json;
}

function deleteETSI($siId, $etId) {
global $dbconn,  $dbErrorMsg;
    $qry = "DELETE FROM siTOetMap WHERE etId = " . $etId . " AND siId = " . $siId;
    if (dbQuery($dbconn, $qry)) {
        return ('{"status":"OK"}');
    } else {
        return $dbErrorMsg;
    }
}

function addSiToET($etId) {
global $dbconn,  $dbErrorMsg;

    foreach ($_POST['items'] as $key => $value) {
        $qry = "INSERT INTO siTOetMap (etId, siId, updated, updatedBy) ";
        $qry .= "VALUES (" . $etId . ", " . $value['siId'] . ", '" . date("Y-m-d h:i") . "', '" . $_POST['u'] . "')";
        if (!dbQuery($dbconn, $qry)) { return ( $dbErrorMsg ); }
    }
    return ('{"status":"OK"}');
}

function addModToET($etId) {
global $dbconn,  $dbErrorMsg;

    foreach ($_POST['items'] as $key => $value) {
        $qry = "INSERT INTO siTObeMap (eId, siId, ismod, updated, updatedBy) ";
        $qry .= "VALUES (" . $etId . ", " . $value['siId'] . ", 1, '" . date("Y-m-d h:i") . "', '" . $_POST['u'] . "')";
        if (!dbQuery($dbconn, $qry)) { return ( $dbErrorMsg ); }
    }
    return ('{"status":"OK"}');
}

function getServiceItemDetails($id) {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT serviceItems.id as siId, serviceItems.make, serviceItems.partno, serviceItems.price, serviceItems.notes as siNotes, ";
    $qry .= "serviceItems.created as siCreated, serviceItems.updated as siUpdated, serviceItems.updatedBy as siUpdatedBy, ";
    $qry .= "serviceItemNames.id as siNameId, serviceItemNames.name as siName ";
    $qry .= "FROM serviceItems, serviceItemNames ";
    $qry .= "WHERE serviceItems.id = " . $id . " ";
    $qry .= "AND serviceItemNames.id = serviceItems.siNameId";
//error_log($qry);
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
	if (mysql_num_rows($result) == 0) {
		$json = '{"resultcount":"0"}';
	} else {
		$row = mysql_fetch_assoc($result);
		$json = '{"resultcount":"' . mysql_num_rows($result)  . '", ';
        $json .= '"siId":"' . $row['siId'] . '",';
        $json .= '"siNameId":"' . $row['siNameId'] . '",';
        $json .= '"name":"' . $row['siName'] . '",';
        $json .= '"make":"' . $row['make'] . '",';
        $json .= '"partno":"' . $row['partno'] . '",';
        $json .= '"price":"' . $row['price'] . '",';
        $json .= '"notes":"' . $row['siNotes'] . '",';
        $json .= '"created":"' . $row['siCreated'] . '",';
        $json .= '"updated":"' . $row['siUpdated'] . '",';
        $json .= '"updatedBy":"' .  $row['siUpdatedBy'] . '"}';
    }
//error_log("si details: " . $json);
	return $json;
}

function addServiceItem() {  //"siNameId":siNameId, "make":make, "partno":partno, "notes":notes, "user":user
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT * FROM serviceItems WHERE partno = '" . $_POST['partno'] . "'";
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
//    if (mysql_num_rows($result) == 1) {
    if ((mysql_num_rows($result) > 0) && ($_POST['partno'] != "")) {  // Have to allow for multiple 'unknown' part numbers
        $json = '{"status":"Duplicate", "msg":"A service item with this Part Number already exists.<br>No action taken."}';
    } else {
        $qry = "INSERT INTO serviceItems (siNameId, make, partno, price, notes, updatedBy)";
        $qry .= " VALUES ('" . $_POST['siNameId'] . "', '" . $_POST['make'] . "','" . $_POST['partno'] . "','" . $_POST['price'] . "','" . $_POST['notes'] . "','" . $_POST['user'] . "')";
        if (dbQuery($dbconn, $qry)) {
            $json = '{"status":"OK", "msg":"Service Item added to database"}';
        } else {
            $json = $dbErrorMsg;
        }
    }
    return $json;
}

function updateServiceItem($id) {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT * FROM serviceItems WHERE partno = '" . $_POST['partno'] . "' AND id <> " . $id;
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
    if ((mysql_num_rows($result) > 0) && ($_POST['partno'] != "")) {  // Have to allow for multiple 'unknown' part numbers
        echo '{"status":"Duplicate", "msg":"A service item with this Part Number already exists.<br>No action taken."}';
    } else {    
        $qry = "UPDATE serviceItems SET ";
        $qry .= "make = '" . $_POST['make'] . "', ";
        $qry .= "partno = '" . $_POST['partno'] . "', ";
        $qry .= "price = '" . $_POST['price'] . "', ";
        $qry .= "notes = '" . $_POST['notes'] . "', ";
        $qry .= "updated = '" . date("Y-m-d h:i") . "', ";
        $qry .= "updatedBy = '" . $_POST['user'] . "' ";
        $qry .= "WHERE id = " . $id;
        $result = dbQuery($dbconn, $qry);
        if (result) {
          echo '{"status":"OK","msg":"Service Item updated."}';
        } else {
            echo $dbErrorMsg;
        }
    }
}

function deleteServiceItem($id) {
global $dbconn,  $dbErrorMsg;
    $qry = "DELETE FROM serviceItems WHERE id = " . $id;
    if (dbQuery($dbconn, $qry)) {
        $json = '{"status":"OK", "msg":"Service Item removed from database"}';
    } else {
        $json = $dbErrorMsg;
    }
    return $json;
}

function getSINamesDropdown() {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT * FROM serviceItemNames ORDER BY name";
    $result = dbQuery($dbconn, $qry);
    $json .= '{"options":[';
    while ($row = mysql_fetch_assoc($result)) {
        $json .= '{';
        $json .= '"id":"' . $row['id'] . '", ';
        $json .= '"name":"' . $row['name'] . '"';
        $json .= '}, ';
    }
    if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
    $json .= ']}';
    return $json;
}

function newSIName($new, $author) {
global $dbconn,  $dbErrorMsg;
    $qry = "INSERT INTO serviceItemNames ";
    $qry .= "(name, createdBy) VALUES ('" . $new . "', '" . $author . "')";
    return dbQuery($dbconn, $qry);
}

/******************************************************************************
 * STANDARD JOBS functions
 ******************************************************************************/
function getStandardJobsList() {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT * FROM standardJobs ORDER BY description";
//error_log($qry);
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
	$json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
	while ($row = mysql_fetch_assoc($result)) {
		$json .= '{';
        $json .= '"id":"' . $row['id'] . '", ';
        $json .= '"description":"' . $row['description'] . '", ';
        $json .= '"LOAmultiplier":"' . $row['LOAmultiplier'] . '", ';
        $json .= '"discount":"' . $row['discount'] . '", ';
        $json .= '"price":"' . $row['price'] . '" ';
        $json .= "}, ";
    }
    if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
    $json .= ']}';
//error_log("sj list: " . $json);
	return $json;
}

function getStandardJobDetails($id){
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT * FROM standardJobs WHERE id = " . $id;
//error_log($qry);
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
	if (mysql_num_rows($result) == 0) {
		$json = '{"resultcount":"0"}';
	} else {
		$row = mysql_fetch_assoc($result);
		$json = '{"resultcount":"' . mysql_num_rows($result)  . '", ';
        $json .= '"id":"' . $row['id'] . '",';
        $json .= '"description":"' . $row['description'] . '",';
        $json .= '"LOAmultiplier":"' . $row['LOAmultiplier'] . '",';
        $json .= '"price":"' . $row['price'] . '",';
        $json .= '"discount":"' . $row['discount'] . '",';
        $json .= '"created":"' . $row['created'] . '",';
        $json .= '"updated":"' . $row['updated'] . '",';
        $json .= '"updatedBy":"' .  $row['updatedBy'] . '"}';
    }
//error_log("sj details: " . $json);
	return $json;
}

function addStandardJob($id) {
global $dbconn,  $dbErrorMsg;

    if ($id == 0) { // 0 means new so we INSERT otherwise we UPDATE
        $qry = "INSERT INTO standardJobs ";
        $qry .= "(description, price, LOAmultiplier, discount, updatedBy) ";
        $qry .= "VALUES ('" . $_POST['description'] . "', '" . $_POST['price'] . "', '" . $_POST['LOAmultiplier'] . "', '" . $_POST['discount'] . "', '" . $_POST['updatedBy'] . "')";
    } else {
        $qry = "UPDATE standardJobs SET ";
        $qry .= "description = '" . $_POST['description'] . "', price = '" . $_POST['price'] . "', LOAmultiplier = '" . $_POST['LOAmultiplier'] . "', discount = '" . $_POST['discount'] . "', updated = '" . date('Y-m-d H:i:s') . "', updatedBy = '" . $_POST['updatedBy'] . "' ";
        $qry .= "WHERE id = " . $id;
    }
    if (dbQuery($dbconn, $qry)) {
        $json = '{"status":"OK", "msg":"Standard Jobs Updated"}';
    } else {
        $json = '{"status":"Failed", "msg":"Failed to update Job information."}';
    }
//error_log($json);
    return $json;
}

function deleteStandardJob($id) {
global $dbconn,  $dbErrorMsg;
    $qry = "DELETE FROM standardJobs WHERE id = " . $id;
    if (dbQuery($dbconn, $qry)) {
        $json = '{"status":"OK", "msg":"Standard Job removed from database"}';
    } else {
        $json = '{"status":"Failed", "msg":"Failed to remove Standard Job:<br>(' . mysql_error() . ')"}';
    }
    return $json;
}
/******************************************************************************
   ***************** End of ENGINE TEMPLATES functions **********************
 ******************************************************************************/


/******************************************************************************
 * USER functions
 ******************************************************************************/
function getUserList() {
global $dbconn,  $dbErrorMsg;
	$qry = "SELECT * FROM users ORDER BY name";
	$result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
	$json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
	while ($row = mysql_fetch_assoc($result)) {
		$json .= "{";
            $json .= '"uId":"' .  $row['id']  . '",';
			$json .= '"username":"' .  $row['name']  . '",';
			$json .= '"type":"' . $row['type'] . '",';
			$s = ($row['status']==1)?"Active":"Disabled";
			$json .= '"status":"' . $s . '"';
		$json .= "},";
	}
	if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
	$json .= ']}';
	return $json;
}

function getUserDetails($id) {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT * FROM users WHERE id = " . $id;
//error_log($qry);
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
	if (mysql_num_rows($result) == 0) {
		$json = '{"resultcount":"0"}';
	} else {
		$row = mysql_fetch_assoc($result);
		$json = '{"resultcount":"' . mysql_num_rows($result)  . '", ';
		$json .= '"id":"' . $row['id'] . '", ';
		$json .= '"name":"' . $row['name'] . '", ';
		$json .= '"mobile":"' . $row['mobile'] . '", ';
		$json .= '"email":"' . $row['email'] . '", ';
		$json .= '"type":"' . $row['type'] . '", ';
		$json .= '"status":"' . $row['status'] . '", ';
		$json .= '"lastConsoleLogin":"' . $row['lastConsoleLogin'] . '", ';
		$json .= '"consoleLoginCount":"' . $row['consoleLoginCount'] . '", ';
		$json .= '"lastMobileLogin":"' . $row['lastMobileLogin'] . '", ';
		$json .= '"mobileLoginCount":"' . $row['mobileLoginCount'] . '", ';
		$json .= '"email":"' . $row['email'] . '", ';
		$json .= '"created":"' . $row['created'] . '", ';
		$json .= '"updated":"' . $row['updated'] . '", ';
		$json .= '"updatedBy":"' . $row['updatedBy'] . '"}';
	}
//error_log($json);
	return $json;
}

function addUser($id) { //  Used for add (id = 0) AND update (id = id)
global $dbconn,  $dbErrorMsg;
    //         var userJSON = {"name":name, "mobile":mobile, "email":email, "type":type, "status":status, "updatedBy":updatedBy};
    if ($id == 0) {
        // First check this is a unique username
        $qry = "SELECT * FROM users WHERE name = '" . $_POST['name'] . "'";
        $result = dbQuery($dbconn, $qry);
        if (!$result) { return $dbErrorMsg; }
        if (mysql_num_rows($result) > 0) {
            $json = '{"status":"Duplicate", "msg":"This username is already registered."}';
            return $json;
        }
        $qry = "INSERT INTO users (name, mobile, email, type, status, updatedBy) ";
        $qry .= "VALUES ('" . $_POST['name'] . "', ";
        $qry .= "'" . $_POST['mobile'] . "', ";
        $qry .= "'" . $_POST['email'] . "', ";
        $qry .= "'" . $_POST['type'] . "', ";
        $qry .= "'" . $_POST['status'] . "', ";
        $qry .= "'" . $_POST['updateBy'] . "') ";
    } else {
        $qry = "UPDATE users SET ";
        $qry .= "name = '" . $_POST['name'] . "', ";
        $qry .= "mobile = '" . $_POST['mobile'] . "', ";
        $qry .= "email = '" . $_POST['email'] . "', ";
        $qry .= "type = '" . $_POST['type'] . "', ";
        $qry .= "status = '" . $_POST['status'] . "', ";
        $qry .= "updated = '" . date('Y-m-d H:i:s') . "', ";
        $qry .= "updatedBy = '" . $_POST['updatedBy'] . "' ";
        $qry .= "WHERE id = " . $id;
    }
    if (dbQuery($dbconn, $qry)) {
        if ($id == 0) { // This was a new user so we send a password link
            $msg = "A User account (" . $_POST['name'] . ") has been created for you on the Driveline Marine Staff Site.\n";
            $msg .= "Please visit the site to set up your password.";
            if (sendEmail($_POST['email'], "dlm@45y.co.uk", "New Account", $msg)) {
                $json = '{"status":"OK", "msg":"User Account registered and Welcome email sent to user."}';
            } else {
                $json = '{"status":"Problem", "msg":"User Account registered but Welcome email failed."}';
            }
        } else {
            $qry = "SELECT id FROM notifications WHERE name = 'NEWBOAT' AND userId = " . $id;
            $result = dbQuery($dbconn, $qry);
            if (!$result) { return $dbErrorMsg; }
            if (mysql_num_rows($result) == 0) {
                $qry = "INSERT INTO notifications (name, userId, mode) VALUES (";
                $qry .= "'NEWBOAT', " . $id . ", '" . $_POST['nbMode'] . "')";
                if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
            } else {
                $qry = "UPDATE notifications SET mode = '" . $_POST['nbMode'] . "' WHERE name = 'NEWBOAT' AND userId = " . $id;
                if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
            }
            $qry = "SELECT id FROM notifications WHERE name = 'NEWJOBSHEET' AND userId = " . $id;
            $result = dbQuery($dbconn, $qry);
            if (!$result) { return $dbErrorMsg; }
            if (mysql_num_rows($result) == 0) {
                $qry = "INSERT INTO notifications (name, userId, mode) VALUES (";
                $qry .= "'NEWJOBSHEET', " . $id . ", '" . $_POST['njsMode'] . "')";
                if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
            } else {
                $qry = "UPDATE notifications SET mode = '" . $_POST['njsMode'] . "' WHERE name = 'NEWJOBSHEET' AND userId = " . $id;
                if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
            }
            $qry = "SELECT id FROM notifications WHERE name = 'UPDATEDJOBSHEET' AND userId = " . $id;
            $result = dbQuery($dbconn, $qry);
            if (!$result) { return $dbErrorMsg; }
            if (mysql_num_rows($result) == 0) {
                $qry = "INSERT INTO notifications (name, userId, mode) VALUES (";
                $qry .= "'UPDATEDJOBSHEET', " . $id . ", '" . $_POST['ujsMode'] . "')";
                if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
            } else {
                $qry = "UPDATE notifications SET mode = '" . $_POST['ujsMode'] . "' WHERE name = 'UPDATEDJOBSHEET' AND userId = " . $id;
                if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
            }
            $qry = "SELECT id FROM notifications WHERE name = 'COMPLETEDJOBSHEET' AND userId = " . $id;
            $result = dbQuery($dbconn, $qry);
            if (!$result) { return $dbErrorMsg; }
            if (mysql_num_rows($result) == 0) {
                $qry = "INSERT INTO notifications (name, userId, mode) VALUES (";
                $qry .= "'COMPLETEDJOBSHEET', " . $id . ", '" . $_POST['cjsMode'] . "')";
                if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
            } else {
                $qry = "UPDATE notifications SET mode = '" . $_POST['cjsMode'] . "' WHERE name = 'COMPLETEDJOBSHEET' AND userId = " . $id;
                if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
            }
            $qry = "SELECT id FROM notifications WHERE name = 'NEWMOBILENOTE' AND userId = " . $id;
            $result = dbQuery($dbconn, $qry);
            if (!$result) { return $dbErrorMsg; }
            if (mysql_num_rows($result) == 0) {
                $qry = "INSERT INTO notifications (name, userId, mode) VALUES (";
                $qry .= "'NEWMOBILENOTE', " . $id . ", '" . $_POST['nmnMode'] . "')";
                if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
            } else {
                $qry = "UPDATE notifications SET mode = '" . $_POST['nmnMode'] . "' WHERE name = 'NEWMOBILENOTE' AND userId = " . $id;
                if (!dbQuery($dbconn, $qry)) { return $dbErrorMsg; }
            }
        }
        $json = '{"status":"OK", "msg":"User details updated"}';
    } else {
        $json = '{"status":"Failed", "msg":"Failed to update user data:<br>(' . mysql_error() . ')"}';
    }
//error_log($json);
    return $json;
}

function deleteUser($id) {
global $dbconn,  $dbErrorMsg;
    $qry = "DELETE FROM users WHERE id = " . $id;
	if (dbQuery($dbconn, $qry)) {
        return ('{"status":"OK"}');
    } else {
        return $dbErrorMsg;
    }
}
/******************************************************************************
   *********************** End of USER functions ***************************
 ******************************************************************************/

function getJobsheetList($s=0, $o=0, $b=0) {  // Site = limit to Site 's',  $o = 1 -> Open only, $o -> 0 All,  $o = -1 -> Closed Only, $b = boatId
global $dbconn,  $dbErrorMsg;

    $qry = "SELECT jobsheets.id as jsId, jobsheets.created as jobsheetsCreated, jobsheets.scheduledDate as scheduledDate, ";
    $qry .= "CONCAT(customers.title, ' ', customers.firstname, ' ' ,customers.lastname) as customerName, ";
    $qry .= "boats.name as boatName, boats.inwater as boatInwater, boats.state as boatState, ";
    $qry .= "sites.name as siteName, ";
    $qry .= "jobsheets.stage as jobsheetStage, ";
    $qry .= "stage.name as stageName, ";
    $qry .= "stage.emoji as stageEmoji ";
    $qry .= "FROM jobsheets, stage, customers, boats, sites ";
    $qry .= "WHERE boats.id = jobsheets.boatId ";
    $qry .= "AND customers.id = jobsheets.customerId ";
    $qry .= "AND sites.id = boats.siteId ";
    $qry .= "AND stage.id = jobsheets.stage ";
    if ($o == 1) { $qry .= "AND jobsheets.stage <> 5 "; }
    if ($o == -1) { $qry .= "AND jobsheets.stage == 5 "; }
    if ($s <> 0) { $qry .= "AND sites.id = " . $s . " "; }
    if ($b <> 0) { $qry .= "AND jobsheets.boatId = " . $b . " "; }
    $qry .= "ORDER BY jobsheets.created DESC";
error_log($qry);
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
//    $today = date("Y") . "-" . date("m") . "-" . date("d");
//error_log("Today = [" . $today . "]");
	$json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
	while ($row = mysql_fetch_assoc($result)) {
		$json .= "{";
            $json .= '"jsId":"' .  $row['jsId']  . '",';
			$json .= '"created":"' .  $row['jobsheetsCreated']  . '",';
			$json .= '"customer":"' . $row['customerName'] . '",';
			$json .= '"boat":"' . $row['boatName'] . '",';
			$json .= '"boatStatus":"' . ((($row['jobsheetStage'] == "Closed")?0:16) + $row['boatInwater'] + $row['boatState']) . '",';
			$json .= '"site":"' . $row['siteName'] . '",';

            if (($row['jobsheetStage'] == 2) && (strtotime($row['scheduledDate']) <= time())) { // If this is scheduled job we need to see if it's 'underway'.
                // Underway- So we overide the stage
                $json .= '"stage":"3",';
                $json .= '"stageName":"Underway",';
                $json .= '"stageEmoji":"1F527"';
            } else {
                $json .= '"stage":"' . $row['jobsheetStage'] . '",';
                $json .= '"stageName":"' . $row['stageName'] . '",';
                $json .= '"stageEmoji":"' . $row['stageEmoji'] . '"';                
            }
		$json .= "},";
	}
	if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
	$json .= ']}';
//error_log($json);
	return $json;
}

function getJobsheetTable() { 
	global $dbconn,  $dbErrorMsg;
	$qry = "SELECT * FROM jobsheets";
  $result = dbQuery($dbconn, $qry);
	if (!$result) { return $dbErrorMsg; }
	$json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
	while ($row = mysql_fetch_assoc($result)) {
		$json .= "{";
			$json .= '"id":"' .  $row['id']  . '",';
			$json .= '"siteId":"' .  $row['siteId']  . '",';
			$json .= '"customerId":"' . $row['customerId'] . '",';
			$json .= '"boatId":"' . $row['boatId'] . '",';
			$json .= '"scheduledDate":"' . $row['scheduledDate'] . '",';
			$json .= '"completedDate":"' . $row['completedDate'] . '",';
			$json .= '"description":"' . $row['description'] . '",';
			$json .= '"parts":"' . $row['parts'] . '",';
			$json .= '"labour":"' . $row['labour'] . '",';
			$json .= '"notes":"' . $row['notes'] . '",';
			$json .= '"stage":"' . $row['stage'] . '",';
			$json .= '"created":"' . $row['created'] . '",';
			$json .= '"updated":"' . $row['updated'] . '",';
			$json .= '"updateBy":"' . $row['updatedBy'] . '"';
		$json .= "},";
	}
	if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
	$json .= ']}';
	return $json;
}

function getJobsheet($id) {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT jobsheets.id as jsId, jobsheets.scheduledDate, jobsheets.completedDate, jobsheets.closedDate, jobsheets.description, jobsheets.parts, jobsheets.labour, jobsheets.notes, jobsheets.created as jobsheetsCreated, jobsheets.stage as jobsheetStage, jobsheets.updatedBy as jobsheetUpdatedBy, ";
    $qry .= "CONCAT(customers.title, ' ', customers.firstname, ' ' ,customers.lastname) as customerName, customers.id as custId, ";
    $qry .= "boats.id as boatId, boats.name as boatName, sites.name as siteName, ";
    $qry .= "jobsheets.stage as jobsheetStage, ";
    $qry .= "stage.name as stageName, ";
    $qry .= "stage.emoji as stageEmoji ";
    $qry .= "FROM jobsheets, customers, boats, sites, stage ";
    $qry .= "WHERE jobsheets.id = " . $id . " ";
    $qry .= "AND boats.id = jobsheets.boatId ";
    $qry .= "AND customers.id = boats.customerId ";
    $qry .= "AND sites.id = boats.siteId ";
    $qry .= "AND stage.id = jobsheets.stage ";
    $qry .= "ORDER BY jobsheetsCreated";
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
	if (mysql_num_rows($result) == 0) {
		$json = '{"resultcount":"0"}';
	} else {
		$row = mysql_fetch_assoc($result);
		$json = '{"resultcount":"' . mysql_num_rows($result)  . '", ';
		$json .= '"id":"' . $row['jsId'] . '", ';
        $json .= '"custId":"' . $row['custId'] . '",';
        $json .= '"customer":"' . $row['customerName'] . '",';
        $json .= '"boatId":"' . $row['boatId'] . '",';
        $json .= '"boat":"' . $row['boatName'] . '",';
        $json .= '"site":"' . $row['siteName'] . '",';
        $json .= '"description":"' . $row['description'] . '",';
        $json .= '"parts":"' . $row['parts'] . '",';
        $json .= '"labour":"' . $row['labour'] . '",';
        $json .= '"notes":"' . $row['notes'] . '",';
		$json .= '"created":"' .  $row['jobsheetsCreated']  . '",';
        $json .= '"scheduledDate":"' . $row['scheduledDate'] . '", ';
        $json .= '"completedDate":"' . $row['completedDate'] . '", ';
        $json .= '"closedDate":"' . $row['closedDate'] . '", ';

        if (($row['jobsheetStage'] == 2) && (strtotime($row['scheduledDate']) <= time())) { // If this is scheduled job we need to see if it's 'underway'.
            // Underway - So we overide the stage
            $json .= '"stage":"3",';
            $json .= '"stageName":"Underway",';
            $json .= '"stageEmoji":"1F527",';
        } else {
            $json .= '"stage":"' . $row['jobsheetStage'] . '",';
            $json .= '"stageName":"' . $row['stageName'] . '",';
            $json .= '"stageEmoji":"' . $row['stageEmoji'] . '",';                
        }

        $json .= '"updatedBy":"' . $row['jobsheetUpdatedBy'] . '"}';
    }
    echo ($json);
//error_log($json);
}

function saveJobsheet() {  // If id = 0 it's a new Job Sheet
global $dbconn,  $dbErrorMsg;
//error_log("saveJobSheet");
    if ($_POST['id'] < 1) {
        $qry = "INSERT INTO jobsheets (customerId, boatId, scheduledDate, description, parts, labour, notes, stage, created, updatedBy ) ";
        $qry .= "VALUES (";
        $qry .= $_POST['customerId'] . ", " . $_POST['boatId'] . ", '" . datetimeStrToDate($_POST['scheduled']) . "', '" . $_POST['description'] . "', '" . $_POST['parts'] . "', '" . $_POST['labour'] . "', '" . $_POST['notes'] . "', '" . $_POST['stage'] . "', '". date('Y-m-d H:i:s') . "', '" . $_POST['updatedBy'] . "' ";
        $qry .= ")";
    } else {
        $qry = "UPDATE jobsheets SET ";
        $qry .= "customerId = " . $_POST['customerId'] . ", ";
        $qry .= "boatId = ". $_POST['boatId'] . ", ";
        $qry .= "scheduledDate= '". datetimeStrToDate($_POST['scheduled']) . "', ";
        $qry .= "completedDate= '". datetimeStrToDate($_POST['completed']) . "', ";
        $qry .= "closedDate= '". datetimeStrToDate($_POST['closed']) . "', ";
        $qry .= "description = '". $_POST['description'] . "', ";
        $qry .= "parts = '". $_POST['parts'] . "', ";
//error_log($_POST['parts']);
        $qry .= "labour = '". $_POST['labour'] . "', ";
//error_log($_POST['labour']);
        $qry .= "notes = '". $_POST['notes'] . "', ";
        $qry .= "stage = '". $_POST['stage'] . "', ";
        $qry .= "updated = '". date('Y-m-d H:i:s') . "', ";
        $qry .= "updatedBy = '". $_POST['updatedBy'] . "' ";
        $qry .= "WHERE id = " . $_POST['id'];
    }
//error_log("QRY = " . $qry);
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
    $id = (($_POST['id'] < 1)?mysql_insert_id():$_POST['id']);
    if ($result) {
        $qry = "UPDATE boats SET inwater = " . $_POST['inwater'] . ", state = " . $_POST['state'] . " WHERE id = " . $_POST['boatId'];
        if (dbQuery($dbconn, $qry)) {
            return ('{"status":"OK","msg":"Job Sheets updated.", "id":' . $id . '}');
        } else {
            return $dbErrorMsg;
        }
    } else {
        return ($dbErrorMsg);
    }
}

function deleteJobsheet($id) {
global $dbconn,  $dbErrorMsg;
	$qry = "DELETE FROM jobsheets WHERE id = " . $id;
	if (dbQuery($dbconn, $qry)) {
        return ('{"status":"OK"}');
    } else {
        return ($dbErrorMsg);
    }
}

function getSchedule() {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT jobsheets.id as id, boats.name as name, jobsheets.scheduledDate as scheduleDate, jobsheets.stage as stage, stage.name as stageName, stage.emoji as emoji ";
    $qry .= "FROM jobsheets, boats, stage ";
    $qry .= "WHERE scheduledDate != '0000-00-00' AND boats.id = jobsheets.boatId AND stage.id = jobsheets.stage";
    $result = dbQuery($dbconn, $qry);
    if (!$result) {
        return $dbErrorMsg;
    } else {
        $json = '{"status":"OK", "resultcount":"' . mysql_num_rows($result)  . '", "results":[';
        while ($row = mysql_fetch_assoc($result)) {
            $json .= "{"; 
            $json .= '"id":"' .  $row['id']  . '",';
            $json .= '"name":"' .  $row['name']  . '",';
            $json .= '"date":"' .  $row['scheduleDate']  . '",';
            $json .= '"stage":"' .  $row['stage']  . '",';
            $json .= '"stageName":"' .  $row['stageName']  . '",';
            $json .= '"emoji":"' .  $row['emoji']  . '"';
            $json .= "}, ";
        }
        if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
        $json .= ']}';
    }
error_log($json);
    return $json;
}

function getStats() {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT sites.id, sites.name FROM sites";
    $result = dbQuery($dbconn, $qry);
    if (!$result) {
        return $dbErrorMsg;
    } else {
        $json = '{"status":"OK", "resultcount":"' . mysql_num_rows($result)  . '", "results":[';
        while ($row = mysql_fetch_assoc($result)) {
            $json .= "{";
                $json .= '"id":"' .  $row['id']  . '",';
                $json .= '"name":"' .  $row['name']  . '",';
                $qry = "SELECT id FROM customers as ccount WHERE siteId = " . $row['id'];
                $res1 = dbQuery($dbconn, $qry);
                if (!$res1) { return ($dbErrorMsg); }
                $json .= '"ccount":"' . mysql_num_rows($res1) . '",';
                $qry = "SELECT id FROM boats as bcount WHERE siteId = " . $row['id'];
                $res2 = dbQuery($dbconn, $qry);
                if (!$res2) { return ($dbErrorMsg); }
                $json .= '"bcount":"' . mysql_num_rows($res2) . '",';
                $qry = "SELECT id FROM jobsheets ";
                $qry .= "WHERE stage <> 'Closed' ";
                $qry .= "AND boatId IN (SELECT boats.id FROM sites, boats WHERE boats.siteId = " . $row['id'] . ")";
//error_log($qry);
                $res3 = dbQuery($dbconn, $qry);
                if (!$res3) { return ($dbErrorMsg); }
                $json .= '"jscount":"' . mysql_num_rows($res3) . '"';
            $json .= "}, ";
        }
        if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
        $json .= ']}';
    }
//error_log($json);
    return $json;
}

// *********************************************************
function getStageTable() {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT * FROM stage";
    $result = dbQuery($dbconn, $qry);
    if (!$result) {
        return $dbErrorMsg;
    } else {
        $json = '{"status":"OK", "resultcount":"' . mysql_num_rows($result)  . '", "results":[';
        while ($row = mysql_fetch_assoc($result)) {
            $json .= "{";
                $json .= '"id":"' .  $row['id']  . '",';
                $json .= '"name":"' .  $row['name']  . '",';					
                $json .= '"emoji":"' .  $row['emoji']  . '"';			
            $json .= "}, ";
        }
        if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
        $json .= ']}';
    }
//error_log($json);
    return $json;			
}

// *********************************************************
function getNoteTable() {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT * FROM notes";
    $result = dbQuery($dbconn, $qry);
    if (!$result) {
        return $dbErrorMsg;
    } else {
        $json = '{"status":"OK", "resultcount":"' . mysql_num_rows($result)  . '", "results":[';
        while ($row = mysql_fetch_assoc($result)) {
            $json .= "{";
                $json .= '"id":"' .  $row['id']  . '",';
                $json .= '"parent":"' .  $row['parent']  . '",';			
                $json .= '"parentRecId":"' .  $row['parentRecId']  . '",';			
                $json .= '"timestamp":"' .  $row['timestamp']  . '",';			
                $json .= '"note":"' .  $row['note']  . '",';			
                $json .= '"source":"' .  $row['source']  . '",';			
                $json .= '"user":"' .  $row['user']  . '"';			
            $json .= "}, ";
        }
        if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
        $json .= ']}';
    }
//error_log($json);
    return $json;			
}

function getMoreNotes($parent, $parentRecId) {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT * FROM notes WHERE parent = '" . $parent . "' AND parentRecId = " . $parentRecId;
    $result = dbQuery($dbconn, $qry);
    if (!$result) {
        return $dbErrorMsg;
    } else {
        $json = '{"status":"OK", "resultcount":"' . mysql_num_rows($result)  . '", "results":[';
        while ($row = mysql_fetch_assoc($result)) {
            $json .= "{";
                $json .= '"id":"' .  $row['id']  . '",';
                $json .= '"parent":"' .  $row['parent']  . '",';			
                $json .= '"parentRecId":"' .  $row['parentRecId']  . '",';			
                $json .= '"timestamp":"' .  $row['timestamp']  . '",';			
                $json .= '"note":"' .  $row['note']  . '",';			
                $json .= '"source":"' .  $row['source']  . '",';			
                $json .= '"user":"' .  $row['user']  . '"';			
            $json .= "}, ";
        }
        if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
        $json .= ']}';
    }
//error_log("geMoreNotes [" . $json . "]");
    return $json;    
}

function importNotesTable(){
	global $dbconn,  $dbErrorMsg;

    $notes = $_POST['notes'];

    $json = json_decode($notes,true);

    foreach ($json[notes] as $note) {
		$qry = "INSERT INTO notes ";
		$qry .= "(parent, parentRecId, timestamp, note, source, user) ";
		$qry .= "VALUES ('" . $note[parent] ."', '" . $note[parentRecId] . "', '" . $note[timestamp] . "','" . $note[note] . "', '" . $note[source] . "', '" . $note[user] . "')";
		$result = dbQuery($dbconn, $qry);
        notify("NEWMOBILENOTE", "Mobile: " + $note[user], $note[parent], $note[note]);
    }
	return '{"status":"OK", "resultcount":"' . $json['notecount'] . '"}';
}

function updateCompletesdJS() {
	global $dbconn,  $dbErrorMsg;

    $completed = $_POST['completedJS'];
/*
{
    "completecount": "1",
    "completed": [
        {
            "jobsheetId": "16",
            "userName": "Spencer",
            "timestamp": "2017-09-17"
        }
    
*/
    $json = json_decode($completed, true);
    
    foreach ($json[completed] as $completedJobsheet) {
        $qry = "UPDATE jobsheets SET stage = 4, completedDate = '" . $completedJobsheet['timestamp'] . "' WHERE id = " . $completedJobsheet['jobsheetId'];
//error_log("Completed [" . $qry . "]");
		$result = dbQuery($dbconn, $qry);
    }
	return '{"status":"OK", "resultcount":"' . $json['completecount'] . '"}';
}

// *********************************************************
function getUserNotifications($uid) {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT * FROM notifications WHERE userId = " . $uid;
//error_log($qry);
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
	$json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
	while ($row = mysql_fetch_assoc($result)) {
		$json .= "{";
            $json .= '"id":"' . $row['id'] . '", ';
            $json .= '"name":"' . $row['name'] . '",';
            $json .= '"userId":"' . $row['userId'] . '",';
            $json .= '"mode":"' . $row['mode'] . '"';
		$json .= "},";
	}
	if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
	$json .= ']}';
//error_log($json);
	return $json;
}

function notify($notification, $from, $title, $msg) {  // Notify Called (from = jon, title = New Job Sheet, msg = Boat: Allegra Job: boo http://45y.co.uk/dlm/jobsheets.php?js=81)
global $dbconn,  $dbErrorMsg;
    
    $notifications = 0;
//error_log("Notify Called (notify = [$notification], from = $from, title = $title, msg = $msg)");
    $qry = "SELECT * FROM notifications WHERE name = '" . $notification . "'";
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
//error_log("result count " . mysql_num_rows($result));
    while ($row = mysql_fetch_assoc($result)) {
        // Get user details
        $qry = "SELECT * FROM users WHERE id = " . $row['userId'];
        $res1 = dbQuery($dbconn, $qry);
        if (!$res1) { return $dbErrorMsg; }
//error_log("user count " . mysql_num_rows($result));
        $row1 = mysql_fetch_assoc($res1);
        if ($row1['name'] != $from) { // Don't end notification to author of notification
            if ($row['mode'] == "EMAIL") {
//error_log("Sending email");
                $r = sendEmail($row1['email'], "dlm@drivelinemarine.com", $title, $msg);
//error_log("Email response [" . $r . "]");
                $qry = "INSERT INTO commslog ";
                $qry .= "(commstype, author, target, sendto, msg) ";
                $qry .= "VALUES ('EMail', '" . $from . "', '" . $row1['name'] . "','" . $row1['email'] . "', '" . mysql_real_escape_string($title) . ": " . mysql_real_escape_string($msg) . "')";
                $result = dbQuery($dbconn, $qry);
                if (!$result) { return $dbErrorMsg; }
                $notifications += 1;
            } else if ($row['mode'] == "SMS") {
//error_log("Sending email");
                $r = sendSMS($row1['mobile'], "Driveline", $title, $msg);
                $qry = "INSERT INTO commslog ";
                $qry .= "(commstype, author, target, sendto, msg) ";
                $qry .= "VALUES ('SMS', '" . $from . "', '" . $row1['name'] . "','" . $row1['mobile'] . "', '" . mysql_real_escape_string($title) . ": " . mysql_real_escape_string($msg) . "')";
                $result = dbQuery($dbconn, $qry);
                if (!$result) { return $dbErrorMsg; }
                $notifications += 1;
            }
        }
    }
    $r = '{"status":"' . $notifications .  ' Notifications Sent"}';
//error_log($r);
    return($r);
}


/*
    sendSMSandLog   Used to send random SMS from SMS popup
*/
function sendSMSandLog($user, $addressee, $to, $subject, $msg) { // sendSMSandLog($_GET['user'], $_POST['addressee'], $_POST['sendTo'], $_POST['subject'], $_POST['msg']);
global $dbconn,  $dbErrorMsg;
    $r = sendSMS($to, "Driveline", $subject, $msg);
//$r = '{"status":"OK", "msg":"SMS Sent"}';

    $qry = "INSERT INTO commslog ";
    $qry .= "(commstype, author, target, sendto, msg) ";
    $qry .= "VALUES ('SMS', '" . $user . "', '" . $addressee . "','" . $to . "', '" . mysql_real_escape_string($subject) . ": " . mysql_real_escape_string($_POST['msg']) . "')";
//error_log("3-" . $qry);
    $result = dbQuery($dbconn, $qry);
    return $r;
}

function getCommsList($to=0) {
global $dbconn,  $dbErrorMsg;
    $qry = "SELECT commslog.* FROM commslog ";
    if ($to != 0) { $qry .= "WHERE commslog.target = " . $to . " "; }
    $qry .= "ORDER BY commslog.timestamp, commslog.target";
//error_log($qry);
    $result = dbQuery($dbconn, $qry);
    if (!$result) { return $dbErrorMsg; }
	$json = '{"status":"OK", "resultcount":"' . mysql_num_rows($result)  . '", "results":[';
	while ($row = mysql_fetch_assoc($result)) {
		$json .= "{";
            $json .= '"id":"' . $row['id'] . '", ';
            $json .= '"commstype":"' . $row['commstype'] . '",';
            $json .= '"target":"' . $row['target'] . '",';
            $json .= '"sendto":"' . $row['sendto'] . '",';
            $json .= '"author":"' . $row['author'] . '",';
            $json .= '"msg":' . json_encode($row['msg']) . ',';
            $json .= '"timestamp":"' . $row['timestamp'] . '"';
		$json .= "},";
	}
	if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
	$json .= ']}';
//error_log($json);
	return $json;
}

function getPhotoList($parent, $parentId) {
global $dbconn,  $dbErrorMsg;
    $found = 0;
    if ($parent == "jobsheets") { // get Boat name
        $qry = "SELECT name FROM boats, jobsheets WHERE ";
        $qry .= "jobsheets.id = " . $parentId . " ";
        $qry .= "AND boats.id = jobsheets.boatId";
//error_log($qry);
        $result = dbQuery($dbconn, $qry);
        if (!$result) { return $dbErrorMsg; }
        $row = mysql_fetch_assoc($result);
        $n = $row['name'];
//error_log($row['name']);
    } else {
        $n = "General";
    }
    $json = '{"status":"OK", "resultcount":"0", "name":"' . $n . '", "results":[';
    $files = glob('uploads/*.jpg');
    foreach ($files as $file) {   // uploads/DLMjobsheets-17-161021103301.jpg
        $pmatch = substr($file,11,strlen($parent));
        $imatch = substr($file,11+strlen($parent)+1, strlen($parentId));

        if (($pmatch == $parent) && ($imatch == $parentId)) {
            $found++;
            $latlon = getPhotoLatLon($file);
            $t = substr($file,11+strlen($parent)+1+strlen($parentId) + 1);
//error_log("t=[" . $t ."]");
            $ts = "20".substr($t,0,2)."-".substr($t,2,2)."-".substr($t,4,2)." ".substr($t, 6,2).":".substr($t,8,2).":".substr($t,10,2);
//error_log($timestamp);
            $json .= '{"image":"' . $file . '","latlon":"' . $latlon . '","timestamp":"' . $ts . '"},';
        }
    }
	if ($found > 0) {
        $json = trim($json, ", ");
        $json = str_replace('"resultcount":"0"','"resultcount":"' . $found . '"',$json);
    }
	$json .= ']}';
	return $json;
}

function getPhotoLatLon($file) {
    if (is_file($file)) {
        $info = exif_read_data($file);
        if ($info !== false) {
            $direction = array('N', 'S', 'E', 'W');
            if (isset($info['GPSLatitude'], $info['GPSLongitude'], $info['GPSLatitudeRef'], $info['GPSLongitudeRef']) &&
                in_array($info['GPSLatitudeRef'], $direction) && in_array($info['GPSLongitudeRef'], $direction)) {

                $lat_degrees_a = explode('/',$info['GPSLatitude'][0]);
                $lat_minutes_a = explode('/',$info['GPSLatitude'][1]);
                $lat_seconds_a = explode('/',$info['GPSLatitude'][2]);
                $lng_degrees_a = explode('/',$info['GPSLongitude'][0]);
                $lng_minutes_a = explode('/',$info['GPSLongitude'][1]);
                $lng_seconds_a = explode('/',$info['GPSLongitude'][2]);

                $lat_degrees = $lat_degrees_a[0] / $lat_degrees_a[1];
                $lat_minutes = $lat_minutes_a[0] / $lat_minutes_a[1];
                $lat_seconds = $lat_seconds_a[0] / $lat_seconds_a[1];
                $lng_degrees = $lng_degrees_a[0] / $lng_degrees_a[1];
                $lng_minutes = $lng_minutes_a[0] / $lng_minutes_a[1];
                $lng_seconds = $lng_seconds_a[0] / $lng_seconds_a[1];

                $lat = (float) $lat_degrees + ((($lat_minutes * 60) + ($lat_seconds)) / 3600);
                $lng = (float) $lng_degrees + ((($lng_minutes * 60) + ($lng_seconds)) / 3600);
                $lat = number_format($lat, 7);
                $lng = number_format($lng, 7);

                $lat = $info['GPSLatitudeRef'] == 'S' ? $lat * -1 : $lat;  //If the latitude is South, make it negative.
                $lng = $info['GPSLongitudeRef'] == 'W' ? $lng * -1 : $lng; //If the longitude is west, make it negative

                return $lat . ", " . $lng;
            }
        }
    }
    return "";
}

function boatsForSticky($site, $sortby) {
global $dbconn;

	$qry = "SELECT boats.id as boatid, boats.name as bname, boats.make as bmake, CONCAT(customers.title, ' ', customers.firstName, ' ', customers.lastName) as cname ";
    $qry .= "FROM  boats, customers ";
    if ($site == "-1") {
        $qry .= "WHERE customers.id = boats.customerId ";
    } else {
        $qry .= "WHERE boats.siteId = " . $site . " ";
        $qry .= "AND customers.id = boats.customerId ";
    }
    $qry .= "ORDER BY boats.name";
	$result = dbQuery($dbconn, $qry);
	$json = '{"resultcount":"' . mysql_num_rows($result)  . '", "results":[';
	while ($row = mysql_fetch_assoc($result)) {
		$json .= '{';
			$json .= '"id":"' .  $row['boatid']  . '",';
			$json .= '"name":"' .  $row['bname']  . '",';
			$json .= '"make":"' .  $row['bmake']  . '",';
			$json .= '"customer":"' .  $row['cname']  . '"';
		$json .= '},';
	}
	if (mysql_num_rows($result) > 0) { $json = trim($json, ", "); }
	$json .= ']}';
	return $json;
}
?>