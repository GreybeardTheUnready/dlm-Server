<?php
error_log("upload.php");
$targetDir = "uploads";

// First check that we got the file, the whole file and nothing but the file...
if($_FILES['file']['error'] > 0) {
	$result = 'Upload failed: ';
	switch($_FILES['file']['error'])  { 	// These are default PHP Errors
		case 1: 						// File exceeded the PHP property upload_max_filesize
		case 2: $result .= "File too big"; break; 		// File has exceeded max_file_size (The hidden element we created on our form)
		case 3: $result .= 'Incomplete upload'; break;		// File was partially uploaded, not the whole thing
		case 4: $result .= 'Unknown Error (4)'; break;		// No was file uploaded
	}
} else {
	// Check that it is a 'legal' file
	$isAllowed = array("image/jpg","image/jpeg");
	if  (!in_array ( $_FILES['file']['type'], $isAllowed )) {
		$result = "File ".$_FILES['file']['name']." was not an allowed type.<br>";
		$result .= "Allowed types are jpg, jpeg";
//error_log($result);
	} else {
		// Looks like we have received a valid image file.
		// So now we need to see if this was invoked by a client upload, or interactively by the user through the console.
		if ($_POST['source'] == "dlmMobile") {
			// Client upload so now we extract the chanid and use it to get the Building and Meter.
			$fn = $_FILES['file']['name'];
//error_log("File = " . $fn);

			$targetPath = $targetDir . DIRECTORY_SEPARATOR . $fn;
//error_log("targetPath = " . $targetPath);
			if(move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
				$result = $_POST['original'];
				error_log("DLM Photo uploaded as $fn Into [ ". $targetDir. "]");
			} else {
				$result = "Failed to upload: [" . $_FILES['file']['error'] . "]";
				error_log("DLM Photo upload error: " . $_FILES['file']['error']);
			}



		} else {
			$result = "Failed to upload: File from unknown source";
			error_log("DLM Photo upload error: File from unknown source");
		}
	}
}
echo $result;

?>