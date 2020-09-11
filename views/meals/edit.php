<?php
	$meal = $response['meal'];
	$item_list = $response['item_list'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div id="edit-meal" class="form meal-container">
					<input type="hidden" name="meal_id" value="<?= $meal->getId(); ?>" />

					<div class="form-group">
						<div class="row">
							<label for="meal_name" class="col-sm-3">Meal Name:</label>
							<div class="col-sm-9">
								<input type="text" id="meal_name" name="meal_name" placeholder="Required" data-validation="<?= getValidationString($meal, "Name"); ?>" value="<?= $meal->getName(); ?>" />
							</div>
						</div>
					</div>

					<div class="form-group">
						<button class="btn btn-primary btn-sm js-update-meal-name">Update</button>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<h3>Current Items in Meal</h3>
				<div class="meal-items-container results-container" data-meal_id="<?= $meal->getId(); ?>">
<?php
					if (sizeof($meal->getMealItems()) == 0)
					{
?>
						<p class="no-results">No Items in this Meal</p>
						<button class="btn btn-danger btn-sm no-results js-remove-meal">Delete Meal</button>
<?php
					}
					else
					{
						foreach ($meal->getMealItems() as $meal_item_id => $meal_item)
						{
							echo getPartialView("MealItem", ['item' => $meal_item]);
						}
					}
?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<h3>Add Item to Meal</h3>
				<div class="form">
					<div class="row">
						<div class="col-xs-8 item-selection-container">
							<input type="hidden" name="meal_id" value="<?= $meal->getId(); ?>" />
							<div id="MealItemSelection">
<?php
								echo getPartialView("MealItemSelection", ['item_list' => $item_list]);
?>
							</div>
						</div>

						<div class="col-xs-4 add-item-to-meal-container">
							<button class="btn btn-primary btn-sm pull-right js-add-item-to-meal">Add to Meal</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
