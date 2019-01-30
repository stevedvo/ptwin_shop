<?php
	include_once('site_init.php');

	if (isset($_POST['action']))
	{
		$action = $_POST['action'];
		$request = $_POST['request'];

		$response = $action($request);
		echo json_encode($response);
		exit();
	}
