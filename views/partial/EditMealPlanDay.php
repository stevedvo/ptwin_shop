<?php
	$model = $params['model'];
?>
<meta name="reloadPartial" content="<?= $model->getDateString(); ?>" />

<div class="form-group">
	<div class="row">
		<label class="col-xs-12" for="mealIncludeTagsFilter">Include Tags</label>
		<div class="col-xs-12">
			<select class="form-control" id="mealIncludeTagsFilter" name="meal_include_tag_filters[]" multiple="multiple" style="width: 100%;">
<?php
				foreach ($model->getTags() as $tag)
				{
?>
					<option value="<?= $tag->getValue(); ?>" <?= in_array($tag->getValue(), $model->getDefaultIncludeTagIds()) ? "selected" : ""; ?>><?= $tag->getText(); ?></option>
<?php
				}
?>
			</select>
		</div>
	</div>
</div>

<div class="form-group">
	<div class="row">
		<label class="col-xs-12" for="mealExcludeTagsFilter">Exclude Tags</label>
		<div class="col-xs-12">
			<select class="form-control" id="mealExcludeTagsFilter" name="meal_exclude_tag_filters[]" multiple="multiple" style="width: 100%;">
<?php
				foreach ($model->getTags() as $tag)
				{
?>
					<option value="<?= $tag->getValue(); ?>" <?= in_array($tag->getValue(), $model->getDefaultExcludeTagIds()) ? "selected" : ""; ?>><?= $tag->getText(); ?></option>
<?php
				}
?>
			</select>
		</div>
	</div>
</div>

<div class="form-group">
	<div class="row">
		<label class="col-xs-12" for="mealId">Meal</label>
		<div class="col-xs-12">
			<select class="form-control" id="mealId" name="meal_id" style="width: 100%;">
				<option value="-1"></option>
<?php
				foreach ($model->getMeals() as $meal)
				{
?>
					<option value="<?= $meal->getValue(); ?>" <?= $meal->getValue() == $model->getMealId() ? "selected" : ""; ?> <?= $meal->getDataAttributesString(); ?>><?= $meal->getText(); ?></option>
<?php
				}
?>
			</select>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<button id="randomMealSelector" class="btn btn-success btn-sm">Random</button>
	</div>
</div>

<input type="hidden" name="meal_plan_date" value="<?= $model->getDateString(); ?>" />
<input type="hidden" name="meal_plan_day_id" value="<?= $model->getId(); ?>" />
<input type="hidden" name="order_item_status" value="<?= $model->getOrderItemStatus(); ?>" />
