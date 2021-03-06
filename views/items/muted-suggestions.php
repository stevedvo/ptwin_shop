<?php
	$muted_items = $response['muted_items'];
	$order = $response['order'];
	$items_in_order = $response['items_in_order'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="results-container suggestions muted striped">
<?php
					if (!is_array($muted_items) || sizeof($muted_items) == 0)
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
								<div class="col-xs-12 results-header-item description-container">
									<p><strong>Description</strong></p>
								</div>
							</div>
						</div>

						<div class="results-body">
<?php
							foreach ($muted_items as $item_id => $item)
							{
?>
								<div class="row form result-item <?= array_key_exists($item->getId(), $items_in_order) ? 'selected' : ''; ?> <?= $item->getMuteTemp() ? 'muted-temp' : 'unmuted-temp'; ?> <?= $item->getMutePerm() ? 'muted-perm' : 'unmuted-perm'; ?>" data-item_id="<?= $item->getId(); ?>" data-order_item_id="<?= array_key_exists($item->getId(), $items_in_order) ? $items_in_order[$item->getId()] : ''; ?>">
									<div class="col-xs-12 description-container">
										<a href="<?= SITEURL; ?>/items/edit/<?= $item->getId(); ?>/"><p><?= $item->getDescription(); ?></p></a>
									</div>

									<div class="col-xs-4 button-container">
										<button class="btn btn-sm btn-primary pull-right js-add-item-to-current-order">Add</button>
										<button class="btn btn-sm btn-danger pull-right js-remove-item-from-current-order">Remove</button>
									</div>

									<div class="col-xs-4 button-container mute-button">
										<button class="btn btn-sm btn-success pull-right js-unmute-suggestion" data-mute_basis="temp">Unmute Temp</button>
										<button class="btn btn-sm btn-danger pull-right js-mute-suggestion" data-mute_basis="temp">Mute Temp</button>
									</div>

									<div class="col-xs-4 button-container mute-button">
										<button class="btn btn-sm btn-success pull-right js-unmute-suggestion" data-mute_basis="perm">Unmute Perm</button>
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
