<!DOCTYPE html>
<html lang="en">
	<head>
<?php
		$page_title = "Edit List";
		include_once('head-section.php');
?>
	</head>

	<body>
<?php
		include_once('header.php');

		if (!isset($_GET['id']) || !is_numeric($_GET['id']))
		{
			$list = false;
		}
		else
		{
			$list = getListById($_GET['id']);
		}
?>
		<main class="wrapper">
			<div class="container">
<?php
				if (!$list)
				{
?>
					<div class="row">
						<div class="col-xs-12">
							<p>Could not find List / invalid request</p>
						</div>
					</div>
<?php
				}
				else
				{
?>
					<div class="row">
						<div class="col-xs-12">
							<div class="list-container">
								<label>List Name:</label>
								<input type="hidden" name="list-id" value="<?= $list->getId(); ?>" />
								<input type="text" name="list-name" placeholder="Required" data-validation="<?= $list->getValidation("Name"); ?>" value="<?= $list->getName(); ?>" />
								<button class="btn btn-primary js-update-list">Update</button>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12">
							// show current items in list [update SQL to include items, add items array property to list object]
							// option to remove [AJAX - which updates select below]
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12">
							// select input to choose available items [not including items already in list]
							// AJAX update
						</div>
					</div>
<?php
				}
?>
			</div>
		</main>
<?php
		include_once('footer.php');
?>
	</body>
</html>
