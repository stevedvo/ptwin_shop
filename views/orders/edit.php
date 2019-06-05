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
					<div class="form order-container" data-order_id="<?= $order->getId(); ?>">
						<label>Date Ordered:</label>
						<input type="text" name="date_ordered" placeholder="Required" data-validation="<?= $order->getValidation("DateOrdered"); ?>" value="<?= !is_null($order->getDateOrdered()) ? $order->getDateOrdered()->format('d-m-Y') : ''; ?>" />
						<button class="btn btn-primary js-update-order">Update</button>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<div class="results-container striped">
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
