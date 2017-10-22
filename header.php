<?php
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
			for ($i=0; $i<$num_rows; $i++)
			{
				$row = mysqli_fetch_array($r, MYSQLI_ASSOC);

				$items[$i] = $row;
?>
				availableItems[<?php echo $i; ?>] = "<?php echo $row['description']; ?>";
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

<header class="wrapper">
	<h1><?php echo $page_title; ?></h1>
	<a href="index.php"><button>Home</button></a>
	<a href="add-to-db.php"><button>Add/Manage Items</button></a>
	<a href="view-by-freq.php"><button>View By Freq</button></a>
	<a href="view-by-dept.php"><button>View By Dept</button></a>
	<a href="manage-dept.php"><button>Manage Dept</button></a>
	<hr/>
	<div class="ui-widget">
		<label for="quick-add">Quick Add: </label>
		<form method="POST">
			<input id="quick-add" name="item-description" />
			<input type="submit" name="quick-add" value="Add" />
		</form>
	</div>
	<hr/>
</header>