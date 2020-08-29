<?php
	$mealPrototype = $response['mealPrototype'];
	$meals = $response['meals'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div id="add-Meal" class="form">
					<fieldset>
						<legend>Add Meal</legend>
						<label for="meal_name">Meal Name:</label>
						<input id="meal_name" type="text" name="meal_name" placeholder="Required" data-validation="<?= getValidationString($mealPrototype, "Name"); ?>" />
						<br/><br/>
						<input type="submit" class="btn btn-primary js-add-meal" value="Add Meal" />
					</fieldset><br/>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<h3>Meals</h3>
				<div class="results-container">
<?php
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
							echo getPartialView("MealListItem", ['item' => $meal]);
						}
					}
?>
				</div>
			</div>
		</div>
	</div>
</main>
