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
#ordercontrols {
	position: fixed;
}
</style>
</head>
<body id="body">
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
  <div class="container">
	<div class="navbar-header">
		<span class="navbar-brand">Store 5 - Tea Order</span>
	</div>
	<ul class="nav navbar-nav navbar-left">
		<li><a href="/">Home</a></li>
		<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">Services<b class="caret"></b></a>
		<ul class="dropdown-menu">
			<li>
			<a href="/shipping">Shipping</a>
			</li>
			<li>	
			<a href="/writeoff" class="">Write Offs</a>
			</li>
			<li>
			<a href="/imperfect">Imperfect</a>
			</li>
		</ul>
		</li>
	</ul>

	<div class="buttons pull-right">

	</div>
	<div class="buttons">
	</div>
  </div>
</div>
<div class="container">
	<form method="post" action="send.php">
	<div class="row">
		<div class="col-md-9">
	<p style="margin: 20px 0 0 0" class="pull-right"><a href="new.php" class="btn btn-default">Back</a></p>
	<h1>Tea Order Review</h1>
<table class="table">
<tr>
	<th>Code</th>
	<th>Name</th>
	<th>Quantity</th>
	<th>Unit Cost</th>
	<th>Line Cost</th>
</tr>
<?php
foreach($_POST as $tea => $qty) :
        if($qty > 0) :
                $sku = explode("-", $tea);
                $q = 'SELECT metrode, name, category, cost FROM teas where sku=' . $sku[1];
                $result = $file_db->query($q);
                $t = $result->fetchAll(PDO::FETCH_ASSOC);
                //print_r($t);
		$linecost = 0;
		$linecost = $qty * $t[0]['cost'];
		$totalcost = $totalcost + ($qty * $t[0]['cost']);
		$formline = $sku[1] . ';;' . $qty . ';;' . $t[0]['cost'];
		?>
                <tr>
		<td><?php echo $sku[1] ?> <small><?php echo  $t[0]['metrode'] ?></small></td>
		<td><?php echo ucwords(strtolower($t[0]['name'])) ?></td>
		<td><?php echo  $qty ?></td>
		<td>$<?php echo  number_format($t[0]['cost'],2) ?>/unit</td>
		<td>$<?php echo number_format($linecost,2)  ?>
		<input type="hidden" 
			name="items[]" 
			id="item<?php echo $sku[1] ?>" 
			value="<?php echo $sku[1] ?>;;<?php echo $qty ?>">
		</td>
		</tr>
<?php
        endif; // qty > 0
endforeach;
/**************************************
* Close db connections                *
**************************************/
// Close file db connection
$file_db = null;

?>
<tr>
	<td colspan="4"></td>
	<td>
	</td>
</tr>
</table>
	
	</div>
	
	<div class="col-md-3">
		<div id="ordercontrols">
		<h2>$<?php echo $totalcost ?></h2>
		<div class="form-group">
		<label>Notes to Metro:
		<textarea name="notestometro" id="notestometro" cols="30" rows="5" class="form-control">Thanks Kathryn!</textarea>
		</label>
		</div>
		<br>		
		<div class="form-group">
		<label>Internal Notes:
		<textarea name="internalnotes" id="internalnotes" cols="30" rows="3" class="form-control"></textarea>
		</label>
		</div>
		<input type="hidden" id="total" name="total" value="<?php echo $totalcost ?>">
		<input type="submit" 
			class="btn btn-success btn-lg" 
			value="Send to Metro" 
			name="send" 
			id="send">
		</div>
		
	</div>

	</div>
</form>
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
