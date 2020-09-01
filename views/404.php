<?php
	$message = isset($response['message']) ? $response['message'] : null;
?>
<main class="wrapper">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
<?php
				if (!is_null($message))
				{
?>
					<p>Error processing request:</p>
					<p><?= $message; ?></p>
<?php
				}
				else
				{
?>
					<p>Page not found.</p>
<?php
				}
?>
			</div>
		</div>
	</div>
</main>
