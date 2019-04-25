<!DOCTYPE html>
<html lang="en">
	<head>
<?php
		$page_title = "All Items";
		include_once('head-section.php');
?>
	</head>

	<body>
<?php
		include_once('header.php');
?>
		<main class="wrapper">
			<div class="container">
<?php
				if (isset($_POST['update']))
				{
					$items_on = array_keys($_POST);
					array_pop($items_on);

					$q = "UPDATE items ";
					$q.= "SET selected = '0'";

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
					$q.= "SET selected = '0'";

					$r = mysqli_query($ptwin_shopDB, $q);
				}

				$q = "SELECT * ";
				$q.= "FROM departments ";
				$q.= "ORDER BY dept_name";

				$r = mysqli_query($ptwin_shopDB, $q);

				if ($r && $r->num_rows > 0)
				{
					$num_rows = $r->num_rows;

					for ($i = 0; $i < $num_rows; $i++)
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
							<div class="row">
								<div class="col-xs-12">
									<h3 class="dept-name"><?= ucfirst($dept_name); ?></h3>
<?php
									$q = "SELECT * ";
									$q.= "FROM item_dept_link ";
									$q.= "INNER JOIN items ON item_dept_link.item_id = items.item_id ";
									$q.= "WHERE dept_id = '$dept_id'";

									$r = mysqli_query($ptwin_shopDB, $q);

									if ($r && $r->num_rows > 0)
									{
										$num_rows = $r->num_rows;
?>
										<table class="dept-table">
<?php
											for ($i = 0; $i < $num_rows; $i++)
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
												<tr class="dept-row <?php if ($selected) echo "selected"; ?>">
													<td class="dept-description"><?= $row['description']; ?></td>
													<td><input type="checkbox" name="<?= $row['item_id']; ?>" <?php if ($selected) echo "checked"; ?> /></td>
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
?>
								</div>
							</div>
<?php
						}
?>
						<br/><br/>
						<div class="row">
							<div class="col-xs-12">
								<input type="submit" name="update" value="Update List" />
								<input type="submit" name="clear-all" value="Clear List" />
							</div>
						</div>
					</form>
<?php
				}
				else
				{
?>
					<div class="row">
						<div class="col-xs-12">
							<p>No departments in DB.</p>
						</div>
					</div>
<?php
				}
?>
			</div>
		</main>
	</body>
</html>
