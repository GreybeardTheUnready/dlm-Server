<?php
require_once ("dlmUtils.php");
require_once ("generalUtils.php");

//error_log("1");
error_log("Dispatch called for " . $_SERVER['REQUEST_URI']);
//foreach ($_POST as $key => $value) {
//    error_log($key . ' has the value of ' . $value);
//}

switch ($_GET['f']) {
	case "login":
		echo login();
		break;
    case "newpw":
        echo updatePassword($_POST['username'], $_POST['password']);
        break;

// Sites ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  
	case "getSitesList":
		echo getSitesList();
		break;
	case "addSite":
		echo addSite();
		break;
	case "getSiteDetails":
		echo getSiteDetails($_GET['sid']);
		break;
	case "updateSite":
		echo updateSite($_GET['sid']);
		break;
	case "deleteSite":
		echo deleteSite($_GET['sid']);
		break;
    case "getSitesDropdown":
        echo getSitesDropdown();
        break;

//Customers ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	case "getCustomerList":
		echo getCustomerList($_GET['s']);
		break;
    case "getCustomerNames":
        echo getCustomerNames();
        break;
	case "getCustomerDetails":
		echo getCustomerDetails($_GET['cid']);
		break;
    case "getCustomerAddresses":
        echo getCustomerAddresses($_GET['siteId']);
        break;
	case "addCustomer":
		echo addCustomer();
		break;
	case "updateCustomer":
		echo updateCustomer($_GET['cid']);
		break;
	case "deleteCustomer":
		echo deleteCustomer($_GET['cid']);
		break;

//Boats ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	case "getBoatTable":
		echo getBoatTable();
		break;
	case "getBoatList":
		echo getBoatList($_GET['s']);
		break;
    case "getBoatNames":
        echo getBoatNames($_GET['cid']);
        break;
	case "getBoatDetails":
		echo getBoatDetails($_GET['bid']);
		break;
	case "addBoat":
		echo addBoat();
		break;
	case "updateBoat":
		echo updateBoat($_GET['id']);
		break;
	case "deleteBoat":
		echo deleteBoat($_GET['id']);
		break;
    case "addEngineToBoat":
        echo addEngineToBoat($_GET['boatId'], $_GET['engineId']);
        break;
    case "getBoatsByET":
        echo getBoatsByET($_GET['id']);
        break;
    case "getBoatsSI":
        echo getBoatsSI($_GET['id']);
        break;

//Engines ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	case "getEngineTable":
		echo getEngineTable();
		break;
	case "getEngineList":
//		echo getEngineList($_GET['bref'], $_GET['u']);
		echo getEngineList();
		break;
	case "getEngineDetails":
		echo getEngineDetails($_GET['eid']);
		break;
    case "updateEngine":
        echo updateEngine($_GET['eid']);
        break;
    case "deleteEngine":
        echo deleteEngine($_GET['eid']);
        break;
	case "getEngineMods":
		echo getEngineMods($_GET['eid']);
		break;
    case "addModToET":
        echo addModToET($_GET['et']);
        break;
//
//    case "addMod":
//        echo addMod($_GET['eid']);
//        break;
    case "deleteMod":
        echo deleteMod($_GET['id']);
        break;

//Engines Templates ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	case "getEngineTemplateTable":
		echo getEngineTemplateTable();
		break;
    case "getEngineTemplateList":
        echo getEngineTemplateList();
        break;
    case "getEngineTemplateDetails":
		echo getEngineTemplateDetails($_GET['eid']);
		break;
    case "addEngineTemplate":
        echo addEngineTemplate();
        break;
    case "updateEngineTemplate":
        echo updateEngineTemplate($_GET['id']);
        break;
    case "deleteEngineTemplate":
        echo deleteEngineTemplate($_GET['id']);
        break;

//Service Items ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    case "getServiceItemsList":
        echo getServiceItemsList();
        break;

    case "getServiceItemDetails":
        echo getServiceItemDetails($_GET['sid']);
        break;

    case "addServiceItem":
        echo addServiceItem();
        break;

    case "updateServiceItem":
        echo updateServiceItem($_GET['sid']);
        break;

    case "deleteServiceItem":
        echo deleteServiceItem($_GET['siId']);
        break;

    case "getSINamesDropdown":
        echo getSINamesDropdown();
        break;

    case "newSIName":
        echo newSIName($_GET['nsin'], $_GET['user']);
        break;

    case "deleteETSI":
        echo deleteETSI($_GET['si'], $_GET['et']);
        break;

    case "addSiToET":
        echo addSiToET($_GET['et']);
        break;

// Standard Jobs ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    case "getStandardJobsList":
        echo getStandardJobsList();
        break;

    case "getStandardJobDetails":
        echo getStandardJobDetails($_GET['sid']);
        break;

    case "addStandardJob":
        echo addStandardJob($_GET['id']);
        break;

    case "deleteStandardJob":
        echo deleteStandardJob($_GET['id']);
        break;

// Users ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	case "getUserList":
		echo getUserList();
		break;
	case "getUserDetails":
		echo getUserDetails($_GET['uid']);
		break;
	case "addUser":
		echo addUser($_GET['uid']);
		break;
	case"deleteUser":
		echo deleteUser($_GET['uid']);
		break;

// Job Sheets ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	case "getJobsheetTable":
        echo getJobsheetTable();
        break;
    case "getJobsheetList":
        echo getJobsheetList($_GET['s'], $_GET['o'], $_GET['b']);
        break;
    case "getJobsheet":
        echo getJobsheet($_GET['jsId']);
        break;
    case "saveJobsheet":
        echo saveJobsheet();
        break;
    case "deleteJobsheet":
        echo deleteJobsheet($_GET['id']);
        break;
    case "getSchedule":
        echo getSchedule();
        break;
        
// Stage ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	case "getStageTable":
		echo getStageTable();
		break;

// Notes ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	case "getNoteTable":
		echo getNoteTable();
		break;
	case "importNotesTable":
		echo importNotesTable();
		break;
    case "getMoreNotes":
        echo getMoreNotes($_GET['parent'], $_GET['parentRecId']);
        break;


// Other Stuff ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    case "updateCompletesdJS":
        echo updateCompletesdJS();
        break;

    case "getStats":
        echo getStats();
        break;

    case "notify":      // {"notification":"NEWJOBSHEET", "from":from, "title":"New Job Sheet", "msg":msg }
//error_log("notify");
        $nType = $_POST['notification'];
        $nFrom = $_POST['from'];
        $nTitle = $_POST['title'];
        $nMsg = $_POST['msg'];
        echo notify($nType, $nFrom, $nTitle, $nMsg);
        break;

    case "sendSMSandLog":
        echo sendSMSandLog($_GET['user'], $_POST['addressee'], $_POST['sendTo'], $_POST['subject'], $_POST['msg']);
        break;

    case "getUserNotifications":
        echo getUserNotifications($_GET['uid']);
        break;

    case "logSms":
		echo logSms($_GET['userId']);
		break;

    case "commsList":
		echo getCommsList($_GET['to']);
		break;

    case "getPhotoList":
        echo getPhotoList($_GET['parent'], $_GET['parentId']);
        break;
        
	case "boatsForSticky":
		echo boatsForSticky($_GET['s'], $_GET['sb']);
		break;

    case "getFileTimestamp":
        echo getFileTimestamp($_GET['fspec']);
        break;
        
    default:
		echo "<br>Unrecognised Request! [" . $_GET['f'] . "]<br>";
}

?>
