<?php
	$item = $params['item'];
?>
<div class="row result-item form" data-item_id="<?= $item->getId(); ?>">
	<div class="col-xs-8 department-name-container">
		<a href="/items/edit/<?= $item->getId(); ?>/"><p data-description="<?= $item->getDescription(); ?>"><?= $item->getDescription(); ?></p></a>
	</div>

	<div class="col-xs-4 col-xs-offset-4 button-container select">
		<button class="btn btn-success btn-sm pull-right js-select-item">Select</button>
	</div>

	<div class="col-xs-4 button-container unselect">
		<button class="btn btn-danger btn-sm pull-right js-unselect-item">Unselect</button>
	</div>
</div>
