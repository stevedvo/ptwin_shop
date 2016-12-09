<?php
?>

<!DOCTYPE html>

<html lang="en">

	<head>
<?php
		$page_title = "Peatwin Shopping";
		include ('head-section.php');
?>
	</head>

	<body>
<?php
		include ('header.php');
?>
		<main class="wrapper">
<?php
			if (isset($_POST['update']))
			{
				$item_description = $_POST['item-description'];
				if ($_POST['item-comments'])
				{
					$item_comments = $_POST['item-comments'];
				}
				else
				{
					$item_comments = "";
				}
				$item_default_qty = $_POST['item-default-qty'];
				$item_id = $_POST['item-id'];

				$q = "UPDATE items ";
				$q.= "SET description='$item_description', comments='$item_comments', default_qty='$item_default_qty' ";
				$q.= "WHERE item_id='$item_id'";

				$r = mysqli_query($ptwin_shopDB, $q);
			}

			if (isset($_POST['remove']))
			{
				$item_id = $_POST['item-id'];

				$q = "UPDATE items ";
				$q.= "SET selected='0' ";
				$q.= "WHERE item_id='$item_id'";

				$r = mysqli_query($ptwin_shopDB, $q);
			}

			$q = "SELECT * ";
			$q.= "FROM items ";
			$q.= "WHERE selected=1";

			$r = mysqli_query($ptwin_shopDB, $q);

			if ($r->num_rows > 0)
			{
				$num_rows = $r->num_rows;
?>
				<table>
					<tr>
						<td>Item</td>
						<td>Comments</td>
						<td>Quantity</td>
						<td>Update</td>
						<td>Remove</td>
					</tr>
<?php
					for ($i=0; $i<$num_rows; $i++)
					{
						$row = mysqli_fetch_array($r, MYSQLI_ASSOC);
?>
						<form action="index.php" method="POST">
							<tr>
								<td><input type="text" name="item-description" value="<?php echo $row['description']; ?>" required /></td>
								<td><input type="text" name="item-comments" value="<?php echo $row['comments']; ?>" /></td>
								<td><input type="number" name="item-default-qty" min="1" value="<?php echo $row['default_qty']; ?>" required /></td>
								<td><input type=submit name="update" value="Update" /></td>
								<td><input type=submit name="remove" value="Remove" /></td>
							</tr>
							<input type="hidden" name="item-id" value="<?php echo $row['item_id']; ?>" />
						</form>
<?php
					}
?>
				</table>
<?php
			}
?>
		</main>
	</body>

</html>