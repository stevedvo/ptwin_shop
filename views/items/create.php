<?php
	$itemPrototype = $response['item_prototype'];
	$lists = $response['lists'];
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
							<input id="description" type="text" name="description" placeholder="Required" data-validation="<?= getValidationString($itemPrototype, "Description"); ?>" />
						</div>
					</div>

					<div class="row">
						<div class="comments-container col-xs-12">
							<label for="comments">Comments:</label>
							<input id="comments" type="text" name="comments" data-validation="<?= getValidationString($itemPrototype, "Comments"); ?>" />
						</div>
					</div>

					<div class="row">
						<div class="default-qty-container col-xs-12">
							<label for="default_qty">Default Qty:</label>
							<input id="default_qty" type="number" name="default-qty" min="1" value="1" data-validation="<?= getValidationString($itemPrototype, "DefaultQty"); ?>" />
						</div>
					</div>

					<div class="row">
						<div class="link-container col-xs-12">
							<label for="link">Link:</label>
							<input id="link" type="text" name="link" data-validation="<?= getValidationString($itemPrototype, "Link"); ?>" />
						</div>
					</div>

					<div class="row">
						<div class="list-container col-xs-12">
							<label for="list">List:</label>
							<select id="list" name="list-id" data-validation="<?= getValidationString($itemPrototype, "ListId"); ?>">
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

					<input type="submit" class="btn btn-primary js-add-item" value="Add Item" />
				</div>
			</div>
		</div>
	</div>
</main>
