<?php
	$suggested_items = $params['suggested_items'];

	if (count($suggested_items) == 0)
	{
?>
		<p class="no-results">No Items can be found</p>
<?php
	}
	else
	{
?>
		<div class="results-header">
			<div class="row">
				<div class="col-xs-9 results-header-item description-container">
					<p><strong>Description</strong></p>
				</div>

				<div class="col-xs-3 results-header-item quantity-container">
					<p><strong>Quantity</strong></p>
				</div>
			</div>
		</div>

		<div class="results-body">
<?php
			foreach ($suggested_items as $item_id => $item)
			{
?>
				<div class="row form result-item <?= $item->isInCurrentOrder() ? 'selected' : ''; ?>" data-item_id="<?= $item->getId(); ?>" data-order_item_id="<?= $item->getOrderItemId(); ?>">
					<div class="col-xs-9 description-container">
						<a href="<?= SITEURL; ?>/items/edit/<?= $item->getId(); ?>/"><p><?= $item->getDescription(); ?></p></a>
					</div>

					<div class="col-xs-3 quantity-container">
						<input type="number" name="quantity" data-validation="<?= $item->getValidation("Quantity"); ?>" value="<?= $item->getQuantity(); ?>" />
					</div>

					<div class="col-xs-12 button-container">
						<button class="btn btn-sm btn-primary pull-left js-add-item-to-current-order">&plus;</button>
						<button class="btn btn-sm btn-primary pull-left js-update-suggested-order-item"><i class="far fa-edit"></i></button>
						<button class="btn btn-sm btn-danger pull-right js-remove-item-from-current-order">&times;</button>
					</div>
				</div>
<?php
			}
?>
		</div>
<?php
	}
?>
