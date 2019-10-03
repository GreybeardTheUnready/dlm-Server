<!DOCTYPE html>
<html lang="en-gb">
<head>
	<?php require_once 'includes/baseHeader.html'; ?>
    <script type="text/javascript" src="js/jquery.qrcode.min.js"></script>
</head>

<body>
	<?php require_once 'includes/basePage.html'; ?>

<!-- Page Specific Content Below -->
<script type="text/javascript">
	$(document).ready(function(){
		$('#pageTitleLeft').html("Select Boats for QR Codes");
		if ($.cookie("user")) {
			$('#user').html($.cookie("user") + " (" + $.cookie("userType") + ")&nbsp;<a href='#' onclick='logout()'>Logout</a>");
			$('#pageContainer').html("<img src='images/spiffygif_110x110.gif' alt='Loading...' />");
			startPage();
		} else {
			var content = "<div style='margin:0px auto;padding-top:100px;width:300px;'>";
			content += "Please <a class='tooltip' onclick='showLogin(\"sites.php\");'>Log In</a> to use these services.";
			content += "</div>";
			$('#user').html('<a onclick="showLogin(\'sites.php\');">Log In</a>');
			$('#pageContainer').html(content);
		}
		$('#Date').html(fullDateStr(new Date()));
		$('.mitem').click(function() {
			if (!$.cookie("user")) {
				alert("Please log in to use these services.");
				showLogin();
			} else {
				location.href = $(this).attr('data-page');
			}
		});
	});
</script>

<!-- Local Functions -->
<script type="text/javascript">
var totalFound;
var qrMax = 21;		// Set this to maximum QR codes per page.
var qrsSelected = 0;
var boats = 0;
var cols = 2;
	
function startPage() {
	var content = "<div id='qrManagement' style='margin:0;padding:10px 0px 15px 0px;;text-align:right;'>";
	content += "<span id='toprint'><b>0</b> Boats selected (max = 21)</span>&nbsp;";
	content += "<button class='btn' onclick='loadPrintPage();'>Generate QR Codes</button>&nbsp;";
	content += "<button class='btn' style='width:100px;' onclick='history.back();'>Cancel</button><br>";
	content += "</div>";
	content += "<div id='boatsList' style='margin:0;padding:4px;'>";
	content += "<img src='images/spiffygif_110x110.gif' alt='Loading...' />";
	content += "</div>";
	$('#pageContainer').html(content);

	$.post("dlmFunctionDispatch.php?f=boatsForSticky&s="+getQueryString("s")+"&sb="+getQueryString("sb"), function(data) {
		if (data.resultcount > 0) {
			totalFound = data.resultcount;
			var pe = "<table style='width:100%;margin:0px; padding:0px'>";
			$.each (data.results, function() {
				if (boats % cols == 0) { pe += "<tr>"; } 
				pe += '<td>';
				pe += "<input style='vertical-align: middle;' type='checkbox' onchange='checkMax(this);' id='chk" + boats + "' value='" + this.id + "|" + this.name + "' />";	
				pe += '<td>' + safeDecode(this.name) + '</td><td>' + safeDecode(this.customer) + '</td>';
				pe += '<td width="80px">&nbsp;</td>';
				boats++;
				if (boats % cols == 0) { pe += "</tr>"; } 
			});
			pe += "</table>";
		} else {
			pe = "<div style='text-align:center;padding-top:50px;font-weight:bold;'>No Boats found matching your selection.</div>";
		}
		displayResults("boatsList", "", pe);
	}, "json");
}

function checkMax(e) {
	if (e.checked) {
		if (qrsSelected == qrMax) {
			alert ("Maximum number of meters selected for printout!");
			e.checked = false;
		} else {
			qrsSelected++;
		}
	} else {
		qrsSelected--;
	}
	$('#toprint').html("<b>" + qrsSelected + " Selected</b> (max = " + qrMax + ")");  //<b>0</b> Meters selected (max = 21)
}

function loadPrintPage() {
	var k;
	var selected = "";
	if (qrsSelected == 0) {
		alert("No boats selected");
	} else {
		for (var i=0; i < totalFound; i++) {
			k = '#chk' + i;
			if ($('#chk'+i).is(':checked')) {
				selected += $('#chk'+i).val() + "~";
			}
		}
		window.open("stickiesPage.html?boats="+selected);
        history.back();
	}
}
</script>
</html>