<?php
	$mealId = $params['mealId'];
	$mealTags = $params['mealTags'];
?>
<div class="meal-tags-container results-container" data-meal_id="<?= $mealId; ?>">
<?php
	if (sizeof($mealTags) < 1)
	{
?>
		<p class="no-results">No Tags on this Meal</p>
<?php
	}
	else
	{
		foreach ($mealTags as $key => $mealTag)
		{
?>
			<div class="row result-item form" data-tag_id="<?= $mealTag->getId(); ?>">
				<div class="col-xs-2 button-container">
					<button class="btn btn-danger btn-sm js-remove-tag-from-meal">&times;</button>
				</div>

				<div class="col-xs-6 meal-tag-name-container">
					<p data-description="<?= $mealTag->getName(); ?>"><a href="<?= SITEURL; ?>/tags/edit/<?= $mealTag->getId(); ?>/"><?= $mealTag->getName(); ?></a></p>
				</div>
			</div>
<?php
		}
	}
?>
</div>
