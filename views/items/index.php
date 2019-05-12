<?php
	$all_items = $response['all_items'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<a class="btn btn-primary" href="/items/create/">Add New Item</a>
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
							<p><a href="/items/edit/<?= $item->getId(); ?>/"><?= $item->getDescription(); ?></a></p>
<?php
						}
					}
?>
				</div>
			</div>
		</div>
	</div>
</main>
