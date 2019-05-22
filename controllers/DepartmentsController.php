<?php
	class DepartmentsController
	{
		private $departments_service;
		private $items_service;

		public function __construct()
		{
			$this->departments_service = new DepartmentsService();
			$this->items_service = new ItemsService();
		}

		public function Index()
		{
			$deptPrototype = new Department();
			$dalResult = $this->departments_service->getAllDepartments();
			$departments = false;

			if (!is_null($dalResult->getResult()))
			{
				$departments = $dalResult->getResult();
			}

			$this->departments_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Manage Departments',
				'template'   => 'views/departments/index.php',
				'page_data'  =>
				[
					'deptPrototype' => $deptPrototype,
					'departments'   => $departments
				]
			];

			renderPage($pageData);
		}

		public function addDepartment($request)
		{
			$department = createDepartment($request);

			if (!entityIsValid($department))
			{
				return false;
			}

			$dalResult = $this->departments_service->getDepartmentByName($department->getName());

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			if ($dalResult->getResult() instanceof Department)
			{
				return false;
			}

			$dalResult = $this->departments_service->addDepartment($department);
			$this->departments_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function Edit($request = null)
		{
			$department = $all_departments = $all_items = false;

			if (is_numeric($request))
			{
				$dalResult = $this->departments_service->getDepartmentById(intval($request));

				if (!is_null($dalResult->getResult()))
				{
					$department = $dalResult->getResult();
				}

				$dalResult = $this->departments_service->getAllDepartments();

				if (!is_null($dalResult->getResult()))
				{
					$all_departments = $dalResult->getResult();
				}

				$dalResult = $this->items_service->getAllItems();

				if (!is_null($dalResult->getResult()))
				{
					$all_items = $dalResult->getResult();
				}
			}

			$this->departments_service->closeConnexion();
			$this->items_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Edit Department',
				'template'   => 'views/departments/edit.php',
				'page_data'  =>
				[
					'department'      => $department,
					'all_departments' => $all_departments,
					'all_items'       => $all_items
				]
			];

			renderPage($pageData);
		}

		public function addItemToDepartment($request)
		{
			$item = $department = false;

			if (!is_numeric($request['item_id']) || !is_numeric($request['dept_id']))
			{
				return false;
			}

			$dalResult = $this->items_service->getItemById(intval($request['item_id']));

			if (!is_null($dalResult->getResult()))
			{
				$item = $dalResult->getResult();
			}

			if (!$item)
			{
				return false;
			}

			$dalResult = $this->departments_service->getDepartmentById(intval($request['dept_id']));

			if (!is_null($dalResult->getResult()))
			{
				$department = $dalResult->getResult();
			}

			if (!$department)
			{
				return false;
			}

			$dalResult = $this->departments_service->addItemToDepartment($item, $department);
			$this->departments_service->closeConnexion();
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function removeItemsFromDepartment($request)
		{
			$item_ids = [];

			if (!is_array($request['item_ids']) || !is_numeric($request['dept_id']))
			{
				return false;
			}

			foreach ($request['item_ids'] as $item_id)
			{
				if (!is_numeric($item_id))
				{
					return false;
				}

				$item_ids[] = intval($item_id);
			}

			$dalResult = $this->departments_service->removeItemsFromDepartment($item_ids, intval($request['dept_id']));
			$this->departments_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function editDepartment($request)
		{
			$department = createDepartment($request);

			if (!entityIsValid($department))
			{
				return false;
			}

			if (is_null($department->getId()))
			{
				return false;
			}

			$dalResult = $this->departments_service->getDepartmentById($department->getId());

			if (!$dalResult->getResult() instanceof Department)
			{
				return false;
			}

			$dept_update = $dalResult->getResult();

			$dept_update->setName($department->getName());

			$dalResult = $this->departments_service->updateDepartment($dept_update);
			$this->departments_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function removeDepartment($request)
		{
			if (!isset($request['dept_id']) || !is_numeric($request['dept_id']))
			{
				return false;
			}

			$dalResult = $this->items_service->getItemsByDepartmentId(intval($request['dept_id']));

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			$items = $dalResult->getResult();

			if (is_array($items) && sizeof($items) > 0)
			{
				return false;
			}

			$dalResult = $this->departments_service->getDepartmentById(intval($request['dept_id']));

			if (!is_null($dalResult->getResult()))
			{
				$department = $dalResult->getResult();
			}

			if (!$department)
			{
				return false;
			}

			$dalResult = $this->departments_service->removeDepartment($department);
			$this->departments_service->closeConnexion();
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}
	}
