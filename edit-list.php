<!DOCTYPE html>
<html lang="en">
	<head>
<?php
		$page_title = "Edit List";
		include_once('head-section.php');
?>
	</head>

	<body>
<?php
		include_once('header.php');

		if (!isset($_GET['id']) || !is_numeric($_GET['id']))
		{
			$list = false;
		}
		else
		{
			$list = getListById($_GET['id']);
			$all_lists = getAllLists();
			$all_items = getAllItems();
		}
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
							<div class="list-container">
								<label>List Name:</label>
								<input type="hidden" name="list-id" value="<?= $list->getId(); ?>" />
								<input type="text" name="list-name" placeholder="Required" data-validation="<?= $list->getValidation("Name"); ?>" value="<?= $list->getName(); ?>" />
								<button class="btn btn-primary js-update-list">Update</button>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12">
							<h3>Current Items in List</h3>
							<div class="list-items-container" data-list_id="<?= $list->getId(); ?>">
<?php
								if (is_array($list->getItems()) && sizeof($list->getItems()) > 0)
								{
									foreach ($list->getItems() as $item_id => $item)
									{
?>
										<p data-item_id="<?= $item->getId(); ?>" data-description="<?= $item->getDescription(); ?>"><?= $item->getDescription(); ?><span class="btn btn-danger btn-sm js-select-item">Select</span><span class="btn btn-danger btn-sm js-unselect-item">Unselect</span></p>
<?php
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
								<select>
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
								<button class="btn btn-primary btn-sm js-move-items-to-list">Confirm</button>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12">
							<h3>Add Item to List</h3>
							<div class="form">
								<input type="hidden" name="list-id" value="<?= $list->getId(); ?>" />
								<select class="item-selection">
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
								<button class="btn btn-primary btn-sm js-add-item-to-list">Add to List</button>
							</div>
						</div>
					</div>
<?php
				}
?>
			</div>
		</main>
<?php
		include_once('footer.php');
?>
	</body>
</html>
