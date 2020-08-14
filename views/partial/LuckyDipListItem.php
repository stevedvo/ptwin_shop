<?php
	$luckyDip = $params['item'];
?>
<div class="row form result-item luckyDip-list-item">
	<input type="hidden" name="luckyDip_id" value="<?= $luckyDip->getId(); ?>" />
	<div class="col-xs-6 luckyDip_name-container">
		<input type="text" name="luckyDip_name" placeholder="Required" data-validation="<?= getValidationString($luckyDip, "Name"); ?>" value="<?= $luckyDip->getName(); ?>" />
	</div>

	<div class="col-xs-3 button-container text-right">
		<a class="btn btn-sm btn-primary" href="<?= SITEURL; ?>/luckydips/edit/<?= $luckyDip->getId(); ?>/">View</a>
	</div>

	<div class="col-xs-3 button-container text-right">
		<button class="btn btn-sm btn-danger js-update-luckyDip">Update</button>
	</div>
</div>
