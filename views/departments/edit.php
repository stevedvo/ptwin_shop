<!DOCTYPE html>
<html lang="en">
	<head>
<?php
		$page_title = "Edit Department";
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
				if (!$department)
				{
?>
					<div class="row">
						<div class="col-xs-12">
							<p>Could not find Department / invalid request</p>
						</div>
					</div>
<?php
				}
				else
				{
?>
					<div class="row">
						<div class="col-xs-12">
							<div class="department-container">
								<label>Department Name:</label>
								<input type="hidden" name="department-id" value="<?= $department->getId(); ?>" />
								<input type="text" name="department-name" placeholder="Required" data-validation="<?= $department->getValidation("Name"); ?>" value="<?= $department->getName(); ?>" />
								<button class="btn btn-primary js-update-department">Update</button>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12">
							<h3>Current Items in Department</h3>
							<div class="department-items-container" data-department_id="<?= $department->getId(); ?>">
<?php
								if (is_array($department->getItems()) && sizeof($department->getItems()) > 0)
								{
									foreach ($department->getItems() as $item_id => $item)
									{
?>
										<p data-item_id="<?= $item->getId(); ?>" data-description="<?= $item->getDescription(); ?>"><?= $item->getDescription(); ?><span class="btn btn-danger btn-sm js-select-item">Select</span><span class="btn btn-danger btn-sm js-unselect-item">Unselect</span></p>
<?php
									}
								}
								else
								{
?>
									<p class="no-results">No Items in this Department</p>
									<button class="btn btn-danger btn-sm no-results js-remove-department">Remove Department</button>
<?php
								}
?>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12">
							<h3>Add Item to Department</h3>
							<div class="form">
								<input type="hidden" name="department-id" value="<?= $department->getId(); ?>" />
								<select class="item-selection">
<?php
									if (is_array($all_items))
									{
										foreach ($all_items as $item_id => $item)
										{
											if (is_array($department->getItems()) && !array_key_exists($item_id, $department->getItems()))
											{
?>
												<option data-item_id="<?= $item->getId(); ?>"><?= $item->getDescription(); ?></option>
<?php
											}
										}
									}
?>
								</select>
								<button class="btn btn-primary btn-sm js-add-item-to-department">Add to Department</button>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12">
							<h3>Remove</h3>
							<div class="form">
								<button class="btn btn-danger btn-sm js-remove-items-from-department">Remove Selected Item(s) from Department</button>
							</div>
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
