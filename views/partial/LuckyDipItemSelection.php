<?php
	$all_items = $params['item_list'];
?>
<select class="item-selection">
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
