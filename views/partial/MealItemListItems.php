<?php
	$mealId = $params['mealId'];
	$mealItems = $params['mealItems'];
?>
<div class="meal-items-container results-container" data-meal_id="<?= $mealId; ?>">
<?php
	if (sizeof($mealItems) == 0)
	{
?>
		<p class="no-results">No Items in this Meal</p>
		<button class="btn btn-danger btn-sm no-results js-remove-meal">Delete Meal</button>
<?php
	}
	else
	{
		foreach ($mealItems as $key => $mealItem)
		{
?>
			<div class="row result-item form" data-item_id="<?= $mealItem->getId(); ?>">
				<div class="col-xs-2 button-container">
					<button class="btn btn-danger btn-sm js-remove-item-from-meal">&times;</button>
				</div>

				<div class="col-xs-6 meal-item-name-container">
					<p data-description="<?= $mealItem->getItemDescription(); ?>"><a href="<?= SITEURL; ?>/items/edit/<?= $mealItem->getItemId(); ?>/"><?= $mealItem->getItemDescription(); ?></a></p>
				</div>

				<div class="col-xs-2 meal-item-quantity-container">
					<input type="number" name="mealItem[<?= $mealItem->getId(); ?>][quantity]" data-validation="<?= getValidationString($mealItem, "Quantity"); ?>" value="<?= $mealItem->getQuantity(); ?>" />
				</div>

				<div class="col-xs-2 button-container">
					<button class="btn btn-primary btn-sm js-update-mealitem">Update</button>
				</div>
			</div>
<?php
		}
	}
?>
</div>
