<!DOCTYPE html>
<html lang="en-gb">
<head>
	<?php require_once 'includes/baseHeader.html'; ?>
    <script type="text/javascript" src="js/jquery.qrcode.min.js"></script>

</head>

<body>
	<?php require_once 'includes/basePage.html'; ?>
<!--
    List, Add, Enable/Disable Users.
    LIst, Add, Edit, Delete Engine Templates.View
    LIst, Add Edit, Delete Service Items.View
    LIst, Add, Edit, Delete Boat Status.
    Review Comms Log.
    Review Logs.
-->

<!-- Page Specific Content Below -->
<script type="text/javascript">
    showLoading();
    $('#user').html($.cookie("user") + " (" + $.cookie("userType") + ")&nbsp;<a href='#' onclick='logout()'>Logout</a>");
    $('#Date').html(fullDateStr(new Date()));
    $('#pageHeader').css({backgroundColor:"#E73F3F", color:"#FFFFFF"});
    $('#pageTitleLeft').html("Adminstration");

    var content = "";

        content += "<div id='leftPanel' style='float:left; width:1025px;margin-top:20px'>";
        content += "<h2>Overview</h2>";
        content += "<table id='statsTable' width='100%'>";
        content += "<thead><tr><th>Site</th><th width='100px'>Customers</th><th width='100px'>Boats</th><th width='140px'>Open Jobsheets</td></tr></thead>";
        content += "</table>";
        content += "</div>";

content += "<br><div id='moreNotes'  style='float:left; width:1025px;margin-top:20px'/>";
    
        content += "<div id='rightPanel' style='float:right; min-height:500px; width:160px;margin:44px 0 0 0;'>";
        content += "<div id='mobileAPK' style='text-align:center'>&nbsp;</div>";
        content += "<button class='btn' style='width:150px' onclick='etMgr();'>Engine Templates</button><br>";
        content += "<button class='btn' style='width:150px' onclick='siMgr();'>Service Items</button><br>";
        content += "<button class='btn' style='width:150px' onclick='sjMgr();'>Standard Jobs</button><br>";
        content += "<button class='btn' style='width:150px' onclick='userMgr();'>Users</button><br>";
        content += "<button class='btn' style='width:150px' onclick='location.assign(\"schedule.php\");'>Jobsheet Calendar</button><br>";  // location.assign("http://www.mozilla.org");
        content += "<button class='btn' style='width:150px' onclick='commsLog();'>Comms Log</button><br>";
        content += "<button class='btn' style='width:150px' onclick='showNotes(\"general\",\"0\");'>General Notes</button><br>";
        content += "<button class='btn' style='width:150px' onclick='showPhotos(\"general\",\"0\");'>General Photos</button><br>";
        content += "<button class='btn' style='width:150px' onclick='createQRs();'>Create QR Codes</button><br>";
        content += "<button class='btn' style='width:150px' onclick='exportAddresses();'>Export Addresses</button><br>";
        content += "</div>"
        
        content += "<br class='clear' />";

    $('#pageContainer').html(content);
    
    $('#mobileAPK').qrcode({width: 75,height: 75, text: 'http://dlmwork.uk/clientkit/dlmMobile.apk' });
    $('#mobileAPK').append('<br><b>Mobile Kit</b><br><br>');

     $.post("dlmFunctionDispatch.php?f=getStats", function(data) {
         hideLoading();
         if (data.status == "OK") {
            for (var i = 0; i < data.resultcount; i++) {
                data.results[i].name = "<a onclick='showSite(" + data.results[i].id + ")'>" + safeDecode(data.results[i].name) + "</a>";
                data.results[i].ccount = "<a href='customers.php?s=" + data.results[i].id + "' >" + data.results[i].ccount + "</a>";
                data.results[i].bcount = "<a href='boats.php?s=" + data.results[i].id + "' >" + data.results[i].bcount + "</a>";
                data.results[i].jscount = "<a href='jobsheets.php?s=" + data.results[i].id + "&o=1&b=0' >" + data.results[i].jscount + "</a>";
            }
            $('#statsTable').dataTable( {
                "iDisplayLength": 10,
                "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                stateSave: true,
                data: data.results,
                "columns": [
                    {"data":"name"},
                    {"data":"ccount", "class":"center"},			
                    {"data":"bcount", "class":"center"},			
                    {"data":"jscount", "class":"center"},			
                ]
            });
        } else {
            myAlert(data.status, data.msg);
        }
     }, "json");
    
    var opt = getQueryString("opt");
    switch(opt) {
        case "etMgr":
            etMgr();
            break;
        case "siMgr":
            siMgr();
            break;
        case "sjMgr":
            sjMgr();
            break;
        case "userMgr":
            userMgr();
            break;
        case "commsLog":
            commsLog();
            break;
        case "mobileLog":
            mobileLog();
            break;
        case "createQRs":
            createQRs();
            break;
        default:
    }
    
