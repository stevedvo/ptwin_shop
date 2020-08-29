<?php
	$luckyDip = $response['luckyDip'];
	$all_items = $response['all_items'];
	$lists = $response['lists'];
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

						<div class="form-group">
							<div class="row">
								<label for="luckyDip_name" class="col-sm-3">Lucky Dip Name:</label>
								<div class="col-sm-9">
									<input type="text" id="luckyDip_name" name="luckyDip_name" placeholder="Required" data-validation="<?= getValidationString($luckyDip, "Name"); ?>" value="<?= $luckyDip->getName(); ?>" />
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="row">
								<label for="luckyDip_list" class="col-sm-3">List:</label>
								<div class="col-sm-3">
									<select id="luckyDip_list" name="luckyDip_list">
										<option value="" selected>Please select...</option>
<?php
										if (is_array($lists))
										{
											foreach ($lists as $list_id => $list)
											{
?>
												<option value="<?= $list->getId(); ?>" <?= $list->getId() == $luckyDip->getListId() ? 'selected' : ''; ?>><?= $list->getName(); ?></option>
<?php
											}
										}
?>
									</select>
								</div>
							</div>
						</div>

						<div class="form-group">
							<button class="btn btn-primary btn-sm js-update-luckyDip">Update</button>
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
							<button class="btn btn-danger btn-sm no-results js-remove-luckyDip">Delete Lucky Dip</button>
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
								<div id="LuckyDipItemSelection">
<?php
									echo getPartialView("LuckyDipItemSelection", ['item_list' => $all_items]);
?>
								</div>
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
