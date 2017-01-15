<?php
?>

<!DOCTYPE html>

<html lang="en">

	<head>
<?php
		$page_title = "Add/Manage Items";
		include ('head-section.php');
?>
	</head>

	<body>
<?php
		include ('header.php');

		// populate lists options
		$lists = [];

		$q = "SELECT * ";
		$q.= "FROM lists";

		$r = mysqli_query($ptwin_shopDB, $q);

		if ($r->num_rows > 0)
		{
			$num_rows = $r->num_rows;

			for ($i=0; $i<$num_rows; $i++)
			{
				$lists[$i] = mysqli_fetch_array($r, MYSQLI_ASSOC);
			}
		}

		// populate department options
		$depts = [];

		$q = "SELECT * ";
		$q.= "FROM departments";

		$r = mysqli_query($ptwin_shopDB, $q);

		if ($r->num_rows > 0)
		{
			$num_rows = $r->num_rows;

			for ($i=0; $i<$num_rows; $i++)
			{
				$depts[$i] = mysqli_fetch_array($r, MYSQLI_ASSOC);
			}
		}

		if (isset($_POST['add-to-db']))
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
			$selected = 0;
			if (isset($_POST['selected']))
			{
				$selected = 1;
			}

			$q = "INSERT INTO items ";
			$q.= "(description, comments, default_qty, selected, list_id) ";
			$q.= "VALUES ('$item_description', '$item_comments', '$item_default_qty', '$selected', '$item_list')";

			$r = mysqli_query($ptwin_shopDB, $q);

			$item_id = $ptwin_shopDB->insert_id;

			if (isset($_POST['item-dept']))
			{
				$dept_id = $_POST['item-dept'];

				$q = "INSERT INTO item_dept_link ";
				$q.= "(dept_id, item_id) ";
				$q.= "VALUES ('$dept_id', '$item_id')";

				$r = mysqli_query($ptwin_shopDB, $q);
			}
		}

?>
		<main class="wrapper">

			<form method="POST">
				<fieldset>
					<legend>Add Item</legend>
					<p>Description:</p>
					<input name="item-description" type="text" placeholder="Required" required <?php if (isset($_SESSION['item-description'])) echo "value='".$_SESSION['item-description']."'"; ?> />
					<p>Comments:</p>
					<input name="item-comments" type="text" placeholder="Optional" />
					<p>Default Order Qty:</p>
					<input name="item-default-qty" type="number" min="1" value="1" required/><br/><br/>
					<select name="item-list">
<?php
						if (!empty($lists))
						{
							for ($i=0; $i<sizeof($lists); $i++)
							{
?>
								<option value="<?php echo $lists[$i]['list_id']; ?>"><?php echo ucfirst($lists[$i]['name']); ?></option>
<?php
							}
						}
?>
					</select>
					<select name="item-dept">
						<option selected disabled>Choose Dept</option>
<?php
						if (!empty($depts))
						{
							for ($i=0; $i<sizeof($depts); $i++)
							{
?>
								<option value="<?php echo $depts[$i]['dept_id']; ?>"><?php echo ucfirst($depts[$i]['dept_name']); ?></option>
<?php
							}
						}
?>
					</select><br/><br/>
					Add to Shopping List: <input type="checkbox" name="selected" <?php if (isset($_SESSION['item-description'])) echo "checked"; ?> /><br/><br/>
					<input type="submit" name="add-to-db" value="Add Item" />
				</fieldset>
			</form>
<?php
			unset($_SESSION['item-description']);
?>
		</main>
	</body>

</html>