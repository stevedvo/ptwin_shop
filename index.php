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
?>
			<div class="results-container">			
<?php
				if ($r->num_rows > 0)
				{
					$odd_row = true;
					$num_rows = $r->num_rows;
					for ($i=0; $i<$num_rows; $i++)
					{
						$row = mysqli_fetch_array($r, MYSQLI_ASSOC);
?>
						<div class="item-container <?php if ($odd_row) echo 'odd-row'; ?>">
							<form action="index.php" method="POST">
								<div class="text-blocks">
									<input type="text" class="item-description" name="item-description" value="<?php echo $row['description']; ?>" required />
									<input type="text" class="item-comments" name="item-comments" value="<?php echo $row['comments']; ?>" />
								</div>
								<div class="qty-container">
									<input type="number" name="item-default-qty" min="1" value="<?php echo $row['default_qty']; ?>" required />
								</div>
								<div class="btns-container">
									<div class="update-container">
										<input class="update" type=submit name="update" value="Update" />
									</div>
									<div class="remove-container">
										<input class="remove" type=submit name="remove" value="Remove" />
									</div>
								</div>
								<input type="hidden" name="item-id" value="<?php echo $row['item_id']; ?>" />
							</form>
						</div>
<?php
						$odd_row = !$odd_row;
					}
				}
				else
				{
					echo "Shopping list is empty.";
				}
?>
			</div>
		</main>
	</body>

</html>