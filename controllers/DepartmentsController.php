<?php
	class DepartmentsController
	{
		private $result;
		private $exception;

		public function __construct($result = null, $exception = null)
		{
			$this->result = $result;
			$this->exception = $exception;
		}

		public function Index()
		{
			include_once('views/departments/index.php');
		}

		public function Edit($request = null)
		{
			var_dump("dept edit");
			var_dump($request);
		}
	}
