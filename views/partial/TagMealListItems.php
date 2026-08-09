<?php
	$tagId = $params['tagId'];
	$tagMeals = $params['tagMeals'];
?>
<div class="tag-meals-container results-container" data-tag_id="<?= $tagId; ?>">
<?php
	if (sizeof($tagMeals) < 1)
	{
?>
		<p class="no-results">No Meals with this Tag</p>
<?php
	}
	else
	{
		foreach ($tagMeals as $key => $tagMeal)
		{
?>
			<div class="row result-item form" data-meal_id="<?= $tagMeal->getId(); ?>">
				<div class="col-xs-2 button-container">
					<button class="btn btn-danger btn-sm js-remove-meal-from-tag">&times;</button>
				</div>

				<div class="col-xs-6 tag-meal-name-container">
					<p data-description="<?= $tagMeal->getName(); ?>"><a href="<?= SITEURL; ?>/meals/edit/<?= $tagMeal->getId(); ?>/"><?= $tagMeal->getName(); ?></a></p>
				</div>
			</div>
<?php
		}
	}
?>
</div>
