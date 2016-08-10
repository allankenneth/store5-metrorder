<?php	
try {

	// Create (connect to) SQLite database in file
	$file_db = new PDO('sqlite:db/teas.sqlite3');
	// Set errormode to exceptions
	$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$delete = 'DELETE FROM notes WHERE id=' . $_GET['nid'];
	$stmt = $file_db->prepare($delete);
	$stmt->execute();
    // Close file db connection
	$file_db = null;
        $go = 'Location: ./order.php?oid=' . $_GET['oid'];
        header($go);	
	
} catch(PDOException $e) {
	// Print PDOException message
	echo $e->getMessage();
}
