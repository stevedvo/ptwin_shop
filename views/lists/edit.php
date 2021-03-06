<?php
	$list = $response['list'];
	$all_lists = $response['all_lists'];
	$all_items = $response['all_items'];
?>
<main class="wrapper">
	<div class="container">
<?php
		if (!$list)
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<p>Could not find List / invalid request</p>
				</div>
			</div>
<?php
		}
		else
		{
?>
			<div class="row">
				<div class="col-xs-12">
					<div id="edit-list" class="form list-container">
						<label>List Name:</label>
						<input type="hidden" name="list-id" value="<?= $list->getId(); ?>" />
						<input type="text" name="list-name" placeholder="Required" data-validation="<?= getValidationString($list, "Name"); ?>" value="<?= $list->getName(); ?>" />
						<button class="btn btn-primary btn-sm js-update-list">Update</button>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<h3>Current Items in List</h3>
					<div class="list-items-container results-container" data-list_id="<?= $list->getId(); ?>">
<?php
						if (is_array($list->getItems()) && sizeof($list->getItems()) > 0)
						{
							foreach ($list->getItems() as $item_id => $item)
							{
								echo getPartialView("ListItem", ['item' => $item]);
							}
						}
						else
						{
?>
							<p class="no-results">No Items in this List</p>
							<button class="btn btn-danger btn-sm no-results js-remove-list">Remove List</button>
<?php
						}
?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<h3>Move Selected Item(s) to List</h3>
					<div class="form">
						<div class="row">
							<div class="col-xs-8 list-selection-container">
								<select class="list-selection">
<?php
									if (is_array($all_lists))
									{
										foreach ($all_lists as $newlist_id => $newlist)
										{
											if ($list->getId() != $newlist_id)
											{
?>
												<option data-list_id="<?= $newlist->getId(); ?>"><?= $newlist->getName(); ?></option>
<?php
											}
										}
									}
?>
								</select>
							</div>

							<div class="col-xs-4 move-items-to-list-container">
								<button class="btn btn-primary btn-sm pull-right js-move-items-to-list">Confirm</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<h3>Add Item to List</h3>
					<div class="form">
						<div class="row">
							<div class="col-xs-8 item-selection-container">
								<input type="hidden" name="list-id" value="<?= $list->getId(); ?>" />
								<select id="addItemToList" class="item-selection">
									<option value="-1"></option>
<?php
									if (is_array($all_items))
									{
										foreach ($all_items as $item_id => $item)
										{
											if (is_array($list->getItems()) && !array_key_exists($item_id, $list->getItems()))
											{
?>
												<option data-item_id="<?= $item->getId(); ?>"><?= $item->getDescription(); ?></option>
<?php
											}
										}
									}
?>
								</select>
							</div>

							<div class="col-xs-4 add-item-to-list-container">
								<button class="btn btn-primary btn-sm pull-right js-add-item-to-list">Add to List</button>
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
