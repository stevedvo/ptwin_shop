<?php
	$suggested_items = $response['suggested_items'];
	$order = $response['order'];
	$items_in_order = $response['items_in_order'];
	$date_from = $response['date_from'];
	$date_to = $response['date_to'];
	$today = new DateTimeImmutable('today');
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="results-container suggestions striped">
					<form method="get" action="<?= SITEURL; ?>/items/" class="row meal-items-date-range-form">
						<input type="hidden" name="view-by" value="upcoming-meal-items" />

						<div class="col-xs-12 meal-items-quick-days" role="group" aria-label="Filter by meal day">
<?php
							for ($offset = 0; $offset <= 4; $offset++)
							{
								$day = $today->modify('+'.$offset.' days');
								$label = $offset === 0 ? 'Today' : ($offset === 1 ? 'Tomorrow' : $day->format('l'));
?>
								<button type="button" class="btn btn-default btn-sm js-meal-items-day" data-day-offset="<?= $offset; ?>" aria-pressed="false" title="<?= $day->format('l j F Y'); ?>"><?= $label; ?> <span class="meal-items-day-date"><?= $day->format('j M'); ?></span></button>
<?php
							}
?>
						</div>

						<div class="col-xs-6">
							<label for="date_from">From Date:</label>&nbsp;
							<input type="date" id="date_from" name="date_from" value="<?= $date_from->format('Y-m-d'); ?>" />
						</div>

						<div class="col-xs-6">
							<label for="date_to">To Date:</label>&nbsp;
							<input type="date" id="date_to" name="date_to" value="<?= $date_to->format('Y-m-d'); ?>" />
						</div>

						<div class="col-xs-12 submit-container text-right">
							<a class="btn btn-warning btn-sm" href="<?= SITEURL; ?>/items/?view-by=upcoming-meal-items">Reset</a>
							<button class="btn btn-primary btn-sm" type="submit">Refresh</button>
						</div>
					</form>

					<div id="upcomingMealItemsResults" aria-live="polite" aria-busy="false">
						<?= getPartialView('UpcomingMealItemListItems', ['suggested_items' => $suggested_items]); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
