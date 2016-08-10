<?php
include('config.php');
define("UPLOAD_DIR", $syspath);
if (!empty($_FILES["myFile"])) {
    $myFile = $_FILES["myFile"];
    if ($myFile["error"] !== UPLOAD_ERR_OK) {
        echo "<p>An error occurred.</p>";
        exit;
    }
    // ensure a safe filename
    $safename = preg_replace("/[^A-Z0-9._-]/i", "_", $myFile["name"]);
    $parts = pathinfo($safename);
    $today = date("Y-m-d-G:h:i");
    $fullname = $parts["filename"] . '-'.$today.'.' . $parts["extension"];
    // preserve file from temporary directory
    $success = move_uploaded_file($myFile["tmp_name"], UPLOAD_DIR . $fullname);
    if (!$success) { 
        echo "<p>Unable to save file.</p>";
        exit;
    } else {
	// set proper permissions on the new file
//	chmod(UPLOAD_DIR . $fullname, 0644);
   	//echo "<p>SUCCESS! <a href=\"./\">Back</a></p>";
	$goto = 'Location: process.php?file='.$fullname;
	header($goto);
    }
}
