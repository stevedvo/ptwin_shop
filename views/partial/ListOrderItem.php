<?php
	$order_item = $params['order_item'];
?>
<div class="row form result-item <?= $order_item->getChecked() ? 'checked' : 'unchecked'; ?>" data-order_item_id="<?= $order_item->getId(); ?>">
	<div class="col-xs-12 order-item-details">
		<div class="row">
			<div class="col-xs-8 description-container">
				<p><a href="<?= SITEURL; ?>/items/edit/<?= $order_item->getItemId(); ?>/"><?= $order_item->getItem()->getDescription(); ?></a></p>
			</div>

			<div class="col-xs-4 quantity-container">
				<input type="number" name="quantity" data-validation="<?= getValidationString($order_item, "Quantity"); ?>" value="<?= $order_item->getQuantity(); ?>" />
			</div>
		</div>
	</div>

	<div class="col-xs-12 order-item-buttons">
		<div class="row">
			<div class="col-xs-4 update button-container">
				<button class="btn btn-sm btn-primary pull-right js-update-order-item">Update</button>
			</div>

			<div class="col-xs-4 remove button-container">
				<button class="btn btn-sm btn-danger pull-right js-remove-order-item">Remove</button>
			</div>

			<div class="col-xs-4 check button-container">
				<button class="btn btn-sm btn-success pull-right js-check-order-item" data-check="check">Check</button>
			</div>

			<div class="col-xs-4 uncheck button-container">
				<button class="btn btn-sm btn-danger pull-right js-check-order-item" data-check="uncheck">Uncheck</button>
			</div>
		</div>
	</div>
</div>
