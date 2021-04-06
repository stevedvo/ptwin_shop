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
				<div id="<?= $mealPlan->getDateString(); ?>" class="col-xs-6 col-sm-3 calendar-box">
<?php
					echo getPartialView("MealPlansCalendarItem", ['mealPlan' => $mealPlan]);
?>
				</div>
<?php
			}
?>
		</div>
	</div>
</main>
