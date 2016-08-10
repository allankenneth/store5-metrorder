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
$o = 'SELECT * FROM orders WHERE id='.$_GET['oid'];
$orderinfo = $file_db->query($o);
$info = $orderinfo->fetchAll(PDO::FETCH_ASSOC);
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
.orderlist {	
	border-top: 1px solid #333;
	margin: 30px 0 0 0;
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
	<div class="col-md-12">
		<a class="btn btn-xs btn-default" href="./">All Orders</a>
	</div>
	<div class="col-md-4">
		<h1>Tea Order #<?php echo $_GET['oid'] ?>
		<div class="btn-group">
<?php 
        if($info[0]['status'] == "active") $label = "btn-warning";
        if($info[0]['status'] == "received") $label = "btn-success";
        if($info[0]['status'] == "issues") $label = "btn-danger";
?>
  <button type="button" class="btn btn-sm <?php echo $label ?> dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
    <?php echo $info[0]['status'] ?> <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu">
    <li><a href="upstat.php?oid=<?php echo strip_tags($_GET['oid']) ?>&upstat=active">active</a></li>
    <li><a href="upstat.php?oid=<?php echo strip_tags($_GET['oid']) ?>&upstat=received">received</a></li>
    <li><a href="upstat.php?oid=<?php echo strip_tags($_GET['oid']) ?>&upstat=issues">issues</a></li>
    <li><a href="remove-order.php?oid=<?php echo strip_tags($_GET['oid']) ?>">DELETE ORDER</a></li>


  </ul>
</div>
		</h1>
		<p><strong>Total: $<?php print number_format($info[0]['total'],2) ?></strong></p>
		<p><strong>Created on:</strong> <?php echo $info[0]['created'] ?></p>
	</div>
	<div class="col-md-4">
<!--			<strong>Internal Notes:</strong><br>-->
			 <?php //echo $info[0]['internalnotes'] ?>
<?php
$noteq = 'SELECT * FROM notes WHERE orderid=' . $_GET['oid'];
$noteresult = $file_db->query($noteq);
$notes = $noteresult->fetchAll(PDO::FETCH_ASSOC);
foreach($notes as $note) {
	print "<div>";
	print "<a href=\"note-remove.php?nid=".$note['id']."&oid=".$note['orderid']."\" class=\"btn btn-default btn-xs\">x</a> ";
	print "<strong>" . date('M jS', $note['date']) . "</strong><br>" . $note['noted'] . "</div>";
}
?><hr>
			<form method="post" action="note-post.php">
			<input type="hidden" name="oid" id="oid" value="<?php print $_GET['oid'] ?>">
			<div class="form-group">
				<textarea name="note" id="note" rows="4" cols="40"></textarea>
			</div>
			<button class="btn btn-default btn-xs">Add New Note</button>
			</form>
		</div>
		<div class="col-md-4">
			<strong>Note sent to Metro:</strong><br>
			 <?php echo $info[0]['notestometro'] ?>
		</div>
	</div>
	<div class="col-md-8 col-md-offset-2">
<table class="table orderlist">
<tr>
        <th>SKU</th>
	<th>Name</th>
        <th>Quantity</th>
</tr>
<?php

$q = 'SELECT * FROM order_items WHERE orderid='.$_GET['oid'];
$result = $file_db->query($q);


foreach($result as $order): 
	$linecost = number_format($order['qty'] * $order['cost'],2);
	$deet = "SELECT * FROM teas WHERE sku=" . $order['sku'];
	$deets = $file_db->query($deet);
	$detail = $deets->fetchAll(PDO::FETCH_ASSOC);
?>
<tr>
	<td><?php echo $order['sku'] ?></td>
	<td><?php echo ucwords(strtolower($detail[0]['name'])) ?></td>
	<td><?php echo $order['qty'] ?></td>
</tr>
<?php endforeach; 
/**************************************
* Close db connections                *
**************************************/
// Close file db connection
$file_db = null;

?>
</table>

</div>
	</div>
</div>
<hr>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="/assets/js/bootstrap.min.js"></script>
<script>
$(function() {

});
</script>
</body>
</html>
