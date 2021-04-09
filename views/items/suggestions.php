<?php
	$suggested_items = $response['suggested_items'];
	$order = $response['order'];
	$items_in_order = $response['items_in_order'];
	$consumption_interval = $response['consumption_interval'];
	$consumption_period = $response['consumption_period'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="results-container suggestions striped">
<?php
					if (count($suggested_items) == 0)
					{
?>
						<p class="no-results">No Items can be found</p>
<?php
					}
					else
					{
						$response['ajax'] = false;

						echo getPartialView("RecentConsumptionForm", $response);
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
								<div class="row form fade-on-mute result-item <?= $item->isInCurrentOrder() ? 'selected' : ''; ?>" data-item_id="<?= $item->getId(); ?>" data-order_item_id="<?= $item->getOrderItemId(); ?>">
									<div class="col-xs-9 description-container">
										<a href="<?= SITEURL; ?>/items/edit/<?= $item->getId(); ?>/"><p><?= $item->getDescription(); ?></p></a>
									</div>

									<div class="col-xs-3 quantity-container">
										<input type="number" name="quantity" data-validation="<?= $item->getValidation("Quantity"); ?>" value="<?= $item->getQuantity(); ?>" />
									</div>

									<div class="col-xs-4 button-container">
										<button class="btn btn-sm btn-primary pull-left js-add-item-to-current-order">&plus;</button>
										<button class="btn btn-sm btn-primary pull-left js-update-suggested-order-item"><i class="far fa-edit"></i></button>
										<button class="btn btn-sm btn-danger pull-right js-remove-item-from-current-order">&times;</button>
									</div>

									<div class="col-xs-4 button-container mute-button">
										<button class="btn btn-sm btn-danger pull-right js-mute-suggestion" data-mute_basis="temp">Mute Temp</button>
									</div>

									<div class="col-xs-4 button-container mute-button">
										<button class="btn btn-sm btn-danger pull-right js-mute-suggestion" data-mute_basis="perm">Mute Perm</button>
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
			</div>
		</div>
	</div>
</main>
