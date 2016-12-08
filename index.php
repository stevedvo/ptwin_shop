<?php
	date_default_timezone_set('UTC');
	// opens DB connexion
	require ('../../init_ptwin_shop.php');
?>

<!DOCTYPE html>

<html lang="en">

	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- additional options to content to prevent zooming on mobile devices, maximum-scale=1, user-scalable=no"-->
		<script type="text/javascript" src="jQuery/jquery-1.12.3.min.js"></script>
		<style type="text/css">
			.main-wrapper
			{
				width: 100%;
			}
		</style>
	</head>

	<body>
		<div class="main-wrapper">
			<h1>hello world</h1>
<?php
			$q = "select * from lists";

			$r = mysqli_query($ptwin_shopDB, $q);

			var_dump($r);

			$row = mysqli_fetch_array($r);

			var_dump($row);
?>
		</div>
	</body>

</html>