<!DOCTYPE html>
<html lang="en-gb">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
<meta content="jonb" name="author">
<title>RITA the meter maid</title>
<link rel='icon' href='images/favicon.png' type='image/png' />
<link href="css/base.css" rel="stylesheet" type="text/css">
<link href="css/rita.css" rel="stylesheet" type="text/css">

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>

<script src="js/config.js" type="text/javascript"></script>
</head>

<body >
	<div id='pageContainer'>
		<div id="Logo" style="margin-top:100px; text-align:center;">
			<img src='images/DrivelineLogoAndName.jpg' />
			<p style='margin-top:40px; margin-bottom:80px;font-size: x-large'>Website Error</p>
		</div>
		<div id='message' style='width:60%; margin: 0 auto; border: thick solid #FF0000; padding:20px;'>
		<?php
			if ($_GET['e']) {
				$errorMsg = $_GET['e'];
            } else {
                $errorMsg = "Unknown Server failure";
            }
            error_log($errorMsg);
            echo "<b>" . $errorMsg . "</b><br><br>";
			echo "<p>Please <a href=http://" . $_SERVER['HTTP_HOST'].">restart DLM</a>.</br>If this problem persists please report.</p>"
		?>
		</div>
	</div>
</body>
</html>
