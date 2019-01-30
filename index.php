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

			if (isset($_POST['clear-list']))
			{
				$q = "UPDATE items ";
				$q.= "SET selected='0'";

				$r = mysqli_query($ptwin_shopDB, $q);
			}

			if (isset($_POST['order-list']))
			{
				$q = "SELECT item_id, description, default_qty, total_qty, link ";
				$q.= "FROM items ";
				$q.= "WHERE selected = 1";

				$r = mysqli_query($ptwin_shopDB, $q);

				echo "<p><strong>On order:</strong></p>";
				$ordered = [];

				if ($r && $r->num_rows > 0)
				{
					$num_rows = $r->num_rows;

					for ($i = 0; $i < $num_rows; $i++)
					{
						$row = mysqli_fetch_array($r, MYSQLI_ASSOC);

						$item_id = $row['item_id'];
						$item_description = $row['description'];
						$new_total_qty = $row['total_qty'] + $row['default_qty'];
						$order_date = date(DATE_W3C);
						$link = $row['link'];

						if (!is_null($link) && !empty($link))
						{
?>
							<a href="<?= $link; ?>" target="_blank">
<?php
						}
								echo "$item_description<br/>";

						if (!is_null($link) && !empty($link))
						{
?>
							</a>
<?php
						}

						$ordered[] = [$item_description => $row['default_qty']];

						$q2 = "UPDATE items ";
						$q2.= "SET total_qty = '$new_total_qty', last_ordered = '$order_date' ";
						$q2.= "WHERE item_id = $item_id";

						$r2 = mysqli_query($ptwin_shopDB, $q2);
					}
?>
					<br/><p><strong>Summary:</strong></p>
					<table>
<?php
						for ($i = 0; $i < $num_rows; $i++)
						{
							foreach ($ordered[$i] as $key => $value)
							{
								echo "<tr><td>$key</td><td>$value</td></tr>";
							}
						}
?>
					</table>
<?php
				}

				$q = "UPDATE items ";
				$q.= "SET selected='0'";

				$r = mysqli_query($ptwin_shopDB, $q);				
			}

			if (isset($_POST['add-usuals']))
			{
				$q = "UPDATE items ";
				$q.= "SET selected='1' ";
				$q.= "WHERE list_id='1'";

				$r = mysqli_query($ptwin_shopDB, $q);
			}

			$q = "SELECT * ";
			$q.= "FROM items ";
			$q.= "WHERE selected=1";

			$r = mysqli_query($ptwin_shopDB, $q);
?>
			<div class="results-container">			
<?php
				if ($r && $r->num_rows > 0)
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
									<input type="text" class="item-description" name="item-description" value="<?= $row['description']; ?>" required />
									<input type="text" class="item-comments" name="item-comments" value="<?= $row['comments']; ?>" />
								</div>
								<div class="qty-container">
									<input type="number" name="item-default-qty" min="1" value="<?= $row['default_qty']; ?>" required />
								</div>
								<div class="btns-container">
									<div class="update-container">
										<input class="update" type=submit name="update" value="Update" />
									</div>
									<div class="remove-container">
										<input class="remove" type=submit name="remove" value="Remove" />
									</div>
								</div>
								<input type="hidden" name="item-id" value="<?= $row['item_id']; ?>" />
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

			<div class="action-btns-container">
				<form method="POST">
					<div class="clear-btn-container">
						<input type="submit" name="clear-list" value="Clear" />
					</div>
					<div class="order-btn-container">
						<input type="submit" name="order-list" value="Order" />	
					</div>
					<div class="add-usuals-btn-container">
						<input type="submit" name="add-usuals" value="Add Usuals" />	
					</div>
				</form>
			</div>
		</main>
	</body>
</html>