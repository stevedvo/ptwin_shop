<?php
	$packsizePrototype = $response['packsize_prototype'];
	$packsizes = $response['packsizes'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div id="add-packsize" class="form">
					<h3>Add Pack Size</h3>
					<div class="row">
						<div class="name-container col-xs-12">
							<label for="packsize_name">Name:</label>
							<input id="packsize_name" type="text" name="packsize_name" placeholder="Required" data-validation="<?= getValidationString($packsizePrototype, "Name"); ?>" />
						</div>

						<div class="shortname-container col-xs-12">
							<label for="packsize_short_name">Short Name:</label>
							<input id="packsize_short_name" type="text" name="packsize_short_name" placeholder="Required" data-validation="<?= getValidationString($packsizePrototype, "ShortName"); ?>" />
						</div>
					</div>
					<br />
					<input type="submit" class="btn btn-primary js-add-packsize" value="Add Pack Size" />
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<h3>Current Pack Sizes</h3>
				<div class="results-container">
<?php
					if (!is_array($packsizes))
					{
?>
						<p class="no-results">No Pack Sizes can be found</p>
<?php
					}
					else
					{
						foreach ($packsizes as $packsize_id => $packsize)
						{
							echo getPartialView("PackSizeListItem", ['item' => $packsize]);
						}
					}
?>
				</div>
			</div>
		</div>
	</div>
</main>
