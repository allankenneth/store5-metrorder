<?php
$filename = $_GET["file"];
$file_db = new PDO('sqlite:db/teas.sqlite3');
$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$file_db->exec("CREATE TABLE IF NOT EXISTS teas (
					id INTEGER PRIMARY KEY AUTOINCREMENT, 
					sku INTEGER, 
					metrode TEXT,
					category TEXT,
					name TEXT, 
					cost INTEGER,
					qty INTEGER,
					min INTEGER,
					max INTEGER,
					multiplier INTEGER)");
// Just in case we need to add a new tea to the database
// we're going to setup our insert statement here;
// if we do need to add it, then all we need to do is execute
// with the appropriate variables instead of contructing this
// from scratch on every loop through the CSV. I _think_ that
// this makes sense, but it could be horribly ineffecient...
$insert = "INSERT INTO teas (sku, metrode, category, name, cost, qty, min, max, multiplier)";
$insert .= "VALUES (:sku, :metrode, :category, :name, :cost, :qty, :min, :max, :multiplier)";
$stmt = $file_db->prepare($insert);
$stmt->bindParam(':name', $teaname);
$stmt->bindParam(':category', $teacat);
$stmt->bindParam(':cost', $teacost);
$stmt->bindParam(':qty', $teaqty);
$stmt->bindParam(':sku', $teasku);
$stmt->bindParam(':metrode', $teametrode);
$stmt->bindParam(':min', $teamin);
$stmt->bindParam(':max', $teamax);
$stmt->bindParam(':multiplier', $teamultiplier);
// Loop thru the CSV, first checking to see if the SKU is 
// already present or not. If it IS, then we just update that 
// SKU with the new quantity and move on. If it's NOT, then
// we just execute the statement that we've prepared above.
$teas = getTeas($filename);
foreach ($teas as $m) {
	// Setup our variables
	$teasku = $m['sku'];
	// Hardcoded value for the Metro item code
	// Meant to be updated manually from the UI
	$teametrode = "CEMP-50C-540";
	$teaqty = $m['qty'];
	$teacost = $m['cost'];
	// Below here are only used on a new insert
	// Hardcoded value for category; meant to be 
	// manually updated from the UI
	$teacat = "Uncategorized";
	$teaname = $m['name'];
	$teamin = 0;
	$teamax = 0;
	$teamultiplier = 12;
	$insertcount = Array();
	// IF SKU EXISTS
/*
[Tue Aug 09 09:50:08.546226 2016] [:error] [pid 22675] [client 206.108.25.19:60177] PHP Warning:  Division by zero in /home/s5/html/metrorder/process.php on line 107, referer: https://store5.ca/metrorder/
[Tue Aug 09 09:50:08.546526 2016] [:error] [pid 22675] [client 206.108.25.19:60177] PHP Fatal error:  Uncaught PDOException: SQLSTATE[HY000]: General error: 1 no such column: _ in /home/s5/html/metrorder/process.php:58\nStack trace:\n#0 /home/s5/html/metrorder/process.php(58): PDO->query('SELECT sku FROM...')\n#1 {main}\n  thrown in /home/s5/html/metrorder/process.php on line 58, referer: https://store5.ca/metrorder/

*/
	$q = 'SELECT sku FROM teas WHERE sku=' . $teasku;
	$r = $file_db->query($q);
	$f = $r->fetchAll();
	if(count($f) > 0) {
		print $teasku . " - " . $teacost . " - Updated<br>";
		$sql = "UPDATE teas SET qty=?, cost=? WHERE sku=?";
		$up = $file_db->prepare($sql);
		$up->execute(array($teaqty, $teacost, $teasku));
	// Then SKU doesn't exist, so we execute our prepared 
	// insert statement now
	} else {
		//print $teasku . " - " . $teacost . " - Inserted<br>";
		array_push($insertcount, $teasku);
		$stmt->execute();
	}
}
$file_db = null;
// This is supposed to show the new teas that were inserted so we have a 
// reference to go by, but this apparently doesn't work ... ? TODO fix this.
if(count($insertcount) > 0) {
	print_r($insertcount);
} else {
	header("Location: /metrorder/new.php");
}

//
//
// This is the method to look at an Iventory Stock Level report
// 5092 1200 STK, CUST METRO
// from Merchant, exported to CSV
// This maps the SKU, the name and the quantity and returns an array
//
function getTeas($filename) {

        $open = "files/" . $filename;
        $f = fopen($open, "r");
        $teas = Array();
        $linecount = 0;
        $tstatdate = 0;
        while (($line = fgetcsv($f)) !== false) {
                $count = 0;
                foreach ($line as $cell) {
                                // FRAGILE HARDCODED MANUAL PARSE LOL
				$itembit = htmlspecialchars($cell);
                                if($count == 75) $name = $itembit;
                                if($count == 78) $sku = $itembit;
                                if($count == 88) $totalcost = $itembit;
				$totalcost = intval(str_replace(',','',$totalcost));
                                if($count == 84) $qty = $itembit;
				$cost = number_format($totalcost / $qty,2);
                                $count++;
                }
		if(!checkBlack($sku)) {
			array_push($teas,['sku' => $sku, 'name' => $name, 'cost' => $cost, 'qty' => $qty]);
		}
        }
        $linecount++;
        fclose($f);
        return $teas;
}
// We need to blacklist certain teas that the report produces
// TODO create a table for this and query against it and thus
// create a UI to manage them. This hardcoded array is just a 
// time saver for now :)
function checkBlack($sku) {
	$blacklist = Array('89447',
				'95817',
				'95815',
				'85822',
				'95816',
				'42459',
				'42440',
				'41663');
	foreach($blacklist as $listed) {
		if($sku == $listed) return true;
	}
	return false;
}
