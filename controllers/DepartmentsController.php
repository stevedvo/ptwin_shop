<?php
	class DepartmentsController
	{
		private $result;
		private $exception;
		private $departments_service;
		private $items_service;

		public function __construct($result = null, $exception = null)
		{
			$this->result = $result;
			$this->exception = $exception;
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
			include_once('views/departments/index.php');
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

				$this->departments_service->closeConnexion();
				$this->items_service->closeConnexion();
			}

			include_once('views/departments/edit.php');
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

			return $dalResult;
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

			return $dalResult;
		}
	}
