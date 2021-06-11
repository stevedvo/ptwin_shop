<?php
	$tags = $params['items'];

	if (sizeof($tags) < 1)
	{
?>
		<p class="no-results">No Tags can be found</p>
<?php
	}
	else
	{
?>
		<table class="table table-bordered table-striped">
			<tbody>
<?php
				foreach ($tags as $id => $tag)
				{
?>
					<tr class="result-item tag-list-item">
						<td><a href="<?= SITEURL; ?>/tags/edit/<?= $tag->getId(); ?>/"><?= $tag->getName(); ?></a></td>
					</tr>
<?php
				}
?>
			</tbody>
		</table>
<?php
	}
