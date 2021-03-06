<?php
	$packsize = $params['item'];
?>
<div class="row form result-item packsize-list-item" data-packsize_id="<?= $packsize->getId(); ?>">
	<div class="col-xs-8 packsize-name-container">
		<input type="text" name="packsize_name" placeholder="Required" data-validation="<?= getValidationString($packsize, "Name"); ?>" value="<?= $packsize->getName(); ?>" />
	</div>

	<div class="col-xs-4 packsize-shortname-container">
		<input type="text" name="packsize_short_name" placeholder="Required" data-validation="<?= getValidationString($packsize, "ShortName"); ?>" value="<?= $packsize->getShortName(); ?>" />
	</div>

	<div class="col-xs-4 col-xs-offset-4 button-container text-right">
		<a class="btn btn-sm btn-primary" href="<?= SITEURL; ?>/packsizes/edit/<?= $packsize->getId(); ?>/">View</a>
	</div>

	<div class="col-xs-4 button-container text-right">
		<button class="btn btn-sm btn-danger js-edit-packsize">Update</button>
	</div>
</div>
