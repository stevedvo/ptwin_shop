<?php
	$department = $response['department'];
	$all_items = $response['all_items'];
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
					<div id="edit-department" class="form department-container">
						<input type="hidden" name="department-id" value="<?= $department->getId(); ?>" />
						<div class="row">
							<div class="col-xs-12">
								<label>Dept Name:</label>
								<input type="text" name="department-name" placeholder="Required" data-validation="<?= getValidationString($department, "Name"); ?>" value="<?= $department->getName(); ?>" />
							</div>
						</div>

						<div class="row">
							<div class="col-xs-12">
								<label>Sequence:</label>
								<input type="number" name="seq" min="0" placeholder="Required" data-validation="<?= getValidationString($department, "Seq"); ?>" value="<?= $department->getSeq(); ?>" />
								<button class="btn btn-primary btn-sm js-update-department">Update</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<h3>Current Items in Department</h3>
					<div class="department-items-container results-container" data-department_id="<?= $department->getId(); ?>">
<?php
						if (is_array($department->getItems()) && sizeof($department->getItems()) > 0)
						{
							foreach ($department->getItems() as $item_id => $item)
							{
								echo getPartialView("DepartmentItem", ['item' => $item]);
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
						<div class="row">
							<div class="col-xs-8 item-selection-container">
								<input type="hidden" name="department-id" value="<?= $department->getId(); ?>" />
								<select id="addItemToDepartment" class="item-selection">
									<option value="-1"></option>
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
							</div>

							<div class="col-xs-4 add-item-to-department-container">
								<button class="btn btn-primary btn-sm pull-right js-add-item-to-department">Add to Dept</button>
							</div>
						</div>
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
