<?php
	class ListsController
	{
		private $lists_service;
		private $items_service;

		public function __construct()
		{
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

			$pageData =
			[
				'page_title' => 'Manage Lists',
				'template'   => 'views/lists/index.php',
				'page_data'  =>
				[
					'list_prototype' => $listPrototype,
					'lists'          => $lists
				]
			];

			renderPage($pageData);
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

		public function Edit($request = null)
		{
			$list = $all_lists = $all_items = false;

			if (is_numeric($request))
			{
				$dalResult = $this->lists_service->getListById(intval($request));

				if (!is_null($dalResult->getResult()))
				{
					$list = $dalResult->getResult();
				}

				$dalResult = $this->lists_service->getAllLists();

				if (!is_null($dalResult->getResult()))
				{
					$all_lists = $dalResult->getResult();
				}

				$dalResult = $this->items_service->getAllItems();

				if (!is_null($dalResult->getResult()))
				{
					$all_items = $dalResult->getResult();
				}
			}

			$this->lists_service->closeConnexion();
			$this->items_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Edit List',
				'template'   => 'views/lists/edit.php',
				'page_data'  =>
				[
					'list'      => $list,
					'all_lists' => $all_lists,
					'all_items' => $all_items
				]
			];

			renderPage($pageData);
			include_once('views/lists/edit.php');
		}

		public function addItemToList($request)
		{
			$item = $list = false;

			if (!is_numeric($request['item_id']) || !is_numeric($request['list_id']))
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

			$dalResult = $this->lists_service->getListById(intval($request['list_id']));

			if (!is_null($dalResult->getResult()))
			{
				$list = $dalResult->getResult();
			}

			if (!$list)
			{
				return false;
			}

			$dalResult = $this->lists_service->addItemToList($item, $list);

			if (!is_null($dalResult->getResult()))
			{
				$dalResult->setPartialView(getPartialView("ListItem", ['item' => $item]));
			}

			$this->lists_service->closeConnexion();
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function editList($request)
		{
			$list = createList($request);

			if (!entityIsValid($list))
			{
				return false;
			}

			if (is_null($list->getId()))
			{
				return false;
			}

			$dalResult = $this->lists_service->getListById($list->getId());

			if (!$dalResult->getResult() instanceof ShopList)
			{
				return false;
			}

			$list_update = $dalResult->getResult();

			$list_update->setName($list->getName());

			$dalResult = $this->lists_service->updateList($list_update);
			$this->lists_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function removeList($request)
		{
			if (!isset($request['list_id']) || !is_numeric($request['list_id']))
			{
				return false;
			}

			$dalResult = $this->items_service->getItemsByListId(intval($request['list_id']));

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			$items = $dalResult->getResult();

			if (is_array($items) && sizeof($items) > 0)
			{
				return false;
			}

			$dalResult = $this->lists_service->getListById(intval($request['list_id']));

			if (!is_null($dalResult->getResult()))
			{
				$list = $dalResult->getResult();
			}

			if (!$list)
			{
				return false;
			}

			$dalResult = $this->lists_service->removeList($list);
			$this->lists_service->closeConnexion();
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function moveItemsToList($request)
		{
			$item_ids = (isset($request['item_ids']) && is_array($request['item_ids'])) ? $request['item_ids'] : null;
			$list_id = (isset($request['list_id']) && is_numeric($request['list_id'])) ? intval($request['list_id']) : null;

			if (is_null($item_ids) || is_null($list_id))
			{
				return false;
			}

			if (sizeof($item_ids) == 0)
			{
				return false;
			}

			$sanitised_ids = [];

			foreach ($item_ids as $item_id)
			{
				$sanitised_ids[] = intval($item_id);
			}

			sort($sanitised_ids);

			$dalResult = $this->items_service->getItemsById($sanitised_ids);

			if (is_null($dalResult->getResult()))
			{
				return false;
			}

			$items = $dalResult->getResult();

			if ($sanitised_ids !== array_keys($items))
			{
				return false;
			}

			$dalResult = $this->lists_service->getListById($list_id);

			if (is_null($dalResult->getResult()))
			{
				return false;
			}

			$list = $dalResult->getResult();

			$dalResult = $this->lists_service->moveItemsToList($items, $list);
			$this->lists_service->closeConnexion();
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}
	}
