<?php
	class ListsController
	{
		private $result;
		private $exception;
		private $lists_service;
		private $items_service;

		public function __construct($result = null, $exception = null)
		{
			$this->result = $result;
			$this->exception = $exception;
			$this->lists_service = new ListsService();
			$this->items_service = new ItemsService();
		}

		public function Index()
		{
			$listPrototype = new ShopList();
			$dalResult = $this->lists_service->getAllLists();
			$lists = false;

			if (!is_null($dalResult->getResult()))
			{
				$lists = $dalResult->getResult();
			}

			$this->lists_service->closeConnexion();
			include_once('views/lists/index.php');
		}

		public function addList($request)
		{
			$list = createList($request);

			if (!entityIsValid($list))
			{
				return false;
			}

			$dalResult = $this->lists_service->getListByName($list->getName());

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			if ($dalResult->getResult() instanceof ShopList)
			{
				return false;
			}

			$dalResult = $this->lists_service->addList($list);
			$this->lists_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		// public function Edit($request = null)
		// {
		// 	$list = $all_lists = $all_items = false;

		// 	if (is_numeric($request))
		// 	{
		// 		$dalResult = $this->lists_service->getListById(intval($request));

		// 		if (!is_null($dalResult->getResult()))
		// 		{
		// 			$list = $dalResult->getResult();
		// 		}

		// 		$dalResult = $this->lists_service->getAllLists();

		// 		if (!is_null($dalResult->getResult()))
		// 		{
		// 			$all_lists = $dalResult->getResult();
		// 		}

		// 		$dalResult = $this->items_service->getAllItems();

		// 		if (!is_null($dalResult->getResult()))
		// 		{
		// 			$all_items = $dalResult->getResult();
		// 		}

		// 		$this->lists_service->closeConnexion();
		// 		$this->items_service->closeConnexion();
		// 	}

		// 	include_once('views/lists/edit.php');
		// }

		// public function addItemToList($request)
		// {
		// 	$item = $list = false;

		// 	if (!is_numeric($request['item_id']) || !is_numeric($request['list_id']))
		// 	{
		// 		return false;
		// 	}

		// 	$dalResult = $this->items_service->getItemById(intval($request['item_id']));

		// 	if (!is_null($dalResult->getResult()))
		// 	{
		// 		$item = $dalResult->getResult();
		// 	}

		// 	if (!$item)
		// 	{
		// 		return false;
		// 	}

		// 	$dalResult = $this->lists_service->getListById(intval($request['list_id']));

		// 	if (!is_null($dalResult->getResult()))
		// 	{
		// 		$list = $dalResult->getResult();
		// 	}

		// 	if (!$list)
		// 	{
		// 		return false;
		// 	}

		// 	$dalResult = $this->lists_service->addItemToList($item, $list);
		// 	$this->lists_service->closeConnexion();
		// 	$this->items_service->closeConnexion();

		// 	return $dalResult->jsonSerialize();
		// }

		// public function removeItemsFromList($request)
		// {
		// 	$item_ids = [];

		// 	if (!is_array($request['item_ids']) || !is_numeric($request['list_id']))
		// 	{
		// 		return false;
		// 	}

		// 	foreach ($request['item_ids'] as $item_id)
		// 	{
		// 		if (!is_numeric($item_id))
		// 		{
		// 			return false;
		// 		}

		// 		$item_ids[] = intval($item_id);
		// 	}

		// 	$dalResult = $this->lists_service->removeItemsFromList($item_ids, intval($request['list_id']));
		// 	$this->lists_service->closeConnexion();

		// 	return $dalResult->jsonSerialize();
		// }

		// public function removeList($request)
		// {
		// 	if (!isset($request['list_id']) || !is_numeric($request['list_id']))
		// 	{
		// 		return false;
		// 	}

		// 	$dalResult = $this->items_service->getItemsByListId(intval($request['list_id']));

		// 	if (!is_null($dalResult->getException()))
		// 	{
		// 		return false;
		// 	}

		// 	$items = $dalResult->getResult();

		// 	if (is_array($items) && sizeof($items) > 0)
		// 	{
		// 		return false;
		// 	}

		// 	$dalResult = $this->lists_service->getListById(intval($request['list_id']));

		// 	if (!is_null($dalResult->getResult()))
		// 	{
		// 		$list = $dalResult->getResult();
		// 	}

		// 	if (!$list)
		// 	{
		// 		return false;
		// 	}

		// 	$dalResult = $this->lists_service->removeList($list);
		// 	$this->lists_service->closeConnexion();
		// 	$this->items_service->closeConnexion();

		// 	return $dalResult->jsonSerialize();
		// }
	}
