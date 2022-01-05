<?php
	$meal = $response['meal'];
	$item_list = $response['item_list'];
?>
<main class="wrapper">
	<div class="container">
<?php
		if ($meal->getIsDeleted())
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<h3 class="text-danger">THIS MEAL HAS BEEN DELETED</h3>
					<div class="form">
						<div class="row">
							<div class="col-xs-4 restore-meal-container">
								<button class="btn btn-primary btn-sm js-restore-meal" data-meal_id="<?= $meal->getId(); ?>">Restore Meal</button>
							</div>
						</div>
					</div>
				</div>
			</div>
<?php			
		}
		else
		{
?>
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
					<div class="tab-section no-margin-top">
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#mealItemsTab" role="tab" data-toggle="tab">Items</a></li>
							<li role="presentation" class=""><a href="#mealTagsTab" role="tab" data-toggle="tab">Tags</a></li>
						</ul>

						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="mealItemsTab">
								<div class="row">
									<div class="col-xs-12">
										<h3>Current Items in Meal</h3>
										<div id="MealItemListItems">
<?php
											echo getPartialView("MealItemListitems", ['mealId' => $meal->getId(), 'mealItems' => $meal->getMealItems()]);
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

							<div role="tabpanel" class="tab-pane" id="mealTagsTab">
								<div class="row">
									<div class="col-xs-12">
										<h3>Current Tags</h3>
										<div id="mealTagListItems">
<?php
											echo getPartialView("MealTagListItems", ['mealId' => $meal->getId(), 'mealTags' => $meal->getTags()]);
?>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-xs-12">
										<h3>Add Tag to Meal</h3>
										<div class="form">
											<div class="row">
												<div class="col-xs-8 tag-selection-container">
													<input type="hidden" name="meal_id" value="<?= $meal->getId(); ?>" />
													<div id="mealTagSelection">
<?php
														echo getPartialView("MealTagSelection", ['tag_list' => $tagList]);
?>
													</div>
												</div>

												<div class="col-xs-4 add-tag-to-meal-container">
													<button class="btn btn-primary btn-sm js-add-tag-to-meal">Add to Meal</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
<?php
		}
?>
	</div>
</main>
