<?php
	$orderItem = $params['orderItem'];
?>
<div class="row form result-item" data-order_item_id="<?= $orderItem->getId(); ?>">
	<div class="col-xs-7 description-container">
		<p><a href="<?= SITEURL; ?>/items/edit/<?= $orderItem->getItemId(); ?>/"><?= $orderItem->getItemDescription(); ?></a></p>
	</div>

	<div class="col-xs-3 quantity-container">
		<input type="number" name="quantity" data-validation="<?= getValidationString($orderItem, "Quantity"); ?>" value="<?= $orderItem->getQuantity(); ?>" />
	</div>

	<div class="col-xs-2 packsize-container">
		<p><?= $orderItem->getItemPackSizeShortName(); ?></p>
	</div>

	<div class="col-xs-4 col-xs-offset-4 update button-container">
		<button class="btn btn-sm btn-primary pull-right js-update-order-item">Update</button>
	</div>

	<div class="col-xs-4 remove button-container">
		<button class="btn btn-sm btn-danger pull-right js-remove-order-item">Remove</button>
	</div>
</div>
