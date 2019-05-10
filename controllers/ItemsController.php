<?php
	class ItemsController
	{
		private $result;
		private $exception;
		private $items_service;

		public function __construct($result = null, $exception = null)
		{
			$this->result = $result;
			$this->exception = $exception;
			$this->items_service = new ItemsService();
		}

		public function Index()
		{
			$dalResult = $this->items_service->getAllItems();
			$items = false;

			if (!is_null($dalResult->getResult()))
			{
				$items = $dalResult->getResult();
			}

			$this->items_service->closeConnexion();
			include_once('views/items/index.php');
		}

		public function Create()
		{
			$itemPrototype = new Item();
			include_once('views/items/create.php');
		}

		public function addItem($request)
		{
			$item = createItem($request);

			if (!entityIsValid($item))
			{
				return false;
			}

			$dalResult = $this->items_service->getItemByName($item->getName());

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			if ($dalResult->getResult() instanceof ShopItem)
			{
				return false;
			}

			$dalResult = $this->items_service->addItem($item);
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function Edit($request = null)
		{
			$item = $all_items = $all_items = false;

			if (is_numeric($request))
			{
				$dalResult = $this->items_service->getItemById(intval($request));

				if (!is_null($dalResult->getResult()))
				{
					$item = $dalResult->getResult();
				}

				$dalResult = $this->items_service->getAllItems();

				if (!is_null($dalResult->getResult()))
				{
					$all_items = $dalResult->getResult();
				}

				$dalResult = $this->items_service->getAllItems();

				if (!is_null($dalResult->getResult()))
				{
					$all_items = $dalResult->getResult();
				}

				$this->items_service->closeConnexion();
				$this->items_service->closeConnexion();
			}

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
