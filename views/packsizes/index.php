<?php
	$packsizePrototype = $response['packsize_prototype'];
	$packsizes = $response['packsizes'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div id="add-packsize" class="form">
					<fieldset>
						<legend>Add Pack Size</legend>
						<div class="row">
							<label class="col-xs-12" for="packsize-name">Pack Size Name:</label>
							<input class="col-xs-12" id="packsize-name" type="text" name="packsize_name" placeholder="Required" data-validation="<?= getValidationString($packsizePrototype, "Name"); ?>" />
						</div>
						<br />
						<label for="packsize-shortname">Short Name:</label>
						<input id="packsize-shortname" type="text" name="packsize_short_name" placeholder="Required" data-validation="<?= getValidationString($packsizePrototype, "ShortName"); ?>" />
						<br/><br/>
						<input type="submit" class="btn btn-primary js-add-packsize" value="Add Pack Size" />
					</fieldset><br/>
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
