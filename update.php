<?php 
$teasku = $_POST['upsku'];
$teametrode = $_POST['upmetrode'];
$teacat = $_POST['upcat'];
$teaname = $_POST['upname'];
$teaqty = $_POST['upqty'];
$teacost = $_POST['upcost'];
$teamin = $_POST['upmin'];
$teamax = 0;
$teamult = $_POST['upmult'];
$teaid = $_POST['tid'];
try {

        // Create (connect to) SQLite database in file
        $file_db = new PDO('sqlite:db/teas.sqlite3');
        // Set errormode to exceptions
        $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Prepare INSERT statement to SQLite3 file db
        $sql = "UPDATE teas SET sku=?, metrode=?, category=?, name=?, qty=?, cost=?, min=?, max=?, multiplier=? WHERE id=?";
        $q = $file_db->prepare($sql);
        $q->execute(array($teasku, $teametrode, $teacat, $teaname, $teaqty, $teacost, $teamin, $teamax, $teamult, $teaid));
	header("Location: ./new.php");
} catch(PDOException $e) {
        // Print PDOException message
        echo $e->getMessage();
}
