<?php
	class DepartmentsService
	{
		private $dal;

		public function __construct()
		{
			$this->dal = new ShopDAL();
		}

		public function closeConnexion()
		{
			$this->dal->closeConnexion();
		}

		public function getAllDepartments()
		{
			return $this->dal->getAllDepartments();
		}

		public function getDepartmentById($dept_id)
		{
			return $this->dal->getDepartmentById($dept_id);
		}
	}
