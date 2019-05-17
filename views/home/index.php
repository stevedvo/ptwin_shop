<?php
	$order = $response['current_order'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="results-container">
<?php
					if (!is_array($order->getOrderItems()))
					{
?>
						<p class="no-results">No Items added to Order yet</p>
<?php
					}
					else
					{
						foreach ($order->getOrderItems() as $order_item_id => $order_item)
						{
?>
							<div class="row form">
								<div class="col-xs-5">
									<p><a href="/items/edit/<?= $order_item->getItemId(); ?>/"><?= $order_item->getItemId(); ?></a></p>
								</div>

								<div class="col-xs-1">
									<input type="number" name="quantity" data-validation="<?= $order_item->getValidation("Quantity"); ?>" value="<?= $order_item->getQuantity(); ?>" />
								</div>

								<div class="col-xs-3">
									<button class="btn btn-sm btn-primary js-update-order-item">Update</button>
								</div>

								<div class="col-xs-3">
									<button class="btn btn-sm btn-danger js-remove-order-item">Remove</button>
								</div>
							</div>
<?php
						}
					}
?>
				</div>
			</div>
		</div>
	</div>
</main>
