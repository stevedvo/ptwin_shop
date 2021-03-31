<?php
	$dateArray = $response['dateArray'];
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
<?php
				foreach ($dateArray as $date)
				{
?>
					<div class="row">
						<div class="col-xs-12">
							<p><?= $date->format('l, d F Y'); ?></p>
						</div>
					</div>
<?php
				}
?>
			</div>
		</div>
	</div>
</main>
