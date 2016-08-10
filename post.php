<?php 
//sku, metrode, category, name, cost, qty, min, max, multiplier
//print_R($_POST);
//exit;
$category = $_POST['inscat'];
$sku = $_POST['inssku'];
$name = $_POST['insname'];
$metrode = $_POST['insmetrode'];
$min = $_POST['insmin'];
$max = 0;
$qty = $_POST['insqty'];
$cost = $_POST['inscost'];
$mult = $_POST['insmult'];
try {
        $file_db = new PDO('sqlite:db/teas.sqlite3');
    	$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	$insert = "INSERT INTO teas (sku, metrode, category, name, cost, qty, min, max, multiplier)";
    	$insert .= "VALUES (:sku, :metrode, :category, :name, :cost, :qty, :min, :max, :multiplier)";
        $stmt = $file_db->prepare($insert);
    	$stmt->bindParam(':category', $category);
    	$stmt->bindParam(':sku', $sku);
    	$stmt->bindParam(':metrode', $metrode);
    	$stmt->bindParam(':name', $name);
    	$stmt->bindParam(':min', $min);
    	$stmt->bindParam(':qty', $qty);
    	$stmt->bindParam(':cost', $cost);
    	$stmt->bindParam(':max', $max);
    	$stmt->bindParam(':multiplier', $mult);
	$stmt->execute();
	header('Location: ./');
} catch(PDOException $e) {
    	print $e->getMessage();
}
