<?php
	$unchecked_order_items = $response['unchecked_order_items'];
?>
<main class="wrapper">
	<div class="container">
<?php
		if (!is_array($unchecked_order_items) || sizeof($unchecked_order_items) == 0)
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<p class="no-results">No unchecked OrderItems can be found</p>
				</div>
			</div>
<?php
		}
		else
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<h3>Unchecked Order Items</h3>
					<div class="results-container unchecked-order-items striped">
<?php
						foreach ($unchecked_order_items as $unchecked_order_item)
						{
							$order = $unchecked_order_item['order'];
							$order_item = $unchecked_order_item['order_item'];
?>
							<div class="row form result-item unchecked" data-order_item_id="<?= $order_item->getId(); ?>">
								<div class="col-xs-12 order-context-container">
									<p><a href="<?= SITEURL; ?>/orders/view/<?= $order->getId(); ?>/">Order #<?= $order->getId(); ?></a> - <?= $order->getDateOrdered() ? $order->getDateOrdered()->format('d-m-Y') : 'Current Order'; ?></p>
								</div>

								<div class="col-xs-7 description-container">
									<p><a href="<?= SITEURL; ?>/items/edit/<?= $order_item->getItemId(); ?>/"><?= $order_item->getItemDescription(); ?></a></p>
								</div>

								<div class="col-xs-2 quantity-container">
									<p><?= $order_item->getQuantity(); ?></p>
								</div>

								<div class="col-xs-1 packsize-container">
									<p><?= $order_item->getItemPackSizeShortName(); ?></p>
								</div>

								<div class="col-xs-2 button-container check">
									<button class="btn btn-sm btn-success pull-right js-check-order-item" data-check="check">Mark Checked</button>
								</div>
							</div>
<?php
						}
?>
					</div>
				</div>
			</div>
<?php
		}
?>
	</div>
</main>