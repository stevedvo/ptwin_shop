<?php
	$mealPlan = $params['mealPlan'];
?>
<div class="calendar-box-header">
	<p class="col-xs-10"><?= $mealPlan->getCalendarHeader(); ?></p>
	<span class="col-xs-2 edit-btn"><i class="far fa-edit"></i></span>
</div>

<div class="calendar-box-body">
	<p class="col-xs-12"><a href="<?= SITEURL; ?>/meals/edit/<?= $mealPlan->getMealId(); ?>"><?= $mealPlan->getMealName(); ?></a></p>
</div>
