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
		$listPrototype = new ShopList();
		$lists = getAllLists();
?>
		<main class="wrapper">
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						<div id="add-list" class="form">
							<fieldset>
								<legend>Add List</legend>
								<p>List Name:</p>
								<input type="text" name="list-name" placeholder="Required" data-validation="<?= $listPrototype->getValidation("Name"); ?>" />
								<br/><br/>
								<input type="submit" class="btn btn-primary js-add-list" value="Add List" />
							</fieldset><br/>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12">
<?php
						if (is_array($lists))
						{
?>
							<table class="results-table">
								<tr>
									<th></th>
									<th>List Name</th>
									<th></th>
								</tr>
<?php
								foreach ($lists as $list_id => $list)
								{
?>
									<tr>
										<input type="hidden" name="list-id" value="<?= $list->getId(); ?>" />
										<td><input type="submit" name="view-list" value="View" /></td>
										<td><input type="text" name="list-name" value="<?= $list->getName(); ?>" /></td>
										<td><input type="submit" name="update-list" value="Update" /></td>
									</tr>
<?php
								}
?>
							</table>
<?php
						}
?>
					</div>
				</div>
			</div>
		</main>
<?php
		include_once('footer.php');
?>
	</body>
</html>
