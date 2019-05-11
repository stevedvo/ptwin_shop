<?php
	class ItemsController
	{
		private $result;
		private $exception;
		private $items_service;
		private $lists_service;

		public function __construct($result = null, $exception = null)
		{
			$this->result = $result;
			$this->exception = $exception;
			$this->items_service = new ItemsService();
			$this->lists_service = new ListsService();
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
			include_once('views/items/index.php');
		}

		public function Create()
		{
			$itemPrototype = new Item();
			$dalResult = $this->lists_service->getAllLists();

			if (is_null($dalResult->getException()))
			{
				$lists = $dalResult->getResult();
			}

			include_once('views/items/create.php');
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
			}

			$this->items_service->closeConnexion();
			$this->lists_service->closeConnexion();

			include_once('views/items/edit.php');
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
