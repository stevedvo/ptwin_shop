<?php
	$tagPrototype = $response['tagPrototype'];
	$tags = $response['tags'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div id="addTag" class="form">
					<fieldset>
						<legend>Add Tag</legend>
						<label for="tagName">Tag Name:</label>
						<input id="tagName" type="text" name="tagName" placeholder="Required" data-validation="<?= getValidationString($tagPrototype, "Name"); ?>" />
						<br/><br/>
						<input type="submit" class="btn btn-primary js-add-tag" value="Add Tag" />
					</fieldset><br/>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<h3>Tags</h3>
				<div id="tagsListItems" class="results-container">
<?php
					echo getPartialView("TagListItems", ['tags' => $tags]);
?>
				</div>
			</div>
		</div>
	</div>
</main>