function etMgr() {
    showLoading();
    var content = "";
        content += "<div id='etList' style='margin:4px 0 0 0;padding:4px;min-height:450px;'>";
        if ($.cookie("userType") == "Admin") {
            content += "<h3 style='text-align:right;margin-bottom:5px;'><a onclick='showEngineTemplate();' >Add Engine Template</a></h3>";
        }
        content += "<table id='etTable' class='display' width='100%' cellspacing='0'>";
        content += "<thead><tr><th>Make</th><th>Model</th><th width=70px'>Cylinders</th><th width=70px'>Capacity</th><th width=70px'>Fuel</th></tr></thead>";
        content += "</table>";
        content += "</div>";
    $('#leftPanel').html(content);
    $.post("dlmFunctionDispatch.php?f=getEngineTemplateList", function(data) {
        hideLoading();
        if (data.resultcount > 0) {
            for (var i = 0; i < data.resultcount; i++) {           
                data.results[i].make = "<a onclick='showEngineTemplate(" + data.results[i].id + ");'>" + safeDecode(data.results[i].make) + "</a>";
                data.results[i].model = safeDecode(data.results[i].model);
            }
            $('#etTable').dataTable( {
                "iDisplayLength": 10,
                "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                stateSave: true,
                data: data.results,
                "columns": [
                    {"data":"make", "class":"left"},
                    {"data":"model", "class":"left"},			
                    {"data":"cylinders", "class":"right"},
                    {"data":"capacity", "class":"right"},
                    {"data":"fuel", "class":"left"}
                ]
            });
        } else {
            $('#leftPanel').html("<h1>No Service Items found.</h1>");
        }
    },"json");
}

function siMgr() {
    showLoading();
    var content = "";
        content += "<div id='siList' style='margin:4px 0 0 0;padding:4px;min-height:450px;'>";
        if ($.cookie("userType") == "Admin") {
            content += "<h3 style='text-align:right;margin-bottom:5px;'><a onclick='showServiceItem();' >Add Service Item</a></h3>";
        }
        content += "<table id='mainsiTable' class='display' width='100%' cellspacing='0'>";
        content += "<thead><tr><th>Name</th><th>Make</th><th width='175px'>PartNo.</th><th width='110px'>Price (&pound;)</th></tr></thead>";
        content += "</table>";
        content += "</div>";
    $('#leftPanel').html(content);
    $.post("dlmFunctionDispatch.php?f=getServiceItemsList", function(data) {
        hideLoading();
        if (data.resultcount > 0) {
            for (var i = 0; i < data.resultcount; i++) {           
                data.results[i].name = "<a onclick='showServiceItem(" + data.results[i].siId + ");'>" + safeDecode(data.results[i].name) + "</a>";
                data.results[i].make = safeDecode(data.results[i].make);
                data.results[i].partno = safeDecode(data.results[i].partno);
            }
            $('#mainsiTable').dataTable( {
                "iDisplayLength": 10,
                "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                stateSave: true,
                data: data.results,
                "columns": [
                    {"data":"name", "class":"left"},
                    {"data":"make", "class":"left"},			
                    {"data":"partno", "class":"right"},			
                    {"data":"price", "class":"right"}
                ]
            });
        } else {
            $('#leftPanel').html("<h1>No Service Items found.</h1>");
        }
    },"json");
}

