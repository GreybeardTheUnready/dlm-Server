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
    $('#pageHeader').css({backgroundColor:"#F76C27", color:"#FFFFFF"});
    $('#pageTitleLeft').html("Job Sheets");
    if ($.cookie("userType") == 'Admin') {
        $('#pageTitleRight').html("<a onclick='showJobsheet(0);' class='tooltip'>Add Job Sheet</a>");
    }

    var content = "<div id='jobsheetList' style='margin:0;padding:4px;min-height:450px; width:100%;'>";
    content += "<table id='jobsheetsTable' class='display' width='100%' cellspacing='0'>";
    content += "<thead><tr><th width='60px'>Job #</th><th width='160px'>Created</th><th>Customer</th><th>Boat</th><th width='80px'>Boat Status</th><th>Site</th><th width='100px'>Stage</th></tr></thead>";
    content += "</table>";
    content += "</div>";
    $('#pageContainer').html(content);

    var targetJobsheet = getQueryString("js");
    if (targetJobsheet) {
        showJobsheet(targetJobsheet);
    } else {
        var args = window.location.search;
        args = args.replace("?", "&");
        $.post("dlmFunctionDispatch.php?f=getJobsheetList"+args, function(data) {
            if (data.resultcount > 0) {
                var stageIndex;
                for (var i = 0; i < data.resultcount; i++) {
//                    data.results[i].jsId = "<a onclick='showJobsheet(" + data.results[i].jsId + ");'>"+data.results[i].jsId+"</a>";
                    data.results[i].customer = safeDecode(data.results[i].customer);
                    data.results[i].boat = safeDecode(data.results[i].boat);
                    data.results[i].boatStatus = "<span style='display:none'>"+data.results[i].boatStatus+"</span><img title='Col #1: Blue = In Water, Grey = Out of Water\nCol #2: Green = AOK, Red = Unusable\nCol #3: White = No Jobsheet, Orange = Jobsheet' src='images/status"+data.results[i].boatStatus+".png' width='65px' height='20px' />";
                    data.results[i].stage = "<span style='display:none'>"+data.results[i].stage+"</span>"+"&#x"+data.results[i].stageEmoji+" "+data.results[i].stageName;
                }
                $('#jobsheetsTable').dataTable( {
                    "iDisplayLength": 10,
                    "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    stateSave: true,
                    data: data.results,
                    "columns": [
                        {"data":"jsId"},
                        {"data":"created", "class":"center"},			
                        {"data":"customer", "class":"center"},			
                        {"data":"boat", "class":"center"},			
                        {"data":"boatStatus", "class":"center"},			
                        {"data":"site", "class":"center"},			
                        {"data":"stage", "class":"left"}
                    ]
                });
                hideLoading();
            } else {
                hideLoading();
                if (data.status == "DB_Error") {
                    myAlert(data.status, data.msg);
                } else {
                    $('#pageContainer').html("<h1>No Jobsheets found.</h1>");
                }
            }
        },"json");

        $('#pageContainer').html(content);



        $('#jobsheetsTable').delegate('tbody > tr', 'click', function () {  // 'this' refers to the current <td>
            showJobsheet($(this.cells[0]).html());    
        });

    }
</script>

<!-- Local Functions -->
<script type="text/javascript">

</script>
<!-- **************************************************************************
 * End of Site functions 
 ************************************************************************** -->

</body>
</html>