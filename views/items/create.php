<!DOCTYPE html>
<html lang="en">
	<head>
<?php
		$page_title = "Add New Item";
		include_once('head-section.php');
?>
	</head>

	<body>
<?php
		include_once('header.php');
?>
		<main class="wrapper">
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						<div id="add-item" class="form">
							<fieldset>
								<legend>Add Item</legend>
								<label for="description">Description:</label>
								<input id="description" type="text" name="description" placeholder="Required" data-validation="<?= $itemPrototype->getValidation("Description"); ?>" />
								<br/><br/>
								<label for="comments">Comments:</label>
								<input id="comments" type="text" name="comments" data-validation="<?= $itemPrototype->getValidation("Comments"); ?>" />
								<br/><br/>
								<label for="default_qty">Default Qty:</label>
								<input id="default_qty" type="text" name="default_qty" data-validation="<?= $itemPrototype->getValidation("DefaultQty"); ?>" />
								<br/><br/>
								<input type="submit" class="btn btn-primary js-add-list" value="Add Item" />
							</fieldset>
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
