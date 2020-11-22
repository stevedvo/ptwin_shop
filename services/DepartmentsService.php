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

		public function verifyDepartmentRequest(array $request) : Department
		{
			try
			{
				$department = null;

				if (!is_numeric($request['dept_id']))
				{
					throw new Exception("Invalid Department ID");
				}

				return $this->getDepartmentById(intval($request['dept_id']));
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function addDepartment($department)
		{
			return $this->dal->addDepartment($department);
		}

		public function getAllDepartments() : array
		{
			try
			{
				$departments = $this->dal->getAllDepartments();

				if (!is_array($departments))
				{
					throw new Exception("Departments not found");
				}

				return $departments;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getAllDepartmentsWithItems() : array
		{
			try
			{
				$departments = $this->dal->getAllDepartmentsWithItems();

				if (!is_array($departments))
				{
					throw new Exception("Departments not found");
				}

				return $departments;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getDepartmentById(int $dept_id) : Department
		{
			try
			{
				$department = $this->dal->getDepartmentById($dept_id);

				if (!($department instanceof Department))
				{
					throw new Exception("Department not found");
				}

				return $department;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getDepartmentByName($dept_name)
		{
			return $this->dal->getDepartmentByName($dept_name);
		}

		public function addItemToDepartment(Item $item, Department $department) : bool
		{
			try
			{
				return $this->dal->addItemToDepartment($item, $department);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function removeItemsFromDepartment($item_ids, $dept_id)
		{
			return $this->dal->removeItemsFromDepartment($item_ids, $dept_id);
		}

		public function updateDepartment(Department $department) : bool
		{
			try
			{
				return $this->dal->updateDepartment($department);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function removeDepartment(Department $department) : bool
		{
			try
			{
				$success = $this->dal->removeDepartment($department);

				if (!$success)
				{
					throw new Exception("Unable to remove Department")
				}

				return $success;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getPrimaryDepartments() : array
		{
			try
			{
				$departments = $this->dal->getPrimaryDepartments();

				if (!is_array($departments))
				{
					throw new Exception("Primary Departments not found");
				}

				return $departments;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
	}