function sjMgr() {
    showLoading();
    var content = "";
        content += "<div id='sjList' style='margin:4px 0 0 0;padding:4px;min-height:450px;'>";
        if ($.cookie("userType") == "Admin") {
            content += "<h3 style='text-align:right;margin-bottom:5px;'><a onclick='showStandardJob(0);' >Add Standard Job</a></h3>";
        }
        content += "<table id='mainsiTable' class='display' width='100%' cellspacing='0'>";
        content += "<thead><tr><th>Description</th><th width='110px'>Price (&pound;)</th></tr></thead>";
        content += "</table>";
        content += "</div>";
    $('#leftPanel').html(content);
    $.post("dlmFunctionDispatch.php?f=getStandardJobsList", function(data) {
        hideLoading();
//        if (data.resultcount > 0) {
            for (var i = 0; i < data.resultcount; i++) {           
                data.results[i].description = "<a onclick='showStandardJob(" + data.results[i].id + ");'>" + safeDecode(data.results[i].description) + "</a>";
                data.results[i].price = numberStringToCurrencyString(data.results[i].price);
                if (data.results[i].LOAmultiplier == 1) { data.results[i].price = data.results[i].price + " per ft"; }
            }
            $('#mainsiTable').dataTable( {
                "iDisplayLength": 10,
                "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                stateSave: true,
                data: data.results,
                "columns": [
                    {"data":"description", "class":"left"},
                    {"data":"price", "class":"right"}
                ]
            });
//        } else {
//            $('#leftPanel').html("<h1>No Standard Jobs found.</h1>");
//        }
    },"json");
}

function userMgr() {
    showLoading();
    var content = "";
        content += "<div id='userList' style='margin:4px 0 0 0;padding:4px;min-height:450px;'>";
        if ($.cookie("userType") == "Admin") {
            content += "<h3 style='text-align:right;margin-bottom:5px;'><a onclick='showUser();' >Add User</a></h3>";
        }
        content += "<table id='mainsiTable' class='display' width='100%' cellspacing='0'>";
        content += "<thead><tr><th>Username</th><th>Type</th><th>Status</th></tr></thead>";
        content += "</table>";
        content += "</div>";
    $('#leftPanel').html(content);
    $.post("dlmFunctionDispatch.php?f=getUserList", function(data) {
        hideLoading();
        if (data.resultcount > 0) {
            for (var i = 0; i < data.resultcount; i++) {           
                data.results[i].username = "<a onclick='showUser(" + data.results[i].uId + ");'>" + safeDecode(data.results[i].username) + "</a>";
            }
            $('#mainsiTable').dataTable( {
                "iDisplayLength": 10,
                "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                stateSave: true,
                data: data.results,
                "columns": [
                    {"data":"username", "class":"left"},
                    {"data":"type", "class":"left"},			
                    {"data":"status", "class":"left"}
                ]
            });
        } else {
            $('#leftPanel').html("<h1>No Users found.</h1>");
        }
    },"json");   
}
    
