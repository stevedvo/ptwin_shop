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
								<label for="list-name">List Name:</label>
								<input id="list-name" type="text" name="list-name" placeholder="Required" data-validation="<?= $listPrototype->getValidation("Name"); ?>" />
								<br/><br/>
								<input type="submit" class="btn btn-primary js-add-list" value="Add List" />
							</fieldset><br/>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12">
						<h3>Current Lists</h3>
						<div class="results-container">
<?php
							if (!is_array($lists))
							{
?>
								<p class="no-results">No Lists can be found</p>
<?php
							}
							else
							{
								foreach ($lists as $list_id => $list)
								{
?>
									<p><a href="/edit-list.php?id=<?= $list->getId(); ?>"><?= $list->getName(); ?></a></p>
<?php
								}
							}
?>
						</div>
					</div>
				</div>
			</div>
		</main>
<?php
		include_once('footer.php');
?>
	</body>
</html>
