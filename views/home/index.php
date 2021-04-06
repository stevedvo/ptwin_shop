<?php
	$order = $response['current_order'];
	$all_lists = $response['all_lists'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div id="current-order-actions-container" class="form" data-order_id="<?= $order->getId(); ?>">
				<div id="confirm-current-order-container" class="col-xs-6">
					<button class="btn btn-sm btn-primary js-confirm-current-order">Confirm Order</button>
				</div>

				<div id="clear-current-order-container" class="col-xs-6">
					<button class="btn btn-sm btn-danger pull-right js-clear-current-order">Clear Order</button>
				</div>

				<div id="add-list-to-current-order-container" class="col-xs-12">
					<div class="select-container">
						<select name="add-list-to-current-order">
<?php
							if (is_array($all_lists))
							{
								foreach ($all_lists as $list_id => $list)
								{
?>
									<option value="<?= $list->getId(); ?>"><?= $list->getName(); ?></option>
<?php
								}
							}
?>
						</select>
					</div>

					<div class="button-container">
						<button class="btn btn-sm btn-primary pull-right js-add-list-to-current-order">Add List To Order</button>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<div class="results-container current-order striped">
<?php
					if (!is_array($order->getOrderItems()))
					{
?>
						<p class="no-results">No Items added to Order yet</p>
<?php
					}
					else
					{
						foreach ($order->getOrderItems() as $orderItemId => $orderItem)
						{
							echo getPartialView("CurrentOrderItem", ['orderItem' => $orderItem]);
						}
					}
?>
				</div>
			</div>
		</div>
	</div>
</main>
