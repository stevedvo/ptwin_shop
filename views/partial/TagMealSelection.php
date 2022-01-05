<?php
	$mealList = $params['meal_list'];
?>
<select id="addMealToTag" class="meal-selection" style="width: 100%;">
	<option value="-1"></option>
<?php
	if (is_array($mealList))
	{
		foreach ($mealList as $mealId => $meal)
		{
?>
			<option value="<?= $meal->getId(); ?>"><?= $meal->getName(); ?></option>
<?php
		}
	}
?>
</select>

<script type="text/javascript">
	$("#addMealToTag").select2(
	{
		placeholder :
		{
			id   : "-1",
			text : "Select a Meal",
		},
		allowClear  : true,
	});
</script>
