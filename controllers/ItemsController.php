<?php
	class ItemsController
	{
		private $items_service;
		private $lists_service;
		private $departments_service;
		private $orders_service;

		public function __construct()
		{
			$this->items_service = new ItemsService();
			$this->lists_service = new ListsService();
			$this->departments_service = new DepartmentsService();
			$this->orders_service = new OrdersService();
			$this->packsizes_service = new PackSizesService();
		}

		public function Index($request, $consumption_interval = DEFAULT_CONSUMPTION_INTERVAL, $consumption_period = DEFAULT_CONSUMPTION_PERIOD)
		{
			$view_by = $all_items = $order = $items_in_order = $collection = false;

			if (isset($request['view-by']))
			{
				switch ($request['view-by'])
				{
					case 'department':
						$view_by = "department";
						break;

					case 'list':
						$view_by = "list";
						break;

					case 'primary_dept':
						$view_by = "primary department";
						break;

					case 'suggestions':
						$view_by = "suggestions";
						break;

					case 'muted-suggestions':
						$view_by = "muted-suggestions";
						break;
				}
			}

			if (!$view_by)
			{
				$dalResult = $this->items_service->getAllItems();

				if (!is_null($dalResult->getResult()))
				{
					$all_items = $dalResult->getResult();
				}

				$pageData =
				[
					'page_title' => 'Manage Items',
					'template'   => 'views/items/index.php',
					'page_data'  => ['all_items' => $all_items]
				];
			}
			elseif ($view_by == "suggestions")
			{
				if (isset($request['consumption_interval']) && is_numeric($request['consumption_interval']) && intval($request['consumption_interval']) > 0)
				{
					$consumption_interval = intval($request['consumption_interval']);
				}

				if (isset($request['consumption_period']))
				{
					if (in_array($request['consumption_period'], CONSUMPTION_PERIODS))
					{
						$consumption_period = $request['consumption_period'];
					}
				}

				$suggested_items = $this->items_service->getAllSuggestedItems($consumption_interval, $consumption_period);

				$pageData =
				[
					'page_title' => 'Suggested Items',
					'template'   => 'views/items/suggestions.php',
					'page_data'  =>
					[
						'suggested_items'      => $suggested_items,
						'consumption_interval' => $consumption_interval,
						'consumption_period'   => $consumption_period
					]
				];
			}
			elseif ($view_by == "muted-suggestions")
			{
				$muted_items = $this->items_service->getAllMutedSuggestedItems();

				$pageData =
				[
					'page_title' => 'Muted Suggestions',
					'template'   => 'views/items/muted-suggestions.php',
					'page_data'  => ['muted_items' => $muted_items]
				];
			}
			else
			{
				switch ($view_by)
				{
					case 'department':
						$dalResult = $this->departments_service->getAllDepartmentsWithItems();
						break;

					case 'list':
						$dalResult = $this->lists_service->getAllListsWithItems();
						break;

					case 'primary department':
						$dalResult = $this->departments_service->getPrimaryDepartments();
						break;
				}

				if (!is_null($dalResult->getResult()))
				{
					$collection = $dalResult->getResult();
				}

				$pageData =
				[
					'page_title' => 'View Items By '.ucwords($view_by),
					'template'   => 'views/items/view-by-collection.php',
					'page_data'  => ['collection' => $collection]
				];
			}

			$order = $this->orders_service->getCurrentOrder();

			if ($order)
			{
				$items_in_order = $order->getItemIdsInOrder();
			}

			$pageData['page_data']['order'] = $order;
			$pageData['page_data']['items_in_order'] = $items_in_order;

			$this->items_service->closeConnexion();
			$this->orders_service->closeConnexion();
			$this->departments_service->closeConnexion();
			$this->lists_service->closeConnexion();

			renderPage($pageData);
		}

		public function Create($request)
		{
			$item = new Item();

			if (isset($request['description']) && !empty($request['description']))
			{
				$item->setDescription($request['description']);
			}

			$dalResult = $this->lists_service->getAllLists();

			if (is_null($dalResult->getException()))
			{
				$lists = $dalResult->getResult();
			}

			$dalResult = $this->packsizes_service->getAllPackSizes();

			if (!is_null($dalResult->getResult()))
			{
				$packsizes = $dalResult->getResult();
			}

			$this->lists_service->closeConnexion();
			$this->packsizes_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Add New Item',
				'breadcrumb' =>
				[
					[
						'link' => '/items/',
						'text' => 'Items'
					],
					[
						'text' => 'Create'
					]
				],
				'template'   => 'views/items/create.php',
				'page_data'  =>
				[
					'item' 	    => $item,
					'lists'     => $lists,
					'packsizes' => $packsizes
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

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			$item_id = $dalResult->getResult();
			$item->setId($item_id);

			if (isset($request['add_to_order']) && $request['add_to_order'] != false)
			{
				$order = $this->orders_service->getCurrentOrder();

				if (!$order)
				{
					return false;
				}

				$order_item = new OrderItem();
				$order_item->setOrderId($order->getId());
				$order_item->setItemId($item->getId());
				$order_item->setQuantity($item->getDefaultQty());
				$order_item->setChecked(0);

				$dalResult = $this->orders_service->addOrderItem($order_item);

				if (!$dalResult->getResult())
				{
					return false;
				}

				$dalResult->getResult()->setItem($item);

				$this->items_service->closeConnexion();
				$this->orders_service->closeConnexion();

				return $dalResult->getResult()->jsonSerialize();
			}

			$this->items_service->closeConnexion();

			return $item->jsonSerialize();
		}

		public function Edit($request = null)
		{
			$lists = $packsizes = $departments = $currentOrder = $currentOrderItems = null;

			$consumption_interval = DEFAULT_CONSUMPTION_INTERVAL;
			$consumption_period = DEFAULT_CONSUMPTION_PERIOD;

			if (isset($_GET['consumption_interval']) && is_numeric($_GET['consumption_interval']) && intval($_GET['consumption_interval']) > 0)
			{
				$consumption_interval = intval($_GET['consumption_interval']);
			}

			if (isset($_GET['consumption_period']))
			{
				if (in_array($_GET['consumption_period'], CONSUMPTION_PERIODS))
				{
					$consumption_period = $_GET['consumption_period'];
				}
			}

			$item = $this->items_service->verifyItemRequest(['item_id' => $request]);

			if ($item)
			{
				$dalResult = $this->lists_service->getAllLists();

				if (!is_null($dalResult->getResult()))
				{
					$lists = $dalResult->getResult();
				}

				$dalResult = $this->packsizes_service->getAllPackSizes();

				if (!is_null($dalResult->getResult()))
				{
					$packsizes = $dalResult->getResult();
				}

				$dalResult = $this->departments_service->getAllDepartments();

				if (!is_null($dalResult->getResult()))
				{
					$departments = $dalResult->getResult();
				}

				$dalResult = $this->orders_service->getOrdersByItem($item);

				if (!is_null($dalResult->getResult()))
				{
					$item->setOrders($dalResult->getResult());
				}

				$currentOrder = $this->orders_service->getCurrentOrder();

				if ($currentOrder)
				{
					$currentOrderItems = $currentOrder->getItemIdsInOrder();
				}

				$item->calculateRecentOrders($consumption_interval, $consumption_period);
			}

			$this->items_service->closeConnexion();
			$this->lists_service->closeConnexion();
			$this->departments_service->closeConnexion();
			$this->orders_service->closeConnexion();
			$this->packsizes_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Edit Item',
				'breadcrumb' =>
				[
					[
						'link' => '/items/',
						'text' => 'Items'
					],
					[
						'text' => 'Edit'
					]
				],
				'template'   => 'views/items/edit.php',
				'page_data'  =>
				[
					'item'                 => $item,
					'lists'                => $lists,
					'all_departments'      => $departments,
					'packsizes'            => $packsizes,
					'consumption_interval' => $consumption_interval,
					'consumption_period'   => $consumption_period,
					'current_order'        => $currentOrder,
					'current_order_items'  => $currentOrderItems
				]
			];

			renderPage($pageData);
		}

		public function editItem($request)
		{
			$item_update = createItem($request);

			if (!entityIsValid($item_update))
			{
				return false;
			}

			$item = $this->items_service->verifyItemRequest($request);

			$item->setDescription($item_update->getDescription());
			$item->setComments($item_update->getComments());
			$item->setDefaultQty($item_update->getDefaultQty());
			$item->setListId($item_update->getListId());
			$item->setLink($item_update->getLink());
			$item->setPackSizeId($item_update->getPackSizeId());
			$item->setMuteTemp($item_update->getMuteTemp());
			$item->setMutePerm($item_update->getMutePerm());

			$dalResult = $this->items_service->updateItem($item);
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

		public function addDepartmentToItem($request)
		{
			$item = $this->items_service->verifyItemRequest($request);
			$department = $this->departments_service->verifyDepartmentRequest($request);

			if (!$item || !$department)
			{
				return false;
			}

			$dalResult = $this->items_service->addDepartmentToItem($department, $item);

			if (!is_null($dalResult->getResult()))
			{
				$dalResult->setPartialView(getPartialView("ItemDepartment", ['department' => $department]));
			}

			$this->departments_service->closeConnexion();
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function removeDepartmentsFromItem($request)
		{
			$dept_ids = [];

			$item = $this->items_service->verifyItemRequest($request);

			if (!$item)
			{
				return false;
			}

			if (!is_array($request['dept_ids']))
			{
				return false;
			}

			foreach ($request['dept_ids'] as $dept_id)
			{
				if (!is_numeric($dept_id))
				{
					return false;
				}

				$dept_ids[] = intval($dept_id);
			}

			$dalResult = $this->items_service->removeDepartmentsFromItem($dept_ids, $item->getId());

			if (!is_null($dalResult->getResult()))
			{
				if (array_search($item->getPrimaryDept(), $dept_ids) !== false)
				{
					$item->setPrimaryDept(null);
					$dalResult = $this->items_service->updateItem($item);
				}
			}

			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function getAllItems($request)
		{
			$dalResult = $this->items_service->getAllItems();

			if (is_array($dalResult->getResult()))
			{
				$items = [];

				foreach ($dalResult->getResult() as $item_id => $item)
				{
					$items[$item->getId()] = $item->jsonSerialize();
				}

				$dalResult->setResult($items);
			}

			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function quickAddItem($request)
		{
			if (!isset($request['description']) || empty($request['description']))
			{
				return false;
			}

			$dalResult = $this->items_service->getItemByDescription($request['description']);

			if (!is_null($dalResult->getResult()))
			{
				$item = $dalResult->getResult();
			}

			if (!$item)
			{
				$item = new Item();
				$item->setDescription($request['description']);
				return $item->jsonSerialize();
			}

			$order = $this->orders_service->getCurrentOrder();

			if (!$order)
			{
				return false;
			}

			$order_item = new OrderItem();
			$order_item->setOrderId($order->getId());
			$order_item->setItemId($item->getId());
			$order_item->setQuantity($item->getDefaultQty());
			$order_item->setChecked(0);

			$dalResult = $this->orders_service->addOrderItem($order_item);

			if (!$dalResult->getResult())
			{
				return false;
			}

			$dalResult->getResult()->setItem($item);
			$dalResult->setPartialView(getPartialView("CurrentOrderItem", ['order_item' => $dalResult->getResult()]));

			$this->items_service->closeConnexion();
			$this->orders_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function quickEditItem($request)
		{
			if (!isset($request['description']) || empty($request['description']))
			{
				return false;
			}

			$dalResult = $this->items_service->getItemByDescription($request['description']);

			if (!is_null($dalResult->getResult()))
			{
				$item = $dalResult->getResult();
			}

			if (!$item)
			{
				return false;
			}

			$this->items_service->closeConnexion();

			return $item->jsonSerialize();
		}

		public function addItemToCurrentOrder($request)
		{
			$item = $this->items_service->verifyItemRequest($request);

			if (!$item)
			{
				return false;
			}

			$order = $this->orders_service->getCurrentOrder();

			if (!$order)
			{
				return false;
			}

			$order_item = new OrderItem();
			$order_item->setOrderId($order->getId());
			$order_item->setItemId($item->getId());
			$order_item->setQuantity($item->getDefaultQty());
			$order_item->setChecked(0);

			$dalResult = $this->orders_service->addOrderItem($order_item);

			if (!$dalResult->getResult())
			{
				return false;
			}

			$dalResult->getResult()->setItem($item);

			$this->items_service->closeConnexion();
			$this->orders_service->closeConnexion();

			return $dalResult->getResult()->jsonSerialize();
		}

		public function removeItemFromCurrentOrder($request)
		{
			if (!isset($request['order_item_id']) || !is_numeric($request['order_item_id']))
			{
				return false;
			}

			$order_item = false;
			$dalResult = $this->orders_service->getOrderItemById(intval($request['order_item_id']));

			if (!is_null($dalResult->getResult()))
			{
				$order_item = $dalResult->getResult();
			}

			if (!$order_item)
			{
				return false;
			}

			$dalResult = $this->orders_service->removeOrderItem($order_item);
			$this->orders_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function setItemPrimaryDepartment($request)
		{
			$item = $this->items_service->verifyItemRequest($request);
			$department = $this->departments_service->verifyDepartmentRequest($request);

			if (!$item || !$department)
			{
				return false;
			}

			$dalResult = $this->items_service->setItemPrimaryDepartment($department, $item);
			$this->departments_service->closeConnexion();
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function resetPrimaryDepartments($request)
		{
			$dalResult = $this->items_service->getItemDepartmentLookupArray();

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			$departments_lookup = $dalResult->getResult();

			if (!is_array($departments_lookup))
			{
				return false;
			}

			foreach ($departments_lookup as $item_id => $dept_id)
			{
				$request =
				[
					'item_id' => $item_id,
					'dept_id' => $dept_id
				];

				$item = $this->items_service->verifyItemRequest($request);
				$department = $this->departments_service->verifyDepartmentRequest($request);

				if (!$item || !$department)
				{
					return false;
				}

				$dalResult = $this->items_service->setItemPrimaryDepartment($department, $item);

				if (!is_null($dalResult->getException()))
				{
					return false;
				}
			}

			$this->items_service->closeConnexion();
			$this->departments_service->closeConnexion();

			return true;
		}

		public function updateItemMuteSetting($request)
		{
			if (!isset($request['item_id']) || !isset($request['mute_basis']))
			{
				return false;
			}

			if (empty($request['mute_basis']) || !($request['mute_basis'] == "temp" || $request['mute_basis'] == "perm"))
			{
				return false;
			}

			$item = $this->items_service->verifyItemRequest($request);

			if (!$item)
			{
				return false;
			}

			$new_setting = isset($request['unmute']) ? 0 : 1;

			switch ($request['mute_basis'])
			{
				case 'temp':
					$item->setMuteTemp($new_setting);
					break;
				case 'perm':
					$item->setMutePerm($new_setting);
					break;
			}

			$dalResult = $this->items_service->updateItem($item);
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function getItemsRecentOrderStatistics($request)
		{
			if (!(isset($request['consumption_interval']) && is_numeric($request['consumption_interval']) && intval($request['consumption_interval']) > 0))
			{
				return false;
			}

			if (!(isset($request['consumption_period']) && !empty($request['consumption_period']) && in_array($request['consumption_period'], CONSUMPTION_PERIODS)))
			{
				return false;
			}

			$item = $this->items_service->verifyItemRequest($request);

			if (!$item)
			{
				return false;
			}

			$dalResult = $this->orders_service->getOrdersByItem($item);

			if (!is_null($dalResult->getResult()))
			{
				$item->setOrders($dalResult->getResult());
			}

			$item->calculateRecentOrders(intval($request['consumption_interval']), $request['consumption_period']);

			$result =
			[
				'itemDailyConsumptionRecent' => "N/A",
				'itemStockNowRecent' => "N/A",
				'itemStockFutureRecent' => "N/A"
			];

			if ($item->hasOrders())
			{
				$result['itemDailyConsumptionRecent'] = round($item->getDailyConsumptionRecent() * 7, 2);
				$result['itemStockNowRecent'] = $item->getStockLevelPrediction(0, "recent");
				$result['itemStockFutureRecent'] = $item->getStockLevelPrediction(7, "recent");
			}

			$this->items_service->closeConnexion();
			$this->orders_service->closeConnexion();

			return $result;
		}
	}
