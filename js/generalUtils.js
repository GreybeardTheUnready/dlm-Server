/**************************************************************************************************
 * General purpose display function - displays 'content' in 'target' element with 'heading'
 * If 'target' begins with "dialog" then a JQuery dialog box with the id 'target' is used.
 * If 'target' ends with + then content is APPENDED to current contents of element
 * 		nb: Does NOT apply to dialogue boxes.
 * @param {string} target
 * @param {string} heading
 * @param {string} content
 */
function displayResults(target, heading, content) {
	// Now check where to display the result
	if (target.substring(0, 6) == "dialog") {
		$(function() {
			$("#"+target).dialog();
			$("#"+target).dialog({
				title:heading, modal:true, width:'auto',
				open: function (event, ui) {
					$(this).animate({
						scrollTop: $(this).scrollTop() + $(this).height()
             		});
         		}
			}); // , width:'auto', resizable: false 
			$("#"+target).html(content);
			$("#"+target).css( { 'max-height' : '700px', 'max-width' : '900px' } );
			$("#dialog").dialog( "option","position", "center" );
		});
	} else {
		if (target.slice(-1) == "+") {
			target = target.substring(0, target.length - 1);
			var current = $("#"+target).html();
			$("#"+target).html(current + "<b>" + heading + "</b>" + content);
		} else {
			$("#"+target).html("<b>" + heading + "</b>" + content);
		}
	}
}

/******************************************************************************
 * fullDateStr(d)
 * Convert Date Object into a long date string e.g. Monday 28 November 2012
 * 
 * @param {Object} d
 */
function fullDateStr(d) {
	var monthNames = [ "January", "February", "March", "April", "May", "June",
	    "July", "August", "September", "October", "November", "December" ];
	var dayNames= ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

	return (dayNames[d.getDay()] + " " + d.getDate() + ' ' + monthNames[d.getMonth()] + ' ' + d.getFullYear());
}

/******************************************************************************
 * Check that s is a valid mysql date
*/
function chkDate(s) {
    if ((isNaN(Date.parse(s))) || (Date.parse(s) < Date.parse("2016-01-01")) || (Date.parse(s) > Date.parse("2050-12-31"))) {
        return false;
    } else {
        return true;
    }  
}

/******************************************************************************
 * get todays date as yyyy-mm-dd
 */
function mysqlDate() {
    var today = new Date();
    var month = (today.getMonth()+1);
    if (month < 10) { month = "0" + month; }
    var day = today.getDate();
    if (day < 10) { day = "0" + day; }
    return (today.getFullYear() + "-" + month + "-" + day);
}


/*****************************************************************************
 * getQueryString
 * Return value of QueryString parameter 'key'
 *  
 * @param {String} key
 */
function getQueryString(key) {
    key = key.replace(/[*+?^$.\[\]{}()|\\\/]/g, "\\$&"); // escape RegEx meta chars
    var match = location.search.match(new RegExp("[?&]"+key+"=([^&]+)(&|$)"));
    return match && decodeURIComponent(match[1].replace(/\+/g, " "));
}

/*****************************************************************************
 * strim(s,c)
 * Remove character 'c' from the end of string 's' (if its there)
 *  
 * @param {String} s
 * @param {Character} c
 */
function strim(s,c) {
	if (s.slice(-1) == c) {
		return s.substring(0, s.length - 1);
	} else {
		return s;
	}
}

function toTitleCase(str) {
    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
}

/******************************************************************************
 * Create a Browser object that can easily check IE version.
 * (Non-IE browsers will return 999);
 * 
 * Use:  if (Browser.Version() < x) { Code for IE version less than x } 
 */
var Browser = {
  Version: function() {
    var version = 999; // we assume a sane browser
    if (navigator.appVersion.indexOf("MSIE") != -1) {
      // bah, IE again, lets downgrade version number
      version = parseFloat(navigator.appVersion.split("MSIE")[1]);
    }
    return version;
  }
};

function selectAll(cls,state) {	// Sets the Checked status of all checkboxes of class 'cls' to 'state'
	$("." + cls).each(function() {
		$(this).prop('checked', state);
	});
}

function htmlEncode(value){
  //create a in-memory div, set it's inner text(which jQuery automatically encodes)
  //then grab the encoded contents back out.  The div never exists on the page.
  return $('<div/>').text(value).html();
}

