<?php 
$orderid = $_GET['oid'];
$orderstat = $_GET['upstat'];
try {

        // Create (connect to) SQLite database in file
        $file_db = new PDO('sqlite:db/teas.sqlite3');
        // Set errormode to exceptions
        $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Prepare INSERT statement to SQLite3 file db
        $sql = "UPDATE orders SET status=? WHERE id=?";
        $q = $file_db->prepare($sql);
        $q->execute(array($orderstat, $orderid));
	$go = "Location: order.php?oid=" . $orderid;
	header($go);
} catch(PDOException $e) {
        // Print PDOException message
        echo $e->getMessage();
}
