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
    $('#pageHeader').css({backgroundColor:"#E7E737", color:"#444444"});
    $('#pageTitleLeft').html("Engines Overview");
    if ($.cookie("userType") == 'Admin') {
        $('#pageTitleRight').html("<a onclick='showEngine(0);' class='tooltip'>Add Engine</a>");
    }
    var content = "<div id='engineList' style='margin:0;padding:4px;min-height:450px; width:100%;'>";
    content += "<table id='engineTable' class='display' width='100%' cellspacing='0'>";
    content += "<thead><tr><th>Boat</th><th>Make</th><th>Model</th><th width='60px'>Cylinders</th><th width='110px'>Capacity</th><th>Fuel</th><th width='150px'>Site</th></tr></thead>";
    content += "</table>";
    content += "</div>";
    $('#pageContainer').html(content);
    $.post("dlmFunctionDispatch.php?f=getEngineList", function(data) {
        hideLoading();
        if (data.resultcount > 0) {
            for (var i = 0; i < data.resultcount; i++) {
                data.results[i].siteName = "<a onclick='showSite(" + data.results[i].siteId + ")'>" + safeDecode(data.results[i].siteName) + "</a>";
                if (data.results[i].boatName != "") {
                    data.results[i].boatName = "<a onclick='showBoat(" + data.results[i].boatId + ")'>" + safeDecode(data.results[i].boatName) + "</a>";
                }
                data.results[i].make = safeDecode(data.results[i].make);
                data.results[i].model = safeDecode(data.results[i].model);
            }
            $('#engineTable').dataTable( {
                "iDisplayLength": 10,
                "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                stateSave: true,
                data: data.results,
                "columns": [
                    {"data":"boatName", "class":"center"},
                    {"data":"make", "class":"center"},			
                    {"data":"model", "class":"center"},			
                    {"data":"cylinders", "class":"center"},			
                    {"data":"capacity", "class":"center"},					
                    {"data":"fuel", "class":"center"},			
                    {"data":"siteName", "class":"center"}			
                ]
            });
        } else {
            $('#pageContainer').html("<h1>No Engines found.</h1>");
        }
        // Check to see if we need to add an engine to a boat.
        var boatId = getQueryString("newengine");
        if (boatId) {
            showEngine(0,boatId);
        }
    },"json");
</script>


<!-- Local Functions -->
<script type="text/javascript">
    
</script>
<!-- **************************************************************************
 * End of Site functions 
 ************************************************************************** -->

</body>
</html>