<?php 
$now = date("U");
$note = $_POST['note'];
$order = $_POST['oid'];
try {
        $file_db = new PDO('sqlite:db/teas.sqlite3');
    	$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$file_db->exec("CREATE TABLE IF NOT EXISTS notes (id INTEGER PRIMARY KEY AUTOINCREMENT, orderid INTEGER, date INTEGER, noted TEXT)");
    	$insert = "INSERT INTO notes (orderid, date, noted) VALUES (:orderid, :date, :noted)";
        $stmt = $file_db->prepare($insert);
    	$stmt->bindParam(':noted', $note);
   	$stmt->bindParam(':date', $now);
   	$stmt->bindParam(':orderid', $order);
	$stmt->execute();
	$go = 'Location: ./order.php?oid=' . $order;
	header($go);
} catch(PDOException $e) {
    	print $e->getMessage();
}
