<?php
	date_default_timezone_set('UTC');
	$sanitised_request = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "?") ?: strlen($_SERVER['REQUEST_URI']));
	$request_array = explode("/", $sanitised_request);

	if (isset($request_array[1]))
	{
		if ($request_array[1] == "ptwin_shop_dev" || $request_array[1] == "ptwin_shop")
		{
			define("SITEURL", "/".$request_array[1]);
			$sanitised_request = substr($sanitised_request, strlen(SITEURL));
		}
		else
		{
			define("SITEURL", "");
		}
	}

	require_once('../../init_ptwin_shop.php');
	require_once('constants.php');
	require_once('controllers.php');
	require_once('dal.php');
	require_once('models.php');
	require_once('functions.php');
	require_once('services.php');
