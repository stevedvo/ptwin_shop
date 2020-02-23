<?php
	$order_item = $params['order_item'];
?>
<div class="row form result-item" data-order_item_id="<?= $order_item->getId(); ?>">
	<div class="col-xs-7 description-container">
		<p><a href="<?= SITEURL; ?>/items/edit/<?= $order_item->getItemId(); ?>/"><?= $order_item->getItem()->getDescription(); ?></a></p>
	</div>

	<div class="col-xs-3 quantity-container">
		<input type="number" name="quantity" data-validation="<?= getValidationString($order_item, "Quantity"); ?>" value="<?= $order_item->getQuantity(); ?>" />
	</div>

	<div class="col-xs-1 packsize-container">
		<p><?= $order_item->getItem()->getPackSizeShortName(); ?></p>
	</div>

	<div class="col-xs-4 col-xs-offset-4 update button-container">
		<button class="btn btn-sm btn-primary pull-right js-update-order-item">Update</button>
	</div>

	<div class="col-xs-4 remove button-container">
		<button class="btn btn-sm btn-danger pull-right js-remove-order-item">Remove</button>
	</div>
</div>
