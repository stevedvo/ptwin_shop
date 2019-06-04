<?php
	$orders = $response['orders'];
?>
<main class="wrapper">
	<div class="container">
<?php
		if (!is_array($orders) || sizeof($orders) == 0)
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<p class="no-results">No Orders can be found</p>
				</div>
			</div>
<?php
		}
		else
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<h3>Orders</h3>
					<div class="results-container striped">
<?php
						foreach ($orders as $order_id => $order)
						{
?>
							<div class="row result-item">
								<div class="col-xs-2 id-container">
									<p>#<?= $order->getId(); ?></p>
								</div>

								<div class="col-xs-4 date-ordered-container">
									<p><?= !is_null($order->getDateOrdered()) ? $order->getDateOrdered()->format('d-m-Y') : ''; ?></p>
								</div>

								<div class="col-xs-3 button-container">
									<a href="<?= SITEURL.'/orders/view/'.$order->getId().'/'; ?>" class="btn btn-sm btn-primary pull-right">View</a>
								</div>

								<div class="col-xs-3 button-container">
									<a href="<?= SITEURL.(!is_null($order->getDateOrdered()) ? '/orders/edit/'.$order->getId().'/' : '/'); ?>" class="btn btn-sm btn-danger pull-right">Edit</a>
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
