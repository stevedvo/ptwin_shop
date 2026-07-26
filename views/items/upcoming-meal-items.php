<?php
	$suggested_items = $response['suggested_items'];
	$order = $response['order'];
	$items_in_order = $response['items_in_order'];
	$date_from = $response['date_from'];
	$date_to = $response['date_to'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="results-container suggestions striped">
					<form method="get" action="<?= SITEURL; ?>/items/" class="row meal-items-date-range-form">
						<input type="hidden" name="view-by" value="upcoming-meal-items" />

						<div class="col-xs-6">
							<label for="date_from">From Date:</label>&nbsp;
							<input type="date" id="date_from" name="date_from" value="<?= $date_from->format('Y-m-d'); ?>" />
						</div>

						<div class="col-xs-6">
							<label for="date_to">To Date:</label>&nbsp;
							<input type="date" id="date_to" name="date_to" value="<?= $date_to->format('Y-m-d'); ?>" />
						</div>

						<div class="col-xs-12 submit-container text-right">
							<a class="btn btn-warning btn-sm" href="<?= SITEURL; ?>/items/?view-by=upcoming-meal-items">Reset</a>
							<button class="btn btn-primary btn-sm" type="submit">Refresh</button>
						</div>
					</form>

<?php
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
				</div>
			</div>
		</div>
	</div>
</main>
