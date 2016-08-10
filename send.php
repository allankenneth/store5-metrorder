<?php 
error_reporting(-1);
ini_set('display_errors', 'On');
$date = date("Y-m-d-g:i");
$status = 'active';
$total = $_POST['total'];
$notestometro = $_POST['notestometro'];
$internalnotes = $_POST['internalnotes'];
$file_db = new PDO('sqlite:db/teas.sqlite3');
$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$file_db->exec("CREATE TABLE IF NOT EXISTS orders (id INTEGER PRIMARY KEY AUTOINCREMENT, created INTEGER, status TEXT, total INTEGER, notestometro TEXT, internalnotes TEXT)");
$insert = "INSERT INTO orders (created, status, total, notestometro, internalnotes)";
$insert .= "VALUES (:created, :status, :total, :notestometro, :internalnotes)";
$stmt = $file_db->prepare($insert);
$stmt->bindParam(':created', $date);
$stmt->bindParam(':status', $status);
$stmt->bindParam(':total', $total);
$stmt->bindParam(':notestometro', $notestometro);
$stmt->bindParam(':internalnotes', $internalnotes);
$stmt->execute();
//$lastid = $stmt->fetch(PDO::FETCH_ASSOC);
//print_r($lastid);	
//print "<h1>Order ID:" . $lastid[0] . "</h1>";
$lastId = $file_db->lastInsertId();
// Moving right along...
$file_db->exec("CREATE TABLE IF NOT EXISTS order_items (id INTEGER PRIMARY KEY AUTOINCREMENT, orderid INTEGER, sku INTEGER, metrode TEXT, name TEXT, qty INTEGER, cost INTEGER)");
$ins = "INSERT INTO order_items (orderid, sku, metrode, name, qty, cost)";
$ins .= "VALUES (:orderid, :sku, :metrode, :name, :qty, :cost)";
$st = $file_db->prepare($ins);
$st->bindParam(':orderid', $last);
$st->bindParam(':sku', $sku);
$st->bindParam(':metrode', $metrode);
$st->bindParam(':name', $name);
$st->bindParam(':qty', $qty);
$st->bindParam(':cost', $cost);
foreach($_POST['items'] as $item) {
	$last = $lastId;
	$bit = explode(';;',$item);
	$sku = $bit[0];
	$qty = $bit[1];
	$dq = "SELECT * FROM teas WHERE sku=" . $sku;
	$deets = $file_db->query($dq);
	$deet = $deets->fetchAll(PDO::FETCH_ASSOC);
	$metrode = $deet[0]['metrode'];
	$name = $deet[0]['name'];
	$cost = $deet[0]['cost'];
	$st->execute();
}
$ordergo = 'Location: ./order.php?oid='.$lastId;
metroSend($file_db, $lastId);
header($ordergo);
// Close file db connection
$file_db = null;


// The function to send the order to Metropolitan
function metroSend($file_db, $oid) {
	$to = "hi@allankenneth.com";
	$subject = "New Tea Order #" . $oid;
	$headers   = array();
	$headers[] = "MIME-Version: 1.0";
	$headers[] = "Content-type: text/html; charset=iso-8859-1";
	$headers[] = "From: Empress Fairmont Store <emp.fairmontstore@fairmont.com>";
	$headers[] = "Cc: Allan Haggett <allan.haggett@fairmont.com>";
	$headers[] = "Reply-To: Elizabeth Stevenson <elizabeth.stevenson@fairmont.com>";
	$headers[] = "X-Mailer: PHP/".phpversion();

	$oq = "SELECT * FROM orders WHERE id=" . $oid;
	$info = $file_db->query($oq);
	$order = $info->fetchAll(PDO::FETCH_ASSOC);
	$message = "<div style='font-family:Times, serif;font-size: 18px;margin: 0 auto; width: 600px'>";
	$message .= "<img style='float:right' src='http://store5.ca/assets/img/fairmont-stores-logo.jpg' width='200'>";
	$message .= "<p style='clear:right;float:right;margin:5px 15px 30px 60px;'>At The Fairmont Empress</p>";
	$message .= "<h1>Order #" . $oid . "</h1>\r\n";
	$message .= "<div><strong>Notes:</strong><br>";
	$message .= $order[0]['notestometro'] . "</div>\r\n";
	$message .= "<h2>Order Items</h2>\r\n";
	$message .= "<table cellpadding='5' style='border-bottom:1px solid #333;font-size: 18px'>\r\n";
	$iq = "SELECT * FROM order_items WHERE orderid=" . $oid;
	$items = $file_db->query($iq);
	foreach($items as $item) {
		$message .= "<tr style='border-bottom: 1px solid #333'>\r\n";
		$message .= "<td style='text-align:right'>" . $item['qty'] . "</td>\r\n";
		$message .= "<td>x</td>\r\n";
		$message .= "<td>" . $item['metrode'] . "</td>\r\n";
		$message .= "<td>" . $item['sku'] . "</td>\r\n";
		$message .= "<td>" . $item['category'] . "</td>\r\n";
		$message .= "<td>" . ucwords(strtolower($item['name'])) . "</td>\r\n";
		$message .= "</tr>\r\n";
	}
	
	$message .= "</table>\r\n";
	$message .= "<p>Elizabeth Stevenson</p>";
	$message .= "<p>The Fairmont Store<br>C/oThe Fairmont Empress Hotel<br>721 Douglas St.<br>Victoria, BC<br>V8W1W5<br>250-385-7730<br>";
	$message .= "</div>\r\n";
	mail($to, $subject, $message, implode("\r\n", $headers));
}