function htmlDecode(value){
  return $('<div/>').html(value).text();
}

var entityMap = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': '&quot;',
    "'": '&#39;',
    "/": '&#x2F;'
  };

function escapeHtml(string) {
    return String(string).replace(/[&<>"'\/]/g, function (s) { return entityMap[s]; });
}

function maintenance(wip, nextPage) {	// Requires JQuery
var	myIP = "86.2.4.178";				// << EDIT AS NEEDED <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
	if (wip) {
		if ($.cookie("userIP") != myIP) {
			alert("Sorry, This page is currently closed for maintenance");
			location.href = nextPage;
		} else {
			alert("Maintenance Mode");
		}
	}
}


function brTon(str) {   // Convert HTML '<br>' tag to /n
    return str.replace(/<br\s*[\/]?>/gmi, '\n');
}

function nTobr(str) {   // Convert /n to HTML '<br>' tag
    return str.replace(/\n/gmi, '<br>');
}

/*****************************************************************************
 * 
 * This function can be used to limit the contents of a Textbox, or Textarea
 */
function maxTxt(tId, wId) {
    var max = $(tId).attr('maxlength'); 
    $(wId).html(max + ' characters remaining');

     $(tId).keyup(function() {
        var text_length = $(tId).val().length;
        var text_remaining = max - text_length;

       $(wId).html(text_remaining + ' characters remaining');
    });
}

/*****************************************************************************
 * 
 * These functions add single quote to the escaped characters
 */

String.prototype.replaceAll = function(target, replacement) {
  return this.split(target).join(replacement);
};

function safeEncode(str) {
    var encStr = str.replace(/input/g, "IN##T");     // For some bizarre reason we have to hide the 'input' on SRN since 20th Oct 2017 !!!!!!
    return encodeURIComponent(encStr).replaceAll(/'/g, "%27");   
}

function safeDecode(str) {
    var decStr = decodeURIComponent(str).replaceAll("%27", "'");
    return(decStr.replace(/IN##T/g, "input"));    // For some bizarre reason we have to hide the 'input' on SRN since 20th Oct 2017 !!!!!! 
}

/*****************************************************************************
 * 
 * JQuery DIALOG version of alert()
 */
function myAlert(title, msg) {
    $('<div />').dialog({
        title: title,
        width: "auto",
        modal: true,
        open: function() { $(this).html(msg); },
        buttons: {
          "Ok": function() {
            $( this ).dialog("close");
          }
        }
      });  //end confirm dialog
}

function buildSelect(name, json) {
	var select = "<select class='mySelect' id='" + name + "'>";
    select += "<option value='-1'></option>";    // ALways start with a blank
    for (var i=0; i<json.options.length; i++) {
        select += "<option value='" + json.options[i].id +  "'";
        if (typeof json.options[i].data !== "undefined") {
            select += " data-arg='" + json.options[i].data + "'";
        }
        select += ">" + safeDecode(json.options[i].name) + "</option>";
    }
	select += "</select>";
	return select;
}

function selectOptionByVal(id, val) {
    $("#"+id).val(val);
}

function selectOptionByText(id, text) {
    $("#"+id+" option").filter(function(){
        return $.trim($(this).text()) ==  text;
    }).prop('selected', true);
}

function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

function numberStringToCurrencyString(n) {
//    var x = parseFloat(n);
    return (Number(parseFloat(n)).toFixed(2))
}

//function showHelper(id, content) {
//    var p = $('#' + id).offset(); // position = { left: 42, top: 567 }
//    
//    $('#helperDiv').css({'top':p.top-50,'left':p.left+20, 'position':'absolute', 'border':'1px solid black', 'padding':'5px'});
//    $('#helperDiv').html(content);
//    $('#helperDiv').show();
//}
//
//function hideHelper() {
//    $('#helperDiv').hide();
//}

function showLoading() {
    $('#loadingDiv').css("visibility", "visible");
}

function hideLoading() {
    $('#loadingDiv').css("visibility", "hidden");
}

function countCols(tableId) {
    var colCount = 0;
    $('#' + tableId + ' tr:nth-child(1) td').each(function () {
        if ($(this).attr('colspan')) {
            colCount += +$(this).attr('colspan');
        } else {
            colCount++;
        }
    });
	return (colCount);
}