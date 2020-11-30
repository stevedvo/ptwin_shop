<?php
	declare(strict_types=1);

	class ListsController
	{
		private $lists_service;
		private $items_service;

		public function __construct()
		{
			$this->lists_service = new ListsService();
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
				$listPrototype = new ShopList();
				$lists = $this->lists_service->getAllLists();

				$this->lists_service->closeConnexion();

				$pageData =
				[
					'page_title' => 'Manage Lists',
					'template'   => 'views/lists/index.php',
					'page_data'  =>
					[
						'list_prototype' => $listPrototype,
						'lists'          => $lists,
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

		public function Edit(?int $request = null) : void
		{
			$list = $allLists = $allItems = null;

			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => [],
			];

			try
			{
				$list = $this->lists_service->verifyListRequest(['list_id' => $request]);
				$allLists = $this->lists_service->getAllLists();
				$allItems = $this->items_service->getAllItems();

				$this->lists_service->closeConnexion();
				$this->items_service->closeConnexion();

				$pageData =
				[
					'page_title' => 'Edit List',
					'breadcrumb' =>
					[
						[
							'link' => '/lists/',
							'text' => 'Lists',
						],
						[
							'text' => 'Edit'
						],
					],
					'template'   => 'views/lists/edit.php',
					'page_data'  =>
					[
						'list'      => $list,
						'all_lists' => $allLists,
						'all_items' => $allItems,
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

		public function addItemToList(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$item = $this->items_service->verifyItemRequest($request);
				$list = $this->lists_service->verifyListRequest($request);

				$success = $this->lists_service->addItemToList($item, $list);

				if (!$success)
				{
					$dalResult->setException(new Exception("Error adding Item to List"));

					return $dalResult->jsonSerialize();
				}
				
				$dalResult->setPartialView(getPartialView("ListItem", ['item' => $item]));

				$this->lists_service->closeConnexion();
				$this->items_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function editList(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$list_update = createList($request);

				if (!entityIsValid($list_update))
				{
					$dalResult->setException(new Exception("List is not valid"));

					return $dalResult->jsonSerialize();
				}

				$list = $this->lists_service->verifyListRequest($request);

				$list->setName($list_update->getName());

				$dalResult->setResult($this->lists_service->updateList($list));

				$this->lists_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function removeList(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$list = $this->lists_service->verifyListRequest($request);

				if (sizeof($list->getItems()) > 0)
				{
					$dalResult->setException(new Exception("Cannot remove List whilst it has Items in it"));

					return $dalResult->jsonSerialize();
				}

				$dalResult->setResult($this->lists_service->removeList($list));

				$this->lists_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function moveItemsToList(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$item_ids = (isset($request['item_ids']) && is_array($request['item_ids'])) ? $request['item_ids'] : null;

				if (!is_array($item_ids))
				{
					$dalResult->setException(new Exception("Item IDs is not a valid array"));

					return $dalResult->jsonSerialize();
				}

				if (sizeof($item_ids) == 0)
				{
					$dalResult->setException(new Exception("No Items to move"));

					return $dalResult->jsonSerialize();
				}

				$sanitised_ids = [];

				foreach ($item_ids as $item_id)
				{
					$sanitised_id = intval($item_id);

					if ($sanitised_id === 0)
					{
						$dalResult->setException(new Exception($item_id." is not a valid Item ID"));

						return $dalResult->jsonSerialize();
					}

					$sanitised_ids[] = $sanitised_id;
				}

				sort($sanitised_ids);

				$items = $this->items_service->getItemsById($sanitised_ids);

				if ($sanitised_ids !== array_keys($items))
				{
					$dalResult->setException(new Exception("Item IDs in request not matched with Item IDs pulled from DB"));

					return $dalResult->jsonSerialize();
				}

				$list = $this->lists_service->verifyListRequest($request);

				$dalResult->setResult($this->lists_service->moveItemsToList($items, $list));

				$this->lists_service->closeConnexion();
				$this->items_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}
	}
