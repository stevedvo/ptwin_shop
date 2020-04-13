<?php
	$item = $response['item'];
	$lists = $response['lists'];
	$packsizes = $response['packsizes'];
	$all_departments = $response['all_departments'];
	$consumption_interval = $response['consumption_interval'];
	$consumption_period = $response['consumption_period'];
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

						<div class="row">
							<div class="packsize-container col-xs-12">
								<label for="packsize_id">Packsize:</label>
								<select id="packsize_id" name="packsize_id" data-validation="<?= getValidationString($item, "PackSizeId"); ?>">
<?php
									if (is_array($packsizes))
									{
										foreach ($packsizes as $packsize_id => $packsize)
										{
?>
											<option value="<?= $packsize->getId(); ?>" <?= $packsize->getId() == $item->getPackSizeId() ? 'selected' : ''; ?>><?= $packsize->getName()." [".$packsize->getShortName()."]"; ?></option>
<?php
										}
									}
?>
								</select>
							</div>
						</div>

						<div class="row">
							<div class="mute-temp-container col-xs-12">
								<label for="mute-temp">Mute Temp:</label>
								<input id="mute-temp" type="checkbox" name="mute-temp" value="1" <?= $item->getMuteTemp() ? 'checked' : ''; ?> data-validation="<?= getValidationString($item, "MuteTemp"); ?>" />
							</div>
						</div>

						<div class="row">
							<div class="mute-perm-container col-xs-12">
								<label for="mute-perm">Mute Perm:</label>
								<input id="mute-perm" type="checkbox" name="mute-perm" value="1" <?= $item->getMutePerm() ? 'checked' : ''; ?> data-validation="<?= getValidationString($item, "MutePerm"); ?>" />
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
					<div class="row">
						<div class="results-container item-order-history">
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
										<div class="col-xs-4 results-header-item order-id-container">
											<p><strong>Order ID</strong></p>
										</div>

										<div class="col-xs-5 results-header-item order-date-container">
											<p><strong>Date Ordered</strong></p>
										</div>

										<div class="col-xs-3 results-header-item order-quantity-container">
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
											<a href="<?= SITEURL; ?>/orders/view/<?= $order->getId(); ?>/">
												<div class="col-xs-4 order-result-item order-id-container">
													<p>#<?= $order->getId(); ?></p>
												</div>

												<div class="col-xs-5 order-result-item order-date-container">
													<p><?= $order->getDateOrdered()->format('d-m-Y'); ?></p>
												</div>

												<div class="col-xs-3 order-result-item order-quantity-container">
													<p><?= $order->getOrderItembyItemId($item->getId())->getQuantity()." ".$item->getPackSizeShortName(); ?></p>
												</div>
											</a>
										</div>
<?php
									}
?>
								</div>
<?php
							}
?>
						</div>
					</div>

					<h4>Stats</h4>
					<div class="results-container item-order-statistics">
						<div class="row">
							<div class="col-xs-8 label-container">
								<p>Total Ordered:</p>
							</div>

							<div class="col-xs-4 statistic-container">
								<p><?= $item->getTotalOrdered()." ".$item->getPackSizeShortName(); ?></p>
							</div>
						</div>
<?php
						$response['ajax'] = true;
						$response['item_id'] = $item->getId();

						echo getPartialView("RecentConsumptionForm", $response);
?>
						<div class="row">
							<div class="col-xs-4 col-xs-offset-4">
								<p>Overall</p>
							</div>

							<div class="col-xs-4">
								<p>Recent</p>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-4">
								<p><?= $item->getPackSizeShortName(); ?>/wk</p>
							</div>

							<div class="col-xs-4">
								<p><?= $item->hasOrders() ? round($item->getDailyConsumptionOverall() * 7, 2) : 'N/A'; ?></p>
							</div>

							<div class="col-xs-4">
								<p id="itemDailyConsumptionRecent"><?= $item->hasOrders() ? round($item->getDailyConsumptionRecent() * 7, 2) : 'N/A'; ?></p>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-4">
								<p>Stock Now</p>
							</div>

							<div class="col-xs-4">
								<p><?= $item->hasOrders() ? $item->getStockLevelPrediction(0, "overall") : 'N/A'; ?> <?= $item->getPackSizeShortName(); ?></p>
							</div>

							<div class="col-xs-4">
								<p><span id="itemStockNowRecent"><?= $item->hasOrders() ? $item->getStockLevelPrediction(0, "recent") : 'N/A'; ?></span> <?= $item->getPackSizeShortName(); ?></p>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-4">
								<p>Stock +7d</p>
							</div>

							<div class="col-xs-4">
								<p><?= $item->hasOrders() ? $item->getStockLevelPrediction(7, "overall") : 'N/A'; ?> <?= $item->getPackSizeShortName(); ?></p>
							</div>

							<div class="col-xs-4">
								<p><span id="itemStockFutureRecent"><?= $item->hasOrders() ? $item->getStockLevelPrediction(7, "recent") : 'N/A'; ?></span> <?= $item->getPackSizeShortName(); ?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
<?php
		}
?>
	</div>
</main>
