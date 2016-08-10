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
<html lang="en">
<head>
	<title>Store 5 - Tea Order</title>
	<link rel="apple-touch-icon" sizes="120x120" href="../rabbit.png">
	<link rel="icon" type="image/x-icon" href="/favicon.ico" >
	<meta name="viewport" content="width=device-width, user-scalable=no">
	<!-- freeakin gargabge: --> 
	<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<link rel="stylesheet" href="../assets/css/bootstrap.css">
	<link rel="stylesheet" href="../assets/css/style.css">
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
	<script src="../assets/js/respond.js"></script>
<style>
.min,.qty {
	text-align: center;
}
.totalqty {
	border: 0;
	border-radius: 3px;
	float: right;
	text-align: center;
	width: 35px;
}
.onorder {
	background: #333;
	color: #FFF;
}
.adjust {
	border: 1px solid #333;
	border-radius: 50%;
	color: #333;
	display: inline-block;
	height: 17px;
	line-height: 12px;
	text-align: center;
	width: 17px;
}
.adjust:hover {
	text-decoration: none;
}
.send {
}
#runningtotal {
	margin: 20px;
	position: fixed;
	text-align: center;
}
.runt {
	font-size: 24px;
}
thead {
	background: #FFF;
}
</style>
</head>
<body id="body">
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
  <div class="container">
	<div class="navbar-header">
		<span class="navbar-brand">Store 5 - Tea Order</span>
	</div>

	<div class="buttons pull-right">
		<a href="#" class="btn btn-default" data-toggle="modal" data-target="#teaAdd">Add New Tea</a>
	</div>
	<div class="buttons">
	</div>
  </div>
</div>
<div class="top container">
<form action="review.php" method="post">
	<div class="row">
       <div class="col-md-12">
                <a class="btn btn-xs btn-default" href="./">Back</a>
        </div>
		<div class="col-md-10">
		<div class="btn-group pull-right send">	

		</div>
		<h1>New Metropolitan Tea Order</h1>
<div id="teas">
			<div class="table-responsive">
<table class="table teatable">
	<thead>
	<tr>
		<th width="120"></th>
		<th class="sort" data-sort="tea-cat" width="150">Category</th>
		<th>SKU</th>
<!--		<th width="180">Metrode</th>-->
		<th width="320">Name</th>
		<th class="min">Min</th>
		<th class="sort" data-sort="tea-qty">Quantity</th>
<!--		<th>Cost</th>-->
		<th width="180">Order</th>
	</tr>
</thead>
<tbody class="list">
	<?php


// Select all data from shipments table 
$result = $file_db->query('SELECT * FROM teas ORDER BY category ASC');
$counter = 0;
$status = '';
$count = 1;	
$totalcost = 0;
foreach($result as $row) :
	$counter++; 
	if($row['min'] > 0) {
		$diff = $row['min'] - $row['qty'];
		$boxes = ceil($diff / $row['multiplier']);
		if($boxes > 0) {
			$orderamt = $boxes * $row['multiplier'];
			$totalcost = $totalcost + ($row['cost'] * $orderamt);
		} else {
			$boxes = 0;
			$orderamt = 0;
		}

		$status = 'bg-primary';

	} else {
		$orderamt = 0;
		$boxes = 0;
		$status = '';
	}
	
	?>
		<tr id="t-<?php echo $counter ?>" class="<?php echo $status ?>">
		<td>
		<div class="btn-group">
		<a class="btn btn-default btn-sm deltea" href="remove.php?tid=<?php echo $row['id'] ?>">x</a>
		<a href="#" class="btn btn-default btn-sm" 
			data-toggle="modal" 
			data-target="#teaEdit" 
			data-id="<?php echo $row['id'] ?>"
			data-cat="<?php echo $row['category'] ?>"
			data-sku="<?php echo $row['sku'] ?>"
			data-metrode="<?php echo $row['metrode'] ?>"
			data-name="<?php echo $row['name'] ?>"
			data-min="<?php echo $row['min'] ?>"
			data-qty="<?php echo $row['qty'] ?>"
			data-cost="<?php echo $row['cost'] ?>"
			data-mult="<?php echo $row['multiplier'] ?>">Edit</a>
		<?php //echo $count ?>
		</div>
		</td>
		<td class="tea-cat"><?php echo $row['category'] ?></td>
		<td><?php echo $row['sku'] ?></td>
