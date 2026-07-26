<?php
	$mealId = $params['mealId'];
	$mealPlanDays = $params['mealPlanDays'];
?>
<div class="meal-plan-days-container results-container" data-meal_id="<?= $mealId; ?>">
<?php
	if (sizeof($mealPlanDays) < 1)
	{
?>
		<p class="no-results">No Dates found for this Meal</p>
<?php
	}
	else
	{
		foreach ($mealPlanDays as $key => $mealPlanDay)
		{
?>
			<div class="row result-item form" data-meal_plan_day_id="<?= $mealPlanDay->getId(); ?>">
				<div class="col-xs-12 meal-plan-day-date-container">
					<p><a href="<?= SITEURL; ?>/meals/plans/?date=<?= $mealPlanDay->getDateString(); ?>"><?= $mealPlanDay->getDate()->format('D d-M-Y'); ?></a></p>
				</div>
			</div>
<?php
		}
	}
?>
</div>
