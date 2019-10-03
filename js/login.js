function showLogin(page) {
	if (typeof page === "undefined") { var page = "index.php"; }
	var loginForm = "<div id='login'>";
		loginForm += "<table>";
		loginForm += "<tr><td>Name</td><td><input id='username' type='text' /></td></tr>";
		loginForm += "<tr><td>Password</td><td><input id='password' type='password' value=''/></td></tr>";
		loginForm += "</table>";
		loginForm += "<span style='font-size:xx-small;'>";
		loginForm += "<a class='tooltip' onclick='loadPWForm();'>Set New Password?</a></span>";
		loginForm += "</div>";
    var NewDialog = $('<div id="loginDialog">\initialising\</div>');
    NewDialog.dialog({
        title: "Please Login",
        modal: true,
        height: 'auto',
        width:'auto',
        resizable: false,
        show: 'fade',
        hide: 'fade',
        buttons: [
            {text: "Submit", click: function() {login($('#username').val(),$('#password').val(), page); $(this).dialog("close");}},
            {text: "Cancel", click: function() {$(this).dialog("close");}}
        ],
        close: function(ev, ui) { $(this).dialog('destroy').remove(); }
    });
	$('#loginDialog').html(loginForm);

$("#password").keypress(function(event) {
    if (event.which == 13) {
        event.preventDefault();
        $('#loginDialog').dialog("close");
        login($('#username').val(),$('#password').val(), page);
    }
});


    return false;
}

function login(u,p, page) {
	var pw = CryptoJS.MD5(p).toString();
	$.post("dlmFunctionDispatch.php?f=login", { username: u, password: pw }, function(data) {
		if (data.status == 'DB_Error') {
			location.href="serverError.php?e="+encodeURI(data.msg);
		}
		if (data.username == u) {
			if (data.userType == "newpassword") {
				myAlert("Account Disabled", "Your account is currently disabled and is <br>Awaiting activation by a Driveline Administrator.");
			} else {
				$.cookie("userId", data.userId, {expires: 1});
				$.cookie("userType", data.userType, {expires: 1});
				$.cookie("user", u,  {expires: 1});
				$('#user').html($.cookie("user") + " (" + $.cookie("userType") + ")&nbsp;<a href='#' onclick='logout()'>Logout</a>");
				$.cookie("userIP", data.ip, {expires: 1}); 
				location.href = page;
			}
		} else {
			alert("Unrecognised Username/Password");
		}
 	}, "json");
}

function logout() {
	$.cookie("user", null);
	$.cookie("userType", null);
	location.href='index.php';
}

/* *********
 * Get a new registration - or new password which is basically the same thing as only 'pre-registered
 * users are allowed.
 */
function loadPWForm() {
	var pwForm = "<table>";
		pwForm += "<tr><td>Username</td><td><input id='un' type='text' size='20' value='" + $('#username').val() + "'/></td></tr>";
		pwForm += "<tr><td>Password</td><td><input id='pw1' type='password' size='20' /></td></tr>";
		pwForm += "<tr><td>Confirm<br>Password</td><td><input id='pw2' type='password' size='20' /></td></tr>";
		pwForm += "</table>";
    $('#loginDialog').dialog( "close" );
    var NewDialog = $('<div id="pwDialog">\initialising\</div>');
    NewDialog.dialog({
        title: "Change Password",
        modal: true,
        height: 'auto',
        width:'auto',
        resizable: false,
        show: 'fade',
        hide: 'fade',
        buttons: [
            {text: "Submit", click: function() {
                if ($('#pw1').val().length < 4) {
                    alert("Passwords must be 4 characters or more.");
                    $('#pw1').val("")
                    $('#pw2').val("");
                } else if ($('#pw1').val() != $('#pw2').val()) {
                    alert("Passwords don't match, please reenter.");
                    $('#pw1').val("")
                    $('#pw2').val("");
                } else {
                    var u = $('#un').val();
                    var pw = CryptoJS.MD5($('#pw1').val()).toString();
                    $.post("dlmFunctionDispatch.php?f=newpw",{ username: u, password: pw }, function(data) {
                        if (data.status == "OK") {
                            myAlert("Password Updated", "For security reasons your account has been disabled and must now <br>be 'Activated' by an Adminstartor on the Driveline Marine Staff Site.")
                        } else {
                            myAlert("Problem", data.status);
                        }
                    }, "json");
                    $(this).dialog("close");
                }
            }
        },
            {text: "Cancel", click: function() {
                $(this).dialog("close");
                }
            }
        ],
        close: function(ev, ui) { $(this).dialog('destroy').remove(); }
    });
	$('#pwDialog').html(pwForm);
    return false;
}

