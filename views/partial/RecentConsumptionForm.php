<?php
	$consumption_interval = $params['consumption_interval'];
	$consumption_period = $params['consumption_period'];
	$ajax = $params['ajax'];
	$item_id = isset($params['item_id']) ? $params['item_id'] : null;
?>
<div class="row">
	<div class="recent-consumption-form" <?= !is_null($item_id) ? 'data-item_id="'.$item_id.'"' : ''; ?>>
		<div class="col-xs-6 consumption-interval-container">
			<label>Interval:</label>&nbsp;
			<input type="number" min="1" name="consumption_interval" value="<?= $consumption_interval; ?>" />
		</div>

		<div class="col-xs-6 consumption-period-container">
			<label>Period:</label>&nbsp;
			<select name="consumption_period">
<?php
				foreach (CONSUMPTION_PERIODS as $period)
				{
?>
					<option value="<?= $period; ?>" <?= $period == $consumption_period ? 'selected' : ''; ?>><?= ucwords($period); ?></option>
<?php
				}
?>
			</select>
		</div>

		<div class="col-xs-6 col-xs-offset-6 submit-container text-right">
			<button class="btn btn-warning btn-sm js-update-recent-consumption" data-ajax="<?= $ajax ? 'true' : 'false'; ?>" data-reset="true">Reset</button>
			<button class="btn btn-primary btn-sm js-update-recent-consumption" data-ajax="<?= $ajax ? 'true' : 'false'; ?>">Refresh</button>
		</div>
	</div>
</div>
