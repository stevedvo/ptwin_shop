<?php
	class DepartmentsService
	{
		private $dal;

		public function __construct()
		{
			$this->dal = new DepartmentsDAL();
		}

		public function closeConnexion()
		{
			$this->dal->closeConnexion();
		}

		public function verifyDepartmentRequest($request)
		{
			$department = false;

			if (!is_numeric($request['dept_id']))
			{
				return false;
			}

			$dalResult = $this->dal->getDepartmentById(intval($request['dept_id']));

			if ($dalResult->getResult() instanceof Department)
			{
				$department = $dalResult->getResult();
			}

			if (!$department)
			{
				return false;
			}

			return $department;
		}

		public function addDepartment($department)
		{
			return $this->dal->addDepartment($department);
		}

		public function getAllDepartments()
		{
			return $this->dal->getAllDepartments();
		}

		public function getAllDepartmentsWithItems()
		{
			return $this->dal->getAllDepartmentsWithItems();
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

		public function getPrimaryDepartments()
		{
			return $this->dal->getPrimaryDepartments();
		}
	}
