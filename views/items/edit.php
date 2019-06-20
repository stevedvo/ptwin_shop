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
					<div id="edit-item" class="form" data-item_id="<?= $item->getId(); ?>">
						<h3>Edit <?= $item->getDescription(); ?></h3>
						<div class="row">
							<div class="description-container col-xs-12">
								<label for="description">Description:</label>
								<input id="description" type="text" name="description" placeholder="Required" value="<?= $item->getDescription(); ?>" data-validation="<?= getValidationString($item, "Description"); ?>" />
							</div>
						</div>

						<div class="row">
							<div class="comments-container col-xs-12">
								<label for="comments">Comments:</label>
								<input id="comments" type="text" name="comments" value="<?= $item->getComments(); ?>" data-validation="<?= getValidationString($item, "Comments"); ?>" />
							</div>
						</div>

						<div class="row">
							<div class="default-qty-container col-xs-12">
								<label for="default_qty">Default Qty:</label>
								<input id="default_qty" type="number" name="default-qty" min="1" value="<?= $item->getDefaultQty(); ?>" data-validation="<?= getValidationString($item, "DefaultQty"); ?>" />
							</div>
						</div>

						<div class="row">
							<div class="link-container col-xs-12">
								<label for="link">Link:</label>
								<input id="link" type="text" name="link" value="<?= $item->getLink(); ?>" data-validation="<?= getValidationString($item, "Link"); ?>" />
							</div>
						</div>

						<div class="row">
							<div class="list-container col-xs-12">
								<label for="list">List:</label>
								<select id="list" name="list-id" data-validation="<?= getValidationString($item, "ListId"); ?>">
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
							</div>
						</div>
						<input type="submit" class="btn btn-primary js-edit-item" value="Edit Item" />
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
						<div class="row">
							<input type="hidden" name="item-id" value="<?= $item->getId(); ?>" />
							<div class="col-xs-8 department-selection-container">
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
							</div>

							<div class="col-xs-4 add-department-to-item-container">
								<button class="btn btn-primary btn-sm pull-right js-add-department-to-item">Add to Item</button>
							</div>
						</div>
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

			<div class="row">
				<div class="col-xs-12">
					<h3>Orders</h3>
					<div class="row results-container">
<?php
						if (!is_array($item->getOrders()) || sizeof($item->getOrders()) < 1)
						{
?>
							<div class="col-xs-12">
								<p class="no-results">No Orders could be found for this Item.</p>
							</div>
<?php
						}
						else
						{
?>
							<div class="results-header col-xs-12">
								<div class="row">
									<div class="col-xs-4 results-header-item">
										<p><strong>Order ID</strong></p>
									</div>

									<div class="col-xs-4 results-header-item">
										<p><strong>Date Ordered</strong></p>
									</div>

									<div class="col-xs-4 results-header-item">
										<p><strong>Quantity</strong></p>
									</div>
								</div>
							</div>

							<div class="results-body col-xs-12">
<?php
								foreach ($item->getOrders() as $order_id => $order)
								{
?>
									<div class="row">
										<div class="col-xs-4 order-result-item">
											<p>#<?= $order->getId(); ?></p>
										</div>

										<div class="col-xs-4 order-result-item">
											<p><?= $order->getDateOrdered()->format('d-m-Y'); ?></p>
										</div>

										<div class="col-xs-4 order-result-item">
											<p><?= $order->getOrderItembyItemId($item->getId())->getQuantity(); ?></p>
										</div>
									</div>
<?php
								}
?>
							</div>
<?php
						}
?>
					</div>

					<h4>Stats</h4>
					<div class="row results-container">
						<div class="col-xs-8">
							<p>Total Ordered:</p>
						</div>

						<div class="col-xs-4">
							<p><?= $item->getTotalOrdered(); ?></p>
						</div>

						<div class="col-xs-8">
							<p>Last Order:</p>
						</div>

						<div class="col-xs-8">
							<?php var_dump($item->getLastOrder()); ?>
						</div>

						<div class="col-xs-8">
							<p>First Order:</p>
						</div>

						<div class="col-xs-8">
							<?php var_dump($item->getFirstOrder()); ?>
						</div>
					</div>
				</div>
			</div>
<?php
		}
?>
	</div>
</main>
