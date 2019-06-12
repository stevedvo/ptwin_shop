<?php
	$collection = $response['collection'];
	$order = $response['order'];
	$items_in_order = $response['items_in_order'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="results-container">
<?php
					if (!is_array($collection) || sizeof($collection) == 0)
					{
?>
						<p class="no-results">No collections can be found</p>
<?php
					}
					else
					{
						foreach ($collection as $collection_id => $collection)
						{
?>
							<div class="collection-container">
								<h4><?= $collection->getName() ?: 'No collection defined'; ?></h4>
								<div class="collection-items-container">
<?php
									if (!is_array($collection->getItems()) || sizeof($collection->getItems()) == 0)
									{
?>
										<p class="no-results">No Items are in this <?= get_class($collection); ?></p>
<?php
									}
									else
									{
										foreach ($collection->getItems() as $item_id => $item)
										{
											$in_order = array_key_exists($item->getId(), $items_in_order) ? true : false;
?>
											<div class="row form result-item <?= $in_order ? 'selected' : ''; ?>" data-item_id="<?= $item->getId(); ?>">
												<div class="col-xs-5 description-container">
													<a href="<?= SITEURL; ?>/items/edit/<?= $item->getId(); ?>/"><p><?= $item->getDescription(); ?></p></a>
												</div>

												<div class="col-xs-3 button-container">
													<button class="btn btn-sm btn-primary pull-right js-add-item-to-current-order">Add to Order</button>
												</div>

												<div class="col-xs-4 button-container">
													<button class="btn btn-sm btn-danger pull-right js-remove-item-from-current-order" data-order_item_id="<?= $in_order ? $items_in_order[$item->getId()] : ''; ?>">Remove from Order</button>
												</div>
											</div>
<?php
										}
									}
?>
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
