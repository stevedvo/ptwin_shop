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
								<label for="packsize_name">Name:</label>
								<input id="packsize_name" type="text" name="packsize_name" placeholder="Required" value="<?= $packsize->getName(); ?>" data-validation="<?= getValidationString($packsize, "Name"); ?>" />
							</div>

							<div class="shortname-container col-xs-12">
								<label for="packsize_short_name">Short Name:</label>
								<input id="packsize_short_name" type="text" name="packsize_short_name" placeholder="Required" value="<?= $packsize->getShortName(); ?>" data-validation="<?= getValidationString($packsize, "ShortName"); ?>" />
							</div>
						</div>
						<br />
						<input type="submit" class="btn btn-primary js-edit-packsize" value="Edit packsize" />
					</div>
				</div>
			</div>
<?php
		}
?>
	</div>
</main>