<!--		<td><?php echo $row['metrode'] ?></td>-->
		<td><?php echo ucwords(strtolower($row['name'])) ?></td>
		<td class="min"><?php echo $row['min'] ?></td>
		<td class="tea-qty"><?php echo $row['qty'] ?> </td>
<!--		<td class="cost">$<?php echo number_format($row['cost'],2) ?></td>-->
		<td>
			<a href="#" class="adjust" data-action="minus" data-currqty="<?php echo $boxes; ?>" data-multiplier="<?php echo $row['multiplier'] ?>">-</a>
			<span><?php echo $boxes ?></span>
			<a href="#" class="adjust" data-action="plus" data-currqty="<?php echo $boxes; ?>" data-multiplier="<?php echo $row['multiplier'] ?>">+</a>
			<small><?php echo ' x ' . $row['multiplier'] . ' = '; ?></small>
			<input readonly id="tea-<?php echo $row['sku'] ?>" name="tea-<?php echo $row['sku'] ?>" class="totalqty <?php if($orderamt>0) echo "onorder" ?>" value="<?php echo $orderamt ?>" data-percost="<?php echo $row['cost'] ?>" size="3">
			
		</td>

		</tr>
	<?php 
	$count++;
	endforeach;
	/**************************************
	* Close db connections                *
	**************************************/
	// Close file db connection
	$file_db = null;
	?>
</tbody>
</table>
</div>
			</div>
		</div>
<div class="col-md-2">
<div id="runningtotal">
<input type="submit" class="btn btn-lg btn-success" value="Review & Send">
</div>
</div>
		<!-- room for 2 columns here-->


	</div>
</form>
</div>
<div class="modal fade" id="teaEdit" tabindex="-1" role="dialog" aria-labelledby="teaEditLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Edit Tea</h4>
      </div>
      <div class="modal-body">
        <form action="update.php" method="post">
	<input type="hidden" id="tid" name="tid" value="">
          <div class="form-group">
            <label for="upcat" class="control-label">Category:</label>
		<select id="upcat" name="upcat">
			<option>Black Box</option>
			<option>Wellness</option>
			<option>100g</option>
			<option>500g</option>
			<option>Empress Blend</option>
			<option>Premium</option>
		</select>
          </div>
           <div class="form-group">
            <label for="upsku" class="control-label">SKU:</label>
            <input type="text" class="form-control" id="upsku" name="upsku">
          </div>
          <div class="form-group">
            <label for="upmetrode" class="control-label">Metro Code:</label>
            <input type="text" class="form-control" id="upmetrode" name="upmetrode">
          </div>
	  <div class="form-group">
            <label for="upname" class="control-label">Name:</label>
            <input type="text" class="form-control" id="upname" name="upname">
          </div>
          <div class="form-group">
            <label for="upqty" class="control-label">Quantity:</label>
            <input type="text" class="form-control" id="upqty" name="upqty">
          </div>
	  <div class="form-group">
            <label for="upcost" class="control-label">Cost:</label>
            <input type="text" class="form-control" id="upcost" name="upcost">
          </div>

          <div class="form-group">
            <label for="upmin" class="control-label">Minimum:</label>
            <input type="text" class="form-control" id="upmin" name="upmin">
          </div>
          <div class="form-group">
            <label for="upmult" class="control-label">Multiplier:</label>
            <input type="text" class="form-control" id="upmult" name="upmult">
          </div>
        <input type="submit" class="btn btn-primary" value="Update Tea">
        </form>
      </div>
      <div class="modal-footer">
	<!--<a href="remove.php" class="btn btn-warning">Delete</a>-->
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="teaAdd" tabindex="-1" role="dialog" aria-labelledby="teaAddLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Add a New Tea</h4>
	<p><em>Note: Manually added teas will not receive a stock update from Merchant, and will need to managed manually</em><p>
      </div>
      <div class="modal-body">
        <form action="post.php" method="post">
	<input type="hidden" id="tid" name="tid" value="">
          <div class="form-group">
            <label for="inscat" class="control-label">Category:</label>
		<select id="inscat" name="inscat">
			<option>Black Box</option>
			<option>Wellness</option>
			<option>100g</option>
			<option>500g</option>
			<option>Empress Blend</option>
			<option>Premium</option>
		</select>
          </div>
           <div class="form-group">
            <label for="inssku" class="control-label">SKU:</label>
            <input type="text" class="form-control" id="inssku" name="inssku">
          </div>
          <div class="form-group">
            <label for="insmetrode" class="control-label">Metro Code:</label>
            <input type="text" class="form-control" id="insmetrode" name="insmetrode">
          </div>
	  <div class="form-group">
            <label for="insname" class="control-label">Name:</label>
            <input type="text" class="form-control" id="insname" name="insname">
          </div>
           <div class="form-group">
            <label for="insqty" class="control-label">Quantity:</label>
            <input type="text" class="form-control" id="insqty" name="insqty">
          </div>         
	  <div class="form-group">
            <label for="inscost" class="control-label">Cost:</label>
            <input type="text" class="form-control" id="inscost" name="inscost">
          </div>
          <div class="form-group">
            <label for="insmin" class="control-label">Minimum:</label>
            <input type="text" class="form-control" id="insmin" name="insmin">
          </div>
          <div class="form-group">
            <label for="insmult" class="control-label">Multiplier:</label>
            <input type="text" class="form-control" id="insmult" name="insmult">
          </div>
        <input type="submit" class="btn btn-primary" value="Add Tea">
        </form>
      </div>
      <div class="modal-footer">
	<!--<a href="remove.php" class="btn btn-warning">Delete</a>-->
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="/assets/js/bootstrap.min.js"></script>

