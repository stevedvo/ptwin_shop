<?php
	$department = $params['department'];
	$isPrimary = isset($params['isPrimary']) ? $params['isPrimary'] : false;
?>
<div class="row result-item form <?= $isPrimary ? 'primary-dept' : ''; ?>" data-dept_id="<?= $department->getId(); ?>">
	<div class="col-xs-8 department-name-container">
		<p data-description="<?= $department->getName(); ?>"><?= $department->getName(); ?></p>
	</div>

	<div class="col-xs-4 button-container set-primary">
		<button class="btn btn-primary btn-sm pull-right js-set-primary-dept">Set As Primary</button>
	</div>

	<div class="col-xs-4 col-xs-offset-4 button-container select">
		<button class="btn btn-success btn-sm pull-right js-select-item">Select</button>
	</div>

	<div class="col-xs-4 button-container unselect">
		<button class="btn btn-danger btn-sm pull-right js-unselect-item">Unselect</button>
	</div>
</div>
