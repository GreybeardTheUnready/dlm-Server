<!DOCTYPE html>
<html lang="en-gb">
<head>
	<?php require_once 'includes/baseHeader.html'; ?>
</head>

<body>
	<?php require_once 'includes/basePage.html'; ?>

<!-- Page Specific Content Below -->
<script type="text/javascript">
    showLoading();
    $('#user').html($.cookie("user") + " (" + $.cookie("userType") + ")&nbsp;<a href='#' onclick='logout()'>Logout</a>");
    $('#Date').html(fullDateStr(new Date()));
    $('#pageHeader').css({backgroundColor:"#6D9DD1", color:"#FFFFFF"});
    $('#pageTitleLeft').html("Boats Overview");
    if ($.cookie("userType") == 'Admin') {
        $('#pageTitleRight').html("<a onclick='showBoat(0,0);' class='tooltip'>Add Boat</a>");
    }

    var content = "<div id='siteList' style='margin:0;padding:4px;min-height:450px; width:100%;'>";
    content += "<table id='sitesTable' class='display' width='100%' cellspacing='0'>";
    content += "<thead><tr><th>Name</th><th width='110px'>Make</th><th width='110px'>Model</th><th width='60px'>LOA (m)</th><th>Owner</th><th width='150px'>Site</th><th>Berth</th><th width='75px'>Status</th></tr></thead>";
    content += "</table>";
    content += "</div>";
    
    $('#pageContainer').html(content);

//    var args = "";
//    var targetSite = getQueryString("s");
//    if (targetSite) { args = "&s="+targetSite; }
    
//    $.post("dlmFunctionDispatch.php?f=getBoatList"+args, function(data) {
    $.post("dlmFunctionDispatch.php?f=getBoatList&s="+getQueryString("s"), function(data) {
        hideLoading();
        if (data.resultcount > 0) {
            for (var i = 0; i < data.resultcount; i++) {           
                data.results[i].name = safeDecode(data.results[i].name);
                data.results[i].make = safeDecode(data.results[i].make);
                data.results[i].model = safeDecode(data.results[i].model);
                data.results[i].customer = safeDecode(data.results[i].customer);
                data.results[i].site = safeDecode(data.results[i].site);
                data.results[i].berth = safeDecode(data.results[i].berth);
                if (data.results[i].berth.length > 15) {
                    data.results[i].berth = data.results[i].berth.substring(0, 15) + "...";
                }
//                data.results[i].berth = safeDecode(data.results[i].berth);
                data.results[i].status = "<span style='display:none'>"+data.results[i].status+"</span><a href=jobsheets.php?s=0&o=0&b=" + data.results[i].id + "><img title='Col #1: Blue = In Water, Grey = Out of Water\nCol #2: Green = AOK, Red = Unusable\nCol #3: White = No Jobsheet, Orange = Jobsheet' src='images/status"+data.results[i].status+".png' width='65px' height='20px' /></a>";
            }
            $('#sitesTable').dataTable( {
                "iDisplayLength": 10,
                "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                stateSave: true,
                data: data.results,
                "columns": [
                    {"data":"name"},
                    {"data":"make", "class":"center"},			
                    {"data":"model", "class":"center"},			
                    {"data":"loa", "class":"center"},			
                    {"data":"customer", "class":"center"},			
                    {"data":"site", "class":"center"},			
                    {"data":"berth", "class":"center"},
                    {"data":"status", "class":"center"}
                ]
            });
        } else {
            $('#pageContainer').html("<h1>No Boats found.</h1>");
        }
        // Check to see if we need to add a new boat.
        var customerId = getQueryString("newboat");
        if (customerId) {
            showBoat(0,customerId);
            $.post("dlmFunctionDispatch.php?f=getCustomerDetails&cid="+customerId, function(data) {
                var cname = data.title + " " + data.firstname + " " + data.lastname;
                $('#boatCustomerName').val(cname);
                $('#boatCustomerId').val(customerId);
                $('#boatCustomerName').prop('disabled', true);
            }, "json");
        }
    },"json");
</script>

<!-- Local Functions -->
<script type="text/javascript">
/******************************************************************************
 * End of Boat functions 
 ******************************************************************************/
</script>
</body>
</html>