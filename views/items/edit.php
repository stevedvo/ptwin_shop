<!DOCTYPE html>
<html lang="en">
	<head>
<?php
		$page_title = "Edit Item";
		include_once('head-section.php');
?>
	</head>

	<body>
<?php
		include_once('header.php');
?>
		<main class="wrapper">
			<div class="container">
<?php
				if (!$item)
				{
?>
					<div class="row">
						<div class="col-xs-12">
							<p>Could not find Item / invalid request</p>
						</div>
					</div>
<?php
				}
				else
				{
?>
					<div class="row">
						<div class="col-xs-12">
							<div id="edit-item" class="form">
								<fieldset>
									<legend>Edit Item</legend>
									<input type="hidden" name="item-id" value="<?= $item->getId(); ?>" />
									<label for="description">Description:</label>
									<input id="description" type="text" name="description" placeholder="Required" value="<?= $item->getDescription(); ?>" data-validation="<?= $item->getValidation("Description"); ?>" />
									<br/><br/>
									<label for="comments">Comments:</label>
									<input id="comments" type="text" name="comments" value="<?= $item->getComments(); ?>" data-validation="<?= $item->getValidation("Comments"); ?>" />
									<br/><br/>
									<label for="default_qty">Default Qty:</label>
									<input id="default_qty" type="number" name="default-qty" min="1" value="<?= $item->getDefaultQty(); ?>" data-validation="<?= $item->getValidation("DefaultQty"); ?>" />
									<br/><br/>
									<label for="total_qty">Total Qty:</label>
									<span id="total_qty"><?= $item->getTotalQty(); ?></span>
									<br/><br/>
									<label for="last_ordered">Last Ordered:</label>
									<span id="last_ordered"><?= !is_null($item->getLastOrdered()) ? $item->getLastOrdered()->format('d-m-Y') : ''; ?></span>
									<br/><br/>
									<label for="link">Link:</label>
									<input id="link" type="text" name="link" value="<?= $item->getLink(); ?>" data-validation="<?= $item->getValidation("Link"); ?>" />
									<br/><br/>
									<label for="list">List:</label>
									<select id="list" name="list-id" data-validation="<?= $item->getValidation("ListId"); ?>">
										<option value="" selected disabled>Please select...</option>
<?php
										if (is_array($lists))
										{
											foreach ($lists as $list_id => $list)
											{
?>
												<option value="<?= $list->getId(); ?>" <?= $list->getId() == $item->getListId() ? 'selected' : ''; ?>><?= $list->getName(); ?></option>
<?php
											}
										}
?>
									</select>
									<br/><br/>
									<input type="submit" class="btn btn-primary js-edit-item" value="Edit Item" />
								</fieldset>
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
