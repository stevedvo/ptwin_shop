<?php
	$luckyDipPrototype = $response['luckyDipPrototype'];
	$luckyDips = $response['luckyDips'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div id="add-LuckyDip" class="form">
					<fieldset>
						<legend>Add Lucky Dip</legend>
						<label for="luckyDip_name">Lucky Dip Name:</label>
						<input id="luckyDip_name" type="text" name="luckyDip_name" placeholder="Required" data-validation="<?= getValidationString($luckyDipPrototype, "Name"); ?>" />
						<br/><br/>
						<input type="submit" class="btn btn-primary js-add-luckyDip" value="Add Lucky Dip" />
					</fieldset><br/>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<h3>Lucky Dips</h3>
				<div class="results-container">
<?php
					if (!is_array($luckyDips))
					{
?>
						<p class="no-results">No Lucky Dips can be found</p>
<?php
					}
					else
					{
						foreach ($luckyDips as $id => $luckyDip)
						{
							echo getPartialView("LuckyDipListItem", ['item' => $luckyDip]);
						}
					}
?>
				</div>
			</div>
		</div>
	</div>
</main>
