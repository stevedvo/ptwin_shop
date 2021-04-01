<?php
	$mealPlans = $response['mealPlans'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
<?php
			foreach ($mealPlans as $dateString => $mealPlan)
			{
?>
				<div class="col-xs-6 col-sm-3 calendar-box" data-datestring="<?= $dateString; ?>">
					<div class="calendar-box-header">
						<p class="col-xs-10"><?= $mealPlan->getCalendarHeader(); ?></p>
						<span class="col-xs-2 edit-btn"><i class="far fa-edit"></i></span>
					</div>
					<div class="calendar-box-body">
						<p><?= $mealPlan->getMealName(); ?></p>
					</div>
				</div>
<?php
			}
?>
		</div>
	</div>
</main>
