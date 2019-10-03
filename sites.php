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
	$(document).ready(function(){
        $('#user').html($.cookie("user") + " (" + $.cookie("userType") + ")&nbsp;<a href='#' onclick='logout()'>Logout</a>");
        $('#Date').html(fullDateStr(new Date()));
        $('#pageHeader').css({backgroundColor:"#666666", color:"#FFFFFF"});
		$('#pageTitleLeft').html("Sites Overview");
        if ($.cookie("userType") == 'Admin') {
            $('#pageTitleRight').html("<a onclick='showSite();' class='tooltip'>Add Site</a>");
        }        
        var content = "<div id='siteList' style='margin:0;padding:4px;min-height:450px; width:100%;'>";
        content += "<table id='sitesTable' class='display' width='100%' cellspacing='0'>";
        content += "<thead><tr><th>Name</th><th>Contact</th><th>Phone</th><th>Email</th><th>Website</th><th width='60px'>Map</th><th width='60px'>Boats</th></tr></thead>";
        content += "</table>";
        content += "</div>";
		$('#pageContainer').html(content);

        $.post("dlmFunctionDispatch.php?f=getSitesList", function(data) {
            hideLoading();
            if (data.resultcount > 0) {
                for (var i = 0; i < data.resultcount; i++) {
                    data.results[i].name = "<a onclick='showSite(\"" + data.results[i].id + "\")'>" + safeDecode(data.results[i].name) + "</a>"; 
                    data.results[i].contact = safeDecode(data.results[i].contact);
                    if ((data.results[i].phone.substring(0, 2) == "07") || (data.results[i].phone.substring(0, 2) == "44") || (data.results[i].phone.substring(0, 3) == "+44")) {
                        data.results[i].phone = "<a onclick='smsPopUp(\"" + data.results[i].phone + "\");'><img style='margin-bottom:-3px;' src='images/smsIcon.png'></a> " + data.results[i].phone;
                    } else {
                        data.results[i].phone = safeDecode(data.results[i].phone);                
                    }
//                    data.results[i].phone = safeDecode(data.results[i].phone);
                    data.results[i].email = safeDecode(data.results[i].email);
                    data.results[i].website = safeDecode(data.results[i].website);
                }
                $('#sitesTable').dataTable( {
                    "iDisplayLength": 10,
                    "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    stateSave: true,
                    data: data.results,
                    "columns": [
                        {"data":"name"},
                        {"data":"contact", "class":"center"},			
                        {"data":"phone", "class":"center"},			
                        {"data":"email", "class":"center"},			
                        {"data":"website", "class":"center"},			
                        {"data":"map", "class":"center"},			
                        {"data":"boats", "class":"center"}
                    ]
                });
            } else {
                $('#pageContainer').html("<h1>No Sites found.</h1>");
            }
        },"json");
	});	
</script>

<!-- Local Functions -->
<script type="text/javascript">
/******************************************************************************
 * End of Site functions 
 ******************************************************************************/
</script>
</body>
</html>