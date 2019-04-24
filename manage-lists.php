<!DOCTYPE html>
<html lang="en">
	<head>
<?php
		$page_title = "Lists";
		include_once('head-section.php');
?>
	</head>

	<body>
<?php
		include_once('header.php');
		$lists = getAllLists();
?>
		<main class="wrapper">
			<div class="container">
				<div class="row">
					<form method="POST">
						<fieldset>
							<legend>Add List</legend>
							<p>List Name:</p>
							<input name="list-name" type="text" placeholder="Required" required />
							<br/><br/>
							<input type="submit" name="add-list" value="Add List" />
						</fieldset><br/>
					</form>
				</div>

				<div class="row">
<?php
					if (sizeof($lists) > 0)
					{
?>
						<table>
							<tr>
								<th></th>
								<th>List Name</th>
								<th></th>
							</tr>
<?php
						foreach ($lists as $list_id => $list)
						{
?>
							<form method="POST">
								<tr>
									<input type="hidden" name="list-id" value="<?= $list->getId(); ?>" />
									<td><input type="submit" name="view-list" value="View" /></td>
									<td><input type="text" name="list-name" value="<?= $list->getName(); ?>" /></td>
									<td><input type="submit" name="update-list" value="Update" /></td>
								</tr>
							</form>
<?php
						}
?>
						</table>
<?php
					}
?>
				</div>
			</div>
		</main>
<?php
		include_once('footer.php');
?>
	</body>
</html>