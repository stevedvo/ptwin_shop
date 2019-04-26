<!DOCTYPE html>
<html lang="en">
	<head>
<?php
		$page_title = "Manage Departments";
		include_once('head-section.php');
?>
	</head>

	<body>
<?php
		include_once('header.php');

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
			$q.= "SET dept_name = '$dept_name' ";
			$q.= "WHERE dept_id = '$dept_id'";

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

			for ($i = 0; $i < $num_rows; $i++)
			{
				$depts[$i] = mysqli_fetch_array($r, MYSQLI_ASSOC);
			}
		}
?>
		<main class="wrapper">
			<div class="container">
<?php
				if (isset($_POST['view-dept']))
				{
					$dept_id = $_POST['dept-id'];
					$dept_name = $_POST['dept-name'];

					if ($_POST['view-dept'] == "Remove")
					{
						$item_id = $_POST['item-id'];

						$q = "DELETE FROM item_dept_link ";
						$q.= "WHERE item_id = '$item_id' AND dept_id = '$dept_id'";

						$r = mysqli_query($ptwin_shopDB, $q);
					}

					if ($_POST['view-dept'] == "Add to Dept")
					{
						$item_id = $_POST['item-id'];

						$q = "INSERT INTO item_dept_link ";
						$q.= "(dept_id, item_id) ";
						$q.= "VALUES ('$dept_id', '$item_id')";

						$r = mysqli_query($ptwin_shopDB, $q);
					}

					$q = "SELECT * ";
					$q.= "FROM item_dept_link ";
					$q.= "INNER JOIN items ON item_dept_link.item_id = items.item_id ";
					$q.= "WHERE dept_id = '$dept_id'";

					$r = mysqli_query($ptwin_shopDB, $q);
?>
					<div class="row">
						<div class="col-xs-12">
							<h3><?= $dept_name; ?></h3>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12">
<?php
							if ($r && $r->num_rows > 0)
							{
								$num_rows = $r->num_rows;
?>
								<table>
<?php
									for ($i = 0; $i < $num_rows; $i++)
									{
										$row = mysqli_fetch_array($r, MYSQLI_ASSOC);
?>
										<form method="POST">
											<input type="hidden" name="dept-id" value="<?= $dept_id; ?>" />
											<input type="hidden" name="dept-name" value="<?= $dept_name; ?>" />
											<input type="hidden" name="item-id" value="<?= $row['item_id']; ?>" />
											<tr>
												<td><?= $row['description']; ?></td>
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
								echo "<p>No items in this department.</p>";
							}
?>
						</div>
					</div>
					<br/>
					<div class="row">
						<div class="col-xs-12">
							<p><strong>Add Item to Dept</strong></p>
<?php
							$q = "SELECT * ";
							$q.= "FROM items ";

							$r = mysqli_query($ptwin_shopDB, $q);

							if ($r && $r->num_rows > 0)
							{
								$num_rows = $r->num_rows;
								$items = [];

								for ($i = 0; $i < $num_rows; $i++)
								{
									$items[$i] = mysqli_fetch_array($r, MYSQLI_ASSOC);
								}
?>
								<form method="POST">
									<input type="hidden" name="dept-id" value="<?= $dept_id; ?>" />
									<input type="hidden" name="dept-name" value="<?= $dept_name; ?>" />
									<select name="item-id">
<?php
										for ($i = 0; $i < $num_rows; $i++)
										{
?>
											<option value="<?= $items[$i]['item_id']; ?>"><?= $items[$i]['description']; ?></option>
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
								echo "<p>No items available to add.</p>";
							}
?>
						</div>
					</div>
<?php
				}
				else
				{
?>
					<div class="row">
						<div class="col-xs-12">
							<form method="POST">
								<fieldset>
									<legend>Add Dept</legend>
									<p>Department Name:</p>
									<input name="dept-name" type="text" placeholder="Required" required />
									<br/><br/>
									<input type="submit" name="add-dept" value="Add Dept" />
								</fieldset>
							</form>
						</div>
					</div>
					<br/>
<?php
					if (sizeof($depts) != 0)
					{
?>
						<div class="row">
							<div class="col-xs-12">
								<table>
									<tr>
										<th></th>
										<th>Department Name</th>
										<th></th>
									</tr>
<?php
									for ($i = 0; $i < sizeof($depts); $i++)
									{
?>
										<form method="POST">
											<tr>
												<input type="hidden" name="dept-id" value="<?= $depts[$i]['dept_id']; ?>" />
												<td><input type="submit" name="view-dept" value="View" /></td>
												<td><input type="text" name="dept-name" value="<?= $depts[$i]['dept_name']; ?>" /></td>
												<td><input type="submit" name="update-dept" value="Update" /></td>
											</tr>
										</form>
<?php
									}
?>
								</table>
							</div>
						</div>
<?php
					}
				}
?>
			</div>
		</main>
	</body>
</html>
