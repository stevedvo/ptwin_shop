<?php
	$order = $response['order'];
	$departments = $response['departments'];
?>
<main class="wrapper">
	<div class="container">
<?php
		if (!$order)
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<p>Could not find Order / invalid request</p>
				</div>
			</div>
<?php
		}
		else
		{
?>
			<div class="row">
				<div class="form order-container col-xs-12" data-order_id="<?= $order->getId(); ?>">
					<div class="row">
						<div class="label-container col-xs-5">
							<label>Date Ordered:</label>
						</div>

						<div class="input-container col-xs-4">
							<input type="text" class="pull-right" name="date_ordered" placeholder="Required" data-validation="<?= $order->getValidation("DateOrdered"); ?>" value="<?= !is_null($order->getDateOrdered()) ? $order->getDateOrdered()->format('d-m-Y') : ''; ?>" />
						</div>

						<div class="button-container col-xs-3">
							<button class="btn btn-sm btn-primary pull-right js-update-order">Update</button>
						</div>
					</div>
				</div>

				<div class="form add-item-to-previous-order-container col-xs-12">
					<div class="row">
						<div class="input-container col-xs-9">
							<input type="text" id="add-item-to-previous-order" class="pull-right" name="item-description" />
						</div>

						<div class="button-container col-xs-3">
							<button class="btn btn-primary btn-sm pull-right js-add-item-to-previous-order">Add</button>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<div class="results-container previous-order striped">
<?php
						if (is_array($order->getOrderItems()) && sizeof($order->getOrderItems()) > 0)
						{
							$current_dept = null;
							$i = 0;

							foreach ($order->getOrderItems() as $order_item_id => $order_item)
							{
								$item_dept = $order_item->getItem()->getPrimaryDept();
								$dept_heading = !is_null($item_dept) ? $departments[$item_dept]->getName() : 'No collection defined';

								if ($i == 0)
								{
?>
									<div class="collection-container">
										<h4><?= $dept_heading; ?></h4>
										<div class="collection-items-container">
<?php
								}
								elseif ($item_dept != $current_dept)
								{
?>
										</div>
									</div>

									<div class="collection-container">
										<h4><?= $dept_heading; ?></h4>
										<div class="collection-items-container">
<?php
								}
?>
											<div class="row form result-item" data-order_item_id="<?= $order_item->getId(); ?>">
												<div class="col-xs-8 description-container">
													<p><a href="<?= SITEURL; ?>/items/edit/<?= $order_item->getItemId(); ?>/"><?= $order_item->getItem()->getDescription(); ?></a></p>
												</div>

												<div class="col-xs-4 quantity-container">
													<input type="number" name="quantity" data-validation="<?= $order_item->getValidation("Quantity"); ?>" value="<?= $order_item->getQuantity(); ?>" />
												</div>

												<div class="col-xs-4 col-xs-offset-4 update button-container">
													<button class="btn btn-sm btn-primary pull-right js-update-order-item">Update</button>
												</div>

												<div class="col-xs-4 remove button-container">
													<button class="btn btn-sm btn-danger pull-right js-remove-order-item">Remove</button>
												</div>
											</div>
<?php
								$current_dept = $item_dept;
								$i++;

								if ($i == sizeof($order->getOrderItems()))
								{
?>
										</div>
									</div>
<?php
								}
							}
						}
						else
						{
?>
							<p class="no-results">No Items in this Order</p>
<?php
						}
?>
					</div>
				</div>
			</div>
<?php
		}
?>
	</div>
</main>
