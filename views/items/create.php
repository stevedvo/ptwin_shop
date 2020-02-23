<?php
	$item = $response['item'];
	$lists = $response['lists'];
	$packsizes = $response['packsizes'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div id="add-item" class="form">
					<h3>Add Item</h3>
					<div class="row">
						<div class="description-container col-xs-12">
							<label for="description">Description:</label>
							<input id="description" type="text" name="description" placeholder="Required" value="<?= $item->getDescription(); ?>" data-validation="<?= getValidationString($item, "Description"); ?>" />
						</div>
					</div>

					<div class="row">
						<div class="comments-container col-xs-12">
							<label for="comments">Comments:</label>
							<input id="comments" type="text" name="comments" data-validation="<?= getValidationString($item, "Comments"); ?>" />
						</div>
					</div>

					<div class="row">
						<div class="default-qty-container col-xs-12">
							<label for="default_qty">Default Qty:</label>
							<input id="default_qty" type="number" name="default-qty" min="1" value="1" data-validation="<?= getValidationString($item, "DefaultQty"); ?>" />
						</div>
					</div>

					<div class="row">
						<div class="link-container col-xs-12">
							<label for="link">Link:</label>
							<input id="link" type="text" name="link" data-validation="<?= getValidationString($item, "Link"); ?>" />
						</div>
					</div>

					<div class="row">
						<div class="list-container col-xs-12">
							<label for="list">List:</label>
							<select id="list" name="list-id" data-validation="<?= getValidationString($item, "ListId"); ?>">
								<option value="" selected disabled>Please select...</option>
<?php
								if (is_array($lists))
								{
									foreach ($lists as $list_id => $list)
									{
?>
										<option value="<?= $list->getId(); ?>"><?= $list->getName(); ?></option>
<?php
									}
								}
?>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="packsize-container col-xs-12">
							<label for="packsize_id">Packsize:</label>
							<select id="packsize_id" name="packsize_id" data-validation="<?= getValidationString($item, "PackSizeId"); ?>">
<?php
								if (is_array($packsizes))
								{
									foreach ($packsizes as $packsize_id => $packsize)
									{
?>
										<option value="<?= $packsize->getId(); ?>"><?= $packsize->getName()." [".$packsize->getShortName()."]"; ?></option>
<?php
									}
								}
?>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="add-to-current-order-container col-xs-12">
							<label for="add-to-current-order">Add to Order:</label>
							<input id="add-to-current-order" type="checkbox" name="add-to-current-order" <?= $item->getDescription() ? 'checked' : ''; ?> />
						</div>
					</div>

					<input type="submit" class="btn btn-primary js-add-item" value="Add Item" />
				</div>
			</div>
		</div>
	</div>
</main>
