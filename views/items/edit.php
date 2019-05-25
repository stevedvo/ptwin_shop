<?php
	$item = $response['item'];
	$lists = $response['lists'];
	$all_departments = $response['all_departments'];
?>
<main class="wrapper">
	<div class="container">
<?php
		if (!$item)
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<p>Could not find Item / invalid request</p>
				</div>
			</div>
<?php
		}
		else
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<div id="edit-item" class="form">
						<fieldset>
							<legend>Edit Item</legend>
							<input type="hidden" name="item-id" value="<?= $item->getId(); ?>" />
							<label for="description">Description:</label>
							<input id="description" type="text" name="description" placeholder="Required" value="<?= $item->getDescription(); ?>" data-validation="<?= $item->getValidation("Description"); ?>" />
							<br/><br/>
							<label for="comments">Comments:</label>
							<input id="comments" type="text" name="comments" value="<?= $item->getComments(); ?>" data-validation="<?= $item->getValidation("Comments"); ?>" />
							<br/><br/>
							<label for="default_qty">Default Qty:</label>
							<input id="default_qty" type="number" name="default-qty" min="1" value="<?= $item->getDefaultQty(); ?>" data-validation="<?= $item->getValidation("DefaultQty"); ?>" />
							<br/><br/>
							<label for="total_qty">Total Qty:</label>
							<span id="total_qty">// todo: sum up Order qtys</span>
							<br/><br/>
							<label for="last_ordered">Last Ordered:</label>
							<span id="last_ordered">// todo: find last Order or = blank</span>
							<br/><br/>
							<label for="link">Link:</label>
							<input id="link" type="text" name="link" value="<?= $item->getLink(); ?>" data-validation="<?= $item->getValidation("Link"); ?>" />
							<br/><br/>
							<label for="list">List:</label>
							<select id="list" name="list-id" data-validation="<?= $item->getValidation("ListId"); ?>">
								<option value="" selected disabled>Please select...</option>
<?php
								if (is_array($lists))
								{
									foreach ($lists as $list_id => $list)
									{
?>
										<option value="<?= $list->getId(); ?>" <?= $list->getId() == $item->getListId() ? 'selected' : ''; ?>><?= $list->getName(); ?></option>
<?php
									}
								}
?>
							</select>
							<br/><br/>
							<input type="submit" class="btn btn-primary js-edit-item" value="Edit Item" />
						</fieldset>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<h3>Current Departments</h3>
					<div class="department-items-container results-container" data-item_id="<?= $item->getId(); ?>">
<?php
						if (!is_array($item->getDepartments()))
						{
?>
							<p class="no-results">Not added to any Departments.</p>
<?php
						}
						else
						{
							foreach ($item->getDepartments() as $dept_id => $department)
							{
								$isPrimary = $item->getPrimaryDept() == $department->getId() ? true : false;

								echo getPartialView("ItemDepartment", ['department' => $department, 'isPrimary' => $isPrimary]);
							}
						}
?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<h3>Add Department to Item</h3>
					<div class="form">
						<input type="hidden" name="item-id" value="<?= $item->getId(); ?>" />
						<select class="department-selection">
<?php
							if (is_array($all_departments))
							{
								foreach ($all_departments as $dept_id => $department)
								{
									if ((!is_array($item->getDepartments())) || (is_array($item->getDepartments()) && !array_key_exists($dept_id, $item->getDepartments())))
									{
?>
										<option data-dept_id="<?= $department->getId(); ?>"><?= $department->getName(); ?></option>
<?php
									}
								}
							}
?>
						</select>
						<button class="btn btn-primary btn-sm js-add-department-to-item">Add to Item</button>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<h3>Remove</h3>
					<div class="form">
						<button class="btn btn-danger btn-sm js-remove-departments-from-item">Remove Selected Department(s) from Item</button>
					</div>
				</div>
			</div>
<?php
		}
?>
	</div>
</main>
