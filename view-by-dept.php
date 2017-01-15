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
			$q.= "FROM departments";

			$r = mysqli_query($ptwin_shopDB, $q);

			if ($r->num_rows > 0)
			{
				$num_rows = $r->num_rows;
				for ($i=0; $i<$num_rows; $i++)
				{
					$row = mysqli_fetch_array($r, MYSQLI_ASSOC);
					$depts[intval($row['dept_id'])] = $row['dept_name'];
				}
?>
				<form method="POST">
<?php
					foreach ($depts as $dept_id => $dept_name)
					{
?>
						<h3 class="dept-name"><?php echo ucfirst($dept_name); ?></h3>
<?php
						$q = "SELECT * ";
						$q.= "FROM item_dept_link ";
						$q.= "INNER JOIN items ON item_dept_link.item_id=items.item_id ";
						$q.= "WHERE dept_id='$dept_id'";

						$r = mysqli_query($ptwin_shopDB, $q);

						if ($r->num_rows > 0)
						{
							$num_rows = $r->num_rows;
?>
							<table class="dept-table">
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
									<tr class="dept-row <?php if($selected) echo "selected"; ?>">
										<td class="dept-description"><?php echo $row['description']; ?></td>
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
							echo "No items in this department.<br/><br/>";
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
				echo "No departments in DB.";
			}

?>
		</main>
	</body>

</html>