function commsLog() {
    showLoading();
    var content = "";
        content += "<div id='commsList' style='margin:0;padding:4px;min-height:450px;'>";
        content += "<h3>Communications Log</h3>";
        content += "<table id='commsTable' class='display' width='100%' cellspacing='0'>";
        content += "<thead><tr><th width='195px'>Addressee</th><th width='75px'>Method</th><th width='175px'>Sent To</th><th width='165px'>Timestamp</th><th>Message</th><th width='75px'>Sent By</th></tr></thead>";
        content += "</table>";
        content += "</div>";
    $('#leftPanel').html(content);
    $.post("dlmFunctionDispatch.php?f=commsList", function(data) {
        hideLoading();
        if (data.status == "OK") {
            if (data.resultcount > 0) {
                for (var i = 0; i < data.resultcount; i++) {           
                    data.results[i].msg = safeDecode(data.results[i].msg);
                }
                $('#commsTable').dataTable( {
                    "iDisplayLength": 10,
                    "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    stateSave: true,
                    data: data.results,
                    "columns": [
                        {"data":"target", "class":"left"},
                        {"data":"commstype", "class":"left"},			
                        {"data":"sendto", "class":"left"},			
                        {"data":"timestamp", "class":"left"},			
                        {"data":"msg", "class":"left"},			
                        {"data":"author", "class":"left"}
                    ]
                });
            } else {
                $('#leftPanel').html("<h1>No Messages found.</h1>");
            }
        } else {
            myAlert(data.status, data.msg);
        }
    },"json");   
}

function mobileLog(){
    myAlert("TBD", "Mobile Log will go here.");
}
    
function createQRs() {
	qrsSelected = 0;  // Make sure we start with nothing selected.
	var pe ="Select Boats for Printing<br>";
	pe += "<span style='font-size:0.75em;color:#444444;'>(NB: Leave Site selector 'blank' to select all sites.)</span><br><br>";
//	pe += "<table>";
//	pe += "<tr><td style='padding:5px;'>Site: </td><td id='qrsite'>Loading...</td></tr>";
//	pe += "</table>";
	pe += "Site: <span id='qrsite'>Loading...</span>";
	pe += "<div style='margin-top:60px;text-align:right;'><button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Cancel</button>&nbsp;&nbsp;";
	pe += "<button class='btn' onclick='showMetersForQR();'>List Boats</button></div>";
	displayResults('dialog', "Print QR Code Labels", pe);
    $.post("dlmFunctionDispatch.php?f=getSitesDropdown", function(data) {
        var sel = buildSelect("selectSite", data);
        $('#qrsite').html(sel);
    }, "json");
}

function showMetersForQR() {
	var s = $('#selectSite').val();
	if (s == 'blank') { s = "ALL"; }
//	var sb = $('#sort').val();
	$("#dialog").dialog("close");
	location.href="stickers.php?s="+s;
}

function exportAddresses() {
	var pe ="Select Site<br>";
	pe += "<span style='font-size:0.75em;color:#444444;'>(NB: Leave Site selector 'blank' to select all sites.)</span><br><br>";
	pe += "Site: <span id='addrList'>Loading...</span>";
	pe += "<div style='margin-top:60px;text-align:right;'><button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Cancel</button>&nbsp;&nbsp;";
	pe += "<button class='btn' onclick='exportAddr();'>Export</button></div>";
	displayResults('dialog', "Addresses", pe);
    $.post("dlmFunctionDispatch.php?f=getSitesDropdown", function(data) {
        var sel = buildSelect("selectSite", data);
        $('#addrList').html(sel);
    }, "json");
}

function exportAddr() {
    siteId = $('#selectSite').val();
    $("#dialog").dialog("close");
    $.post("dlmFunctionDispatch.php?f=getCustomerAddresses&siteId=" + siteId, function(data) {
        pe = '<div style="margin-top:20px;text-align:right;">';
        pe += '<p>Download Customer Address Book as CSV file for Excel</p></br></br>';
        pe += '<p><a href="addresses.csv" download="Addresses.csv"><button onclick="$(\'#dialog\').dialog(\'close\');" class=\'btn\'>Download</button></a>';
        pe += '&nbsp;&nbsp<button class="btn" onclick="$(\'#dialog\').dialog(\'close\');">Cancel</button></p>';
        pe += '</div>';
        displayResults('dialog', "Download Address File", pe);
    }, "json");    
}
</script>

<!-- Local Functions -->
<script type="text/javascript">
 . 
</script>
<!-- **************************************************************************
 * End of Site functions 
 ************************************************************************** -->

</body>
</html>