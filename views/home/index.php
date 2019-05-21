<?php
	$order = $response['current_order'];
?>
<main class="wrapper">
	<div class="container">
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
						foreach ($order->getOrderItems() as $order_item_id => $order_item)
						{
?>
							<div class="row form result-item" data-order_item_id="<?= $order_item->getId(); ?>">
								<div class="col-xs-8 description-container">
									<p><a href="/items/edit/<?= $order_item->getItemId(); ?>/"><?= $order_item->getItem()->getDescription(); ?></a></p>
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
						}
					}
?>
				</div>
			</div>
		</div>
	</div>
</main>
