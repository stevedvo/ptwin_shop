<?php
	$order = $response['order'];
	$departments = $response['departments'];
?>
<main class="wrapper">
	<div class="container">
<?php
		if (!$order)
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<p>Could not find Order / invalid request</p>
				</div>
			</div>
<?php
		}
		else
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<p>Order ID: <?= $order->getId(); ?></p>
					<p>Date Ordered: <?= $order->getDateOrdered() ? $order->getDateOrdered()->format('d-m-Y') : ""; ?></p>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<div class="results-container striped">
<?php
						if (is_array($order->getOrderItems()) && sizeof($order->getOrderItems()) > 0)
						{
							$current_dept = null;

							foreach ($order->getOrderItems() as $order_item_id => $order_item)
							{
								$item_dept = $order_item->getItem()->getPrimaryDept();

								if ($item_dept != $current_dept)
								{
									$current_dept = $item_dept;
?>
									<div class="collection-container">
										<h4><?= $departments[$item_dept]->getName() ?: 'No collection defined'; ?></h4>
										<div class="collection-items-container">
										</div>
									</div>
<?php
								} // need to sort this out: end collection container when all items in that primary dept have been output, and start a new collection container to display next department
?>
								<div class="row result-item">
									<div class="col-xs-2 description-container">
										<a href="<?= SITEURL; ?>/items/edit/<?= $order_item->getItemId(); ?>/"><p><?= $order_item->getItem()->getDescription(); ?></p></a>
									</div>

									<div class="col-xs-2 comments-container">
										<p><?= $order_item->getItem()->getComments(); ?></p>
									</div>

									<div class="col-xs-7 link-container">
										<a href="<?= $order_item->getItem()->getLink(); ?>" target="_blank"><p><?= $order_item->getItem()->getLink(); ?></p></a>
									</div>

									<div class="col-xs-1 quantity-container">
										<p><?= $order_item->getQuantity(); ?></p>
									</div>
								</div>
<?php
							}
						}
						else
						{
?>
							<p class="no-results">No Items in this Order</p>
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
