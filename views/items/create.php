<?php
	$itemPrototype = $response['item_prototype'];
	$lists = $response['lists'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div id="add-item" class="form">
					<fieldset>
						<legend>Add Item</legend>
						<label for="description">Description:</label>
						<input id="description" type="text" name="description" placeholder="Required" data-validation="<?= $itemPrototype->getValidation("Description"); ?>" />
						<br/><br/>
						<label for="comments">Comments:</label>
						<input id="comments" type="text" name="comments" data-validation="<?= $itemPrototype->getValidation("Comments"); ?>" />
						<br/><br/>
						<label for="default_qty">Default Qty:</label>
						<input id="default_qty" type="number" name="default-qty" min="1" value="1" data-validation="<?= $itemPrototype->getValidation("DefaultQty"); ?>" />
						<br/><br/>
						<label for="link">Link:</label>
						<input id="link" type="text" name="link" data-validation="<?= $itemPrototype->getValidation("Link"); ?>" />
						<br/><br/>
						<label for="list">List:</label>
						<select id="list" name="list-id" data-validation="<?= $itemPrototype->getValidation("ListId"); ?>">
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
						<br/><br/>
						<input type="submit" class="btn btn-primary js-add-item" value="Add Item" />
					</fieldset>
				</div>
			</div>
		</div>
	</div>
</main>
