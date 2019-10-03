/******************************************************************************
 * Site Functions
 * 		showSite
 * 		addSite
 * 		editSite
 * 		updateSite
 * 		deleteSite  
 ******************************************************************************/
function showSite(id) {
	var frmTitle = "Site Details";
	var pe = "<div id='viewsiteform' style='width:680px;'>";
        pe += "<input id='siteId' type='text' style='display:none' />";
		pe += "<table><tr>";
		pe += "<td colspan='2'>Site Name<br><input id='sitename' type='text' style='width:160px;' maxlength='25' disabled ></td>";
		pe += "<td style='width:30px;'><br><a href='' id='sitemaplink' target='_blank'><img src='images/map24x24.png'></a></td>";
        pe += "<td colspan='3' width='480px'>Address<br><input id='siteaddress' type='text' style='width:450px' maxlength='90' disabled ></td>";
        pe += "</tr>";
		pe += "<tr>";
        pe += "<td colspan='2'>Contact<br><input id='sitecontact' type='text' style='width:160px;' maxlength='25' disabled ></td>";
        pe += "<td style='width:30px;'><br><span id='sitephone1Icon' /></td>";
        pe += "<td><br><input id='sitephone1' type='text' width='20px' maxlength='16' disabled ></td>"
        pe += "<td style='width:30px;'><br><span id='sitephone2Icon' /></td>";
        pe += "<td><br><input id='sitephone2' type='text' width='20px' maxlength='16' disabled ></td>"
        pe += "</tr>";
        pe += "<td colspan='6'></td>";
		pe += "<tr>";
        pe += "<td style='width:30px;'><span id='siteEmailLink' /></td><td colspan='5'><input id='siteemail' type='text' style='width:630px;' maxlength='200' disabled ></td>";
        pe += "</tr>";
		pe += "<tr>";
        pe += "<td style='width:30px;'><span id='siteWebLink' /></td><td colspan='5'><input id='sitewebsite' type='text' style='width:630px;' maxlength='200' disabled ></td>";
        pe += "</tr>";
		pe += "<tr>";
        pe += "<td colspan='6'>Notes<br>";
            pe += "<textarea name='sitenotes' id='sitenotes' style='width:660px' rows='4' wrap='soft' maxlength='200' onkeydown='maxTxt(\"#sitenotes\",\"#sitenotesCC\");' disabled></textarea>";
        pe += "<div id='sitenotesCC' style='font-size:0.7em; color:#666666; text-align:right;padding-right:5px'>&nbsp;</div></td>";
		pe += "</tr>";
        pe += "</table>";
		pe += "</div>";
		
		if(typeof id === "undefined") {
			frmTitle = "Enter " + frmTitle;
			pe += "<div id='siteBtns' style='text-align:right;padding-top:15px;'>";
			pe += "<button class='btn' onclick='addSite();'>Add</button>&nbsp;&nbsp;";
			pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
			pe += "</div>";
		} else {
			pe += "<div id='siteUpdates' style='width:45%;float:left;padding:15px 0 0 5px;color:#666666;font-size:0.7em'>";
            pe += "Created: <span id='created' /><br>";
            pe += "Last Update: <span id='updated' /> <span id='updatedBy' /><br>";
            pe += "</div>";
            pe += "<div id='siteBtns' style='width:45%;float:right;text-align:right;padding-top:15px;'>";
            if ($.cookie("userType") == "Admin") {
                pe += "<button class='btn' onclick='editSite(\"" + id +  "\");'>Edit</button>&nbsp;&nbsp;";
                pe += "<button class='btn' onclick='deleteSite(\"" + id +  "\");'>Delete</button>&nbsp;&nbsp;";
            }
			pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
			pe += "</div>";
		}

	displayResults('dialog', frmTitle, pe);

	if(typeof id === "undefined") {
        $('#sitename').prop('disabled', false);
        $('#sitemaplink').html("<img src='images/map24x24.png'>");
        $('#siteaddress').prop('disabled', false);
        $('#sitecontact').prop('disabled', false);
        $('#sitephone1Icon').html("<img src='images/phone24x24.png'>");
        $('#sitephone1').prop('disabled', false);
        $('#sitephone2Icon').html("<img src='images/phone24x24.png'>");
        $('#sitephone2').prop('disabled', false);
        $('#siteEmailLink').html("<img src='images/email24x24.png'>");
        $('#siteemail').prop('disabled', false);
        $('#siteWebLink').html("<img src='images/www24x24.png'>");
        $('#sitewebsite').prop('disabled', false);
        $('#sitenotes').prop('disabled', false);
	} else {
		$.post("dlmFunctionDispatch.php?f=getSiteDetails&sid="+id, function(data) {
            $('#siteId').val(data.id);
            $('#sitename').val(safeDecode(data.name));
            $('#siteaddress').val(safeDecode(data.address));
            $('#sitecontact').val(safeDecode(data.contact));
            data.phone1 = safeDecode(data.phone1);
            $('#sitephone1').val(data.phone1);
            if ((data.phone1.substring(0, 2) == "07") || (data.phone1.substring(0, 4) == "44 7") || (data.phone1.substring(0, 5) == "+44 7")) {
                $('#sitephone1Icon').html("<a onclick='smsPopUp(\"" + data.phone1 + "\", \"" + safeDecode(data.contact) + "\");'><img style='margin-bottom:-3px;' src='images/smsIcon.png'></a>");
            } else {
                $('#sitephone1Icon').html("<img src='images/phone24x24.png'>");                
            }
            data.phone2 = safeDecode(data.phone2);
            $('#sitephone2').val(data.phone2);
            if ((data.phone2.substring(0, 2) == "07") || (data.phone2.substring(0, 4) == "44 7") || (data.phone2.substring(0, 5) == "+44 7")) {
                $('#sitephone2Icon').html("<a onclick='smsPopUp(\"" + data.phone2 + "\", \"" + safeDecode(data.contact) + "\");'><img style='margin-bottom:-3px;' src='images/smsIcon.png'></a>");
            } else {
                $('#sitephone2Icon').html("<img src='images/phone24x24.png'>");               
            }
            $('#siteemail').val(safeDecode(data.email));
            if (data.email) {
                $('#siteEmailLink').html("<a href='mailto:" + safeDecode(data.email) + "'><img src='images/email24x24.png'></a>");
            } else {
                $('#siteEmailLink').html("<img src='images/email24x24.png'>");
            }
            $('#sitewebsite').val(safeDecode(data.website));
            if (data.website) {
                var website = safeDecode(data.website);
                if (website.substring(0,4) == "www.") {
                    website = "http://" + website;
                }
                $('#siteWebLink').html("<a href='" + website + "' target='_blank'><img src='images/www24x24.png'></a>");
            } else {
                $('#siteWebLink').html("<img src='images/www24x24.png'>");
            }
            $('#sitemaplink').attr('href','http://google.co.uk/maps?q="' + data.address + '"');
            $('#sitenotes').val(safeDecode(data.notes));
            $('#sitenotesCC').html(data.notes.length + " characters of 250 Max");
            $('#created').html(data.created);
            if (data.updated == "0000-00-00 00:00:00") {
                $('#updated').html("Never");
            } else {
                $('#updated').html(data.updated);
                $('#updatedBy').html(" by: " + data.updatedBy);
            }
		}, "json");
	}
}

function addSite() {
	// Check mandatory fields
	if ($('#sitename').val() === "") {
		myAlert("Incomplete Information", "Please check that you have entered a Site Name");
	} else {
        var sname = safeEncode($('#sitename').val());
        var sa = safeEncode($('#siteaddress').val());
        var sc = safeEncode($('#sitecontact').val());
        var sp1 = $('#sitephone1').val();
        var sp2 = $('#sitephone2').val();
        var se = safeEncode($('#siteemail').val());
        var sw = safeEncode($('#sitewebsite').val());
        var sn = safeEncode($('#sitenotes').val());
        var su = $.cookie('user');
        var siteJSON = {"sname":sname, "sa":sa, "sc":sc, "sp1":sp1, "sp2":sp2, "se":se, "sw":sw, "sn":sn, "su":su};
        $.post("dlmFunctionDispatch.php?f=addSite", siteJSON, function(data) {
			if (data.status == "OK") {
				location.reload();
			} else {
				alert(data.msg);
			}
		}, "json"); 	
	} 
}

function editSite(id) {
	var pe;
	pe = "<button class='btn' onclick='updateSite(\"" + id +  "\");'>Save</button>&nbsp;&nbsp;";
	pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Cancel</button>";
	$('#siteBtns').html(pe);
    $('#siteaddress').prop('disabled', false);
    $('#sitecontact').prop('disabled', false);
    $('#sitephone1').prop('disabled', false);
    $('#sitephone2').prop('disabled', false);
    $('#siteemail').prop('disabled', false);
    $('#sitewebsite').prop('disabled', false);
    $('#sitenotes').prop('disabled', false);
}

function updateSite(id) {
	var sa = safeEncode($('#siteaddress').val());
	var sc = safeEncode($('#sitecontact').val());
	var sp1 = $('#sitephone1').val();
	var sp2 = $('#sitephone2').val();
    if (($('#siteemail').val() != "") && (!isEmail($('#siteemail').val()))) {
        myAlert("Invalid Email", "PLease enter a valid email address (or leave blank.)");
        return;
    }
	var se = $('#siteemail').val();
	var sw = safeEncode($('#sitewebsite').val());
	var sn = safeEncode($('#sitenotes').val());
    var su = $.cookie('user');
    var siteJSON = {"sa":sa, "sc":sc, "sp1":sp1, "sp2":sp2, "se":se, "sw":sw, "sn":sn, "su":su};
    $.post("dlmFunctionDispatch.php?f=updateSite&sid="+id, siteJSON, function(data) {
        if (data.status == "OK") {
            location.reload();
        } else {
            myAlert("Operation Complete", data.msg);
        }
    }, "json");
}

