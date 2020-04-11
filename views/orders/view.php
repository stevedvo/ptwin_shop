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
					<p>Order #<?= $order->getId(); ?> - Date Ordered: <?= $order->getDateOrdered() ? $order->getDateOrdered()->format('d-m-Y') : ""; ?></p>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<a href="<?= SITEURL.'/orders/print/'.$order->getId().'/'; ?>" class="btn btn-primary btn-sm" target="_blank">Print</a>
					<a href="<?= SITEURL.'/orders/edit/'.$order->getId().'/'; ?>" class="btn btn-primary btn-sm">Edit</a>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<div class="results-container view-previous-order striped">
<?php
						if (is_array($order->getOrderItems()) && sizeof($order->getOrderItems()) > 0)
						{
							$current_dept = null;
							$i = 0;

							foreach ($order->getOrderItems() as $order_item_id => $order_item)
							{
								$item_dept = $order_item->getItem()->getPrimaryDept();
								$dept_heading = !is_null($item_dept) ? $departments[$item_dept]->getName() : 'No collection defined';

								if ($i == 0)
								{
?>
									<div class="collection-container">
										<h4><?= $dept_heading; ?></h4>
										<div class="collection-items-container">
<?php
								}
								elseif ($item_dept != $current_dept)
								{
?>
										</div>
									</div>

									<div class="collection-container">
										<h4><?= $dept_heading; ?></h4>
										<div class="collection-items-container">
<?php
								}
?>
											<div class="row result-item">
												<div class="col-xs-8 description-container">
													<a href="<?= SITEURL; ?>/items/edit/<?= $order_item->getItemId(); ?>/"><p><?= $order_item->getItem()->getDescription(); ?></p></a>
												</div>

												<div class="col-xs-4 comments-container">
													<p><?= $order_item->getItem()->getComments(); ?></p>
												</div>

												<div class="col-xs-8 link-container">
													<a href="<?= $order_item->getItem()->getLink(); ?>" target="_blank"><p><?= $order_item->getItem()->getLink(); ?></p></a>
												</div>

												<div class="col-xs-2 quantity-container">
													<p><?= $order_item->getQuantity(); ?></p>
												</div>

												<div class="col-xs-2 packsize-container">
													<p><?= $order_item->getItemPackSizeShortName(); ?></p>
												</div>
											</div>
<?php
								$current_dept = $item_dept;
								$i++;

								if ($i == sizeof($order->getOrderItems()))
								{
?>
										</div>
									</div>
<?php
								}
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
