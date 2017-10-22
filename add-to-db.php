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

		if ($r && $r->num_rows > 0)
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

			if ($_POST['item-link'])
			{
				$item_link = $_POST['item-link'];
			}
			else
			{
				$item_link = null;
			}

			$selected = 0;

			if (isset($_POST['selected']))
			{
				$selected = 1;
			}

			$q = "INSERT INTO items ";
			$q.= "(description, comments, default_qty, selected, list_id, link) ";
			$q.= "VALUES ('$item_description', '$item_comments', '$item_default_qty', '$selected', '$item_list', '$item_link')";

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

		if (isset($_POST['edit-item']))
		{
			$item_id = $_POST['item-id'];

			if ($_POST['edit-item']=="Remove Dept")
			{
				$dept_id = $_POST['dept-id'];

				$q = "DELETE FROM item_dept_link ";
				$q.= "WHERE item_id='$item_id' AND dept_id='$dept_id'";

				$r = mysqli_query($ptwin_shopDB, $q);
			}

			if ($_POST['edit-item']=="Apply Changes")
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

				if ($_POST['item-link'] && !empty($_POST['item-link']))
				{
					$item_link = $_POST['item-link'];
				}
				else
				{
					$item_link = null;
				}

				$q = "UPDATE items ";
				$q.= "SET description='$item_description', comments='$item_comments', default_qty='$item_default_qty', list_id='$item_list', link='$item_link' ";
				$q.= "WHERE item_id='$item_id'";

				$r = mysqli_query($ptwin_shopDB, $q);

				if (isset($_POST['item-dept']))
				{
					$dept_id = $_POST['item-dept'];

					$q = "INSERT INTO item_dept_link ";
					$q.= "(dept_id, item_id) ";
					$q.= "VALUES ('$dept_id', '$item_id')";

					$r = mysqli_query($ptwin_shopDB, $q);
				}
			}

			$q1 = "SELECT * ";
			$q1.= "FROM items ";
			$q1.= "INNER JOIN lists ON items.list_id=lists.list_id ";
			$q1.= "WHERE item_id='$item_id'";

			$r1 = mysqli_query($ptwin_shopDB, $q1);

			$q2 = "SELECT * ";
			$q2.= "FROM item_dept_link ";
			$q2.= "INNER JOIN items ON item_dept_link.item_id=items.item_id ";
			$q2.= "INNER JOIN departments ON item_dept_link.dept_id=departments.dept_id ";
			$q2.= "WHERE item_dept_link.item_id='$item_id'";

			$r2 = mysqli_query($ptwin_shopDB, $q2);

			$item_depts = [];

			if ($r2 && $r2->num_rows > 0)
			{
				$num_rows = $r2->num_rows;

				for ($i=0; $i<$num_rows; $i++)
				{
					$item_depts[$i] = mysqli_fetch_array($r2, MYSQLI_ASSOC);
				}
			}

			if ($r1 && $r1->num_rows > 0)
			{
				$num_rows = $r1->num_rows;

				for ($i=0; $i<$num_rows; $i++)
				{
					$row = mysqli_fetch_array($r1, MYSQLI_ASSOC);
?>
					<h3><?= $row['description']; ?></h3>
					<form method="POST" id="item-update">
						<input type="hidden" name="item-id" value="<?= $item_id; ?>">
						<fieldset>
							Description:<br/>
							<input type="text" name="item-description" value="<?= $row['description']; ?>" required /><br/><br/>
							Comments:<br/>
							<input type="text" name="item-comments" value="<?= $row['comments']; ?>" /><br/><br/>
							Link:<br/>
							<input type="url" name="item-link" value="<?= $row['link']; ?>" /><br/><br/>
							Order Qty:<br/>
							<input type="number" name="item-default-qty" min="1" value="<?= $row['default_qty']; ?>" required /><br/><br/>
							Last Ordered:<br/>
							<?= $row['last_ordered']; ?><br/><br/>
							List:<br/>
							<select name="item-list">
<?php
								if (!empty($lists))
								{
									for ($j=0; $j<sizeof($lists); $j++)
									{
?>
										<option value="<?= $lists[$j]['list_id']; ?>" <?php if($lists[$j]['list_id']==$row['list_id']) echo "selected" ?>><?= ucfirst($lists[$j]['name']); ?></option>
<?php
									}
								}
?>
							</select><br/><br/>
					</form>
							Department(s):<br/>
<?php
							if (sizeof($item_depts)>0)
							{
?>
								<table>
<?php
									for ($j=0; $j<sizeof($item_depts); $j++)
									{
?>
										<form method="POST">
											<input type="hidden" name="item-id" value="<?= $item_id;?>" />
											<input type="hidden" name="dept-id" value="<?= $item_depts[$j]['dept_id'];?>" />
											<tr>
												<td><?= $item_depts[$j]['dept_name'];?></td>
												<td><input type="submit" name="edit-item" value="Remove Dept" /></td>
											</tr>
										</form>
<?php
									}
?>
								</table><br/><br/>
<?php
							}
							else
							{
								echo "None specified.<br/><br/>";
							}
?>
							Add to Department:<br/>
							<select name="item-dept" form="item-update">
								<option selected disabled>Choose Dept</option>
<?php
								if (!empty($depts))
								{
									for ($j=0; $j<sizeof($depts); $j++)
									{
?>
										<option value="<?= $depts[$j]['dept_id']; ?>"><?= ucfirst($depts[$j]['dept_name']); ?></option>
<?php
									}
								}
?>
							</select><br/><br/>

							<input type="submit" name="edit-item" value="Apply Changes" />
						</fieldset>
					</form>
<?php
				}
			}


		}
		else
		{
?>
			<main class="wrapper">

				<form method="POST">
					<fieldset>
						<legend><strong>Add Item</strong></legend>
						<p>Description:</p>
						<input name="item-description" type="text" placeholder="Required" required <?php if (isset($_SESSION['item-description'])) echo "value='".$_SESSION['item-description']."'"; ?> />
						<p>Comments:</p>
						<input name="item-comments" type="text" placeholder="Optional" />
						<p>Link:</p>
						<input name="item-link" type="url" placeholder="Optional" />
						<p>Default Order Qty:</p>
						<input name="item-default-qty" type="number" min="1" value="1" required/><br/><br/>
						<select name="item-list">
<?php
							if (!empty($lists))
							{
								for ($i=0; $i<sizeof($lists); $i++)
								{
?>
									<option value="<?= $lists[$i]['list_id']; ?>"><?= ucfirst($lists[$i]['name']); ?></option>
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
									<option value="<?= $depts[$i]['dept_id']; ?>"><?= ucfirst($depts[$i]['dept_name']); ?></option>
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

				echo "<br/><br/>";

				if (sizeof($items)!=0)
				{
?>
					<fieldset>
						<legend><strong>Manage Items</strong></legend>
						<table>
							<tr>
								<td><strong>Description</strong></td>
								<td><strong>Comments</strong></td>
								<td><strong>Order Qty</strong></td>
								<td></td>
							</tr>
<?php
							for ($i=0; $i<sizeof($items); $i++)
							{
?>
								<form method="POST">
									<input type="hidden" name="item-id" value="<?= $items[$i]['item_id']; ?>" />
									<tr>
										<td>
<?php
											if (!is_null($items[$i]['link']) && !empty($items[$i]['link']))
											{
?>
												<a href="<?= $items[$i]['link']; ?>" target="_blank">
<?php
											}

													echo $items[$i]['description'];

											if (!is_null($items[$i]['link']) && !empty($items[$i]['link']))
											{
?>
												</a>
<?php
											}
?>
												
										</td>
										<td><?= $items[$i]['comments']; ?></td>
										<td><?= $items[$i]['default_qty']; ?></td>
										<td><input type="submit" name="edit-item" value="Edit Item" /></td>
									</tr>
								</form>
<?php
							}
?>
						</table>
					</fieldset>
<?php
				}
				else
				{
					echo "No items in DB.";
				}
?>
			</main>
<?php
		}
?>
	</body>

</html>