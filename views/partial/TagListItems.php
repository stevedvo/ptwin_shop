<?php
	$tags = $params['tags'];

	if (sizeof($tags) < 1)
	{
?>
		<p class="no-results">No Tags can be found</p>
<?php
	}
	else
	{
		foreach ($tags as $id => $tag)
		{
?>
			<div class="row form result-item tag-list-item">
				<input type="hidden" name="tag_id" value="<?= $tag->getId(); ?>" />
				<div class="col-xs-12 tag_name-container">
					<a class="btn btn-sm btn-primary" href="<?= SITEURL; ?>/tags/edit/<?= $tag->getId(); ?>/"><?= $tag->getName(); ?></a>
				</div>
			</div>
<?php
		}
	}
