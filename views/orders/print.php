<?php
	$order = $response['order'];
	$departments = $response['departments'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
<?php
			if (!$order)
			{
?>
				<p>Could not find Order / invalid request</p>
<?php
			}
			else
			{
?>
				<div class="col-xs-12">
					<p>Order #<?= $order->getId(); ?> - Date Ordered: <?= $order->getDateOrdered() ? $order->getDateOrdered()->format('d-m-Y') : ""; ?></p>
				</div>

				<div class="col-xs-6 results-container print-previous-order striped">
<?php
					if (!(is_array($order->getOrderItems()) && sizeof($order->getOrderItems()) > 0))
					{
?>
						<p class="no-results">No Items in this Order</p>
<?php
					}
					else
					{
						$new_column_cutoff = ceil(sizeof($order->getOrderItems()) / 2);
						$cols = 1;
						$current_dept = null;
						$i = 0;

						foreach ($order->getOrderItems() as $order_item_id => $order_item)
						{
							$item_dept = $order_item->getItem()->getPrimaryDept();
							$dept_heading = !is_null($item_dept) ? $departments[$item_dept]->getName() : 'No collection defined';
							$description = $order_item->getItem()->getDescription();

							if (!empty($order_item->getItem()->getComments()))
							{
								$description.= " [".$order_item->getItem()->getComments()."]";
							}

							if ($i == 0)
							{
?>
								<div class="collection-container">
									<h4><?= $dept_heading; ?></h4>
									<div class="col-xs-12 collection-items-container">
<?php
							}
							elseif ($item_dept != $current_dept)
							{
?>
									</div>
								</div>
<?php
								if ($i > $new_column_cutoff && $cols < 2)
								{
?>
				</div>

				<div class="col-xs-6 results-container print-previous-order striped">
<?php
									$cols++;
								}
?>
								<div class="collection-container">
									<h4><?= $dept_heading; ?></h4>
									<div class="col-xs-12 collection-items-container">
<?php
							}
?>
										<div class="row result-item">
											<div class="col-xs-9 description-container">
												<p><?= $description; ?></p>
											</div>

											<div class="col-xs-3 quantity-container">
												<p><?= $order_item->getQuantity()." ".$order_item->getItemPackSizeShortName(); ?></p>
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
?>
				</div>
<?php
			}
?>
		</div>
	</div>
</main>
