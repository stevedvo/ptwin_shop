<?php
?>

<!DOCTYPE html>

<html lang="en">

	<head>
<?php
		$page_title = "Add Items To DB";
		include ('head-section.php');
?>
	</head>

	<body>
<?php
		include ('header.php');

		// populate lists options
		$options = [];

		$q = "SELECT * ";
		$q.= "FROM lists";

		$r = mysqli_query($ptwin_shopDB, $q);

		if ($r->num_rows > 0)
		{
			$num_rows = $r->num_rows;

			for ($i=0; $i<$num_rows; $i++)
			{
				$options[$i] = mysqli_fetch_array($r, MYSQLI_ASSOC);
			}
		}

		if ($_POST)
		{
			$item_description = $_POST['item-description'];
			$item_default_qty = $_POST['item-default-qty'];
			$item_list = $_POST['item-list'];
			if ($_POST['item-comments'])
			{
				$item_comments = $_POST['item-comments'];
			}
			else
			{
				$item_comments = "";
			}

			$q = "INSERT INTO items ";
			$q.= "(description, comments, default_qty, list_id) ";
			$q.= "VALUES ('$item_description', '$item_comments', '$item_default_qty', '$item_list')";

			var_dump($q);
			
			$r = mysqli_query($ptwin_shopDB, $q);

			var_dump($r);
			die();

		}

?>
		<main class="wrapper">

			<form method="POST" action="add-to-db.php">
				<fieldset>
					<legend>Add Item</legend>
					<p>Description:</p>
					<input name="item-description" type="text" placeholder="Required" required/>
					<p>Comments:</p>
					<input name="item-comments" type="text" placeholder="Optional" />
					<p>Default Order Qty:</p>
					<input name="item-default-qty" type="integer" value="1" required/><br/>
					<select name="item-list">
<?php
						if (!empty($options))
						{
							for ($i=0; $i<sizeof($options); $i++)
							{
?>
								<option value="<?php echo $options[$i]['list_id']; ?>"><?php echo ucfirst($options[$i]['name']); ?></option>
<?php
							}
						}
?>
					</select>
					<input type="submit" value="Add Item" />
				</fieldset>
			</form>
<?php

?>
		</main>
	</body>

</html>