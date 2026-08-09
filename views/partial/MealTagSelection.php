<?php
	$tagList = $params['tag_list'];
?>
<select id="addTagToMeal" class="tag-selection" style="width: 100%;">
	<option value="-1"></option>
<?php
	if (is_array($tagList))
	{
		foreach ($tagList as $tagId => $tag)
		{
?>
			<option value="<?= $tag->getId(); ?>"><?= $tag->getName(); ?></option>
<?php
		}
	}
?>
</select>

<script type="text/javascript">
	$("#addTagToMeal").select2(
	{
		placeholder :
		{
			id   : "-1",
			text : "Select a Tag",
		},
		allowClear  : true,
	});
</script>
