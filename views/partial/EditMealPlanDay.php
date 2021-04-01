<?php
	$model = $params['model'];
?>
<div class="form-group">
	<div class="row">
		<label class="col-xs-12" for="mealId">Meal</label>
		<div class="col-xs-12">
			<select class="form-control" id="mealId" name="meal_id">
<?php
				foreach ($model->getMeals() as $meal)
				{
?>
					<option value="<?= $meal->getValue(); ?>"><?= $meal->getText(); ?></option>
<?php
				}
?>
			</select>
		</div>
	</div>
</div>

<input type="hidden" name="meal_plan_date" value="<?= $model->getDateString(); ?>" />
<input type="hidden" name="meal_plan_day_id" value="<?= $model->getId(); ?>" />
<input type="hidden" name="order_item_status" value="<?= $model->getOrderItemStatus(); ?>" />