<script src="../assets/js/list-1.0.2.js"></script>
<script src="../assets/js/float-thead/dist/jquery.floatThead.min.js"></script>
<script>
// breakout of the Fairmont wifi pesterer (or any frame for that matter. Frames are bullshit)
if (top.location != location) {
	top.location.href = document.location.href ;
}
$(function() {

	var options = {
                page: 400,
                valueNames: [ 'tea-cat','tea-qty']
        };
        var teaList = new List('teas', options);

	var $table = $('table.teatable');
	$table.floatThead({scrollingTop:50});

	$('#teaEdit').on('show.bs.modal', function (event) {
	  var button = $(event.relatedTarget);
	  var sku = button.data('sku');
	  var metrode = button.data('metrode');
	  var id = button.data('id');
	  var cat = button.data('cat');
	  var name = button.data('name');
	  var qty = button.data('qty');
	  var cost = button.data('cost');
	  var min = button.data('min');
	  var mult = button.data('mult');
	  var modal = $(this);
	  modal.find('#tid').val(id);
	  modal.find('#upmetrode').val(metrode);
	  modal.find('#upcat').val(cat);
	  modal.find('#upsku').val(sku);
	  modal.find('#upqty').val(qty);
	  modal.find('#upcost').val(cost);
	  modal.find('#upname').val(name);
	  modal.find('#upmin').val(min);
	  modal.find('#upmult').val(mult);
	});

	// We need to loop though all of our input fields and create a total
	// So we can update the UI when the user +/- a tea qty
	function calcTotal() {
		total = 0;
		$('.totalqty').each(function(){
			teaQty = $(this).attr('value');
			if(teaQty > 0) {
				cost = $(this).attr('data-percost');
				linetotal = cost * parseInt(teaQty);
				total = total + linetotal;
			}
		});
		total = parseFloat(total).toFixed(2);
		$('#ordertotal').html(total);
	}

	$('a.adjust').click(function(e){
                e.preventDefault();
		var ordertotal = $('#ordertotal').text();
		//console.log(ordertotal);
                var that = $(this);
                var action = $(this).attr('data-action');
                var currqty = parseInt($(this).attr('data-currqty'));
                var mult = parseInt($(this).attr('data-multiplier'));
		// Yeah, yeah, this is very poor code. There's a better
		// way to do it, I'm sure. I'm learnin by failing over and over.
		// Insert obligatory "it works" here.
                if(action == "minus") {
                        newq = currqty - 1;
			newtotal = newq * mult;
			that.next('span').html(newq);
			that.next().next().attr("data-currqty", newq);
			that.next().next().next().next('.totalqty').attr('value',newtotal);
			// nextnextnextnext?? For real?
			that.attr('data-currqty',newq);
			if(newq == 0) {
				that.next().next().removeClass('onorder');
			}
			calcTotal();
                } else {
                        newq = currqty + 1;
			newtotal = newq * mult;
			that.prev().html(newq);
			that.prev().prev().attr("data-currqty", newq);
			that.next().next('.totalqty').attr('value',newtotal).addClass('onorder');
			that.attr("data-currqty", newq);
			calcTotal();
                }
        });
	$('.deltea').on('click',function(){
		 if(confirm('Delete this tea from the database? If it is in inventory, it will be imported again with the next order unless you add it to the blacklist.')) {
			return;
		} else {
			return false;
		}
	});


});
</script>
</body>
</html>