function deleteSite(sid) {
	var prompt = "Delete <b>" + safeDecode($('#sitename').val()) + "</b><br><br>";
	prompt += "<p style='font-style:italic;'><b>Note:</b>: All Boats associated with this site (" + safeDecode($('#sitename').val()) + ") will also be deleted.</p><br>";
	prompt += "<p style='color:red; text-align:center;'>This operation cannot be undone!</p>";
	$(function() {
		$('<div />').html(prompt).dialog({
			title: 'Delete Site',
			resizable: false,
			width:350,
			height:"auto",
			modal: true,
            close : function(){ $(this).empty(); $(this).remove(); },
			buttons: {
				"Delete": function() {
		        	$.post("dlmFunctionDispatch.php?f=deleteSite&sid=" + sid, function(data) {
						if (data.status == "OK") {
							alert("Site Deleted!");
							location.reload();
						} else {
							alert(data.status);
						}
						$( this ).dialog( "close" );
					}, "json");
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
}


/******************************************************************************
 * CUSTOMER Functions
 * 		showCustomer
 * 		addCustomer
 * 		editCustomer
 * 		updateCustomer
 * 		deleteCustomer
 ******************************************************************************/
function showCustomer(id) {
	var frmTitle = "Customer Details";
	var pe = "<div id='viewcustomerform' style='width:680px;'>";
        pe += "<input id='customerId' type='text' style='display:none' />";
        pe += "<div style='width:100%;margin-bottom:5px;'>";
            pe += "<div style='width:80px; margin-right:20px; float:left'>Title<br><input id='customerTitle' type='text' style='width:80px;' maxlength='10' disabled ></div>";
            pe += "<div style='width:240px; margin-right:30px; float:left'>Firstname<br><input id='customerFirstname' type='text' style='width:250px;' maxlength='15' disabled ></div>";
            pe += "<div style='width:300px; float:left'>Lastname<br><input id='customerLastname' type='text' style='width:290px;' maxlength='15' disabled ></div>";
            pe += "<br class='clear' />";
        pe += "</div>";
        pe += "<div style='width:100%'>";
            pe += "Address<br><input id='customerAddress1' style='width:660px' type='text' maxlength='25' disabled >";
        pe += "</div>";
        pe += "<div style='width:100%'>";
            pe += "<input id='customerAddress2' style='width:660px' type='text' maxlength='25' disabled >";
        pe += "</div>";
        pe += "<div style='width:100%;margin-bottom:10px;'>";
            pe += "<input id='customerAddress3' style='width:660px' type='text' maxlength='25' disabled >";
        pe += "</div>";
        pe += "<div style='width:100%;margin-bottom:10px;'>";
            pe += "<div style='width:235px; margin-right:20px; float:left;'>County<br><input id='customerCounty' type='text' style='width:235px; maxlength='25' disabled ></div>";
            pe += "<div style='width:180px;float:left;'>Post Code<br><input id='customerPostcode' type='text' style='width:150px; maxlength='25' disabled ></div>";
            pe += "<div style='width:240px;float:left;'><br><span id='customerPhoneIcon' style='width:30px'><img src='images/phone24x24.png'></span>&nbsp;<input id='customerPhone' type='text' style='width:194px; maxlength='16' disabled ></div>";
            pe += "<br class='clear' />";
        pe += "</div>";
        pe += "<div style='width:100%;margin-bottom:5px;'>";
            pe += "<span id='customerEmailIcon' style='width:30px'><img src='images/email24x24.png'></span>&nbsp;<input id='customerEmail' type='text' style='width:630px;' maxlength='50' disabled ></div>"
        pe += "</div>";
        pe += "<div style='width:100%'>";
            pe += "Notes<br><textarea name='customerNotes' id='customerNotes' style='width:660px' rows='4' wrap='soft' maxlength='200' onkeydown='maxTxt(\"#customerNotes\",\"#customerNotesCC\");' disabled></textarea>";
        pe += "</div>";
        pe += "<div style='width:100%'>";
            pe += "<div id='customerNotesCC' style='font-size:0.7em; color:#666666; text-align:right;padding-right:5px'>&nbsp;</div></td>";
        pe += "</div>";
		pe += "</div>";
		
		if(typeof id === "undefined") {
			frmTitle = "Enter " + frmTitle;
			pe += "<div id='customerBtns' style='text-align:right;padding-top:15px;'>";
			pe += "<button class='btn' onclick='addCustomer();'>Add</button>&nbsp;&nbsp;";
			pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
			pe += "</div>";
		} else {
			pe += "<div id='customerUpdates' style='width:45%;float:left;padding:15px 0 0 5px;color:#666666;font-size:0.7em'>";
            pe += "Created: <span id='created' /><br>";
            pe += "Last Update: <span id='updated' /> <span id='updatedBy' /><br>";
            pe += "</div>";
            pe += "<div id='customerBtns' style='width:45%;float:right;text-align:right;padding-top:15px;'>";
            if ($.cookie("userType") == "Admin") {
                pe += "<button class='btn' onclick='editCustomer(\"" + id +  "\");'>Edit</button>&nbsp;&nbsp;";
                pe += "<button class='btn' onclick='deleteCustomer(\"" + id +  "\");'>Delete</button>&nbsp;&nbsp;";
            }
            pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
			pe += "</div>";
		}
    
        pe += "<br class='clear' />";
        pe += "<div style='margin-top:20px; border-top:thin solid #666666;padding-top:4px'>";
        pe += "Boat: <span id='customerBoatName'>&nbsp;</span>&nbsp;&nbsp;&nbsp;Moored at <span id='customerSiteName'>&nbsp;</span>";
        pe += "</div>";

	displayResults('dialog', frmTitle, pe);

	if(typeof id === "undefined") {
        $('#customerTitle').prop('disabled', false);
        $('#customerFirstname').prop('disabled', false);
        $('#customerLastname').prop('disabled', false);
        $('#customerAddress1').prop('disabled', false);
        $('#customerAddress2').prop('disabled', false);
        $('#customerAddress3').prop('disabled', false);
        $('#customerCounty').prop('disabled', false);
        $('#customerPostcode').prop('disabled', false);
        $('#customerPhone').prop('disabled', false);
        $('#customerEmail').prop('disabled', false);
        $('#customerNotes').prop('disabled', false);
	} else {
		$.post("dlmFunctionDispatch.php?f=getCustomerDetails&cid="+id, function(data) {
            $('#customerId').val(data.id);
            $('#customerTitle').val(safeDecode(data.title));
            $('#customerFirstname').val(safeDecode(data.firstname));
            $('#customerLastname').val(safeDecode(data.lastname));
            $('#customerAddress1').val(safeDecode(data.address1));
            $('#customerAddress2').val(safeDecode(data.address2));
            $('#customerAddress3').val(safeDecode(data.address3));
            $('#customerCounty').val(safeDecode(data.county));
            $('#customerPostcode').val(safeDecode(data.postcode));
            data.phone = safeDecode(data.phone);
            $('#customerPhone').val(data.phone);
            var cn = safeDecode(data.title) + " " + safeDecode(data.firstname) + " " + safeDecode(data.lastname);
            if ((data.phone.substring(0, 2) == "07") || (data.phone.substring(0, 4) == "44 7") || (data.phone.substring(0, 5) == "+44 7")) {
                $('#customerPhoneIcon').html("<a onclick='smsPopUp(\"" + data.phone + "\", \"" + cn + "\");'><img style='margin-bottom:-3px;' src='images/smsIcon.png'></a>");
            }
            $('#customerEmail').val(safeDecode(data.email));
            $('#customerNotes').val(safeDecode(data.notes));
            $('#customerNotesCC').html(data.notes.length + " characters of 250 Max");
            $('#created').html(data.created);
            if (data.updated == "0000-00-00 00:00:00") {
                $('#updated').html("Never");
            } else {
                $('#updated').html(data.updated);
                $('#updatedBy').html(" by: " + data.updatedBy);
            }
            $('#customerBoatName').html(safeDecode(data.boatName));
            $('#customerSiteName').html(safeDecode(data.siteName));
		}, "json");
	}
}

function addCustomer() {
	// Check mandatory fields
	if (($('#customerFirstname').val() + $('#customerLastname').val()) == "") {
		myAlert("Missing Infomation", "Please check that you have entered a name");
	} else {
        var ct = safeEncode($('#customerTitle').val());
        var cf = safeEncode($('#customerFirstname').val());
        var cl = safeEncode($('#customerLastname').val());
        var ca1 = safeEncode($('#customerAddress1').val());
        var ca2 = safeEncode($('#customerAddress2').val());
        var ca3 = safeEncode($('#customerAddress3').val());
        var cc = safeEncode($('#customerCounty').val());
        var cpc = safeEncode($('#customerPostcode').val());
        var cph = safeEncode($('#customerPhone').val());
        if (($('#customerEmail').val() != "") && (!isEmail($('#customerEmail').val()))) {
            alert("Please enter a valid email address");
            return;
        }
        var ce = safeEncode($('#customerEmail').val());
        var cn = safeEncode($('#customerNotes').val());
        var cu = $.cookie('user');
        var customerJSON = {"ct":ct, "cf":cf, "cl":cl, "ca1":ca1, "ca2":ca2, "ca3":ca3, "cc":cc, "cpc":cpc, "cph":cph, "ce":ce, "cn":cn, "cu":cu};
        $.post("dlmFunctionDispatch.php?f=addCustomer", customerJSON, function(data) {
			if (data.status == "OK") {
                // Now prompt for boat
                $('<div />').dialog({
                    modal: true,
                    title: "Now Add Boat",
                    width: "auto",
                    open: function() { $(this).html("Register Boat for this customer?"); },
                    close : function(){ $(this).empty(); $(this).remove(); }, 
                    buttons: {
                      "Yes": function() {
                          $( this ).dialog("close");
                          location.href = "boats.php?newboat=" + data.id;
                      },
                      "No": function() {
                          $( this ).dialog("close");
                          location.reload();
                      }
                    }
                  });  //end confirm dialog 
			} else {
				myAlert(data.status, data.msg);
			}
		}, "json"); 	
	} 
}

function editCustomer(id) {
	var pe;
	pe = "<button class='btn' onclick='updateCustomer(\"" + id +  "\");'>Save</button>&nbsp;&nbsp;";
	pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Cancel</button>";
	$('#customerBtns').html(pe);
    $('#customerTitle').prop('disabled', false);
    $('#customerFirstname').prop('disabled', false);
    $('#customerLastname').prop('disabled', false);
    $('#customerAddress1').prop('disabled', false);
    $('#customerAddress2').prop('disabled', false);
    $('#customerAddress3').prop('disabled', false);
    $('#customerCounty').prop('disabled', false);
    $('#customerPostcode').prop('disabled', false);
    $('#customerPhone').prop('disabled', false);
    $('#customerEmail').prop('disabled', false);
    $('#customerNotes').prop('disabled', false);
    if ($('#customerBoatName').html() == "unknown") {
        $('#customerBoatName').html("<a href='boats.php?newboat=" + id + "'; ><img src='images/plus.png' />Add Boat.</a>");
    }
}

function updateCustomer(id) {
    var ct = safeEncode($('#customerTitle').val());
    var cf = safeEncode($('#customerFirstname').val());
    var cl = safeEncode($('#customerLastname').val());
    var ca1 = safeEncode($('#customerAddress1').val());
    var ca2 = safeEncode($('#customerAddress2').val());
    var ca3 = safeEncode($('#customerAddress3').val());
    var cc = safeEncode($('#customerCounty').val());
    var cpc = safeEncode($('#customerPostcode').val());
    var cph = safeEncode($('#customerPhone').val());
    if (($('#customerEmail').val() != "") && (!isEmail($('#customerEmail').val()))) {
        alert("Please enter a valid email address");
        return;
    }
    var ce = safeEncode($('#customerEmail').val());
    var cn = safeEncode($('#customerNotes').val());
    var cu = $.cookie('user');
    var customerJSON = {"ct":ct, "cf":cf, "cl":cl, "ca1":ca1, "ca2":ca2, "ca3":ca3, "cc":cc, "cpc":cpc, "cph":cph, "ce":ce, "cn":cn, "cu":cu};
    $.post("dlmFunctionDispatch.php?f=updateCustomer&cid="+id, customerJSON, function(data) {
        if (data.status == "OK") {
            location.reload();
        } else {
            myAlert(data.status, data.msg);
        }
    }, "json");
}

function deleteCustomer(cid) {
    var customerFullname = $('#customerTitle').val() + " " + safeEncode($('#customerFirstname').val()) + " " + safeEncode($('#customerLastname').val());
	var prompt = "Delete <b>" + customerFullname + "</b><br><br>";
	prompt += "<p style='font-style:italic;'><b>Note 1:</b>: All Boats associated with this customer along with boat history will also be deleted.</p><br>";
	prompt += "<p style='font-style:italic;'><b>Note 2:</b>: Job Sheets will NOT be deleted.</p><br>";
	prompt += "<p style='color:red; text-align:center;'>This operation cannot be undone!</p>";
	$(function() {
		$('<div />').html(prompt).dialog({
			title: 'Delete Customer',
			resizable: false,
			width:350,
			height:"auto",
			modal: true,
            close : function(){ $(this).empty(); $(this).remove(); },
			buttons: {
				"Delete": function() {
		        	$.post("dlmFunctionDispatch.php?f=deleteCustomer&cid=" + cid, function(data) {
						if (data.status == "OK") {
							myAlert("Operation Complete", "Customer Deleted!");
							location.reload();
						} else {
							myAlert(data.status, data.msg);
						}
						$( this ).dialog( "close" );
					}, "json");
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
}

/******************************************************************************
 * USER Functions
 *
 ******************************************************************************/
function showUser(id) {
    var frmTitle = "User Details";
	var pe = "<div id='viewuserform' style='width:720px;'>";
        pe += "<input id='userId' type='text' style='display:none' />";
		pe += "<div style='float:left;width:90px;margin-right:20px;' >Username<br><input id='username' type='text' style='width:90px;' maxlength='25' disabled ></div>";
		pe += "<div style='float:left;width:100px;margin-right:20px;'>Mobile<br><input id='usermobile' type='text' style='width:100px;' maxlength='25' disabled ></div>";
		pe += "<div style='float:left;width:250px;margin-right:20px;'>Email<br><input id='useremail' type='text' style='width:250px;' maxlength='45' disabled ></div>";
        pe += "<div style='float:left;width:90px;margin-right:20px;'>User Type<br><select id='usertype' style='width:90px;' disabled ><option value='User'>User</option><option value='Admin'>Admin</option></select></div>";
        pe += "<div style='float:left;width:90px;margin-right:20px;'>Status<br><select id='userstatus' style='width:90px;' disabled ><option value='0'>Disabled</option><option value='1'>Active</option></select></div>";
        pe += "<br class='clear' />";
        pe += "</div>";
        pe += "<br><h4>Notifications</h4>";
        pe += "<div style='margin:0 0 20px 80px;width:550px;border:thin solid #888585;padding:10px;'>";
            pe += "<div style='float:left;width:200px;margin:10px 20px 0px 0px;'>New Boat</div>";
            pe += "<div style='float:left;width:220px;margin-right:20px;margin-bottom:0px;'>";
                pe += "by <select id='nbMode' disabled><option value='IGNORE'>&nbsp;</option><option value='SMS'>SMS</option><option value='EMAIL'>EMail</option></select><br>";
            pe += "</div>";
            pe += "<br class='clear' />";
            pe += "<div style='float:left;width:200px;margin:10px 20px 0px 0px;'>New Job Sheet</div>";
            pe += "<div style='float:left;width:220px;margin-right:20px;margin-bottom:0px;'>";
                pe += "by <select id='njsMode' disabled><option value='IGNORE'>&nbsp;</option><option value='SMS'>SMS</option><option value='EMAIL'>EMail</option></select><br>";
            pe += "</div>";
            pe += "<br class='clear' />";
            pe += "<div style='float:left;width:200px;margin:10px 20px 0px 0px;'>Job Sheet Updated</div>";
            pe += "<div style='float:left;width:220px;margin-right:20px;margin-bottom:0px;'>";
                pe += "by <select id='ujsMode' disabled><option value='IGNORE'>&nbsp;</option><option value='SMS'>SMS</option><option value='EMAIL'>EMail</option></select><br>";
            pe += "</div>";
            pe += "<br class='clear' />";
            pe += "<div style='float:left;width:200px;margin:10px 20px 0px 0px;'>Job Sheet Completed</div>";
            pe += "<div style='float:left;width:220px;margin-right:20px;margin-bottom:0px;'>";
                pe += "by <select id='cjsMode' disabled><option value='IGNORE'>&nbsp;</option><option value='SMS'>SMS</option><option value='EMAIL'>EMail</option></select><br>";
            pe += "</div>";
            pe += "<br class='clear' />";
            pe += "<div style='float:left;width:200px;margin:10px 20px 0px 0px;'>New Mobile Notes</div>";
            pe += "<div style='float:left;width:220px;margin-right:20px;margin-bottom:0px;'>";
                pe += "by <select id='nmnMode' disabled><option value='IGNORE'>&nbsp;</option><option value='SMS'>SMS</option><option value='EMAIL'>EMail</option></select><br>";
            pe += "</div>";
            pe += "<br class='clear' />";
        pe += "</div>";
        pe += "<div style='float:left;width:325px;'>Last Console Login: <span id='lastConsole'>&nbsp;</span></div>";
        pe += "<div style='float:left;width:325px;'>Total: <span id='totalConsole'>&nbsp;</span></div>";
        pe += "<br class='clear' />";
        pe += "</div>";
		
		if(typeof id === "undefined") {
			frmTitle = "Enter " + frmTitle;
			pe += "<div id='userBtns' style='text-align:right;padding-top:15px;'>";
			pe += "<button class='btn' onclick='addUser(0);'>Add</button>&nbsp;&nbsp;";
			pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
			pe += "</div>";
		} else {
			pe += "<div id='userUpdates' style='width:45%;float:left;padding:15px 0 0 5px;color:#666666;font-size:0.7em'>";
            pe += "Created: <span id='created' /><br>";
            pe += "Last Update: <span id='updated' /> <span id='updatedBy' /><br>";
            pe += "</div>";
            pe += "<div id='userBtns' style='width:45%;float:right;text-align:right;padding-top:15px;'>";
			if ($.cookie("userType") == "Admin") {
                pe += "<button class='btn' onclick='editUser(" + id +  ");'>Edit</button>&nbsp;&nbsp;";
                pe += "<button class='btn' onclick='deleteUser(" + id +  ");'>Delete</button>&nbsp;&nbsp;";
            }
			pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
			pe += "</div>";
		}


	displayResults('dialog', frmTitle, pe);

	if(typeof id === "undefined") {
        $('#username').prop('disabled', false);
        $('#usermobile').prop('disabled', false);
        $('#useremail').prop('disabled', false);
        $('#usertype').prop('disabled', false);
        $('#userstatus').prop('disabled', false);
        $('#nbMode').prop('disabled', false);
        $('#njsMode').prop('disabled', false);
        $('#ujsMode').prop('disabled', false);
        $('#cjsMode').prop('disabled', false);
        $('#nmnMode').prop('disabled', false);
	} else {
		$.post("dlmFunctionDispatch.php?f=getUserDetails&uid="+id, function(data) {
            $('#userId').val(data.uid);
            $('#username').val(safeDecode(data.name));
            $('#usermobile').val(data.mobile);
            $('#useremail').val(safeDecode(data.email));
            selectOptionByVal('usertype', data.type);
            selectOptionByVal('userstatus', data.status);
            $('#lastConsole').html(data.lastConsoleLogin);
            $('#totalConsole').html(data.consoleLoginCount);
            $('#lastMobile').html(data.lastMobileLogin);
            $('#totalMobile').html(data.mobileLoginCount);
            $('#created').html(data.created);
            if (data.updated == null) {
                $('#updated').html("Never");b
            } else {
                $('#updated').html(data.updated);
                $('#updatedBy').html(" by: " + data.updatedBy);
            }
            // Now get Notifications
            $.post("dlmFunctionDispatch.php?f=getUserNotifications&uid="+id, function(ndata) {
                for (var i = 0; i < ndata.resultcount; i++) {
                    if (ndata.results[i].name == "NEWBOAT") {
                        selectOptionByVal('nbMode', ndata.results[i].mode);
                    }
                    if (ndata.results[i].name == "NEWJOBSHEET") {
                        selectOptionByVal('njsMode', ndata.results[i].mode);
                    }
                    if (ndata.results[i].name == "UPDATEDJOBSHEET") {
                        selectOptionByVal('ujsMode', ndata.results[i].mode);
                    }
                    if (ndata.results[i].name == "COMPLETEDJOBSHEET") {
                        selectOptionByVal('cjsMode', ndata.results[i].mode);
                    }
                    if (ndata.results[i].name == "NEWMOBILENOTE") {
                        selectOptionByVal('nmnMode', ndata.results[i].mode);
                    }
                }                    
            }, "json");
		}, "json");
	}
}

function editUser(id) {
	var pe;
	pe = "<button class='btn' onclick='addUser(" + id +  ");'>Save</button>&nbsp;&nbsp;";
	pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Cancel</button>";
	$('#userBtns').html(pe);
    $('#usermobile').prop('disabled', false);
    $('#useremail').prop('disabled', false);
    $('#usertype').prop('disabled', false);
    $('#userstatus').prop('disabled', false);
    $('#nbMode').prop('disabled', false);
    $('#njsMode').prop('disabled', false);
    $('#ujsMode').prop('disabled', false);
    $('#cjsMode').prop('disabled', false);
    $('#nmnMode').prop('disabled', false);
}

function addUser(id) {       //  Used for add (id = 0) AND update (id = id)
	// Check mandatory fields
	if (($('#username').val() === "") || (!(isEmail($('#useremail').val())))){
		myAlert("Incomplete Information", "Please check that you have entered a Username and valid Email address");
	} else {
        var name = safeEncode($('#username').val());
        var mobile = $('#usermobile').val();
        var email = $('#useremail').val();
        var type = $('#usertype').val();
        var nbMode = $('#nbMode').val();
        var njsMode = $('#njsMode').val();
        var ujsMode = $('#ujsMode').val();
        var cjsMode = $('#cjsMode').val();
        var nmnMode = $('#nmnMode').val();
        var status = $('#userstatus').val();
        var updatedBy = $.cookie('user');
        var userJSON = {"name":name, "mobile":mobile, "email":email, "type":type, "nbMode":nbMode, "njsMode":njsMode, "ujsMode":ujsMode, "cjsMode":cjsMode, "nmnMode":nmnMode, "status":status, "updatedBy":updatedBy};
        $.post("dlmFunctionDispatch.php?f=addUser&uid="+id, userJSON, function(data) {
			myAlert(data.status, data.msg);
            setTimeout( function() { window.location.href = "admin.php?opt=userMgr"; }, 1000);
		}, "json"); 	
	} 
}

function deleteUser(id) {
    $.post("dlmFunctionDispatch.php?f=deleteUser&uid="+id, function(data) {
        if (data.status == "OK") {
            window.location.href = "admin.php?opt=userMgr";
        } else {
            myAlert("Operation Complete", data.msg);
        }
    }, "json");
}
/******************************************************************************
 * BOAT Functions
 * 		showBoat
 * 		addBoat
 * 		editBoat
 * 		updateBoat
 * 		deleteBoat
 ******************************************************************************/
function showBoat(id, customerId) {
	var frmTitle = "Boat Details";
	var pe = "<div id='viewboatform' style='width:680px;'>";
        pe += "<input id='boatId' type='text' style='display:none' />";
        pe += "<input id='boatCustomerId' type='text' style='display:none' />";
        pe += "<div style='width:100%;margin-bottom:15px;'>";
            pe += "<div style='width:210px; margin-right:20px; float:left'>Name<br><input id='boatName' type='text' style='width:200px;' maxlength='30' disabled ></div>";
            pe += "<div style='width:210px; margin-right:20px; float:left'>Make<br><input id='boatMake' type='text' style='width:200px;' maxlength='30' disabled ></div>";
            pe += "<div style='width:210px; float:left'>Model<br><input id='boatModel' type='text' style='width:200px;' maxlength='30' disabled ></div>";
            pe += "<br class='clear' />";
        pe += "</div>";
        pe += "<div style='width:100%;margin-bottom:15px;'>";
            pe += "<div style='width:50px; margin-right:20px; float:left'>Year<br><input id='boatYear' type='text' style='width:50px;' maxlength='4' disabled ></div>";
            pe += "<div style='width:70px; margin-right:20px; float:left'>LOA (m)<br><input id='boatLOA' type='text' style='width:70px;' maxlength='10' disabled ></div>";
            pe += "<div style='width:70px; margin-right:20px; float:left'>Beam<br><input id='boatBeam' type='text' style='width:70px;' maxlength='15' disabled ></div>";
            pe += "<div style='width:100px; margin-right:20px; float:left'>Registration<br><input id='boatRegno' type='text' style='width:100px;' maxlength='10' disabled ></div>";
            pe += "<div style='width:300px; float:left'>Owner<br><span id='custHolder' /></div>";
            pe += "<br class='clear' />";
        pe += "</div>";
        pe += "<div style='width:100%;margin-bottom:15px;'>";
            pe += "<div style='width:210px; margin-right:20px; float:left'>";
                pe += "Site<br><span id='siteHolder' />";
            pe += "</div>";
            pe += "<div style='width:430px; float:left'>Berth<br><input id='boatBerth' type='text' style='width:430px;' maxlength='50' disabled ></div>";
            pe += "<br class='clear' />";
        pe += "</div>";
        pe += "<div id='enginesSection' style='width:100%;border:thin solid #666666;'>";
            pe += "<div style='text-align:center'>No Engines Registered</div>";
            pe += "<div style='float:right;'>&nbsp;</div>";
            pe += "<br class='clear' />";
        pe += "</div>";
        pe += "<div style='margin:10px 0 20px 0'>";
            pe += "<b>Status:</b>&nbsp;&nbsp;&nbsp;";
            pe += "<input type='checkbox' id='inwater' value='8' checked disabled >In Thames</input>&nbsp;&nbsp;&nbsp;&nbsp;";
            pe += "<input type='radio' id='aok' name='state' value='4' checked disabled >&nbsp;All OK</input>&nbsp;&nbsp;";
            pe += "<input type='radio' id='usable' name='state' value='2' disabled >&nbsp;Usable</input>&nbsp;&nbsp;";
            pe += "<input type='radio' id='unusable' name='state' value='1' disabled >&nbsp;Unusable</input>";
        pe += "</div>";
        pe += "<div style='width:100%'>";
            pe += "Keys<br><textarea id='boatKeys' style='width:660px; max-height:50px;' rows='2' wrap='soft' maxlength='200' onkeydown='maxTxt(\"#boatKeys\",\"#boatKeysCC\");' disabled></textarea>";
        pe += "</div>";
        pe += "<div style='width:100%'>";
            pe += "<div id='boatKeysCC' style='font-size:0.7em; color:#666666; text-align:right;padding-right:5px'>&nbsp;</div></td>";
        pe += "</div>";
        pe += "<div style='width:100%'>";
            pe += "Notes<br><textarea id='boatNotes' style='width:660px' rows='4' wrap='soft' maxlength='200' onkeydown='maxTxt(\"#boatNotes\",\"#boatNotesCC\");' disabled></textarea>";
        pe += "</div>";
        pe += "<div style='width:100%'>";
            pe += "<div id='boatNotesCC' style='font-size:0.7em; color:#666666; text-align:right;padding-right:5px'>&nbsp;</div></td>";
        pe += "</div>";
		pe += "</div>";
		
		if((typeof id === "undefined") || (id === 0)) {
			frmTitle = "Enter " + frmTitle;
			pe += "<div id='boatBtns' style='text-align:right;padding-top:15px;'>";
			pe += "<button class='btn' onclick='addBoat(0);'>Add</button>&nbsp;&nbsp;";
			pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
			pe += "</div>";
		} else {
			pe += "<div id='boatUpdates' style='width:45%;float:left;padding:15px 0 0 5px;color:#666666;font-size:0.7em'>";
            pe += "Created: <span id='created' /><br>";
            pe += "Last Update: <span id='updated' /> <span id='updatedBy' /><br>";
            pe += "</div>";
            pe += "<div id='boatBtns' style='width:45%;float:right;text-align:right;padding-top:15px;'>";
			pe += "<button class='btn' onclick='editBoat(\"" + id +  "\");'>Edit</button>&nbsp;&nbsp;";
			pe += "<button class='btn' onclick='deleteBoat(\"" + id +  "\");'>Delete</button>&nbsp;&nbsp;";
			pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
			pe += "</div>";
		}

    displayResults('dialog', frmTitle, pe);
    

	if((typeof id === "undefined") || (id === 0)) {
        $('#boatName').prop('disabled', false);
        $('#boatMake').prop('disabled', false);
        $('#boatModel').prop('disabled', false);
        $('#boatYear').prop('disabled', false);
        $('#boatLOA').prop('disabled', false);
        $('#boatBeam').prop('disabled', false);
        $('#boatRegno').prop('disabled', false);
        $('#boatCustomerName').prop('disabled', false);
        $('#inwater').prop('disabled', false);
        $('#aok').prop('disabled', false);
        $('#usable').prop('disabled', false);
        $('#unusable').prop('disabled', false);
        $.post("dlmFunctionDispatch.php?f=getCustomerNames", function(data) {
            var selcust = buildSelect("selectCust", data);
            $('#custHolder').html(selcust);
            if (customerId > 0) {
                selectOptionByVal('selectCust', customerId);
                $('#selectCust').prop('disabled',true);
            } else {
                $('#selectCust').prop('disabled',false);
            }
        }, "json");
        
        $.post("dlmFunctionDispatch.php?f=getSitesDropdown", function(data) {
            var sel = buildSelect("selectSite", data);
            $('#siteHolder').html(sel);
        }, "json");
        $('#boatBerth').prop('disabled', false);
        $('#boatEngine').prop('disabled', false);
        $('#boatKeys').prop('disabled', false);
        $('#boatNotes').prop('disabled', false);
        
	} else {
		$.post("dlmFunctionDispatch.php?f=getBoatDetails&bid="+id, function(data) {
            $('#boatId').val(data.id);
            $('#boatName').val(safeDecode(data.name));
            $('#boatMake').val(safeDecode(data.make));
            $('#boatModel').val(safeDecode(data.model));
            $('#boatYear').val(safeDecode(data.year));
            $('#boatLOA').val(data.LOA);
            $('#boatBeam').val(data.beam);
            $('#boatRegno').val(safeDecode(data.Regno));
            $.post("dlmFunctionDispatch.php?f=getCustomerNames", function(custdata) {
                var selcust = buildSelect("selectCust", custdata);
                $('#custHolder').html(selcust);
                $('#selectCust').prop('disabled',true);
                selectOptionByVal('selectCust', data.CustomerId);
            }, "json");
            $.post("dlmFunctionDispatch.php?f=getSitesDropdown", function(sitedata) {
                var sel = buildSelect("selectSite", sitedata);
                $('#siteHolder').html(sel);
                $('#selectSite').prop('disabled',true);
                selectOptionByVal('selectSite', data.SiteId);
            }, "json");
            $('#boatSiteName').val(safeDecode(data.SiteName));
            $('#boatBerth').val(safeDecode(data.Berth));
            pe = "";
            if (data.engines.length > 0) {
                pe += "<div style='width:100%;margin-bottom:5px;background:#EEEEEE;'>";
                pe += "<div style='width:150px; margin-right:20px; float:left'>Type</div>";
                pe += "<div style='width:150px; margin-right:20px; float:left'>Make</div>";
                pe += "<div style='width:150px; margin-right:20px; float:left'>Model</div>";
                pe += "<div style='width:150px; float:left'>Serial No.</div>";
                pe += "<br class='clear' />";
                pe += "</div>";
                for (i=0; i < data.engines.length; i++) {
                    pe += "<div style='width:100%;margin-bottom:5px;'>";
                    pe += "<div style='width:150px; margin-right:20px; float:left; text-align:left;'>";
                        pe += "<a onclick='showEngine(" + data.engines[i].EngineId + "," + id + ");'>&nbsp;" + safeDecode(data.engines[i].EngineType) + "</a>";
                    pe += "</div>";
                    pe += "<div style='width:150px; margin-right:20px; float:left; text-align:left;'>&nbsp;" + safeDecode(data.engines[i].EngineMake) + "</div>";
                    pe += "<div style='width:150px; margin-right:20px; float:left; text-align:left;'>&nbsp;" + safeDecode(data.engines[i].EngineModel) + "</div>";
                    pe += "<div style='width:150px; float:left; text-align:right;'>&nbsp;" + data.engines[i].EngineSerialno + "</div>";
                    pe += "<br class='clear' />";
                    pe += "</div>";                   
                }
                pe += "<div style='float:right;'><a onclick='showEngine(0, " + id + ");'><img src='images/plus.png' title='Add Engine' /></a></div>";
            } else {
                pe += "<div style='float:left; background:red; color:white;border-top:solid thin #AAAAAA;'>No Engines Registered</div>";
                pe += "<div style='float:right; border-bottom:solid thin #AAAAAA;'><a onclick='showEngine(0, " + id + ");'><img src='images/plus.png' title='Add Engine' /></a></div>";
                pe += "<br class='clear' />";            
            }
            $('#enginesSection').html(pe);
            $('#boatKeys').val(safeDecode(data.boatKeys));
            $('#inwater').prop( "checked", ((data.inwater == 8)?true:false));
            if (data.state == 4) {
                $('#aok').prop( "checked", true);
            } else if (data.state == 2) {
                $('#usable').prop( "checked", true);
            } else if (data.state == 1) {
                $('#unusable').prop( "checked", true);
            }
            $('#boatNotes').val(safeDecode(data.notes));
            $('#boatNotesCC').html(data.notes.length + " characters of 250 Max");
            $('#created').html(data.created);
            if (data.updated == "0000-00-00 00:00:00") {
                $('#updated').html("Never");
            } else {
                $('#updated').html(data.updated);
                $('#updatedBy').html(" by: " + data.updatedBy);
            }
		}, "json");
	}
}

function addBoat(id) {      // Used for Add New Boat AND update existing boat
	// Check mandatory fields
	if (($('#boatName').val() === "") || ($('#selectSite').val() == -1) || ($('#selectCust').val() == -1)) {
		myAlert("Missing Infomation", "Please check that you have entered a Boat Name, a Customer and selected a Site.");
	} else {
        var name = safeEncode($('#boatName').val());
        var make = safeEncode($('#boatMake').val());
        var model = safeEncode($('#boatModel').val());
        var year = safeEncode($('#boatYear').val());
        var loa = safeEncode($('#boatLOA').val());
        if ((loa.substr(loa.length - 1)).toUpperCase() == "F") {    // If LOA ends in 'f' we convert to Metres.
            loa = loa.substring(0, (loa.length - 1));
            loa = loa * 0.3048;
        }
        var beam = safeEncode($('#boatBeam').val());
        if ((beam.substr(loa.length - 1)).toUpperCase() == "F") {
            beam = beam.substring(0, (loa.length - 1));
            beam = beam * 0.3048;
        }
        var regno = safeEncode($('#boatRegno').val());
        var owner = safeEncode($('#selectCust').val());
        var site = safeEncode($('#selectSite').val());
        var berth = safeEncode($('#boatBerth').val()); 
        var inwater = (($('#inwater').prop('checked'))?$('#inwater').val():"0");
        var state = $("input:radio[name='state']:checked").val()
        var keys = safeEncode($('#boatKeys').val());
        var notes = safeEncode($('#boatNotes').val());
        
        var u = $.cookie('user');
        var boatJSON = {"name":name, "make":make, "model":model, "year":year, "loa":loa, "beam":beam, "regno":regno, "owner":owner, "site":site, "berth":berth, "keys":keys, "inwater":inwater, "state":state, "notes":notes, "u":u};
        if((typeof id === "undefined") || (id == 0)) {
            // It's a new boat so Add it to db
            $.post("dlmFunctionDispatch.php?f=addBoat", boatJSON, function(data) {
                if (data.status == "OK") {
                    var msg = name + " (" + make + ") added at " + $("#selectSite option:selected").text();
                    var from = $.cookie('user');
                    var notification = {"notification":"NEWBOAT", "from":from, "title":"New Boat", "msg":msg };
                    $.post("dlmFunctionDispatch.php?f=notify", notification, function(notifydata) {
                        // Now prompt for engine
                        $('<div />').dialog({
                            modal: true,
                            title: "Now Add Engine",
                            width: "auto",
                            open: function() { $(this).html("Register Engine for this Boat?"); },
                            close : function(){ $(this).empty(); $(this).remove(); },
                            buttons: {
                              "Yes": function() {
                                  $( this ).dialog("close");
                                  location.href = "engines.php?newengine=" + data.id;
                              },
                              "No": function() {
                                  $( this ).dialog("close");
                                  location.reload();
                              }
                            }
                          });  //end confirm dialog                         
                        }, "json");
                } else {
                    myAlert(data.status, data.msg);
                }
            }, "json");
        } else {
            // It must be an update
            $.post("dlmFunctionDispatch.php?f=updateBoat&id=" + id, boatJSON, function(data) {
                if (data.status == "OK") {
                    myAlert("Operation Complete", "Boat Details Updated");
                    location.reload();
                } else {
                    myAlert(data.status, data.msg);
                }
            }, "json");
        }
    }
}    

function editBoat(id) {
	var pe;
	pe = "<button class='btn' onclick='addBoat(\"" + id +  "\");'>Save</button>&nbsp;&nbsp;";
	pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Cancel</button>";
	$('#boatBtns').html(pe);
    $('#boatMake').prop('disabled', false);
    $('#boatModel').prop('disabled', false);
    $('#boatYear').prop('disabled', false);
    $('#boatLOA').prop('disabled', false);
    $('#boatBeam').prop('disabled', false);
    $('#boatRegno').prop('disabled', false);
    $('#boatBerth').prop('disabled', false);
    $('#boatKeys').prop('disabled', false);
    $('#boatNotes').prop('disabled', false);
    $('#selectCust').prop('disabled', false);
    $('#selectSite').prop('disabled', false);
    $('#inwater').prop('disabled', false);
    $('#aok').prop('disabled', false);
    $('#usable').prop('disabled', false);
    $('#unusable').prop('disabled', false);
}

function deleteBoat(id) {
	var prompt = "Delete <b>" + $('#boatName').val() + "</b><br><br>";
	prompt += "<p style='font-style:italic;'><b>Note 1:</b>: Any Engine associated with this boat along with boat and engine history will also be deleted.</p><br>";
	prompt += "<p style='font-style:italic;'><b>Note 2:</b>: Job Sheets associated with this boat and its engine will NOT be deleted.</p><br>";
	prompt += "<p style='color:red; text-align:center;'>This operation cannot be undone!</p>";
    $('<div />').html(prompt).dialog({
        title: 'Delete Boat',
        resizable: false,
        width:350,
        height:"auto",
        modal: true,
        close : function(){ $(this).empty(); $(this).remove(); },
        buttons: {
            "Delete": function() {
                $.post("dlmFunctionDispatch.php?f=deleteBoat&id=" + id, function(data) {
                    if (data.status == "OK") {
                        myAlert("Operation Complete", "Boat Deleted!");
                        location.reload();
                    } else {
                        myAlert(data.status, data.msg);
                    }
                    $( this ).dialog( "close" );
                }, "json");
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        }
    });
}

/******************************************************************************
 * ENGINE Functions
 ******************************************************************************/
/**************************************************************************************************
 * showEngine can be called with engine id or boat id.
 * If engine id is supplied then that is used to access the engine table directly
 * If boat id is supplied then the first record in the engine database that has a matching boatId is fetched, if no engine is found then
 * this is a new engine for that boat.
 * If both args are missing or 0 this is a new engine and a boat will be required in the form.
 * @param {int} engineId -  Engine Id
 * @param {int} boatId - Boat Id
 * @returns {N/A} Displays User Form in 'dialog'
 */
function showEngine(engineId, boatId) {
    showLoading();
    // Set up base form
	var frmTitle = "Engine Details";
	var pe = "<div id='viewengineform' style='width:735px;'>";
    pe += "<div style='width:350px;float:left;'>Boat<br><span id='boatHolder' style='width:340px'/></div>";
    pe += "<div style='width:350px;float:left;margin-left:20px;'>Site<br><span id='siteHolder' style='width:340px;'/></div>";
    pe += "<br class='clear' />";
    pe += "<input id='engineId' type='text' style='display:none' />";
    pe += "<div style='width:100%;margin-bottom:5px;'>";
        pe += "<div style='width:240px; margin-right:20px; float:left'>Make&nbsp;&&nbsp;Model<br><span id='makeHolder' style='width:240px'/></div>";
        pe += "<div style='width:115px; margin-right:20px; float:left'>Type<br>";
            pe += "<select id='type' style='width:115px;' disabled >";
            pe += "<option value=-1></option>";
            pe += "<option value='Inboard'>Inboard</option>";
            pe += "<option value='Outboard'>Outboard</option>";
            pe += "<option value='Sterndrive'>Sterndrive</option>";
            pe += "<option value='V-Drive'>V-Drive</option>";
            pe += "<option value='Hybrid'>Hybrid</option>";
            pe += "<option value='Other'>Other</option>";
            pe += "</select>";
        pe += "</div>";
        pe += "<div style='width:55px; margin-right:20px; float:left'>Cylinders<br><input id='cylinders' type='text' style='width:55px;' maxlength='30' disabled ></div>";
        pe += "<div style='width:55px; margin-right:20px; float:left'>Capacity<br><input id='capacity' type='text' style='width:55px;' maxlength='30' disabled ></div>";
        pe += "<div style='width:55px; margin-right:20px; float:left'>Fuel<br><input id='fuel' type='text' style='width:55px;' maxlength='30' disabled ></div>";
        pe += "<div style='width:90px; margin-right:20px; float:left'>Serial No.<br><input id='serialnumber' type='text' style='width:90px;' maxlength='30' disabled ></div>";
        pe += "<br class='clear' />";
        pe += "<div style='width:715px;'>";
            pe += "Notes<br><textarea name='engineNotes' id='engineNotes' style='width:100%' rows='4' wrap='soft' maxlength='200' onkeydown='maxTxt(\"#engineNotes\",\"#engineNotesCC\");' disabled></textarea>";
        pe += "</div>";
        pe += "<div style='width:100%;margin-right:20px;'>";
            pe += "<div id='engineNotesCC' style='font-size:0.7em; color:#666666; text-align:right;padding-right:5px'>&nbsp;</div></td>";
        pe += "</div>";
    pe += "</div>";
    
    pe += "<div id='siSection' style='width:100%; margin:0px;'>&nbsp</div>";

    pe += "<div style='width:100%'>";
        pe += "<div id='engineUpdates' style='width:325px;float:left;padding:15px 0 0 5px;color:#666666;font-size:0.7em'></div>";
        pe += "<div id='engineBtns' style='width:250px;float:right;text-align:right;margin-top:5px;padding-top:5px;'>&nbsp;</div>";
        pe += "<br class='clear' />";
    pe += "</div>";
    displayResults('dialog', frmTitle, pe);    // Show Form
    
    /**************************************************************************
     * Now we load form
     ***/

    // First set up the Boat, Site and Engine Dropdowns
    $.post("dlmFunctionDispatch.php?f=getSitesDropdown", function(data) {
        var selectSite = buildSelect("selectSite", data);
        $('#siteHolder').html(selectSite);
        selectOptionByText('selectSite',"Walk-in");
        $('#selectSite').prop('disabled',true);
        $.post("dlmFunctionDispatch.php?f=getBoatNames&cid=0", function(data) { // cid=0 means all boats
            if (data.status == "DB_Error") {
                alert(data.msg);
            }
            var selboat = buildSelect("selectBoat", data);
            $('#boatHolder').html(selboat);
            if (boatId > 0) {  // Pre-select boat if supplied
                selectOptionByVal('selectBoat', boatId);
                $('#selectBoat').prop('disabled',true);
                var selected = $('#selectBoat').find('option:selected');    // Now set the Site based on the Boat selected (Second value of otion)
                selectOptionByVal('selectSite', selected.data('arg'));
                $('#selectSite').prop('disabled',true);
            } else {
                $('#selectBoat').prop('disabled',false);
            }
            $('#selectBoat').change(function(){
                if ($("#selectBoat").val() > 0) {
                    var selected = $(this).find('option:selected');
                    selectOptionByVal('selectSite', selected.data('arg'));
                    $('#selectSite').prop('disabled',true);
                }
            });
            hideLoading();
            // Now we load list of Engine Templates
            $.post("dlmFunctionDispatch.php?f=getEngineTemplateList", function(etData) {
                var selJson = '{"options":[';
                for (var i=0; i<etData.resultcount; i++) {
                    selJson += '{';
                    selJson += '"id":"' + etData.results[i].id + '", ';
                    selJson += '"name":"' + etData.results[i].make + ' ' + etData.results[i].model + '"';
                    selJson += '},';
                }
                if (i > 0) { selJson = strim(selJson, ","); }
                selJson += ']}';
                selJSONobj = $.parseJSON(selJson);
                var sel = buildSelect("selEngine", selJSONobj);
                $('#makeHolder').html(sel);

                if ((typeof engineId === "undefined") || (engineId == 0)) {    // If no engine id set up for New
                    frmTitle = "Enter " + frmTitle;
                    $('#type').prop('disabled', false);
                    $('#serialnumber').prop('disabled', false);
                    $('#engineNotes').prop('disabled', false);
                    editEngine(engineId,1);   // Use editEngine function to get data for new engine
                } else {    // Load form with details of supplied engine (engineId)
                    pe = "Created: <span id='created' /><br>";
                    pe += "Last Update: <span id='updated' /> <span id='updatedBy' /><br>";
                    $('#engineUpdates').html(pe);
                    pe = "";
                    if ($.cookie("userType") == "Admin") {
                        pe += "<button class='btn' onclick='editEngine(\"" + engineId +  "\", 0);'>Edit</button>&nbsp;&nbsp;";
                        pe += "<button class='btn' onclick='deleteEngine(\"" + engineId +  "\");'>Delete</button>&nbsp;&nbsp;";
                    }
                    pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
                    $('#engineBtns').html(pe);
                    $.post("dlmFunctionDispatch.php?f=getEngineDetails&eid="+engineId, function(data) {
                        if (data.status == "DB_Error") {
                            myAlert(data.status, data.msg);
                        } else {
                            selectOptionByVal('selEngine', data.etId);  // Select correct Engine Template
                            $('#selEngine').prop('disabled', true);     // ...and lock dropdown
                            $('#engineId').val(data.id);
                            selectOptionByVal('type', data.engineType);
                            $('#serialnumber').val(data.serialno);
                            $('#cylinders').val(safeDecode(data.cylinders));
                            $('#capacity').val(safeDecode(data.capacity));
                            $('#fuel').val(data.fuel);
                            $('#engineNotes').val(safeDecode(data.notes));
                            $('#created').html(data.created);
                            $('#updated').html(data.updated);
                            $('#updatedBy').html(data.updatedBy);
                            selectOptionByVal('selectBoat', data.boatId);
                            $('#selectBoat').prop('disabled',true);
                            selectOptionByVal('selectSite', data.siteId);
                            $('#selectSite').prop('disabled',true);
                            if (data.serviceItems.length > 0) {
                                var pe = "";
                                var typeColor;
                                for (i=0; i < data.serviceItems.length; i++) {
                                    if (data.serviceItems[i].siIsmod == 1) {
                                        typeColor = "E7E737";
                                    } else {
                                        typeColor = "a7a7a7";
                                    }
                                    pe += "<div  id='" + data.serviceItems[i].siName.replace(/ /g, '-')  + "' style='width:100%;margin:0px;padding:0;'>";
                                        pe += "<div class='siElement' style='width:10px;background:#" + typeColor + ";'>&nbsp;</div>";
                                        pe += "<div class='siElement' style='width:220px;'>&nbsp;" + safeDecode(data.serviceItems[i].siName) + "</div>";
                                        pe += "<div class='siElement' style='width:200px;'>&nbsp;" + safeDecode(data.serviceItems[i].siMake) + "</div>";
                                        pe += "<div class='siElement' style='width:120px;'>&nbsp;" + safeDecode(data.serviceItems[i].siPartno) + "</div>";
                                        if (data.serviceItems[i].siIsmod == 1) {
                                            pe += "<div id='EM-" + data.serviceItems[i].siMapId + "' class='siDelMod'>";
                                                pe += "<a onclick='deleteMod(" + data.serviceItems[i].siMapId + " );'>";
                                                pe += "<img src='images/deleteX.gif'></a>";
                                            pe += "</div>";
                                        }
                                        pe += "<input type='text' class='siQty' ";
                                                if ((data.serviceItems[i].siQty == "0") || (data.serviceItems[i].siQty == "")) {
                                                    pe += " style='background:red; color:white;' ";
                                                } 
                                                pe += "id='siQty-" + data.serviceItems[i].siMapId + "' maxlength='10' disabled value='" + safeDecode(data.serviceItems[i].siQty) + "'/>";
                                        pe += "<br class='clear' />";
                                    pe += "</div>";
                                }
                                $('#siSection').html(pe);
                            }
                        }
                    }, "json"); // End of getEngineDetails                
                }           
            }, "json"); // End of getEngineTemplateList
        }, "json");  // End of getBoatNames
}, "json"); // End of getSiteNames
}


/**************************************************************************************************
 * editEngine - also used to add new engines
 * If id is supplied then that is used to access the engine table directly and available for edit
 * if id equals 0 then all fields are blank and data for a new engine can be entered
 * @param {int} id -  Engine Id
 * @param {int} isNew - 1 = New Engine, 0 = Existing Engine
 */
function editEngine(id, isNew) {
    if ($('#selectBoat').val() == -1) { $('#selectBoat').prop('disabled',false); }
    $('#type').prop('disabled', false);
    $('#serialnumber').prop('disabled', false);
// # 011017 Don't allow change of engine #    
    if (isNew) { $('#selEngine').prop('disabled', false); }
    $('#engineNotes').prop('disabled', false);
    $('[class="siQty"]').prop('disabled', false);
    $('[class="siQty"]').change(function() {
        if($(this).val() != 0) { 
            $(this).css("background-color", "white");
            $(this).css("color", "black");
        }
    });
    $('[class="siDelMod"]').each(function(){  // Show delete option
       $(this).css("visibility", "visible");
    });
    // addSItoETform(engineId, 1)
    $('#siSection').append("<a onclick='addSItoETform(" + id + ",1);'><img src='images/plus.png' title='Add modification' />Add Modification</a>");

	var pe = "<button class='btn' onclick='updateEngine(\"" + id +  "\");'>Save</button>&nbsp;&nbsp;";
	pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Cancel</button>";
	$('#engineBtns').html(pe);

// ########    
// # 011017 - Stop allowing change of engine. If they want to changethe engine they must simply delete the current one and add a new one
// ########

    /***************************************************************************************
     If this is a new engine we need to watch for the selection so we can populate the form.
     ***************************************************************************************/
    if (isNew) {
        $("#selEngine").change(function() {  // Load Engine features & service Items
            var etId = $("#selEngine").val();
            $.post("dlmFunctionDispatch.php?f=getEngineTemplateDetails&eid=" + etId, function(data) {
                $('#cylinders').val(safeDecode(data.cylinders));
                $('#capacity').val(safeDecode(data.capacity));
                $('#fuel').val(data.fuel);
                $('#notes').val(data.notes);
                if (data.serviceItems.length > 0) {
                    pe = "<div style='width:250px; margin:0px; float:left; text-align:center;'>Service Part</div>";
                    pe += "<div style='width:120px; margin:0px; float:left; text-align:center;'>&nbsp;Make</div>";
                    pe += "<div style='width:120px; margin:0px; float:left; text-align:center;'>&nbspPartno</div>";
                    pe += "<div style='width:100px; margin:0px; float:right; text-align:center;'>Qty</div>";
                    pe += "<br class='clear' />";
                    for (i=0; i < data.serviceItems.length; i++) {
                        pe += "<div  id='" + data.serviceItems[i].siName.replace(/ /g, '-')  + "' style='width:100%;margin:0px;padding:0;'>";
                            pe += "<div class='siElement' style='width:10px;background:#a7a7a7;'>&nbsp;</div>";
                            pe += "<div class='siElement' style='width:220px;'>" +  safeDecode(data.serviceItems[i].siName) + "</div>";
                            pe += "<div class='siElement' style='width:120px;'>&nbsp;" + safeDecode(data.serviceItems[i].siMake) + "</div>";
                            pe += "<div class='siElement' style='width:120px;'>&nbsp;" + safeDecode(data.serviceItems[i].siPartno) + "</div>";
                            if (typeof data.serviceItems[i].siQty === "undefined") {
                                data.serviceItems[i].siQty = "0";
                            }
                            pe += "<input type='text' class='siQty' id='siQty-" + data.serviceItems[i].siId + "' maxlength='10' disabled value='" + safeDecode(data.serviceItems[i].siQty) + "'/>";
                            pe += "<br class='clear' />";
                        pe += "</div>";
                    }
                    $('#siSection').html(pe);
                } else {
                    myAlert("No Data", "No Service Items recorded for this engine.");
                }
            },"json");
        });
    }
}

/******************************************************************************
 * deleteEngine - deletes engine record engineId, removing engine from a boat
 *
 * @param {int} engineId - id in engines table
 */

function deleteEngine(engineId) {
	var prompt = "Delete Engine<br><br>";
	prompt += "<p style='font-style:italic;'>This operation will delete the engine from this boat, any modified service items associated with this engine will also be deleted.</p><br>";
	prompt += "<p style='color:red; text-align:center;'>This operation cannot be undone!</p>";
    $('<div />').html(prompt).dialog({
        title: 'Delete Engine',
        resizable: false,
        width:350,
        height:"auto",
        modal: true,
        close : function(){ $(this).empty(); $(this).remove(); },
        buttons: {
            "Delete": function() {
                $.post("dlmFunctionDispatch.php?f=deleteEngine&eid=" + engineId, function(data) {
                    if (data.status == "OK") {
                        myAlert("Operation Complete", "Engine Deleted!");
                        location.reload();
                    } else {
                        myAlert(data.status, data.msg);
                    }
                    $( this ).dialog( "close" );
                }, "json");
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        }
    });
}

/******************************************************************************
 * deleteMod deletes an entry in the engineMods with id
 *
 * param {int} id - engineMods Id
 */
function deleteMod(id) {
    $.post("dlmFunctionDispatch.php?f=deleteMod&id="+id, function(data) {
        if (data.status == "OK") {
            location.reload();
        } else {
            myAlert(data.status, data.msg);
        }
    }, "json");
}

/******************************************************************************
 * updateEngine - updates record for engine engineId.
 * NOTE: If engineId = 0 then thiis is actually the creation of a new engine.
 *
 * param {int} id - engineMods Id
 */
function updateEngine(id) {
    var etId = $("#selEngine").val();
    var engineType = safeEncode($('#type').val());
    if ((engineType == -1) || (etId == -1) || ($('#selectSite').val() == -1)) {
        myAlert("Missing Information", "Please make sure you have specified Engine, Engine type and Site.");
    } else {
        var siteId = $('#selectSite').val();
        var boatId = $('#selectBoat').val();
        var serialno = safeEncode($('#serialnumber').val());
        var notes = safeEncode($('#engineNotes').val());
        var u = $.cookie('user');
        var engineJSON = {"etId":etId, "engineType":engineType, "serialno":serialno, "siteId":siteId, "boatId":boatId, "notes":notes, "siqty":[], "u":u};
        $(".siQty").each(function( index ) {    // Get quantities of standard service items
            siMapId = $(this).attr('id').substring(6);
            siQty = $('#siQty-'+siMapId).val();
            engineJSON.siqty.push({"siMapId":siMapId, "siQty":siQty});
        });

        $.post("dlmFunctionDispatch.php?f=updateEngine&eid="+id, engineJSON, function(data) {
            if (data.status == "OK") {
                location.href = "engines.php";
            } else {
                myAlert(data.status, data.msg);
            }
        }, "json");
    }
}
/******************************************************************************
 * ENGINE TEMPLATE Functions
 * 		showEngineTemplate
 * 		editEngineTemplate
 ******************************************************************************/
function showEngineTemplate(id) {
	var frmTitle = "Engine Template Details";
	var pe = "<div id='viewenginetemplateform' style='width:680px;'>";
        pe += "<input id='engineId' type='text' style='display:none' />";
        pe += "<div style='width:100%;margin-bottom:15px;'>";
            pe += "<div style='width:180px; margin-right:20px; float:left'>Make<br><input id='make' type='text' style='width:180px;' maxlength='30' disabled ></div>";
            pe += "<div style='width:180px; margin-right:20px; float:left'>Model<br><input id='model' type='text' style='width:180px;' maxlength='30' disabled ></div>";
            pe += "<div style='width:70px; margin-right:20px; float:left'>Cylinders<br><input id='cylinders' type='text' style='width:70px;' maxlength='30' disabled ></div>";
            pe += "<div style='width:70px; margin-right:20px; float:left'>Capacity<br><input id='capacity' type='text' style='width:70px;' maxlength='30' disabled ></div>";
            pe += "<div style='width:70px; margin-right:20px; float:left'>Fuel<br><input id='fuel' type='text' style='width:70px;' maxlength='30' disabled ></div>";
        pe += "<br class='clear' />";
        pe += "</div>";
        pe += "<div style='width:100%;border-bottom:thin solid #AAAAAA; margin-bottom:5px;'>";
            pe += "<div style='width:240px; margin:0px; float:left'>&nbsp</div>";
            pe += "<div id='makeHdr' style='width:250px; margin:0px; float:left; text-align:center;visibility:hidden;'>Make</div>";
            pe += "<div id='partHdr' style='width:120px; margin:0px; float:left; text-align:center;visibility:hidden;'>Partno</div>";
            pe += "<br class='clear' />";
        pe += "</div>";

        pe += "<div id='siSection' style='width:100%'>&nbsp</div>";

		if(typeof id === "undefined") {
			frmTitle = "Enter " + frmTitle;
			pe += "<div id='engineBtns' style='text-align:right;padding-top:15px;'>";
			pe += "<button class='btn' onclick='addEngineTemplate();'>Add</button>&nbsp;&nbsp;";
			pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
			pe += "</div>";
		} else {
			pe += "<div id='engineTemplateUpdates' style='width:45%;float:left;padding:15px 0 0 5px;color:#666666;font-size:0.7em'>";
            pe += "Created: <span id='created' /><br>";
            pe += "Last Update: <span id='updated' /> <span id='updatedBy' /><br>";
            pe += "</div>";
            pe += "<div id='engineTemplateBtns' style='width:45%;float:right;text-align:right;padding-top:15px;'>";
            if ($.cookie("userType") == "Admin") {
                pe += "<button class='btn' onclick='editEngineTemplate(\"" + id +  "\");'>Edit</button>&nbsp;&nbsp;";
                pe += "<button class='btn' onclick='deleteEngineTemplate(\"" + id +  "\");'>Delete</button>&nbsp;&nbsp;";
            }
            pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
			pe += "</div>";
		}

	displayResults('dialog', frmTitle, pe);

	if(typeof id === "undefined") {
        $('#make').prop('disabled', false);
        $('#model').prop('disabled', false);
        $('#cylinders').prop('disabled', false);
        $('#capacity').prop('disabled', false);
        $('#fuel').prop('disabled', false);
        $('#notes').prop('disabled', false);
	} else {
		$.post("dlmFunctionDispatch.php?f=getEngineTemplateDetails&eid="+id, function(data) {
            $('#engineId').val(data.id);
            $('#make').val(safeDecode(data.make));
            $('#model').val(safeDecode(data.model));
            $('#cylinders').val(safeDecode(data.cylinders));
            $('#capacity').val(safeDecode(data.capacity));
            $('#fuel').val(data.fuel);
            $('#notes').val(data.notes);
            pe = "";
            if (data.serviceItems.length > 0) {
                $('#makeHdr').css("visibility", "visible")
                $('#partHdr').css("visibility", "visible")
                for (i=0; i < data.serviceItems.length; i++) {
                    pe += "<div  id='" + data.serviceItems[i].siName.replace(/ /g, '-')  + "' style='width:100%;margin:0px;padding:0;'>";
                        pe += "<div id='ET-" + data.serviceItems[i].siId + "' style='width:20px; margin:0px; float:left; visibility:hidden;'>";
                            pe += "<a onclick='deleteETSI(" + id + ", " + safeDecode(data.serviceItems[i].siId) + " );'>";
                            pe += "<img src='images/deleteX.gif'></a>";
                        pe += "</div>";
                        pe += "<div style='width:220px; margin:0px; float:left;'>&nbsp;" +  safeDecode(data.serviceItems[i].siName) + "</div>";
                        pe += "<div style='width:250px; margin:0px; float:left; text-align:left;'>&nbsp;" + safeDecode(data.serviceItems[i].siMake) + "</div>";
                        pe += "<div style='width:120px; margin:0px; float:left; text-align:left;'>&nbsp;" + safeDecode(data.serviceItems[i].siPartno) + "</div>";
                        pe += "<br class='clear' />";
                    pe += "</div>";
                }
                $('#siSection').html(pe);
            }
            $('#created').html(data.created);
            if (data.updated == "0000-00-00 00:00:00") {
                $('#updated').html("Never");
            } else {
                $('#updated').html(data.updated);
                $('#updatedBy').html(" by: " + data.updatedBy);
            }
		}, "json");

	}
}

function addEngineTemplate() {
	// Check mandatory fields
	if ($('#make').val() === "") {
		myAlert("Incomplete Information", "Please check that you have entered a Make");
	} else {
        var make = safeEncode($('#make').val());
        var model = safeEncode($('#model').val());
        var cylinders = safeEncode($('#cylinders').val());
        var capacity = $('#capacity').val();
        var fuel = $('#fuel').val();
//        var notes = safeEncode($('#notes').val());
        var u = $.cookie('user');
//        var etJSON = {"make":make, "model":model, "cylinders":cylinders, "capacity":capacity, "fuel":fuel, "notes":notes, "u":u};
        var etJSON = {"make":make, "model":model, "cylinders":cylinders, "capacity":capacity, "fuel":fuel, "u":u};
        $.post("dlmFunctionDispatch.php?f=addEngineTemplate", etJSON, function(data) {
			if (data.status == "OK") {
				location.href = "admin.php?opt=etMgr";
			} else {
				myAlert("No action taken.", data.msg);
                location.href = "admin.php?opt=etMgr";
			}
		}, "json"); 	
	} 
}

function editEngineTemplate(id) {
	var pe;
	pe = "<button class='btn' onclick='updateEngineTemplate(\"" + id +  "\");'>Save</button>&nbsp;&nbsp;";
	pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Cancel</button>";
	$('#engineTemplateBtns').html(pe);
    $('#make').prop('disabled', false);
    $('#model').prop('disabled', false);
    $('#cylinders').prop('disabled', false);
    $('#capacity').prop('disabled', false);
    $('#fuel').prop('disabled', false);
    $('#notes').prop('disabled', false);
    $("div[id^='ET-']").each(function(){  // Show delete option
       $('#'+this.id).css("visibility", "visible");
    });
    $('#siSection').append("<div style='margin:5px;'><a onclick='addSItoETform("+id+", 0);'><img src='images/plus.png'>&nbsp;Add Service Item<a></div>");
}

function updateEngineTemplate(id) {
	// Check mandatory fields
	if ($('#make').val() === "") {
		myAlert("Incomplete Information", "Please check that you have entered a Make");
	} else {
        var make = safeEncode($('#make').val());
        var model = safeEncode($('#model').val());
        var cylinders = safeEncode($('#cylinders').val());
        var capacity = $('#capacity').val();
        var fuel = $('#fuel').val();
        var notes = safeEncode($('#notes').val());
        var u = $.cookie('user');
        var etJSON = {"make":make, "model":model, "cylinders":cylinders, "capacity":capacity, "fuel":fuel, "notes":notes, "u":u};
        $.post("dlmFunctionDispatch.php?f=updateEngineTemplate&id=" + id, etJSON, function(data) {
			if (data.status == "OK") {
				location.href = "admin.php?opt=etMgr";
			} else {
				myAlert("No action taken.", data.msg);
			}
		}, "json"); 	
	}     
}

function deleteEngineTemplate(id) {
    // First we check to see if there are any Boats using this engine.
    $.post("dlmFunctionDispatch.php?f=getBoatsByET&id="+id, function(data) {
        if (data.resultcount < 1) {
            $.post("dlmFunctionDispatch.php?f=deleteEngineTemplate&id="+id, function(data) {
                myAlert("Operation Complete", "Engine Template Deleted");
                $('#dialog').dialog( "close" );
                location.href = "admin.php?opt=etMgr";               
            });
        } else {
            myAlert("Engine Template cannot be deleted", "Engines of this type are registered to " + data.resultcount + " boat" + ((data.resultcount != 1)?"s":""));
        }
    }, "json");
}

/******************************************************************************
 * SERVICE ITEMS Functions
 * 		showServiceItem
 * 		addServiceItem
 *      editServiceItem
 *      updateServiceItem
 *      deleteServiceItem
 ******************************************************************************/
function showServiceItem(id) {
	var frmTitle = "Service Item Details";
	var pe = "<div id='viewserviceitemform' style='width:680px;'>";
        pe += "<input id='siId' type='text' style='display:none' />";
        pe += "<div style='width:100%;margin-bottom:15px;'>";
            pe += "<div style='display:none; float:left'><input id='siId' type='text' style='width:180px;' maxlength='30' disabled ></div>";
            pe += "<div style='display:none; float:left'><input id='siNameId' type='text' style='width:180px;' maxlength='30' disabled ></div>";
            pe += "<div style='width:220px; margin-right:20px; float:left'>Name<br><span  id='nameHolder'><input id='name' type='text' style='width:220px;' maxlength='30' disabled ></span></div>";
            pe += "<div style='width:160px; margin-right:20px; float:left'>Make<br><input id='make' type='text' style='width:160px;' maxlength='30' disabled ></div>";
            pe += "<div style='width:120px; margin-right:20px; float:left'>Partno<br><input id='partno' type='text' style='width:120px;' maxlength='30' disabled ></div>";
            pe += "<div style='width:100px; margin-right:20px; float:left'>Price<br><input id='price' type='text' style='width:100px;' maxlength='10' disabled ></div>";
            pe += "<br class='clear' />";
            pe += "<div style='width:100%'>";
            pe += "Notes<br><textarea name='notes' id='notes' style='width:660px' rows='4' wrap='soft' maxlength='200' onkeydown='maxTxt(\"#notes\",\"#notesCC\");' disabled></textarea>";
            pe += "</div>";
            pe += "<div style='width:100%'>";
            pe += "<div id='notesCC' style='font-size:0.7em; color:#666666; text-align:right;padding-right:5px'>&nbsp;</div></td>";
            pe += "</div>";
        pe += "<br class='clear' />";
        pe += "</div>";

		if(typeof id === "undefined") {
			frmTitle = "Enter " + frmTitle;
			pe += "<div id='siBtns' style='text-align:right;padding-top:15px;'>";
			pe += "<button class='btn' onclick='addServiceItem();'>Add</button>&nbsp;&nbsp;";
			pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
			pe += "</div>";
		} else {
			pe += "<div id='engineTemplateUpdates' style='width:45%;float:left;padding:15px 0 0 5px;color:#666666;font-size:0.7em'>";
            pe += "Created: <span id='created' /><br>";
            pe += "Last Update: <span id='updated' /> <span id='updatedBy' /><br>";
            pe += "</div>";
            pe += "<div id='siBtns' style='width:45%;float:right;text-align:right;padding-top:15px;'>";
            if ($.cookie("userType") == "Admin") {
                pe += "<button class='btn' onclick='editServiceItem(\"" + id +  "\");'>Edit</button>&nbsp;&nbsp;";
                pe += "<button class='btn' onclick='deleteServiceItem(\"" + id +  "\");'>Delete</button>&nbsp;&nbsp;";
            }
            pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
			pe += "</div>";
		}

	displayResults('dialog', frmTitle, pe);

	if(typeof id === "undefined") { // Must be trying to add new Service Item
        $.post("dlmFunctionDispatch.php?f=getSINamesDropdown", function(data) {
            var sel = buildSelect("selectName", data);
            $('#nameHolder').html(sel);
            $('#selectName').append($("<option></option>")
                                    .attr("value",0)
                                    .css({"background-color":"blue", "color":"white", "text-align":"center", "padding":"4px"})
                                    .text("New Type"));
            $("#selectName").change(function() {
                if ($(this).val() == "0") {
                    $('#dialog').dialog('close');   // Close this instance of Add Service Item form
                    pe = "<input id='newSIName' type='text' style='width:300px;' maxlength='30' >";
                    $('<div />').html(pe).dialog({
                        title: 'Enter new Service Item Type',
                        resizable: false,
                        width:"auto",
                        height:"auto",
                        modal: true,
                        close : function(){ $(this).empty(); $(this).remove(); }, 
                        buttons: {
                            "Add": function() {
                                var nsin = safeEncode($("#newSIName").val());
                                var user = $.cookie('user');
                                $.post("dlmFunctionDispatch.php?f=newSIName&nsin=" + nsin + "&user=" + user, function(data) {
                                    if (data) {
                                        showServiceItem();
                                    } else {
                                        myAlert("Operation Failed", "Item was not added to database.");
                                    }
                                });                                
                                $(this).dialog( "close" );
                            },
                            Cancel: function() {
                                $(this).dialog( "close" );

                            }
                        }
                    });
                }
            });
        }, "json");
        $('#make').prop('disabled', false);
        $('#partno').prop('disabled', false);
        $('#price').prop('disabled', false);
        $('#notes').prop('disabled', false);
	} else {
		$.post("dlmFunctionDispatch.php?f=getServiceItemDetails&sid="+id, function(data) {
            if (data.status == "DB_Error") {
                myAlert(data.status, data.msg);
            } else {
                $('#siId').val(data.siId);
                $('#siNameId').val(data.siNameid);
                $('#name').val(safeDecode(data.name));
                $('#make').val(safeDecode(data.make));
                $('#partno').val(safeDecode(data.partno));
                $('#price').val(safeDecode(data.price));
                $('#notes').val(safeDecode(data.notes));
                $('#created').html(data.created);
                $('#updated').html(data.updated);
                $('#updatedBy').html(" by " + data.updatedBy);
            }
		}, "json");
	}    
}

function addServiceItem() {
    var siNameId = $('#selectName').val();
    var make = safeEncode($('#make').val());
	var partno= safeEncode($('#partno').val());
	var price= $('#price').val();
    if ($.isNumeric(price)) {
        var notes = safeEncode($('#notes').val());
        var user = $.cookie('user');
        var siJSON = {"siNameId":siNameId, "make":make, "partno":partno, "price":price, "notes":notes, "user":user};

        $.post("dlmFunctionDispatch.php?f=addServiceItem", siJSON, function(data) {
            myAlert(data.status, data.msg);
            $('#dialog').dialog( "close" );
            setTimeout(function () {location.href = "admin.php?opt=sietMgr";}, 1000);
        }, "json");
    } else {
        myAlert("Not a Number", "Price must only contain numbers, or 0");
    }
}

function editServiceItem(id) {
    var pe;
    pe = "<button class='btn' onclick='updateServiceItem(\"" + id +  "\");'>Save</button>&nbsp;&nbsp;";
    pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Cancel</button>";
    $('#siBtns').html(pe);
    $('#make').prop('disabled', false);
    $('#partno').prop('disabled', false);
    $('#price').prop('disabled', false);
    $('#notes').prop('disabled', false);
}

function updateServiceItem(id) {
	var make = safeEncode($('#make').val());
	var partno= safeEncode($('#partno').val());
	var price= safeEncode($('#price').val());
    var price = $('#price').val();
    if ($.isNumeric(price)) {
        var notes = safeEncode($('#notes').val());
        var user = $.cookie('user');
        var siJSON = {"make":make, "partno":partno, "price":price, "notes":notes, "user":user};

        $.post("dlmFunctionDispatch.php?f=updateServiceItem&sid="+id, siJSON, function(data) {
            myAlert(data.status, data.msg);
            $('#dialog').dialog( "close" );
            setTimeout(function () { location.href = "admin.php?opt=siMgr"; }, 1000);
        }, "json");
    } else {
        myAlert("Not a Number", "Price must only contain numbers, or 0");
    }
}

function deleteServiceItem(id) {
    $.post("dlmFunctionDispatch.php?f=deleteServiceItem&siId=" + id, function(data) {
        myAlert(data.status, data.msg);
        $('#dialog').dialog( "close" );
        location.href = "admin.php?opt=siMgr";
    }, "json"); 
}

/******************************************************************************
 * STANDARD JOBS Functions
 * 		showStandardJob
 * 		addStandardJob
 *      editStandardJob
 *      updateStandardJob
 *      deleteStandardJob
 ******************************************************************************/

function showStandardJob(id) {
	var frmTitle = "Standard Job Details";
	var pe = "<div id='viewstandardjobform' style='width:680px;'>";
            pe += "<input id='sjId' type='text' style='display:none' />";
            pe += "<div style='width:100%;margin-bottom:15px;'>";
                pe += "Description<br><input type='text' name='description' id='description' style='width:660px' maxlength='200' onkeydown='maxTxt(\"#description\",\"#descriptionCC\");' disabled></textarea>";
                pe += "<div id='descriptionCC' style='font-size:0.7em; color:#666666; text-align:right;padding-right:5px'>&nbsp;</div>";
            pe += "</div>";
            pe += "<div style='width:100%'>";
                pe += "Unit Price (&pound;) <input id='price' type='text' style='width:100px;' maxlength='10' disabled />&nbsp;&nbsp;";
                pe += "&nbsp;&nbsp;Volume Discount (%) <input type='text' id='discount' size='3' disabled /><br><br>";
                pe += "<input type='checkbox' id='LOAmultiplier' disabled >Priced by LOA (in Feet)</input>&nbsp;&nbsp;";
            pe += "</div>";
            pe += "<br class='clear' />";
        pe += "</div>";

    if((typeof id === "undefined") || (id == 0)) {  // If this is new then they can Add or Cancel
        frmTitle = "Enter " + frmTitle;
        pe += "<div id='sjBtns' style='text-align:right;padding-top:15px;'>";
        pe += "<button class='btn' onclick='addStandardJob(0);'>Add</button>&nbsp;&nbsp;";
        pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
        pe += "</div>";
        displayResults('dialog', frmTitle, pe);
        $('#description').prop('disabled',false);
        $('#LOAmultiplier').prop('disabled',false);
        $('#price').prop('disabled',false);
        $('#discount').prop('disabled',false);
    } else {    // Existing Job so they can Edit, Delete or Cancel
        pe += "<div style='width:45%;float:left;padding:15px 0 0 5px;color:#666666;font-size:0.7em'>";
        pe += "Created: <span id='created' /><br>";
        pe += "Last Update: <span id='updated' /> <span id='updatedBy' /><br>";
        pe += "</div>";
        pe += "<div id='sjBtns' style='width:45%;float:right;text-align:right;padding-top:15px;'>";
        if ($.cookie("userType") == "Admin") {
            pe += "<button class='btn' onclick='editStandardJob(\"" + id +  "\");'>Edit</button>&nbsp;&nbsp;";
            pe += "<button class='btn' onclick='deleteStandardJob(\"" + id +  "\");'>Delete</button>&nbsp;&nbsp;";
        }
        pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
        pe += "</div>";
        displayResults('dialog', frmTitle, pe);

        $.post("dlmFunctionDispatch.php?f=getStandardJobDetails&sid="+id, function(data) {
            if (data.status == "DB_Error") {
                myAlert(data.status, data.msg);
            } else {
                $('#sjId').val(data.id);
                $('#description').val(safeDecode(data.description));
                if (data.LOAmultiplier == 1) {
                    $('#LOAmultiplier').prop('checked', true);
                }
                $('#price').val(data.price);
                $('#discount').val(data.discount);
                $('#created').html(data.created);
                $('#updated').html(data.updated);
                $('#updatedBy').html(" by " + data.updatedBy);
            }
        }, "json");
    }
}

function addStandardJob(id) {
    var description = safeEncode($('#description').val());
    var discount = $('#discount').val();
    var LOAmultiplier = ($('#LOAmultiplier').prop("checked")?1:0);
    var price = $('#price').val();
    if ($.isNumeric(price)) {
        var user = $.cookie('user');
        var json = {"description":description, "LOAmultiplier":LOAmultiplier, "price":price, "discount":discount, "updatedBy":user};
        $.post("dlmFunctionDispatch.php?f=addStandardJob&id="+id, json, function(data) {
            myAlert(data.status, data.msg);
            location.href = "admin.php?opt=sjMgr";
        }, "json");
    } else {
        myAlert("Not a Number", "Price must only contain numbers, or 0");        
    }
}

function editStandardJob(id) {
    $('#description').prop('disabled',false);
    $('#LOAmultiplier').prop('disabled',false);
    $('#price').prop('disabled',false);
    $('#discount').prop('disabled',false);
    pe = "<button class='btn' onclick='addStandardJob(" + id + ");'>Save</button>&nbsp;&nbsp;";
    pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
    $('#sjBtns').html(pe);
}

function deleteStandardJob(id) {
    $.post("dlmFunctionDispatch.php?f=deleteStandardJob&id=" + id, function(data) {
        myAlert(data.status, data.msg);
        $('#dialog').dialog( "close" );
        location.href = "admin.php?opt=sjMgr";
    }, "json"); 
}

function selectStandardJobs() {
    $.post("dlmFunctionDispatch.php?f=getStandardJobsList", function(data) {
        if (data.resultcount > 0) {
            var pe = "<table>";
            for (var i = 0; i < data.resultcount; i++) {
                data.results[i].price = numberStringToCurrencyString(data.results[i].price);
                if (data.results[i].LOAmultiplier == 1) { data.results[i].price = data.results[i].price + " per ft"; }
                pe += "<tr><td><input name='stdjobs' type='checkbox' value='" + i + "'>" + safeDecode(data.results[i].description) + "</td>";
                pe += "<td>" + numberStringToCurrencyString(data.results[i].price) + " " + ((data.results[i].LOAmultiplier == 1)?" per ft":"") + "</td></tr>"; 
            }
            pe += "</table>";
            pe += "<div style='float:right; width:250px;'>";
            pe += "<button class='btn' onclick='addStandardJobsToJobSheet("+JSON.stringify(data)+");'>Add to Jobsheet</button>&nbsp;&nbsp;";
            pe += "<button class='btn' onclick='$(\"#dialog\").dialog(\"close\");'>Close</button>";
            displayResults('dialog', "Select Standard Jobs", pe);
        } else {
            myAlert("Nothing to choose", "No Standard Jobs Found");
        }
    },"json");
}

function addStandardJobsToJobSheet(data) {
    var p, f, idx;

    $('input[name=stdjobs]:checked').each(function() { 
        if (data.results[$(this).val()].LOAmultiplier == 1) {
            if (isNaN($('#boatLOA').html()) || ($('#boatLOA').html() < 1)) {
                myAlert("Missing Data", "No LOA recorded for this boat, <b>price for " + safeDecode(data.results[$(this).val()].description) + " set to ZERO</b>");
                p = 0;
            } else {
                f = $('#boatLOA').html() * 3.28084;  // Convert length (in Mtrs) to Feet.
                p = parseFloat(data.results[$(this).val()].price) * f;
            }
            p = numberStringToCurrencyString(p);
        } else {
            p = data.results[$(this).val()].price;
        }
//        var rowidx = $('#JSLabour tr').length;
        if ($('#JSLabour tr').length == 0) {
            rowidx = 0;     // Start from zero if new table, otherwise...
        } else {
            rowidx = getIdxForNextRow("JSLabour");
        }
        var newItem = "<tr style='height:20px;padding:0px;margin:0px;'>";
            newItem += "<td style='padding:0px;width:50px'>";
                newItem += "<input type='text' id='editlabqty-" + rowidx + "' class='jsPart' size='3' disable='true' value='1' />";
            newItem += "</td>";
            newItem += "<td>" + safeDecode(data.results[$(this).val()].description)+"</td>";
            newItem += "<td class='priceCol' id = 'editlabunit-" + rowidx + "'>£"+numberStringToCurrencyString(p)+"</td>";
            newItem += "<td style='display:none;' id = 'editlabdiscount-" + rowidx + "'>" + data.results[$(this).val()].discount+"</td>";
            newItem += "<td class='priceCol' id = 'editlabtotal-" + rowidx + "' style='text-align:right;'>£"+numberStringToCurrencyString(p)+"</td>";
        newItem += "</tr>";
        $('#JSLabour').append(newItem);
        $('#JSchanged').html("1");      // Mark page dirty
        $('#dialog').dialog("close");

        setLABQtyWatch();
    });
}

function setLABQtyWatch() {
    $('[id^="editlabqty-"]').change(function() {  // Now watch for changes of quantity
    var targetId = $(this).attr('id').substring(11);
    $(this).attr("value", $(this).val());  // Save as new default value so that it gets stored in the HTML
    var labQty = $(this).val();
    var unitCost = $('#editlabunit-'+targetId).html().substring(1);
    var discount = $('#editlabdiscount-'+targetId).html();
    var total = (labQty * unitCost).toFixed(2);
    if ((labQty > 1) && (discount > 0)) { // Now check for discount
        total = total * ((100-discount)/100);
    }
    $('#editlabtotal-'+targetId).html("£"+ numberStringToCurrencyString(total));
    $('#editlabtotal-'+targetId).effect("highlight", {color: 'SpringGreen'}, 100);
});
}
/******************************************************************************
 * Other Functions
 *****************************************************************************/
function addSItoETform(id, isMod) {
    var content;
    content = "<div style='margin:0;padding:4px;min-height:250px; width:800px;'>";
    content += "<table id='siTable' class='display' style='width;100%, border:thin solid #00F;' cellspacing='0'>";
    content += "<thead><tr><th width='210px'>Name</th><th>Make</th><th>Partno</th><th>Notes</th></tr></thead>";
    content += "</table></div>";
	$(function() {
		$('<div />').html(content).dialog({
			title: 'Select Service Item',
			resizable: false,
			width:"auto",
			height:"auto",
			modal: true,
            close : function(){ $(this).empty(); $(this).remove(); }, 
			buttons: {
				"Add": function() {
                    var u = $.cookie('user');
                    var checkedJSON = {"etId":id, "items":[], "u":u };
                    var si;
                    $('[id^="siName-"]').each(function(index, element){
                      if(element.checked){
                          si = element.id.substring(7);
                          checkedJSON.items.push({"siId":si});
                      } 
                    });

                    if (isMod) {
                        addSiAsEngineMod(checkedJSON, id);
                    } else {
                        addSiToEngineTemplate(checkedJSON, id);
                    }
                    $(this).dialog( "close" );
				},
				Cancel: function() {
					$(this).dialog( "close" );
				}
			}
		});
	});

    // get list of standard Service Items
    $.post("dlmFunctionDispatch.php?f=getServiceItemsList", function(data) {
        if (data.resultcount > 0) {
            for (var i = 0; i < data.resultcount; i++) {
                data.results[i].name = '<input type="checkbox" id="siName-'+data.results[i].siId+'"  name="siName" value="' + data.results[i].siId + '" />&nbsp;' + safeDecode(data.results[i].name);
                data.results[i].make = safeDecode(data.results[i].make);
                data.results[i].partno = safeDecode(data.results[i].partno);
                data.results[i].notes = safeDecode(data.results[i].notes);
            }
            $('#siTable').dataTable( {
                "iDisplayLength": 10,
                "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                stateSave: true,
                data: data.results,
                "columns": [
                    {"data":"name", "class":"left"},
                    {"data":"make", "class":"center"},			
                    {"data":"partno", "class":"center"},
                    {"data":"notes", "class":"center"}			
                ]
            });
        } else {
            $('#siTable').html("<h1>No Service Items found.</h1>");
        }
    }, "json");        
}

function addSiToEngineTemplate(sitems, etId) {
    $.post("dlmFunctionDispatch.php?f=addSiToET&et=" + etId, sitems, function(data) {
        if (data.status = "OK") {
            myAlert("Operation Complete", "Item added to Engine Template");
            location.reload();
        } else {
            myAlert("Operation Failed", "Item was not added to Engine Template.");
        }
    }, "json");    
}

function addSiAsEngineMod(sitems, etId) {
    $.post("dlmFunctionDispatch.php?f=addModToET&et=" + etId, sitems, function(data) {
        if (data.status = "OK") {
            $(".ui-dialog-content").dialog("close");    // Close (all) dialog boxes
            myAlert("Operation Complete", "Modification added to Engine.");
//            location.reload();
        } else {
            myAlert("Operation Failed", "Modification was not added to Engine.");
        }
    }, "json");    
}
function deleteETSI(etId, siId) {
    $.post("dlmFunctionDispatch.php?f=deleteETSI&si=" + siId + "&et=" + etId, function(data) {
        if (data.status == "OK") {
            $(dialog).dialog(close);
            showEngineTemplate(etId);
        } else {
            myAlert("Operation Failed", "Item was not removed from Engine Template.");
        }
    }, "json");        
}
function deleteEngineMod(eId, siId) {
    $.post("dlmFunctionDispatch.php?f=deleteEngineMod&si=" + siId + "&eid=" + eId, function(data) {
        if (data.status == "OK") {
            $(dialog).dialog(close);
            showEngine(eId);
        } else {
            myAlert("Operation Failed", "Item was not removed from Engine Template.");
        }
    }, "json");        
}

///******************************************************************************
// * Jobsheet Functions
// *****************************************************************************/
function showJobsheet(id) {
//========================================================================
// Boat status.  (All good - No outstanding jobs, Safe to use - But outstanding works, Awaiting decommission or recommission or repair, Laid up for winter)
// WIP Notes (Freeform 500)
// Stage:  1 = Draft, 2 = Scheduled, 3 = Due, 4 = Completed,  5 = Closed
// Photo Link
//========================================================================
    showLoading();
    id = ((typeof id !== 'undefined') ? id : 0);
    var content = "";
    content += "<div id='jobsheetno' style='margin-top:10px;font-size:1.25em;'>";
        content += "<div id='jsTitle' style='float:left;width:800px;'>Job Sheet #123&nbsp;&nbsp;Created: dd-mmm-yyyy hh:mm&nbsp;&nbsp;Author: (username)</div>";
        content += "<div style='float:right; width:180px;text-align:right;'>Stage: <span id='jsStage'>Draft</span><span id='jsStageNo' style='visibility:hidden;'>0</span></div>";
        content += "<br class='clear' />";
    content += "</div>";

    content += "<div>";
        content += "<div id='js1' style='margin:10px 50px 0px 0px;float:left; width:455px;'>";  // Left box Customer
            content += "<b>Customer</b><br><span id='custHolder'><select id='selectCust' style='width:455px;'><option /></select></span>";
            content += "<div id='js3' style='margin:10px 34px 0px 0px;;float:left; width:450px;height:120px;border:thin solid #666666;padding:4px;'>&nbsp;</div>";
        content += "</div>";

        content += "<div id='js2' style='margin:10px 35px 0px 0px;float:left; width:450px;'>";  // Middle box Boat
            content += "<b>Boat</b><br><span id='boatHolder'><select id='selectBoat' style='width:225px;margin-right:30px'><option /></select></span>";
            content += "<b>Start Date</b> <input style='width:75px;' type='text' id='datepicker'>";
            content += "<a href='schedule.php'><img src='images/schedule.png' title='Show Jobsheet Calendar' /></a>";
            content += "<div id='js4' style='margin-top:10px;float:left; width:460px;height:120px;border:thin solid #666666;padding:4px;'>&nbsp;</div>";
        content += "</div>";

        content += "<div id='boatLOA' style='display:none' />";
        content += "<div id='btnDiv' style='margin-top:10px;height:195px;margin-left:20px;float:left; width:180px;'>"; 
                content += "<b>Boat Status</b><br>";
                content += "<input type='checkbox' id='inwater' value='8' checked>In Thames</input><br>";
                content += "<input type='radio' id='aok' name='state' value='4' checked>&nbsp;All OK</input><br>";
                content += "<input type='radio' id='usable' name='state' value='2'>&nbsp;Usable</input><br>";
                content += "<input type='radio' id='unusable' name='state' value='1'>&nbsp;Unusable</input>";
                content += "<hr style='margin-bottom:5px'>";
                /**********/
                content += "<span id='b1' style='visibility:hidden;'><button class='btn' id='loadSP' style='width:150px' onclick='loadServiceParts($(\"#selectBoat\").val());'>Add Service Parts</button><br></span>";
                content += "<span id='b2' style='visibility:hidden;'><button class='btn' id='loadJOB' style='width:150px' onclick='selectStandardJobs();'>Add Standard Jobs</button><br></span>";
                content += "<span id='b3' style='visibility:hidden;'><button class='btn' style='width:150px' onclick='setJobsheetStage(4);'>Job Completed</button><br></span>";
                content += "<span id='b4' style='visibility:hidden; position:relative; top:-60px'><b>Completed: <span id='completedDate' ></span></b></span>";
                content += "<span id='b5' style='visibility:hidden; position:relative; top:-20px'><button class='btn' style='width:150px' onclick='setJobsheetStage(5);'>Job Closed</button><br></span>";
                content += "<span id='b6' style='visibility:hidden; position:relative; top:-80px'><b>Closed: <span id='closedDate'></span></b></span>";
                /* Services Btn, jobs Btn, complete Btn, complete date, closed btn, closed date */
                /**********/
                content += "</div>";    
            content += "</div>";
        content += "</div>";
    content += "</div>";

    content += "<div style='width:100%;'>";
        content += "<div id='desc' style='width:48%;float:left;margin:10px 10px 0px 0px;'><b>Description</b><br>";
            content += "<textarea id='description' style='width:100%;' rows='4' wrap='soft' maxlength='400' onkeydown='maxTxt(\"#description\",\"#descriptionCC\");' ></textarea>";
            content += "<div id='descriptionCC' style='width:500px;float:right; font-size:0.7em; color:#666666; text-align:right;padding-right:5px'>&nbsp;</div></td>";
        content += "</div>";    
        content += "<div id='notesdiv' style='width:48%;float:right;margin:10px 10px 0px 0px;'><b>Notes</b><br>";
            content += "<textarea id='notes' style='width:100%;' rows='4' wrap='soft' maxlength='400' onkeydown='maxTxt(\"#notes\",\"#notesCC\");' ></textarea>";
            content += "<div id='notesCC' style='width:500px;float:right; font-size:0.7em; color:#666666; text-align:right;padding-right:5px'>&nbsp;</div></td>";
        content += "</div>";    
        content += "<br class='clear' />";
    content += "</div>";
    
    content += "<div style='width:100%; margin:0px 0px 0px 0px;'>";
        content += "<div style='width:48%;float:left;'>";  // LABOUR PANEL
            content += "<div style='width:100%;'>";
                content += "<span style='float:left;'><b>Labour</b></span>";
                content += "<span id='addNSLabour' style='float:right;'><a onclick='addJSLabour();'><b>add non-standard labour</b></a></span>";
            content += "</div>";
            content += "<div id='labour' style='border:thin solid #666666; width:100%;height:150px;float:left;overflow-y:scroll;'>";
                content += "<table id='JSLabour' style='width:100%'></table>";
            content += "</div>";    
        content += "</div>";
    
        content += "<div style='width:48%;float:right;'>";  // PARTS PANEL
            content += "<div style='width:100%;'>";
                content += "<span style='float:left;'><b>Parts</b></span>";
                content += "<span id='addNSPart' style='float:right;'><a onclick='addJSParts();'><b>add non-standard part</b></a></span>";
            content += "</div>";
            content += "<div id='parts' style='border:thin solid #666666; width:100%;height:150px;float:right;overflow-y:scroll;'>";
                content += "<table id='JSserviceItems' style='width:100%'></table>";
            content += "</div>";    
        content += "</div>";
        content += "<br class='clear' />";
    content += "</div>";

    content += "<div>";
        content += "<div id='moreNotesBtn' style='width:40%;float:left;padding-top:15px;'>"; 
        content += "<button class='btn' id='notesBtn' onclick='showNotes(\"jobsheets\",\"" + id +  "\");'>Notes</button>";
        content += "<button class='btn' id='photosBtn' onclick='showPhotos(\"jobsheets\",\"" + id +  "\");'>Photos</button>";
        content += "</div>";
        content += "<div id='jobsheetBtns' style='width:45%;float:right;text-align:right;padding-top:15px;'>";
        content += "<button class='btn' onclick='previewInvoice(\"" + id + "\")'>Print Preview</button>&nbsp;&nbsp;";
        content += "<button class='btn' onclick='saveJobsheet(\"" + id +  "\");'>Save</button>&nbsp;&nbsp;";

        if (id > 0) {   // If this is an existing (i.e. previously saved) Jobsheet offer a delete option
            content += "<button class='btn' onclick='deleteJobsheet(\"" + id +  "\");'>Delete</button>&nbsp;&nbsp;";
        }
        content += "<button class='btn' id='cancelBtn' onclick='location.href=\"jobsheets.php?s=0&o=0&b=0\";'>Cancel</button>";
        content += "</div>";
    content += "</div>";
    content += "<br class='clear' />";
    content += "<br><div id='moreNotes' />";
    content += "<div id='JSchanged' style='display:none'>0</div>"  // Somewhere to hold a 'page' global

    $('#pageContainer').html(content);
    
    if (id === 0) {
        $('#JSchanged').html("1");      // Mark page dirty 
    }
    $('#desc').change(function() {
        $('#JSchanged').html("1");      // Mark page dirty 
    });
    $('#notesdiv').change(function() {
        $('#JSchanged').html("1");      // Mark page dirty 
    });

    $( function() {
        $( "#datepicker" ).datepicker( { minDate: new Date() });
        $( "#datepicker" ).datepicker( "option", "dateFormat", "yy-mm-dd" );  // minDate: new Date(2007, 1 - 1, 1);
        $( "#datepicker" ).click(function() {
            if ($('#selectCust').val() == -1) {
                myAlert("Missing critical information","Please select a Customer before scheduling this Jobsheet.");
            }
        });
        $( "#datepicker" ).change(function() {
            if (confirm("Press 'OK' if jobsheet information is complete, or press 'Cancel' and scheduled date will be ignored.") == true) {
                // Keep date and set stage = 'Scheduled' (unless scheduled date is today)
                if ($('#datepicker').val() == mysqlDate()) {
                    setJobsheetStage(3);
                } else {
                    setJobsheetStage(2);
                }
                $('#JSchanged').html("1");      // Mark page dirty
            } else {
                $('#datepicker').val("* NOT SET *");  // Remove date and leave stage as 'Draft'
            }
        });
    } );    
    
    if (id == 0)  {
        setJobsheetStage(1);
        hideLoading();
    }
    $.post("dlmFunctionDispatch.php?f=getJobsheet&jsId="+id, function(jsdata) {
        if ((id > 0)&&(jsdata.resultcount < 1)) {
            myAlert("Not Found", "Jobsheet #" + id + " not found!");
        } else {
            $('#jsTitle').html("Job Sheet #" + " - " + "&nbsp;&nbsp;Created: " + fullDateStr(new Date()) + "&nbsp;&nbsp;Author: " + $.cookie('user'));        
            $.post("dlmFunctionDispatch.php?f=getCustomerNames", function(cndata) {
                var selcust = buildSelect("selectCust", cndata);
                $('#custHolder').html(selcust);
                $('#selectCust').prop('disabled',false);
                $("#selectCust").css("width","455px");
                $("#selectCust").css("margin-right","30px");
                $("#selectCust").change(function() {    // Add 'onchange' to set up customer box
                    var custId = $('#selectCust').val();
                    $.post("dlmFunctionDispatch.php?f=getCustomerDetails&cid="+custId, function(cddata) {
                        var custDetails = "";
                        custDetails += safeDecode(cddata.title) + " ";
                        custDetails += safeDecode(cddata.firstname) + " ";
                        custDetails += safeDecode(cddata.lastname) + "<br>";
                        custDetails += ((safeDecode(cddata.address1))? safeDecode(cddata.address1) + ",<br>" : "");
                        custDetails += ((safeDecode(cddata.address2))? safeDecode(cddata.address2) + ",<br>" : "");
                        custDetails += ((safeDecode(cddata.address3))? safeDecode(cddata.address3) + ",<br>" : "");
                        custDetails += ((safeDecode(cddata.county))? safeDecode(cddata.county) + " " : "");
                        custDetails += ((safeDecode(cddata.postcode))? safeDecode(cddata.postcode) + "<br>" : "<br>");
                        custDetails += ((safeDecode(cddata.phone))? "tel: " + safeDecode(cddata.phone) + "<br>" : "<br>");
                        custDetails += ((safeDecode(cddata.email))? "email: " + safeDecode(cddata.email) + "<br>" : "<br>");
                        $('#js3').html(custDetails);
                        hideLoading();
                    }, "json");
                });
                $.post("dlmFunctionDispatch.php?f=getBoatNames&cid=0", function(bndata) { // cid=0 means all boats
                    if (bndata.status == "DB_Error") {
                        alert(bndata.msg);
                    }
                    var selboat = buildSelect("selectBoat", bndata);
                    $('#boatHolder').html(selboat);
                    $('#selectBoat').prop('disabled',false);
                    $("#selectBoat").attr("style","width:225px;margin-right:30px");
                    $("#selectBoat").change(function() {
                        var boatId = $('#selectBoat').val();
                        var boatDetails = "";
                        $.post("dlmFunctionDispatch.php?f=getBoatDetails&bid="+boatId, function(boatdata) {
                            boatDetails += ((safeDecode(boatdata.name))? safeDecode(boatdata.name) + "<br>" : "");
                            boatDetails += ((safeDecode(boatdata.make))? safeDecode(boatdata.make) + " " : "");
                            boatDetails += ((safeDecode(boatdata.model))? safeDecode(boatdata.model) + "<br>" : "<br>");
                            boatDetails += ((boatdata.LOA)? "LOA: " + boatdata.LOA + "<br>" : "LOA: **UNKNOWN**<br>");
                            boatDetails += ((safeDecode(boatdata.SiteName))? "Site: " + safeDecode(boatdata.SiteName) + "<br>" : "");
                            boatDetails += ((safeDecode(boatdata.Berth))? "Berth: " + safeDecode(boatdata.Berth) + "<br>" : "");
                            boatDetails += ((safeDecode(boatdata.boatKeys))? "Keys: " + safeDecode(boatdata.boatKeys) + "<br>" : "");
                            $('#boatLOA').html(boatdata.LOA);
                            $('#js4').html(boatDetails);
                            $('#inwater').prop( "checked", ((boatdata.inwater == 8)?true:false));
                            if (boatdata.state == 4) {
                                $('#aok').prop( "checked", true);
                            } else if (boatdata.state == 2) {
                                $('#usable').prop( "checked", true);
                            } else if (boatdata.state == 1) {
                                $('#unusable').prop( "checked", true);
                            }
                            if ($('#selectCust').val() == -1) {
                                selectOptionByVal('selectCust', boatdata.CustomerId);
                                $('#selectCust').trigger( "change" );
                            } else {
                                if ($('#selectCust').val() != boatdata.CustomerId) {
                                    myAlert("Mismatch", "This Boat is not registered to this customer!.");
                                }
                            }
                        }, "json");
                        $('.btn').attr("disabled", false);
                    });

                    if (id > 0) {  // If this is existing jobsheet we set  up all we know.
                        $('#jsTitle').html("Job Sheet #" + id + "&nbsp;&nbsp;Created: " + jsdata.created + "&nbsp;&nbsp;Updated By: " + jsdata.updatedBy);        
                        jsdata.stage = parseInt(jsdata.stage); // Value from db is a string
                        if ((jsdata.scheduledDate == "0000-00-00")||(jsdata.scheduledDate == "1970-01-01")) {
                            $('#datepicker').val("* NOT SET *");
                        } else {
                            $('#datepicker').val(jsdata.scheduledDate);
                        }
                        if (chkDate($('#datepicker').val())) {
                            $('#datepicker').attr("disabled", true);
                            
                        }
                        selectOptionByVal('selectBoat', jsdata.boatId);
                        $('#selectBoat').trigger( "change" );
                        $('#selectCust').trigger( "change" );
                        $('#description').val(safeDecode(jsdata.description));
//=======================================                         
                        var storedParts = safeDecode(jsdata.parts);
                        if (storedParts.substring(0,7) == '{"parts') {
                            // Build displayable table
                            $('#parts').html(partsJson2Table(storedParts));
                            setSIQtyWatch();   // Now set up event watcher for qty change & remove 'Add Service Parts' button
                            $('#loadSP').attr('disable', true);
                            $('#loadSP').hide();                            
                        } else {
                            /*
                                Now we try to deal with Jobsheets that predate V1.5.  There are 3 possibilities:-
                                 1 - No parts have been set (yet)
                                 2 - Parts have been set (properly with the updateable table format of V1.4 or later)
                                 3 - Parts have been loaded but in flat text (Pre V1.4)
                            */
                            var storedParts = safeDecode(jsdata.parts); 
                            if (storedParts.indexOf("£") > 0) {  //  Do we have any parts data stored?
                                if (storedParts.indexOf("editsiqty") == -1) {  // Some parts data so is it Pre or post V1.4 structure?  
                                    storedParts = reformatJSParts(storedParts);  // Pre V1.4 - so we have to convert
                                }
                                $('#parts').html(storedParts);
                                setSIQtyWatch();   // Now set up event watcher for qty change & remove 'Add Service Parts' button
                                $('#loadSP').attr('disable', true);
                                $('#loadSP').hide();                            
                            }
                        }
//=======================================                       
                        var storedJobs = safeDecode(jsdata.labour);
                        if (storedJobs.substring(0,6) == '{"jobs') {
                            // Build displayable table
                            $('#labour').html(jobsJson2Table(storedJobs));
                            setLABQtyWatch();   // Now set up event watcher for qty change & remove 'Add Service Parts' button                        
                        } else {
                            // Now repeat the reformatting for Labour
                            var storedJobs = safeDecode(jsdata.labour);
                            if (storedJobs.indexOf("£") > 0) {  //  Do we have any jobs data stored?
                                if (storedJobs.indexOf("editlabqty") == -1) {  // Some parts data so is it Pre or post V1.4 structure?  
                                    storedJobs = reformatJSJobs(storedJobs);  // Pre V1.4 - so we have to convert
                                }
                                $('#labour').html(storedJobs);
                                setLABQtyWatch();   // Now set up event watcher for qty change & remove 'Add Service Parts' button                      
                            }
                        }
//=======================================
                        $('#notes').val(safeDecode(jsdata.notes));
                        $('#selectCust').prop('disabled',true);
                        $('#selectBoat').prop('disabled',true);
                        $('#scheduledDate').html(jsdata.scheduledDate);
                        $('#completedDate').html(jsdata.completedDate);
                        $('#closedDate').html(jsdata.closedDate);
                        
                        setJobsheetStage(jsdata.stage);
                        if (jsdata.stage == 5) { // If Closed no changes are allowed
                            $('textarea').prop('disabled',true);
                            $('input').prop('disabled',true);
                            $('#datepicker').prop('disabled',true);
                            $('#addNSLabour').html('');
                            $('#addNSPart').html('');
                            $('.btn').attr("disabled", true);
                            $('#cancelBtn').attr("disabled", false);
                            $('#notesBtn').attr("disabled", false);
                            $('#photosBtn').attr("disabled", false);
                        }
                        
                    } else {  // New jobsheet
                        $('#datepicker').val("* NOT SET *");
                        $('.btn').attr("disabled", true);
                        $('#cancelBtn').attr("disabled", false);
                    }
                }, "json"); 

            }, "json");
        }
    }, "json");
}

function setSIQtyWatch() {   // editsiqty-
    $('[id^="editsiqty-"]').change(function() {  // Now watch for changes of quantity
        var targetId = $(this).attr('id').substring(10);
        $(this).attr("value", $(this).val());  // Save as new default value so that it gets stored in the HTML
        var partQty = $(this).val();
        var unitCost = $('#editsiunit-'+targetId).html().substring(1);
        var total = (partQty * unitCost).toFixed(2);
        $('#editsitotal-'+targetId).html("£"+ numberStringToCurrencyString(total));
        $('#editsitotal-'+targetId).effect("highlight", {color: 'SpringGreen'}, 100);
        $('#JSchanged').html("1");      // Mark page dirty
    });
}

function addJSParts() {
    //+ 
    // Here we allow user to add anything to the Parts section - this will only be stored in the Jobsheet and will not affect siTObeMap
    // So we create a dynamic dialog box and prompt for quantity name, make, partno and unit cost

    var prompt = "<table style='width:100%'>";
    prompt += "<tr><td>Qty</td><td>Item</td><td>Make</td><td>PartNo</td><td>Unit Cost (£)</td>";
    prompt += "<tr style='height:20px;padding:0px;margin:0px;'>";
        prompt += "<td><input type='text-align:right;' size='3' id='addJSPartqty' value=''/></td>";
        prompt += "<td><input type='text' class='addPartField' size='20' maxlength='40' id='addJSPartname' value='' /></td>";
        prompt += "<td><input type='text' class='addPartField' size='15' maxlength='25' id='addJSPartmake' value='' /></td>";
        prompt += "<td><input type='text' class='addPartField' size='10' maxlength='15' id='addJSPartpartno' value='' /></td>";
        prompt += "<td><input type='text' class='addPartField' size='5' maxlength='8' id='addJSPartunitCost' value='' /></td>";
    prompt += "</tr>";
    prompt += "</table>";
    
	$(function() {
		$('<div />').html(prompt).dialog({
			title: 'Add Item to Jobsheet',
			resizable: false,
			width:"auto",
			height:"auto",
			modal: true,
			buttons: {
				"Add Item": function() {
                    var newItem = "";
                    var total = "";
                    var rowidx = getIdxForNextRow("JSserviceItems");
                    if ($.isNumeric($('#addJSPartqty').val())) {
                        total = ($('#addJSPartqty').val() * $('#addJSPartunitCost').val()).toFixed(2);
                    }
                    newItem = "<tr style='height:20px;padding:0px;margin:0px;'>";
                        newItem += "<td style='padding:0px;width:50px'>";
                            newItem += "<input type='text' id='editsiqty-" + rowidx + "' class='jsPart' size='3' disable='true' value='" + $('#addJSPartqty').val() + "' />";
                        newItem += "</td>";
                        newItem += "<td>"+$('#addJSPartname').val()+"</td>";
                        newItem += "<td>"+$('#addJSPartmake').val()+"</td>";
                        newItem += "<td>"+$('#addJSPartpartno').val()+"</td>";
                        newItem += "<td class='priceCol' id = 'editsiunit-" + rowidx + "'>£"+numberStringToCurrencyString($('#addJSPartunitCost').val())+"</td>";
                        newItem += "<td class='priceCol' id = 'editsitotal-" + rowidx + "' style='text-align:right;'>£"+numberStringToCurrencyString(total)+"</td>";
                    newItem += "</tr>";
                    $('#JSserviceItems').append(newItem);
                    $('#JSchanged').html("1");      // Mark page dirty
                    setSIQtyWatch();
                    $(this).dialog('destroy').remove()
				},
				Cancel: function() {
                    newItem = "";
					$(this).dialog('destroy').remove()
				}
			}
		});
	});
}

function addJSLabour() {
    //+ 
    // Here we allow user to add anything to the Labour section - this will only be stored in the JObsheet and will not affect siTObeMap
    // So we create a dynamic dialog box and prompt for quantity description and unit cost
    var prompt = "<table style='width:100%'>";
    prompt += "<tr><td>Qty</td><td>Description</td><td>Unit Cost (£)</td><td>Discount</td><td></tr>";
    prompt += "<tr style='height:20px;padding:0px;margin:0px;'>";
        prompt += "<td><input type='text-align:right;' size='3' id='addJSLabourqty' value=''/></td>";
        prompt += "<td><input type='text' class='addPartField' size='50' maxlength='1000' id='addJSLabourDescription' value='' /></td>";
        prompt += "<td><input type='text' class='addPartField' size='5' maxlength='8' id='addJSLabourunitCost' value='' /></td>";
        prompt += "<td><input type='text' class='addPartField' size='5' maxlength='8' id='addJSLabourDiscount' value='0' /></td>";
    prompt += "</tr>";
    prompt += "</table>";
    
	$(function() {
		$('<div />').html(prompt).dialog({
			title: 'Add non-standard labour to Jobsheet',
			resizable: false,
			width:"auto",
			height:"auto",
			modal: true,
			buttons: {
				"Add Item": function() {
                    var newJob = "";
                    var total = "";
                    var rowidx = getIdxForNextRow("JSLabour");

                    total = ($('#addJSLabourqty').val() * $('#addJSLabourunitCost').val()).toFixed(2);
                    if (($('#addJSLabourqty').val() > 1)&&($('#addJSLabourDiscount').val() > 0)) {
                        total = (parseFloat(total) * ((100-$('#addJSLabourDiscount').val())/100)).toFixed(2);
                    }
                    total = numberStringToCurrencyString(total);
                    newItem = "<tr style='height:20px;padding:0px;margin:0px;'>";
                        newItem += "<td style='padding:0px;width:50px'>";
                            newItem += "<input type='text' id='editlabqty-" + rowidx + "' class='jsPart' size='3' disable='true' value='" + $('#addJSLabourqty').val() + "' />";
                        newItem += "</td>";
                        newItem += "<td>"+$('#addJSLabourDescription').val()+"</td>";
                        newItem +=  "<td class='priceCol' id = 'editlabunit-" + rowidx + "'>£"+numberStringToCurrencyString($('#addJSLabourunitCost').val())+"</td>";
                        newItem += "<td style='display:none;' id = 'editlabdiscount-" + rowidx + "'>"+$('#addJSLabourDiscount').val()+"</td>";
                        newItem += "<td class='priceCol' id = 'editlabtotal-" + rowidx + "' style='text-align:right;'>£"+numberStringToCurrencyString(total)+"</td>";
                    newItem += "</tr>";
                    $('#JSLabour').append(newItem);
                    $('#JSchanged').html("1");      // Mark page dirty
                    setLABQtyWatch();
                    $(this).dialog('destroy').remove()
				},
				Cancel: function() {
                    newItem = "";
					$(this).dialog('destroy').remove()
				}
			}
		});
	});
}

function setJobsheetStage(stage) {  /* b1=Services Btn, b2=jobs Btn, b3=complete Btn, b4=complete date, b5=closed btn, b6=closed date */
    var shortDate = mysqlDate();

    $('#jsStageNo').html(stage);
    switch (stage) {
        case 1:
            $('#jsStage').html("Draft");
            $('#b1').css("visibility", "visible");
            $('#b2').css("visibility", "visible");
            break;
        case 2:
            $('#jsStage').html("Scheduled");
            $('#b1').css("visibility", "visible");
            $('#b2').css("visibility", "visible");
            $('#b3').css("visibility", "visible");
            $('#b5').css("visibility", "visible");
            break;
        case 3:
            $('#jsStage').html("Underway");
            $('#b1').css("visibility", "visible");
            $('#b2').css("visibility", "visible");
            $('#b3').css("visibility", "visible");
            $('#b5').css("visibility", "visible");
            break;
        case 4:
            $('#jsStage').html("Completed");
            if (!chkDate($('#completedDate').html())) {
                $('#completedDate').html(shortDate);
            }
            $('#b1').css("visibility", "hidden");
            $('#b2').css("visibility", "hidden");
            $('#b3').css("visibility", "hidden");
            $('#b4').css("visibility", "visible");
            $('#b5').css("visibility", "visible");

            break;
        case 5:
            $('#jsStage').html("Closed"); // if this is beling closed before completed - we set the completed date to the same as the closed date.
            if (!chkDate($('#completedDate').html())) { $('#completedDate').html(shortDate); }
            if (!chkDate($('#closedDate').html())) { 
                $('#closedDate').html(shortDate); 
            }
            $('#b1').css("visibility", "hidden");
            $('#b2').css("visibility", "hidden");
            $('#b3').css("visibility", "hidden");
            $('#b4').css("visibility", "visible");
            $('#b5').css("visibility", "hidden");
            $('#b6').css("visibility", "visible");
            break;
    }
}

function saveJobsheet(id) {  // id == 0 means new Jobsheet   

    if ($('#selectCust').val() == -1) {
        myAlert("Missing critical information","Please select a Customer before saving this Jobsheet");
    } else {
        var customerId = $('#selectCust').val();
        var boatId = $('#selectBoat').val();
        var boat = $('#selectBoat option:selected').text();
        var scheduled = (($('#datepicker').val() == "* NOT SET *")?"0000-00-00":$('#datepicker').val());
        var completed = (($('#completedDate').html() == null)?"0000-00-00":$('#completedDate').html());
        var jsclosed = (($('#closedDate').html() == null)?"0000-00-00":$('#closedDate').html());
        var description = safeEncode($('#description').val());
        var updatedBy = $.cookie('user');
        var parts = safeEncode(partsTable2json("parts"));
//alert(jobsTable2json("labour"));
//        removeZeroQty("labour");     // Remove jobs with zero quantity
//        var labour = safeEncode($('#labour').html());
        var labour = safeEncode(jobsTable2json("labour"));
        var notes = safeEncode($('#notes').val());
        var inwater = (($('#inwater').prop('checked'))?$('#inwater').val():"0");
        var state = $("input:radio[name='state']:checked").val(); // Boat State
        var stage = $('#jsStageNo').html();
        var jsJSON = {"id":id, "customerId":customerId, "boatId":boatId, "scheduled":scheduled, "completed":completed, "closed":jsclosed, "description":description, "parts":parts, "labour":labour, "notes":notes, "inwater":inwater, "state":state, "stage":stage, "updatedBy":updatedBy};
        $.post("dlmFunctionDispatch.php?f=saveJobsheet", jsJSON, function(data) {
            if (data.status == "OK") {
                $('#JSchanged').html("0");      // clear dirty flag
                myAlert(data.status, data.msg);
                var msg = "Boat: " + boat + " Job: " + description + " " + window.location.href + "?js=" + data.id;
                var from = $.cookie('user');
                if (stage == 2) {  // Only send notifications when a Jobsheet is Scheduled
                    var notification = {"notification":"NEWJOBSHEET", "from":from, "title":"New Job Sheet", "msg":msg };
                    $.post("dlmFunctionDispatch.php?f=notify", notification, function(notifydata) {
                        myAlert("Notifications", notifydata.status);
                    }, "json");
                }
                location.reload();
            } else {
                myAlert("Operation Failed", "It broke :-(");
            }
        }, "json");
    }
}

function deleteJobsheet(id) {
	var prompt = "Delete <b>Jobsheet #" + id + "</b> for " + $('#selectBoat option:selected').text() + "<br><br>";
	prompt += "<p style='color:red; text-align:center;'>This operation cannot be undone!</p>";
    $('<div />').html(prompt).dialog({
        title: 'Delete Jobsheet',
        resizable: false,
        width:350,
        height:"auto",
        modal: true,
        close : function(){ $(this).empty(); $(this).remove(); },
        buttons: {
            "Delete": function() {
                $.post("dlmFunctionDispatch.php?f=deleteJobsheet&id=" + id, function(data) {
                    if (data.status == "OK") {
                        myAlert("Operation Complete", "Jobsheet Deleted!");
                        location.reload();
                    } else {
                        myAlert(data.status, data.msg);
                    }
                    $( this ).dialog( "close" );
                }, "json");
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        }
    });
}

function loadServiceParts(boatId) {
    var parts = "";
    $.post("dlmFunctionDispatch.php?f=getBoatsSI&id=" + boatId, function(data) {
        if (data.resultcount == 0) {
            myAlert("Missing Data", "No Service Parts are recorded for this boat");
        } else {
            for (i=0; i<data.resultcount; i++) {
                if (data.resultcount > 1) {
                    $('#JSserviceItems').append("<tr><td colspan=5><b>Engine " + (i+1) + "</b></td></tr>");
                }
                for (j=0; j<data.engines[i].serviceItems.length; j++) {
                    var rowidx = $('#JSserviceItems tr').length;    // Use row number as unique index for items
                    parts = "<tr style='height:20px;padding:0px;margin:0px;'>";
                        parts += "<td style='padding:0px;width:50px'>";
                            parts += "<input type='text' class='jsPart' size='3' id='editsiqty-"+(rowidx)+"' value='" + data.engines[i].serviceItems[j].siQty + "' />";
                        parts += "</td>";
                        parts += "<td>" + data.engines[i].serviceItems[j].siName + "</td>";
                        parts += "<td>" + safeDecode(data.engines[i].serviceItems[j].siMake) + " " + safeDecode(data.engines[i].serviceItems[j].siPartno) + "<td>";
                        if ($.isNumeric(data.engines[i].serviceItems[j].siPrice)) {
                            parts += "<td class='priceCol' id='editsiunit-"+(rowidx)+"'>£" + numberStringToCurrencyString(data.engines[i].serviceItems[j].siPrice) + "</td>";
                            parts += "<td class='priceCol' id='editsitotal-"+(rowidx)+"' style='text-align:right'>£" + numberStringToCurrencyString((data.engines[i].serviceItems[j].siQty * data.engines[i].serviceItems[j].siPrice)) + "</td>";
                        }
                    parts += "</tr>";
                    $('#JSserviceItems').append(parts);
                }                
            }
            $('#JSchanged').html("1");      // Mark page dirty
            setSIQtyWatch();           
        }
    }, "json");
}

// Remove any row from a parts/jobs table that has a quantity of zero
function removeZeroQty(selector) {
    if ($('#'+selector+' tr').length) { // Only test tables that actually have rows.
        for (var i=0; i<$('#'+selector+' tr').length; i++) {
            var x = $('#'+selector+' tr:eq('+i+') td:eq(0)').html();  // Column zero is the Quantity column
            if (x.indexOf('value=\"0\"') > 0) { 
                $('#'+selector+' tr:eq('+i+')').remove();
            }
        }
    }
}

function partsTable2json(selector) { // Convert parts or labour table to JSON 
var jsonData = "";
var id, idx;
var engNo = 1;
var rowCount = $('#'+selector+' tr').length;
    if (rowCount > 0) { // Only test tables that actually have rows.
        for (var i=0; i<rowCount; i++) {    // Now process every row
//            if ($('#'+selector+' tr:eq('+i+') td:eq(0)').html().substring(3,9) == "Engine") {
            if ($('#'+selector+' tr:eq('+i+') td:eq(0)').html().indexOf("Engine") > -1) {
                // Skip if its an Engine heading)
                jsonData += '{"Engine":"' + engNo + '"}, ';
                engNo ++;
            } else {
                // First we get the Qty from the first column (it's the default value of an 'input' field)
                id = $('#'+selector+' tr:eq('+i+') td:eq(0) input').attr('id');
                if ($('#'+id).prop("defaultValue") > 0) {   // If the Qty is zero we delete this line
                    jsonData += '{"' + id + '":';
                    idx = id.substring(id.indexOf('-')+1);
                    jsonData += '"' + $('#'+id).prop("defaultValue") + '",';

                    id = $('#'+selector+' tr:eq('+i+') td:eq(1) input').attr('id');
                    if (id === undefined) {
                        jsonData += '"part-'+idx+'":';
                    } else {
                        jsonData += '"' + id + '",';
                    }
                    jsonData += '"'+$('#'+selector+' tr:eq('+i+') td:eq(1)').html()+'",';

                    id = $('#'+selector+' tr:eq('+i+') td:eq(2) input').attr('id');
                    if (id === undefined) {
                        jsonData += '"make-'+idx+'":';
                    } else {
                        jsonData += '"' + id + '",';
                    }               
                    jsonData += '"'+$('#'+selector+' tr:eq('+i+') td:eq(2)').html()+'",';

                    id = $('#'+selector+' tr:eq('+i+') td:eq(3) input').attr('id');
                    if (id === undefined) {
                        jsonData += '"model-'+idx+'":';
                    } else {
                        jsonData += '"' + id + '",';
                    }               
                    jsonData += '"'+$('#'+selector+' tr:eq('+i+') td:eq(3)').html()+'",';

                    id = $('#'+selector+' tr:eq('+i+') td:eq(4) input').attr('id');
                    if (id === undefined) {
                        jsonData += '"editsiunit-'+idx+'":';
                    } else {
                        jsonData += '"' + id + '",';
                    }               
                    jsonData += '"'+$('#'+selector+' tr:eq('+i+') td:eq(4)').html()+'",';

                    id = $('#'+selector+' tr:eq('+i+') td:eq(5) input').attr('id');
                    if (id === undefined) {
                        jsonData += '"editsitotal-'+idx+'":';
                    } else {
                        jsonData += '"' + id + '",';
                    }               
                    jsonData += '"'+$('#'+selector+' tr:eq('+i+') td:eq(5)').html()+'"},';
                }
            }
        }
        return('{"parts":[' + strim(jsonData,',') + ']}');
    } else {
        return ("")  // Nothing to convert so return nothing
    }
}

function jobsTable2json(selector) { // Convert parts or labour table to JSON 
var jsonData = "";
var id, idx, cidx;
var rowCount = $('#'+selector+' tr').length;
var colCount = countCols(selector);
    if (rowCount > 0) { // Only test tables that actually have rows.
        for (var i=0; i<rowCount; i++) {    // Now process every row
            if ($('#'+selector+' tr:eq('+i+') td:eq(0)').html().substring(3,9) != "Engine") {  // Skip if its an Engine heading)
                cidx = 0;
                // First we get the Qty from the first column (it's the default value of an 'input' field)
                id = $('#'+selector+' tr:eq('+i+') td:eq('+ cidx +') input').attr('id');
                if ($('#'+id).prop("defaultValue") > 0) {   // If the Qty is zero we delete this line
                    jsonData += '{"' + id + '":';
                    idx = id.substring(id.indexOf('-')+1);
                    jsonData += '"' + $('#'+id).prop("defaultValue") + '",';

                    cidx++;
                    id = $('#'+selector+' tr:eq('+i+') td:eq('+ cidx +') input').attr('id');
                    if (id === undefined) {
                        jsonData += '"job-'+idx+'":';
                    } else {
                        jsonData += '"' + id + '",';
                    }
                    jsonData += '"'+$('#'+selector+' tr:eq('+i+') td:eq('+ cidx +')').html()+'",';

                    cidx++;
                    id = $('#'+selector+' tr:eq('+i+') td:eq('+ cidx +') input').attr('id');
                    if (id === undefined) {
                        jsonData += '"editlabunit-'+idx+'":';
                    } else {
                        jsonData += '"' + id + '",';
                    }               
                    jsonData += '"'+$('#'+selector+' tr:eq('+i+') td:eq('+ cidx +')').html()+'",';

                    // Now we have to allow for some rows that don't have a discount column
                    // If there is only 4 columns we have to create a discount column here
                    cidx++;
                    if (countCols(selector) == 4) {
                        jsonData += '"editlabdiscount-'+idx+'":';
                        jsonData += '"0",';
                    } else {
                        id = $('#'+selector+' tr:eq('+i+') td:eq('+ cidx +') input').attr('id');
                        if (id === undefined) {
                            jsonData += '"editlabdiscount-'+idx+'":';
                        } else {
                            jsonData += '"' + id + '",';
                        }               
                        jsonData += '"'+$('#'+selector+' tr:eq('+i+') td:eq('+ cidx +')').html()+'",';                       
                    }

                    cidx++;
                    id = $('#'+selector+' tr:eq('+i+') td:eq('+ cidx +') input').attr('id');
                    if (id === undefined) {
                        jsonData += '"editlabtotal-'+idx+'":';
                    } else {
                        jsonData += '"' + id + '",';
                    }               
                    jsonData += '"'+$('#'+selector+' tr:eq('+i+') td:eq('+ cidx +')').html()+'"},';
                }
            }
        }
    }
    return('{"jobs":[' + strim(jsonData,',') + ']}');
}


function previewInvoice(id) {
    if ($('#JSchanged').html() != "0") {
        alert("Preview is not available because you have made changes that have not been saved");
    } else {
        window.open("printInvoice.php?id="+id , "_blank");
    }
}

function smsPopUp(to, addressee) {
	var prompt = "Send to: " + to + "&nbsp;&nbsp;Title:&nbsp;<input type'text' id='smstitle' style='width:200px' maxlength='25' /><br>";
	prompt += "Message:<br>";
    prompt += "<textarea id='smstext' style='width:560px' rows='2' wrap='soft' maxlength='129' onkeyup='maxTxt(\"#smstext\",\"#smstextCC\");'></textarea>";
    prompt += "<div id='smstextCC' style='font-size:0.7em; color:#666666; text-align:right;padding-right:5px'>&nbsp;</div></td>";
	$(function() {
		$('<div />').html(prompt).dialog({
			title: 'Send SMS',
			resizable: false,
			width:600,
			height:"auto",
			modal: true,
			buttons: {
				"Send": function() {
                    var subj = $('#smstitle').val();
                    var msg = $('#smstext').val();
                    var smsJson = {"addressee":addressee, "sendTo": to, "subject":subj, "msg": msg };
                     $( this ).dialog( "close" );
                    $.post("dlmFunctionDispatch.php?f=sendSMSandLog&user="+$.cookie('user'), smsJson, function(data) {
                        if (data.status == "OK") {
                            myAlert("Text Message Sent", data.status);
                        } else {
                            myAlert("Text Message Not Sent", data.status);
                        }
                    }, "json");
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
}


function showNotes(parent, parentRecId) {
    var notes = "";
    $.post("dlmFunctionDispatch.php?f=getMoreNotes&parent=" + parent + "&parentRecId=" + parentRecId, function(data) {
        if (data.resultcount == 0) {
            myAlert("Nothing Found", "No more notes are recorded.");
        } else {
            notes += "<table id='notesTable' class='display' width='100%' cellspacing='0'>";
            notes += "<thead><tr><th width='200px'>Timestamp</th><th width='120px'>Source</th><th width='120px'>Author</th><th>Notes</th></tr></thead>";
            notes += "</table>";
            for (i=0; i<data.resultcount; i++) {
                data.results[i].note = safeDecode(data.results[i].note);
                data.results[i].note = nTobr(data.results[i].note);
            }
            $('#moreNotes').html(notes);
            $('#notesTable').dataTable( {
                "iDisplayLength": 10,
                "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                stateSave: true,
                data: data.results,
                "columns": [
                    {"data":"timestamp", "class":"center"},			
                    {"data":"source", "class":"center"},			
                    {"data":"user", "class":"center"},			
                    {"data":"note", "class":"center"}
                ]
            });
            $('#moreNotes').append("<br><button class='btn' onclick='hideMoreNotes();'>Hide Notes</button>");
            $('#moreNotes').slideToggle("slow");
        }
    }, "json");    
}

function showPhotos(parent, parentId) {  //getPhotoList&parent=jobsheets&parentId=16
    $.post("dlmFunctionDispatch.php?f=getPhotoList&parent=" + parent + "&parentId=" + parentId, function(data) {
        if (data.resultcount == 0) {
            myAlert("Nothing Found", "No photos recorded.");
        } else {
            myAlert("Photos", data.resultcount + " Photos");
            location.href = "photoGallery.html?p=" + parent + "&id=" + parentId;
        }
    }, "json");
}

function hideMoreNotes() {
    $('#moreNotes').slideToggle("slow");
}

//=================================================================================================================================
// Reformat 'old' Jobsheet Parts
//
function reformatJSParts(oldParts) {
    var j;
    var qty;
    var thisLine;
    var partsTable = "";
    var unitCost;
    var unitPrice;
    var total;
    
    var newParts = oldParts.replace(/\n/gmi, "~");  // First convert all the newlines /n into single character so we can use 'split'
    var partsArray = newParts.split("~");           // Now split into an array (of parts lines)
    
    for (var i=0; i<partsArray.length; i++) {       // Now parse all lines
        thisLine = partsArray[i].split(" ");        // Now break line into 'words'
        if (thisLine[0] != "") {                    // Don't process empty lines
            j = 0;
            partsTable += "<tr>";
            if (thisLine[j] == "Engine") {  // If the this line is an 'Engine' heading then store that in table row and move on
                partsTable += "<td colspan='5'>"+thisLine[j++] + " " + thisLine[j] + "</td>";
            } else {
                if ($.isNumeric(thisLine[j])) {     // Check first 'word' is the quantity
                    qty = thisLine[j++];
                } else {
                    qty = 1;  // If not specified assume Qty 1
                }
                partsTable += "<td style='padding:0px;width:50px'>";
                partsTable += "<input type='text' id='editsiqty-" + i + "' class='jsPart' size='3' disable='true' value='" + qty + "'/></td>";
                if (thisLine[j] == "x") { j++; }  // Skip the 'x' if there is one
                
                partsTable += "<td>";   // We can't safely unpack the Make, Model & Partno so now get everything up to the cost
                for (j; j<thisLine.length; j++) {
                    if (thisLine[j].substr(0,1) == "£") {               // Now add the £xx
                        thisLine[j] = thisLine[j].replace(/ea/gi, "");  // Drop the 'ea' as in £23.45ea
                        unitCost = thisLine[j].substr(1);               // Just get the actual value
                        unitPrice = "<td class='priceCol' id = 'editsiunit-" + i + "'>£" + unitCost + "</td>";
                        break;
                    }
                    partsTable += (thisLine[j] + " ");  
                }
                partsTable += "</td>";
                partsTable += "<td></td><td></td>";     // Add blanks for Model and PartNo.
                partsTable += unitPrice;
                total = (qty * parseFloat(unitCost)) + " ";
                partsTable += "<td class='priceCol' id = 'editsitotal-" + i + "' style='text-align:right;'>£"+numberStringToCurrencyString(total)+"</td>";
            }
            partsTable += "</tr>";
        }
    }
    partsTable = "<table id='JSserviceItems' style='width:100%'>" + partsTable + "</table>";  // Now load into table format.
    alert("Parts data updated");
    return (partsTable);
}

//=================================================================================================================================
// Reformat 'old' Jobsheet Jobs
//
function reformatJSJobs(oldJobs) {
    var j;
    var qty;
    var thisLine;
    var jobsTable = "";
    var unitCost;
    var unitPrice;
    var total;
    
    var newJobs = oldJobs.replace(/\n/gmi, "~");  // First convert all the newlines /n into single character so we can use 'split'
    var jobsArray = newJobs.split("~");           // Now split into an array (of jobs lines)
    
    for (var i=0; i<jobsArray.length; i++) {       // Now parse all lines
        thisLine = jobsArray[i].split(" ");        // Now break line into 'words'
        if (thisLine[0] != "") {                    // Don't process empty lines
            j = 0;
            jobsTable += "<tr>";
            if (thisLine[j] == "Engine") {  // If the this line is an 'Engine' heading then store that in table row and move on
                jobsTable += "<td colspan='5'>"+thisLine[j++] + " " + thisLine[j] + "</td>";
            } else {
                if ($.isNumeric(thisLine[j])) {     // Check first 'word' is the quantity
                    qty = thisLine[j++];
                } else {
                    qty = 1;  // If not specified assume Qty 1
                }
                jobsTable += "<td style='padding:0px;width:50px'>";
                jobsTable += "<input type='text' id='editlabqty-" + i + "' class='jsPart' size='3' disable='true' value='" + qty + "'/></td>";
                if (thisLine[j] == "x") { j++; }  // Skip the 'x' if there is one
                
                jobsTable += "<td>";   // We can't safely unpack the Make, Model & Partno so now get everything up to the cost
                for (j; j<thisLine.length; j++) {
                    if (thisLine[j].substr(0,1) == "£") {               // Now add the £xx
                        if (thisLine[j] == "£") {  // Check to see if they left a space after the £ 
                            j++;  // In which case we have to get the actual price from the next word.
                            unitCost = thisLine[j];
                        } else {  // Oterwise we skip the £ and get the actual value
                            unitCost = thisLine[j].substr(1);               // Just get the actual value
                        }
                        thisLine[j] = thisLine[j].replace(/ea/gi, "");  // Drop the 'ea' as in £23.45ea
                        
                        unitPrice = "<td class='priceCol' id = 'editlabunit-" + i + "'>£" + unitCost + "</td>";
                        break;
                    }
                    jobsTable += (thisLine[j] + " ");  
                }
                jobsTable += "</td>";
                jobsTable += unitPrice;
                total = (qty * parseFloat(unitCost)) + " ";
                jobsTable += "<td class='display:none;' id = 'editlabdiscount-" + i + "' >0</td>";                
                jobsTable += "<td class='priceCol' id = 'editlabtotal-" + i + "' style='text-align:right;'>£"+numberStringToCurrencyString(total)+"</td>";
            }
            jobsTable += "</tr>";
        }
    }
    jobsTable = "<table id='JSLabour' style='width:100%'>" + jobsTable + "</table>";  // Now load into table format.
    alert("Jobs data updated");
    return (jobsTable);
}

function partsJson2Table(partsJson) {
    var partsJson = JSON.parse(partsJson);
    var partsObj = partsJson.parts;
    var partsTable = "";
    $.each(partsJson.parts,function() {
        partsTable += "<tr>";
        $.each(this, function(k, v) {
            if (k.indexOf("Engine") != -1) {
                partsTable += "<td colspan=5><b>" + k + " " + v + "</td>";
                return true;
            } else if (k.indexOf("editsiqty") != -1) { // It's a Qty so we set up the input field
                partsTable += "<td><input type='text' id='" + k + "' class='jsPart' size='3' disable='true' value='" + v + "' /></td>";
            } else if ((k.indexOf("unit") != -1)||(k.indexOf("total") != -1)){
                partsTable += "<td id='" + k + "' class='priceCol' >" + v + "</td>";
            } else {
                partsTable += "<td id='" + k + "'>" + v + "</td>";
            }
        });
        partsTable += "</tr>";
    });
    return ("<table id='JSserviceItems' style='width:100%'>" + partsTable + "</table>");
}

function jobsJson2Table(jobsJson) {
    var jobsJson = JSON.parse(jobsJson);
    var jobsObj = jobsJson.jobs;
    var jobsTable = "";
    $.each(jobsJson.jobs,function() {
        jobsTable += "<tr>";
        $.each(this, function(k, v) {
            if (k.indexOf("Engine") != -1) {
                jobsTable += "<td colspan=5><b>" + k + " " + v + "</td>";
                return true;
            } else if (k.indexOf("editlabqty") != -1) { // It's a Qty so we set up the input field
                jobsTable += "<td><input type='text' id='" + k + "' class='jsjob' size='3' disable='true' value='" + v + "' /></td>";
            } else if ((k.indexOf("unit") != -1)||(k.indexOf("total") != -1)){
                jobsTable += "<td id='" + k + "' class='priceCol' >" + v + "</td>";
             } else if (k.indexOf("discount") != -1){
                jobsTable += "<td style='display:none;' id='" + k + "' >" + v + "</td>";
             } else {
                jobsTable += "<td id='" + k + "'>" + v + "</td>";
            }
        });
        jobsTable += "</tr>";
    });
    return ("<table id='JSLabour' style='width:100%'>" + jobsTable + "</table>");
}

// Get next index for parts or jobs row
function getIdxForNextRow(thisTable) {
    var idx = $('#'+thisTable+' tr:last td:eq(0) input').attr('id');
    idx = parseInt(idx.substring(idx.indexOf('-')+1));
    idx += 1;
    return(idx); 
}