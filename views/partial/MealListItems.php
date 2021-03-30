<?php
	$meals = $params['items'];

	if (sizeof($meals) < 1)
	{
?>
		<p class="no-results">No Meals can be found</p>
<?php
	}
	else
	{
		foreach ($meals as $id => $meal)
		{
?>
			<div class="row form result-item meal-list-item">
				<input type="hidden" name="meal_id" value="<?= $meal->getId(); ?>" />
				<div class="col-xs-12 meal_name-container">
					<a class="btn btn-sm btn-primary" href="<?= SITEURL; ?>/meals/edit/<?= $meal->getId(); ?>/"><?= $meal->getName(); ?></a>
				</div>
			</div>
<?php
		}
	}
