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

		public function addDepartment($department)
		{
			return $this->dal->addDepartment($department);
		}

		public function getAllDepartments()
		{
			return $this->dal->getAllDepartments();
		}

		public function getDepartmentById($dept_id)
		{
			return $this->dal->getDepartmentById($dept_id);
		}

		public function getDepartmentByName($dept_name)
		{
			return $this->dal->getDepartmentByName($dept_name);
		}

		public function addItemToDepartment($item, $department)
		{
			return $this->dal->addItemToDepartment($item, $department);
		}

		public function removeItemsFromDepartment($item_ids, $dept_id)
		{
			return $this->dal->removeItemsFromDepartment($item_ids, $dept_id);
		}

		public function updateDepartment($department)
		{
			return $this->dal->updateDepartment($department);
		}

		public function removeDepartment($department)
		{
			return $this->dal->removeDepartment($department);
		}
	}
