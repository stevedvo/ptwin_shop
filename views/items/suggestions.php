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
					if (!is_array($suggested_items) || sizeof($suggested_items) == 0)
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
								<div class="col-xs-4 results-header-item description-container">
									<p><strong>Description</strong></p>
								</div>

								<div class="col-xs-3 results-header-item stock-level-container">
									<p><strong>Est. (Overall)</strong></p>
								</div>

								<div class="col-xs-3 results-header-item stock-level-container">
									<p><strong>Est. (Recent)</strong></p>
								</div>
							</div>
						</div>

						<div class="results-body">
<?php
							foreach ($suggested_items as $item_id => $item)
							{
?>
								<div class="row form fade-on-mute result-item <?= array_key_exists($item->getId(), $items_in_order) ? 'selected' : ''; ?>" data-item_id="<?= $item->getId(); ?>">
									<div class="col-xs-4 description-container">
										<a href="<?= SITEURL; ?>/items/edit/<?= $item->getId(); ?>/"><p><?= $item->getDescription(); ?></p></a>
									</div>

									<div class="col-xs-3 stock-level-container">
										<p><?= $item->getStockLevelPrediction(7, 'overall'); ?></p>
									</div>

									<div class="col-xs-3 stock-level-container">
										<p><?= $item->getStockLevelPrediction(7, 'recent'); ?></p>
									</div>

									<div class="col-xs-2 button-container">
										<button class="btn btn-sm btn-primary pull-right js-add-item-to-current-order">Add</button>
										<button class="btn btn-sm btn-danger pull-right js-remove-item-from-current-order" data-order_item_id="<?= array_key_exists($item->getId(), $items_in_order) ? $items_in_order[$item->getId()] : ''; ?>">Remove</button>
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
