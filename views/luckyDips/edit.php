<?php
	$luckyDip = $response['luckyDip'];
	$all_items = $response['all_items'];
?>
<main class="wrapper">
	<div class="container">
<?php
		if (!$luckyDip)
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<p>Could not find Lucky Dip / invalid request</p>
				</div>
			</div>
<?php
		}
		else
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<div id="edit-luckyDip" class="form luckyDip-container">
						<input type="hidden" name="luckyDip_id" value="<?= $luckyDip->getId(); ?>" />
						<div class="row">
							<div class="col-xs-12">
								<label for="luckyDip_name">Lucky Dip Name:</label>
								<input type="text" id="luckyDip_name" name="luckyDip_name" placeholder="Required" data-validation="<?= getValidationString($luckyDip, "Name"); ?>" value="<?= $luckyDip->getName(); ?>" />
								<button class="btn btn-primary btn-sm js-update-luckyDip">Update</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<h3>Current Items in Lucky Dip</h3>
					<div class="luckyDip-items-container results-container" data-luckydip_id="<?= $luckyDip->getId(); ?>">
<?php
						if (sizeof($luckyDip->getItems()) == 0)
						{
?>
							<p class="no-results">No Items in this Lucky Dip</p>
							<button class="btn btn-danger btn-sm no-results js-remove-luckyDip">Remove Lucky Dip</button>
<?php
						}
						else
						{
							foreach ($luckyDip->getItems() as $item_id => $item)
							{
								echo getPartialView("LuckyDipItem", ['item' => $item]);
							}
						}
?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<h3>Add Item to Lucky Dip</h3>
					<div class="form">
						<div class="row">
							<div class="col-xs-8 item-selection-container">
								<input type="hidden" name="luckyDip_id" value="<?= $luckyDip->getId(); ?>" />
								<select class="item-selection">
<?php
									if (is_array($all_items))
									{
										foreach ($all_items as $item_id => $item)
										{
											if (!array_key_exists($item_id, $luckyDip->getItems()))
											{
?>
												<option value="<?= $item->getId(); ?>"><?= $item->getDescription(); ?></option>
<?php
											}
										}
									}
?>
								</select>
							</div>

							<div class="col-xs-4 add-item-to-luckyDip-container">
								<button class="btn btn-primary btn-sm pull-right js-add-item-to-luckyDip">Add to Lucky Dip</button>
							</div>
						</div>
					</div>
				</div>
			</div>
<?php
		}
?>
	</div>
</main>
