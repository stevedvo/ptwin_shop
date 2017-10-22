<?php
	date_default_timezone_set('UTC');
	// opens DB connexion
	require ('../../init_ptwin_shop.php');
	require ('functions.php');

	$q = "SELECT item_id,description,link FROM items";

	$r = mysqli_query($ptwin_shopDB, $q);

	if ($r && $r->num_rows > 0)
	{
		$num_rows = $r->num_rows;
		for ($i=0; $i<$num_rows; $i++)
		{
			$row = mysqli_fetch_array($r, MYSQLI_ASSOC);

			var_dump($row);

			$item_id = $row['item_id'];
			$search_term = rawurlencode($row['description']);

			$q = "UPDATE `items` SET `link` = 'https://www.tesco.com/groceries/product/search/default.aspx?searchBox=$search_term&newSort=true&search=Search' WHERE `items`.`item_id` = $item_id";

			// echo "$q<br/>";

			// $r2 = mysqli_query($ptwin_shopDB, $q);
		}
	}
?>