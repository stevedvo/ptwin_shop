<?php
	$all_items = $params['item_list'];
?>
<select id="addItemToMeal" class="item-selection">
	<option value="-1"></option>
<?php
	if (is_array($all_items))
	{
		foreach ($all_items as $item_id => $item)
		{
?>
			<option value="<?= $item->getId(); ?>"><?= $item->getDescription(); ?></option>
<?php
		}
	}
?>
</select>

<script type="text/javascript">
	$("#addItemToMeal").select2(
	{
		placeholder :
		{
			id   : "-1",
			text : "Select an Item",
		},
		allowClear  : true,
	});
</script>
