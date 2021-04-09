<?php
	$mealPlans = $response['mealPlans'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
<?php
			$i = 0;

			foreach ($mealPlans as $dateString => $mealPlan)
			{
?>
				<div id="<?= $mealPlan->getDateString(); ?>" class="col-xs-6 col-sm-3 calendar-box">
<?php
					echo getPartialView("MealPlansCalendarItem", ['mealPlan' => $mealPlan]);
?>
				</div>
<?php
				if ($i > 3 && $mealPlan->getWeekdayNumber() == "0")
				{
?>
					<div class="col-xs-6 col-sm-3 calendar-box"></div>
<?php
				}

				$i++;
			}
?>
		</div>
	</div>
</main>
