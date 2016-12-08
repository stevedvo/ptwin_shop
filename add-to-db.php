<?php
	date_default_timezone_set('UTC');
	// opens DB connexion
	require ('../../init_ptwin_shop.php');
	require ('functions.php');
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

		if ($_POST)
		{
			$item_description = $_POST['item-description'];
			$item_default_qty = $_POST['item-default-qty'];
			$item_list = $_POST['item-list'];
			if ($_POST['item-comments'])
			{
				$item_comments = $_POST['item-comments'];				
			}

			$q = "INSERT INTO 'items' ";
			$q.= "(			'description', 			'comments', 		'default_qty', 			'list_id') ";
			$q.= "VALUES (	'$item_description', 	'$item_comments', 	'$item_default_qty', 	'$list_id')";

			var_dump($q);
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
						<option value="usuals">Usuals</option>
						<option value="regulars">Regulars</option>
						<option value="extras">Extras</option>
					</select>
					<input type="submit" value="Add Item" />
				</fieldset>
			</form>
<?php

?>
		</main>
	</body>

</html>