<?php
	date_default_timezone_set('UTC');
	// opens DB connexion
	if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false)
	{
		require_once('../../init_ptwin_shop_dev.php');
	}
	else
	{
		require_once('../../init_ptwin_shop.php');
	}

	require_once('functions.php');
	