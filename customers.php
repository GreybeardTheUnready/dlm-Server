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
        $('#pageHeader').css({backgroundColor:"#7E45D3", color:"#FFFFFF"});
		$('#pageTitleLeft').html("Customers Overview");
        if ($.cookie("userType") == 'Admin') {
            $('#pageTitleRight').html("<a onclick='showCustomer();' class='tooltip'>Add Customer</a>");
        }
        var content = "<div id='customerList' style='margin:0;padding:4px;min-height:450px; width:100%;'>";
        content += "<table id='customerTable' class='display' width='100%' cellspacing='0'>";
        content += "<thead><tr><th width='200px'>Name</th><th width='200px'>Phone</th><th width='250px'>Email</th><th width='180px'>Site</th><th width='230px'>Boat</th></tr></thead>";
        content += "</table>";
        content += "</div>";
		$('#pageContainer').html(content);
	});
    var siteId = getQueryString("s");
	$.post("dlmFunctionDispatch.php?f=getCustomerList&s=" + siteId, function(data) {
        hideLoading();
        if (data.resultcount > 0) {
                for (var i = 0; i < data.resultcount; i++) {
                    var cname = safeDecode(data.results[i].lastname) + ", " + safeDecode(data.results[i].title) + " " + safeDecode(data.results[i].firstname);
                    var fullname = safeDecode(data.results[i].title) + " " + safeDecode(data.results[i].firstname) + safeDecode(data.results[i].lastname);
                    data.results[i].name = "<a onclick='showCustomer(" + data.results[i].id + ");'>" + cname +"</a>";
                    data.results[i].phone = safeDecode(data.results[i].phone); 
                    if ((data.results[i].phone.substring(0, 2) == "07") || (data.results[i].phone.substring(0, 2) == "44") || (data.results[i].phone.substring(0, 3) == "+44")) {
                        data.results[i].phone = "<a onclick='smsPopUp(\"" + data.results[i].phone + "\", \"" + fullname + "\");'><img style='margin-bottom:-3px;' src='images/smsIcon.png'></a> " + data.results[i].phone;        
                    }
                    data.results[i].email = safeDecode(data.results[i].email);
                    data.results[i].boat = safeDecode(data.results[i].boat);
                    data.results[i].boat = "<a onclick='showBoat(" + data.results[i].boatId + "," + data.results[i].id + " )'>" + data.results[i].boat + "</a>";
                }
		    $('#customerTable').dataTable( {
			    "iDisplayLength": 10,
			    "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		    	stateSave: true,
		        data: data.results,
		        "columns": [
					{"data":"name"},
					{"data":"phone", "class":"phone", "class":"right"},			
					{"data":"email", "class":"center"},
                    {"data":"site", "class":"center"},
					{"data":"boat", "class":"center"}		
		        ]
		    });
		} else {
			$('#pageContainer').html("<h1>No Customers found.</h1>");
		}
	},"json");	
</script>

<!-- Local Functions -->
<script type="text/javascript">

/******************************************************************************
 * End of Customer functions 
 ******************************************************************************/
</script>
</body>
</html>