<?php
	$department = $params['item'];
?>
<div class="row form result-item department-list-item">
	<input type="hidden" name="department-id" value="<?= $department->getId(); ?>" />
	<div class="col-xs-8 department-name-container">
		<input type="text" name="department-name" placeholder="Required" data-validation="<?= getValidationString($department, "Name"); ?>" value="<?= $department->getName(); ?>" />
	</div>

	<div class="col-xs-4 department-seq-container">
		<input type="number" name="seq" min="0" placeholder="Required" data-validation="<?= getValidationString($department, "Seq"); ?>" value="<?= $department->getSeq(); ?>" />
	</div>

	<div class="col-xs-4 col-xs-offset-4 button-container text-right">
		<a class="btn btn-sm btn-primary" href="<?= SITEURL; ?>/departments/edit/<?= $department->getId(); ?>/">View</a>
	</div>

	<div class="col-xs-4 button-container text-right">
		<button class="btn btn-sm btn-danger js-update-department">Update</button>
	</div>
</div>
