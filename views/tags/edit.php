<?php
	$tag = $response['tag'];
	$mealList = $response['meal_list'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div id="editTag" class="form tag-container">
					<input type="hidden" name="tag_id" value="<?= $tag->getId(); ?>" />
					<div class="form-group">
						<div class="row">
							<label for="tag_name" class="col-sm-3">Tag Name:</label>
							<div class="col-sm-9">
								<input type="text" id="tag_name" name="tag_name" placeholder="Required" data-validation="<?= getValidationString($tag, "Name"); ?>" value="<?= $tag->getName(); ?>" />
							</div>
						</div>
					</div>

					<div class="form-group">
						<button class="btn btn-primary btn-sm js-update-tag-name">Update</button>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<h3>Current Meals with Tag:</h3>
				<div id="tagMealListItems">
<?php
					echo getPartialView("TagMealListItems", ['tagId' => $tag->getId(), 'tagMeals' => $tag->getMeals()]);
?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<h3>Add Meal to Tag:</h3>
				<div class="form">
					<div class="row">
						<div class="col-xs-8 meal-selection-container">
							<input type="hidden" name="tag_id" value="<?= $tag->getId(); ?>" />
							<div id="tagMealSelection">
<?php
								echo getPartialView("TagMealSelection", ['meal_list' => $mealList]);
?>
							</div>
						</div>

						<div class="col-xs-4 add-meal-to-tag-container">
							<button class="btn btn-primary btn-sm js-add-meal-to-tag">Add to Tag</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
