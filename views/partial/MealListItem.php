<?php
	$meal = $params['item'];
?>
<div class="row form result-item meal-list-item">
	<input type="hidden" name="meal_id" value="<?= $meal->getId(); ?>" />
	<div class="col-xs-6 meal_name-container">
		<input type="text" name="meal_name" placeholder="Required" data-validation="<?= getValidationString($meal, "Name"); ?>" value="<?= $meal->getName(); ?>" />
	</div>

	<div class="col-xs-3 button-container text-right">
		<a class="btn btn-sm btn-primary" href="<?= SITEURL; ?>/meals/edit/<?= $meal->getId(); ?>/">View</a>
	</div>

	<div class="col-xs-3 button-container text-right">
		<button class="btn btn-sm btn-danger js-update-meal">Update</button>
	</div>
</div>
