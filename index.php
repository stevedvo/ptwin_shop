<?php
	date_default_timezone_set('UTC');
	// opens DB connexion
	require ('../../init_ptwin_shop.php');
?>

<!DOCTYPE html>

<html lang="en">

	<head>
<?php
		$page_title = "Peatwin Shopping";
		include ('head-section.php');
?>
	</head>

	<body>
<?php
		include ('header.php');
?>
		<main class="wrapper">
<?php
			$q = "select * from lists";

			$r = mysqli_query($ptwin_shopDB, $q);

			var_dump($r);

			$row = mysqli_fetch_array($r);

			var_dump($row);
?>
		</main>
	</body>

</html>