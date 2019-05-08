<?php
	if (isset($_POST['action']) && isset($_POST['request']))
	{
		require_once("load.php");

		$action = $_POST['action'];
		$request = $_POST['request'];

		if (isset($_POST['controller']))
		{
			$controller = $_POST['controller']."Controller";

			if (class_exists($controller))
			{
				$object = new $controller;
				$result = $object->{$action}($request);
			}
		}
		elseif (function_exists($action))
		{
			$result = $action($request);
		}

		echo json_encode($result);
		exit;
	}
