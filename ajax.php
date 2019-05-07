<?php
	if (isset($_POST['action']) && isset($_POST['request']))
	{
		require_once("load.php");

		$action = $_POST['action'];
		$request = $_POST['request'];

		if (function_exists($action))
		{
			$result = $action($request);

			echo json_encode($result);
			exit;
		}
	}