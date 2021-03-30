<?php
	declare(strict_types=1);

	class DepartmentsController
	{
		private $departments_service;
		private $items_service;

		public function __construct()
		{
			$this->departments_service = new DepartmentsService();
			$this->items_service = new ItemsService();
		}

		public function Index() : void
		{
			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => [],
			];

			try
			{
				$deptPrototype = new Department();
				$departments = $this->departments_service->getAllDepartments();

				$this->departments_service->closeConnexion();

				$pageData =
				[
					'page_title' => 'Manage Departments',
					'template'   => 'views/departments/index.php',
					'page_data'  =>
					[
						'deptPrototype' => $deptPrototype,
						'departments'   => $departments,
					],
				];

				renderPage($pageData);
			}
			catch (Exception $e)
			{
				$pageData['page_data'] = ['message' => $e->getMessage()];

				renderPage($pageData);
			}
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

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			if (!is_null($dalResult->getResult()))
			{
				$department->setId($dalResult->getResult());
				$dalResult->setPartialView(getPartialView("DepartmentListItem", ['item' => $department]));
			}

			$this->departments_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function Edit(?int $request = null) : void
		{
			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => []
			];

			try
			{
				$department = $this->departments_service->verifyDepartmentRequest(['dept_id' => $request]);
				$allItems = $this->items_service->getAllItems();

				$this->departments_service->closeConnexion();
				$this->items_service->closeConnexion();

				$pageData =
				[
					'page_title' => 'Edit Department',
					'breadcrumb' =>
					[
						[
							'link' => '/departments/',
							'text' => 'Departments'
						],
						[
							'text' => 'Edit'
						]
					],
					'template'   => 'views/departments/edit.php',
					'page_data'  =>
					[
						'department' => $department,
						'all_items'  => $allItems
					]
				];

				renderPage($pageData);
			}
			catch (Exception $e)
			{
				$pageData['page_data'] = ['message' => $e->getMessage()];

				renderPage($pageData);
			}
		}

		public function addItemToDepartment(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$item = $this->items_service->verifyItemRequest($request);
				$department = $this->departments_service->verifyDepartmentRequest($request);

				$success = $this->departments_service->addItemToDepartment($item, $department);

				if (!$success)
				{
					$dalResult->setException(new Exception("Error adding Item to Department"));

					return $dalResult->jsonSerialize();
				}

				$dalResult->setPartialView(getPartialView("DepartmentItem", ['item' => $item]));

				$this->departments_service->closeConnexion();
				$this->items_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
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

		public function editDepartment(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$dept_update = createDepartment($request);

				if (!entityIsValid($dept_update))
				{
					$dalResult->setException(new Exception("Department is not valid"));

					return $dalResult->jsonSerialize();
				}

				$department = $this->departments_service->verifyDepartmentRequest($request);

				$department->setName($dept_update->getName());
				$department->setSeq($dept_update->getSeq());

				$success = $this->departments_service->updateDepartment($department);

				if (!$success)
				{
					$dalResult->setException(new Exception("Error updating Deprtment"));

					return $dalResult->jsonSerialize();
				}

				$dalResult->setResult($success);

				$this->departments_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function removeDepartment(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$department = $this->departments_service->verifyDepartmentRequest($request);

				if (sizeof($department->getItems()) > 0)
				{
					$dalResult->setException(new Exception("Cannot remove Department whilst it has Items in it"));

					return $dalResult->jsonSerialize();
				}

				$dalResult->setResult($this->departments_service->removeDepartment($department));

				$this->departments_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}
	}
