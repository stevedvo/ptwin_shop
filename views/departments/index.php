<?php
	$deptPrototype = $response['deptPrototype'];
	$departments = $response['departments'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div id="add-department" class="form">
					<fieldset>
						<legend>Add Department</legend>
						<label for="department-name">Department Name:</label>
						<input id="department-name" type="text" name="department-name" placeholder="Required" data-validation="<?= getValidationString($deptPrototype, "Name"); ?>" />
						<br/><br/>
						<input type="submit" class="btn btn-primary js-add-department" value="Add Department" />
					</fieldset><br/>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<h3>Current Departments</h3>
				<div class="results-container">
<?php
					if (!is_array($departments))
					{
?>
						<p class="no-results">No Departments can be found</p>
<?php
					}
					else
					{
						foreach ($departments as $department_id => $department)
						{
?>
							<div class="row form result-item">
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
<?php
						}
					}
?>
				</div>
			</div>
		</div>
	</div>
</main>
