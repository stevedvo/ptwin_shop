<?php
	declare(strict_types=1);

	class LuckyDipsController
	{
		private $luckyDips_service;
		private $items_service;

		public function __construct()
		{
			$this->luckyDips_service = new LuckyDipsService();
			$this->items_service = new ItemsService();
		}

		public function Index()
		{
			$luckyDipPrototype = new LuckyDip();
			$dalResult = $this->luckyDips_service->getAllLuckyDips();
			$luckyDips = false;

			if (!is_null($dalResult->getResult()))
			{
				$luckyDips = $dalResult->getResult();
			}

			$this->luckyDips_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Manage Lucky Dips',
				'template'   => 'views/luckyDips/index.php',
				'page_data'  =>
				[
					'luckyDipPrototype' => $luckyDipPrototype,
					'luckyDips'         => $luckyDips
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
					'department'      => $department,
					'all_departments' => $all_departments,
					'all_items'       => $all_items
				]
			];

			renderPage($pageData);
		}

		public function addItemToDepartment($request)
		{
			$item = $this->items_service->verifyItemRequest($request);
			$department = $this->items_service->verifyDepartmentRequest($request);

			if (!$item || !$department)
			{
				return false;
			}

			$dalResult = $this->departments_service->addItemToDepartment($item, $department);

			if (!is_null($dalResult->getResult()))
			{
				$dalResult->setPartialView(getPartialView("DepartmentItem", ['item' => $item]));
			}

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

			$dept_update = $this->departments_service->verifyDepartmentRequest($request);

			if (!$dept_update)
			{
				return false;
			}

			$dept_update->setName($department->getName());
			$dept_update->setSeq($department->getSeq());

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
