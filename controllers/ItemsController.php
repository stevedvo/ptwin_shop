<?php
	class ItemsController
	{
		private $result;
		private $exception;
		private $items_service;
		private $lists_service;
		private $departments_service;

		public function __construct($result = null, $exception = null)
		{
			$this->result = $result;
			$this->exception = $exception;
			$this->items_service = new ItemsService();
			$this->lists_service = new ListsService();
			$this->departments_service = new DepartmentsService();
		}

		public function Index()
		{
			$dalResult = $this->items_service->getAllItems();
			$all_items = false;

			if (!is_null($dalResult->getResult()))
			{
				$all_items = $dalResult->getResult();
			}

			$this->items_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Manage Items',
				'template'   => 'views/items/index.php',
				'page_data'  => ['all_items' => $all_items]
			];

			renderPage($pageData);
		}

		public function Create()
		{
			$itemPrototype = new Item();
			$dalResult = $this->lists_service->getAllLists();

			if (is_null($dalResult->getException()))
			{
				$lists = $dalResult->getResult();
			}

			$this->lists_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Add New Item',
				'template'   => 'views/items/create.php',
				'page_data'  =>
				[
					'item_prototype' => $itemPrototype,
					'lists'          => $lists
				]
			];

			renderPage($pageData);
		}

		public function addItem($request)
		{
			$item = createItem($request);

			if (!entityIsValid($item))
			{
				return false;
			}

			$dalResult = $this->items_service->getItemByDescription($item->getDescription());

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			if ($dalResult->getResult() instanceof Item)
			{
				return false;
			}

			$dalResult = $this->items_service->addItem($item);
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function Edit($request = null)
		{
			$item = $lists = false;

			if (is_numeric($request))
			{
				$dalResult = $this->items_service->getItemById(intval($request));

				if (!is_null($dalResult->getResult()))
				{
					$item = $dalResult->getResult();
				}
			}

			if ($item)
			{
				$dalResult = $this->lists_service->getAllLists();

				if (!is_null($dalResult->getResult()))
				{
					$lists = $dalResult->getResult();
				}

				$dalResult = $this->departments_service->getAllDepartments();

				if (!is_null($dalResult->getResult()))
				{
					$departments = $dalResult->getResult();
				}
			}

			$this->items_service->closeConnexion();
			$this->lists_service->closeConnexion();
			$this->departments_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Edit Item',
				'template'   => 'views/items/edit.php',
				'page_data'  =>
				[
					'item'            => $item,
					'lists'           => $lists,
					'all_departments' => $departments
				]
			];

			renderPage($pageData);
		}

		public function editItem($request)
		{
			$item = createItem($request);

			if (!entityIsValid($item))
			{
				return false;
			}

			if (is_null($item->getId()))
			{
				return false;
			}

			$dalResult = $this->items_service->getItemById($item->getId());

			if (!$dalResult->getResult() instanceof Item)
			{
				return false;
			}

			$item_update = $dalResult->getResult();

			$item_update->setDescription($item->getDescription());
			$item_update->setComments($item->getComments());
			$item_update->setDefaultQty($item->getDefaultQty());
			$item_update->setListId($item->getListId());
			$item_update->setLink($item->getLink());

			$dalResult = $this->items_service->updateItem($item_update);
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function removeItem($request)
		{
			if (!isset($request['item_id']) || !is_numeric($request['item_id']))
			{
				return false;
			}

			$dalResult = $this->items_service->getItemsByItemId(intval($request['item_id']));

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			$items = $dalResult->getResult();

			if (is_array($items) && sizeof($items) > 0)
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

			$dalResult = $this->items_service->removeItem($item);
			$this->items_service->closeConnexion();
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}
	}
