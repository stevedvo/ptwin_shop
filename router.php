<?php
	preg_match_all("/([a-zA-Z0-9]{1,})/", substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "?") ?: strlen($_SERVER['REQUEST_URI'])), $matches);

	$controller = "Home";
	$action = "Index";
	$request = isset($_GET['id']) ? intval($_GET['id']) : $_GET;
	$found = false;

	switch (sizeof($matches[0]))
	{
		case 1:
			$controller = ucwords($matches[0][0]);
			break;
		case 2:
			$controller = ucwords($matches[0][0]);
			$action = ucwords($matches[0][1]);
			break;
		case 3:
			$controller = ucwords($matches[0][0]);
			$action = ucwords($matches[0][1]);
			$request = ucwords($matches[0][2]);
			break;
	}

	$controller = $controller."Controller";

	if (class_exists($controller))
	{
		$object = new $controller;

		if (method_exists($object, $action))
		{
			$found = true;
			$object->{$action}($request);
		}
	}

	if (!$found)
	{
		$pageData =
		[
			'page_title' => 'Not Found',
			'template'   => 'views/404.php',
			'page_data'  => []
		];

		renderPage($pageData);
	}

	exit;