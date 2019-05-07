<?php
	global $ptwin_shopDB;
	session_start();
	if (isset($_POST['quick-add']))
	{
		$item_description = $_POST['item-description'];

		$q = "SELECT item_id ";
		$q.= "FROM items ";
		$q.= "WHERE description='$item_description'";

		$r = mysqli_query($ptwin_shopDB, $q);

		if ($r && $r->num_rows > 0)
		{
			$q = "UPDATE items ";
			$q.= "SET selected='1' ";
			$q.= "WHERE description='$item_description'";

			$r = mysqli_query($ptwin_shopDB, $q);			
		}
		else
		{
			$_SESSION['item-description'] = $item_description;
			header("Location: add-to-db.php", http_response_code(303));
			exit;
		}
	}

	$q = "SELECT * ";
	$q.= "FROM items ";

	$r = mysqli_query($ptwin_shopDB, $q);

	$items = [];

	if ($r && $r->num_rows > 0)
	{
		$num_rows = $r->num_rows;
?>
		<script type="text/javascript">
			$(function()
			{
				var availableItems = [];
<?php
				for ($i = 0; $i < $num_rows; $i++)
				{
					$row = mysqli_fetch_array($r, MYSQLI_ASSOC);

					$items[$i] = $row;
?>
					availableItems[<?= $i; ?>] = "<?= $row['description']; ?>";
<?php
				}
?>
				$("#quick-add").autocomplete(
				{
					source: availableItems
				});
			});
		</script>
<?php
	}
?>

<header class="wrapper page-header">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<h1><?= $page_title; ?></h1>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<a class="btn btn-primary" href="/">Home</a>
				<a class="btn btn-primary" href="add-to-db.php">Add/Manage Items</a>
				<a class="btn btn-primary" href="view-by-freq.php">View By Freq</a>
				<a class="btn btn-primary" href="/lists/">Manage Lists</a>
				<a class="btn btn-primary" href="view-by-dept.php">View By Dept</a>
				<a class="btn btn-primary" href="/departments/">Manage Depts</a>
			</div>
			<hr/>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<div class="ui-widget">
					<label for="quick-add">Quick Add: </label>
					<form method="POST">
						<input id="quick-add" name="item-description" />
						<input type="submit" name="quick-add" value="Add" />
					</form>
				</div>
			</div>
		</div>
	</div>
</header>
