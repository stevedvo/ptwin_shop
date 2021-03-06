<?php
	$all_items = $response['all_items'];
	$order = $response['order'];
	$items_in_order = $response['items_in_order'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<a class="btn btn-primary" href="<?= SITEURL; ?>/items/create/">Add New Item</a>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<h3>Current Items</h3>
				<div class="results-container">
<?php
					if (!is_array($all_items) || sizeof($all_items) == 0)
					{
?>
						<p class="no-results">No Items can be found</p>
<?php
					}
					else
					{
						foreach ($all_items as $item_id => $item)
						{
?>
							<div class="row form result-item <?= array_key_exists($item->getId(), $items_in_order) ? 'selected' : ''; ?>" data-item_id="<?= $item->getId(); ?>" data-order_item_id="<?= array_key_exists($item->getId(), $items_in_order) ? $items_in_order[$item->getId()] : ''; ?>">
								<div class="col-xs-5 description-container">
									<a href="<?= SITEURL; ?>/items/edit/<?= $item->getId(); ?>/"><p><?= $item->getDescription(); ?></p></a>
								</div>

								<div class="col-xs-3 button-container">
									<button class="btn btn-sm btn-primary pull-right js-add-item-to-current-order">Add to Order</button>
								</div>

								<div class="col-xs-4 button-container">
									<button class="btn btn-sm btn-danger pull-right js-remove-item-from-current-order">Remove from Order</button>
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
