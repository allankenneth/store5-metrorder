<?php

try {
	// Create (connect to) SQLite database in file
	$file_db = new PDO('sqlite:db/teas.sqlite3');
	// Set errormode to exceptions
	$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
} catch(PDOException $e) {
	// Print PDOException message
	echo $e->getMessage();
	exit();
}
?>
<!DOCTYPE html>
<html lang="en" manifest="/cache.manifest">
<head>
	<title>Store 5 - Tea Order</title>
	<link rel="apple-touch-icon" sizes="120x120" href="../rabbit.png">
	<link rel="icon" type="image/x-icon" href="/favicon.ico" >
	<meta name="viewport" content="width=device-width, user-scalable=no">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<link rel="stylesheet" href="../assets/css/bootstrap.css">
	<link rel="stylesheet" href="../assets/css/style.css">
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
<!--	TODO Remove? FRom when I had to support IE8
<script src="../assets/js/respond.js"></script>-->
<style>
.instruct { display: none; }
.orderlink {
	font-weight: bold;
}
</style>
</head>
<body id="body">
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
  <div class="container">
	<div class="navbar-header">
		<a href="/" class="navbar-brand">Store 5</a>
	</div>
	<div class="buttons pull-right">

	</div>
	<div class="buttons">
	</div>
  </div>
</div>
<div class="container">
	<div class="row">

		<h1>Metropolitan Tea Order</h1>
		<div class="col-md-4">
			<h3>Previous Orders</h3>
                        <ul class="list-group">
<?php 
$result = $file_db->query('SELECT * FROM orders ORDER BY id DESC LIMIT 7'); 
$count = 0;
foreach($result as $order):
	if($count == 0) $lastorder = $order['created'];
	$count++;
	if($order['status'] == "active") $label = "label-warning";
	if($order['status'] == "received") $label = "label-success";
	if($order['status'] == "issues") $label = "label-danger";
?>
		<li class="list-group-item">
		<span title="<?php echo $order['internalnotes'] ?>" class="label <?php echo $label ?>"><?php echo $order['status'] ?></span>&nbsp;
		<a class="orderlink" href="order.php?oid=<?php echo $order['id'] ?>"><?php echo $order['created'] ?></a>
		<span class="pull-right">$<?php echo $order['total'] ?></span>
		</li>
<?php endforeach; ?>
                        </ul>
		</div>
		<div class="col-md-4">
			<h3>New Order</h3>
			<hr>
			<form action="upload.php" method="post" enctype="multipart/form-data" class="up"> 
				<div class="form-group">
					<input size="30" type="file" name="myFile"> 
					<hr>
					<button type="submit" class="btn btn-lg btn-success btn-block" value="Upload">
					Upload Inventory Stock Level
					</button>
				</div>
			</form>
			<p>From here, you'll be taken to a list of teas where you can choose quantities of each, review your order and send it to Metropolitan for processing and shipment. </p>
			<p><a class="btn btn-default" href="new.php">Order with old stock levels</a></p>
		</div>
		<div class="col-md-4">
			<h3><a href="#" class="showinstruct">Instructions</a></h3>
			<div class="instruct">
			<p>In Merchant Back Office, run an <strong>Inventory Stock Level</strong> with the following parameters:</p>
			<ul>
			<li>Department: 5092</li>
			<li>Class: 1200</li>
			<li>Category: CUST &amp; STK (in that order)</li>
			<li>Brand: METRO</li>
			<li>Print Detail &amp; Description should both be checked</li>
			</ul>
			<p>When the report comes up, click the export button (very far upper-left). Change the file type to CSV and save it to your desktop.</p>
			<p>Once that's done, come back here, click "Choose File;" locate and "Open" the newly exported "Inventory Stock Level.csv;" Then click the green 'Upload &hellip;' button. </p>
			</div>
		</div>
	</div>
</div>
<hr>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="/assets/js/bootstrap.min.js"></script>
<script>
// breakout of the Fairmont wifi pesterer (or any frame)
if (top.location != location) {
        top.location.href = document.location.href ;
}
$(function() {
$(".showinstruct").click(function(){ $(".instruct").slideToggle();});
});
</script>
</body>
</html>
