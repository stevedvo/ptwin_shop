<?php
	$item = $params['item'];
?>
<div class="row result-item form" data-item_id="<?= $item->getId(); ?>">
	<div class="col-xs-2 button-container">
		<button class="btn btn-danger btn-sm js-remove-item-from-luckyDip">&times;</button>
	</div>
	<div class="col-xs-10 luckydip-item-name-container">
		<a href="<?= SITEURL; ?>/items/edit/<?= $item->getId(); ?>/"><p data-description="<?= $item->getDescription(); ?>"><?= $item->getDescription(); ?></p></a>
	</div>
</div>
