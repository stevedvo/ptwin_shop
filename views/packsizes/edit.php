<?php
	$packsize = $response['packsize'];
	// $all_packsizes = $response['all_packsizes'];
	// $all_items = $response['all_items'];
?>
<main class="wrapper">
	<div class="container">
<?php
		if (!$packsize)
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<p>Could not find packsize / invalid request</p>
				</div>
			</div>
<?php
		}
		else
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<div id="edit-packsize" class="form" data-packsize_id="<?= $packsize->getId(); ?>">
						<h3>Edit <?= $packsize->getName(); ?></h3>
						<div class="row">
							<div class="name-container col-xs-12">
								<label for="name">Name:</label>
								<input id="name" type="text" name="name" placeholder="Required" value="<?= $packsize->getName(); ?>" data-validation="<?= getValidationString($packsize, "Name"); ?>" />
							</div>
						</div>

						<div class="row">
							<div class="comments-container col-xs-12">
								<label for="shortname">Short Name:</label>
								<input id="shortname" type="text" name="shortname" value="<?= $packsize->getShortName(); ?>" data-validation="<?= getValidationString($packsize, "ShortName"); ?>" />
							</div>
						</div>
						<input type="submit" class="btn btn-primary js-edit-packsize" value="Edit packsize" />
					</div>
				</div>
			</div>
<?php
		}
?>
	</div>
</main>
