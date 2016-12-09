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
			$q = "select * from items";

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
					</tr>
<?php
					for ($i=0; $i<$num_rows; $i++)
					{
						$row = mysqli_fetch_array($r, MYSQLI_ASSOC);
?>
						<tr>
							<td><?php echo $row['description']; ?></td>
							<td><?php echo $row['comments']; ?></td>
							<td><?php echo $row['default_qty']; ?></td>
							<td><input type=checkbox /></td>
						</tr>
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