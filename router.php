<?php
	preg_match_all("/([a-zA-Z0-9]{1,})/", $_SERVER['REQUEST_URI'], $matches);

	$controller = "Home";
	$action = "Index";
	$request = null;

	switch (sizeof($matches[0]))
	{
		case 1:
			$controller = ucwords($matches[0][0]);
			$action = "Index";
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
		$object->{$action}($request);
	}
	else
	{
		include_once('404.php');
	}

	exit;