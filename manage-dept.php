<?php
?>

<!DOCTYPE html>

<html lang="en">

	<head>
<?php
		$page_title = "Manage Departments";
		include ('head-section.php');
?>
	</head>

	<body>
<?php
		include ('header.php');

		if (isset($_POST['add-dept']))
		{
			$dept_name = $_POST['dept-name'];

			$q = "INSERT INTO departments ";
			$q.= "(dept_name) ";
			$q.= "VALUES ('$dept_name')";

			$r = mysqli_query($ptwin_shopDB, $q);
		}

		if (isset($_POST['update-dept']))
		{
			$dept_id 	= $_POST['dept-id'];
			$dept_name 	= $_POST['dept-name'];

			$q = "UPDATE departments ";
			$q.= "SET dept_name='$dept_name' ";
			$q.= "WHERE dept_id='$dept_id'";

			$r = mysqli_query($ptwin_shopDB, $q);
		}

		$depts = [];

		$q = "SELECT * ";
		$q.= "FROM departments ";
		$q.= "ORDER BY dept_name";

		$r = mysqli_query($ptwin_shopDB, $q);

		if ($r && $r->num_rows > 0)
		{
			$num_rows = $r->num_rows;

			for ($i=0; $i<$num_rows; $i++)
			{
				$depts[$i] = mysqli_fetch_array($r, MYSQLI_ASSOC);
			}
		}

?>
		<main class="wrapper">
<?php
			if (isset($_POST['view-dept']))
			{
				$dept_id 	= $_POST['dept-id'];
				$dept_name 	= $_POST['dept-name'];

				echo "<h3>$dept_name</h3>";

				if ($_POST['view-dept']=="Remove")
				{
					$item_id = $_POST['item-id'];

					$q = "DELETE FROM item_dept_link ";
					$q.= "WHERE item_id='$item_id' AND dept_id='$dept_id'";

					$r = mysqli_query($ptwin_shopDB, $q);
				}

				if ($_POST['view-dept']=="Add to Dept")
				{
					$item_id = $_POST['item-id'];

					$q = "INSERT INTO item_dept_link ";
					$q.= "(dept_id, item_id) ";
					$q.= "VALUES ('$dept_id', '$item_id')";

					$r = mysqli_query($ptwin_shopDB, $q);
				}

				$q = "SELECT * ";
				$q.= "FROM item_dept_link ";
				$q.= "INNER JOIN items ON item_dept_link.item_id=items.item_id ";
				$q.= "WHERE dept_id='$dept_id'";

				$r = mysqli_query($ptwin_shopDB, $q);
				
				if ($r && $r->num_rows > 0)
				{
					$num_rows = $r->num_rows;
?>
					<table>
<?php
					for ($i=0; $i<$num_rows; $i++)
					{
						$row = mysqli_fetch_array($r, MYSQLI_ASSOC);
?>
						<form method="POST">
							<input type="hidden" name="dept-id" value="<?php echo $dept_id; ?>" />
							<input type="hidden" name="dept-name" value="<?php echo $dept_name; ?>" />
							<input type="hidden" name="item-id" value="<?php echo $row['item_id']; ?>" />
							<tr>
								<td><?php echo $row['description']; ?></td>
								<td><input type="submit" name="view-dept" value="Remove" /></td>
							</tr>
						</form>
<?php
					}
?>
					</table>
<?php
				}
				else
				{
					echo "No items in this department.";
				}

				echo "<br/><br/><strong>Add Item to Dept</strong><br/>";

				$q = "SELECT * ";
				$q.= "FROM items ";

				$r = mysqli_query($ptwin_shopDB, $q);

				if ($r && $r->num_rows > 0)
				{
					$num_rows = $r->num_rows;
					$items = [];

					for ($i=0; $i<$num_rows; $i++)
					{
						$items[$i] = mysqli_fetch_array($r, MYSQLI_ASSOC);
					}
?>
					<form method="POST">
						<input type="hidden" name="dept-id" value="<?php echo $dept_id; ?>" />
						<input type="hidden" name="dept-name" value="<?php echo $dept_name; ?>" />
						<select name="item-id">
<?php
							for ($i=0; $i<$num_rows; $i++)
							{
?>
								<option value="<?php echo $items[$i]['item_id']; ?>"><?php echo $items[$i]['description']; ?></option>
<?php
							}
?>
						</select>
						<input type="submit" name="view-dept" value="Add to Dept" />
					</form>
<?php
				}
				else
				{
					echo "No items available to add.";
				}

			}
			else
			{
?>				<form method="POST">
					<fieldset>
						<legend>Add Dept</legend>
						<p>Department Name:</p>
						<input name="dept-name" type="text" placeholder="Required" required />
						<br/><br/>
						<input type="submit" name="add-dept" value="Add Dept" />
					</fieldset><br/>
				</form>
<?php
				if (sizeof($depts)!=0)
				{
?>
					<table>
						<tr>
							<td>Dept ID</td>
							<td>Department Name</td>
							<td></td>
						</tr>
<?php
					for ($i=0; $i<sizeof($depts); $i++)
					{
?>
						<form method="POST">
							<tr>
								<input type="hidden" name="dept-id" value="<?php echo $depts[$i]['dept_id']; ?>" />
								<td><input type="submit" name="view-dept" value="View" /></td>
								<td><input type="text" name="dept-name" value="<?php echo $depts[$i]['dept_name']; ?>" /></td>
								<td><input type="submit" name="update-dept" value="Update" /></td>
							</tr>
						</form>
<?php
					}
?>
					</table>
<?php
				}
			}
?>
		</main>
	</body>

</html>