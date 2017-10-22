<?php
?>

<!DOCTYPE html>

<html lang="en">

	<head>
<?php
		$page_title = "All Items";
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
				$items_on = array_keys($_POST);
				array_pop($items_on);

				$q = "UPDATE items ";
				$q.= "SET selected='0'";

				$r = mysqli_query($ptwin_shopDB, $q);

				$q = "UPDATE items ";
				$q.= "SET selected = CASE item_id ";

					foreach ($items_on as $item_id)
					{
						$q.= "WHEN $item_id THEN 1 ";
					}

				$q.= "ELSE selected END";

				$r = mysqli_query($ptwin_shopDB, $q);

			}

			if (isset($_POST['clear-all']))
			{
				$q = "UPDATE items ";
				$q.= "SET selected='0'";

				$r = mysqli_query($ptwin_shopDB, $q);
			}

			$q = "SELECT * ";
			$q.= "FROM lists";

			$r = mysqli_query($ptwin_shopDB, $q);

			if ($r && $r->num_rows > 0)
			{
				$num_rows = $r->num_rows;
				for ($i=0; $i<$num_rows; $i++)
				{
					$row = mysqli_fetch_array($r, MYSQLI_ASSOC);
					$lists[intval($row['list_id'])] = $row['name'];
				}
?>
				<form method="POST">
<?php
					foreach ($lists as $cat_id => $cat_name)
					{
?>
						<h3 class="category-name"><?php echo ucfirst($cat_name); ?></h3>
<?php
						$q = "SELECT * ";
						$q.= "FROM items ";
						$q.= "WHERE list_id = '$cat_id'";

						$r = mysqli_query($ptwin_shopDB, $q);

						if ($r && $r->num_rows > 0)
						{
							$num_rows = $r->num_rows;
?>
							<table class="category-table">
<?php
								for ($i=0; $i<$num_rows; $i++)
								{
									$row = mysqli_fetch_array($r, MYSQLI_ASSOC);

									if ($row['selected'] == 1)
									{
										$selected = TRUE;
									}
									else
									{
										$selected = FALSE;
									}
?>
									<tr class="category-row <?php if($selected) echo "selected"; ?>">
										<td class="category-description"><?php echo $row['description']; ?></td>
										<td><input type="checkbox" name="<?php echo $row['item_id']; ?>" <?php if($selected) echo "checked"; ?> /></td>
									</tr>
<?php
								}
?>
							</table>
<?php
						}
						else
						{
							echo "No items in this category.<br/><br/>";
						}
					}
?>
					<br/><br/>
					<input type="submit" name="update" value="Update List" />
					<input type="submit" name="clear-all" value="Clear List" />
				</form>
<?php
			}
			else
			{
				echo "No categories in DB.";
			}

?>
		</main>
	</body>

</html>