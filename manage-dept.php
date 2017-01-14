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

?>
		<main class="wrapper">

			<form method="POST" action="manage-dept.php">
				<fieldset>
					<legend>Add Dept</legend>
					<p>Department Name:</p>
					<input name="dept-name" type="text" placeholder="Required" required />
					<br/><br/>
					<input type="submit" name="add-dept" value="Add Dept" />
				</fieldset><br/>
			</form>
<?php
			if (isset($_POST['view-dept']))
			{
				$dept_id 	= $_POST['dept-id'];
				$dept_name 	= $_POST['dept-name'];

				echo "Code to display/edit items in '$dept_name' [$dept_id] to be inserted here.";
			}
			else
			{
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