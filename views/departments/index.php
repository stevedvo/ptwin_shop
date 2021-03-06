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
							echo getPartialView("DepartmentListItem", ['item' => $department]);
						}
					}
?>
				</div>
			</div>
		</div>
	</div>
</main>
