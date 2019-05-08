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

		public function addItemToDepartment($item, $department)
		{
			return $this->dal->addItemToDepartment($item, $department);
		}

		public function removeItemsFromDepartment($item_ids, $dept_id)
		{
			return $this->dal->removeItemsFromDepartment($item_ids, $dept_id);
		}
	}
