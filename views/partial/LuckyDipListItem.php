<?php
	$luckyDip = $params['item'];
?>
<div class="row form result-item luckyDip-list-item">
	<input type="hidden" name="luckyDip_id" value="<?= $luckyDip->getId(); ?>" />
	<div class="col-xs-12 luckyDip_name-container">
		<a class="btn btn-sm btn-primary" href="<?= SITEURL; ?>/luckydips/edit/<?= $luckyDip->getId(); ?>/"><?= $luckyDip->getName(); ?></a>
	</div>
</div>
