<?php
	declare(strict_types=1);

	class ItemsController
	{
		private $items_service;
		private $lists_service;
		private $departments_service;
		private $orders_service;
		private $luckyDips_service;
		private $meals_service;
		private ItemsViewModelBuilder $itemsViewModelBuilder;

		public function __construct()
		{
			$this->items_service = new ItemsService();
			$this->lists_service = new ListsService();
			$this->departments_service = new DepartmentsService();
			$this->orders_service = new OrdersService();
			$this->packsizes_service = new PackSizesService();
			$this->luckyDips_service = new LuckyDipsService();
			$this->meals_service = new MealsService();
			$this->itemsViewModelBuilder = new ItemsViewModelBuilder();
		}

		public function Index(array $request, int $consumptionInterval = DEFAULT_CONSUMPTION_INTERVAL, string $consumptionPeriod = DEFAULT_CONSUMPTION_PERIOD) : void
		{
			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => [],
			];

			try
			{
				$viewBy = $items = $collection = null;

				$order = $this->orders_service->getCurrentOrder();
				$itemsInOrder = $order->getItemIdsInOrder();

				if (isset($request['view-by']))
				{
					switch ($request['view-by'])
					{
						case 'department':
						case 'list':
						case 'suggestions':
						case 'muted-suggestions':
							$viewBy = $request['view-by'];
							break;

						case 'primary_dept':
							$viewBy = "primary department";
							break;
					}
				}

				if (is_null($viewBy))
				{
					$items = $this->items_service->getAllItems();

					$pageData =
					[
						'page_title' => 'Manage Items',
						'template'   => 'views/items/index.php',
						'page_data'  => ['all_items' => $items],
					];
				}
				elseif ($viewBy == "suggestions")
				{
					if (isset($request['consumption_interval']) && is_numeric($request['consumption_interval']) && intval($request['consumption_interval']) > 0)
					{
						$consumptionInterval = intval($request['consumption_interval']);
					}

					if (isset($request['consumption_period']))
					{
						if (in_array($request['consumption_period'], CONSUMPTION_PERIODS))
						{
							$consumptionPeriod = $request['consumption_period'];
						}
					}

					$suggestedItems = $this->items_service->getAllSuggestedItems($consumptionInterval, $consumptionPeriod);

					$suggestionsViewModels = $this->itemsViewModelBuilder->createSuggestionsViewModels($suggestedItems, $order);

					$pageData =
					[
						'page_title' => 'Suggested Items',
						'template'   => 'views/items/suggestions.php',
						'page_data'  =>
						[
							'suggested_items'      => $suggestionsViewModels,
							'consumption_interval' => $consumptionInterval,
							'consumption_period'   => $consumptionPeriod,
						],
					];
				}
				elseif ($viewBy == "muted-suggestions")
				{
					$mutedItems = $this->items_service->getAllMutedSuggestedItems();

					$pageData =
					[
						'page_title' => 'Muted Suggestions',
						'template'   => 'views/items/muted-suggestions.php',
						'page_data'  => ['muted_items' => $mutedItems],
					];
				}
				else
				{
					switch ($viewBy)
					{
						case 'department':
							$collection = $this->departments_service->getAllDepartmentsWithItems();
							break;

						case 'list':
							$collection = $this->lists_service->getAllListsWithItems();
							break;

						case 'primary department':
							$collection = $this->departments_service->getPrimaryDepartments();
							break;
					}

					$pageData =
					[
						'page_title' => 'View Items By '.ucwords($viewBy),
						'template'   => 'views/items/view-by-collection.php',
						'page_data'  => ['collection' => $collection],
					];
				}

				$pageData['page_data']['order'] = $order;
				$pageData['page_data']['items_in_order'] = $itemsInOrder;

				$this->items_service->closeConnexion();
				$this->orders_service->closeConnexion();
				$this->departments_service->closeConnexion();
				$this->lists_service->closeConnexion();

				renderPage($pageData);
			}
			catch (Exception $e)
			{
				$pageData['page_data'] = ['message' => $e->getMessage()];

				renderPage($pageData);
			}
		}

		public function Create(array $request) : void
		{
			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => [],
			];

			try
			{
				$item = new Item();

				if (isset($request['description']) && !empty($request['description']))
				{
					$item->setDescription($request['description']);
				}

				$lists = $this->lists_service->getAllLists();
				$packSizes = $this->packsizes_service->getAllPackSizes();

				$this->lists_service->closeConnexion();
				$this->packsizes_service->closeConnexion();

				$pageData =
				[
					'page_title' => 'Add New Item',
					'breadcrumb' =>
					[
						[
							'link' => '/items/',
							'text' => 'Items',
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
						'packSizes' => $packSizes,
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

		public function addItem(array $request) : array // returns either a serialised DalResult Exception or serialised Item
		{
			$dalResult = new DalResult();

			try
			{
				$item = createItem($request);

				if (!entityIsValid($item))
				{
					$dalResult->setException(new Exception("Item is not valid"));

					return $dalResult->jsonSerialize();
				}

				if (!$this->items_service->itemDoesNotExist($item->getDescription()))
				{
					$dalResult->setException(new Exception("Item '".$item->getDescription()."' already exists"));

					return $dalResult->jsonSerialize();
				}

				$item = $this->items_service->addItem($item);

				if (isset($request['add_to_order']) && $request['add_to_order'] != false)
				{
					$order = $this->orders_service->getCurrentOrder();

					$orderItem = new OrderItem();
					$orderItem->setOrderId($order->getId());
					$orderItem->setItemId($item->getId());
					$orderItem->setQuantity($item->getDefaultQty());
					$orderItem->setChecked(0);

					$orderItem = $this->orders_service->addOrderItem($orderItem);

					$this->orders_service->closeConnexion();
				}

				$this->items_service->closeConnexion();

				return $item->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function Edit(?int $request = null) : void
		{
			$lists = $packSizes = $departments = $currentOrder = $currentOrderItems = null;

			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => [],
			];

			try
			{
				$consumptionInterval = DEFAULT_CONSUMPTION_INTERVAL;
				$consumptionPeriod = DEFAULT_CONSUMPTION_PERIOD;

				if (isset($_GET['consumption_interval']) && is_numeric($_GET['consumption_interval']) && intval($_GET['consumption_interval']) > 0)
				{
					$consumptionInterval = intval($_GET['consumption_interval']);
				}

				if (isset($_GET['consumption_period']))
				{
					if (in_array($_GET['consumption_period'], CONSUMPTION_PERIODS))
					{
						$consumptionPeriod = $_GET['consumption_period'];
					}
				}

				$item = $this->items_service->verifyItemRequest(['item_id' => $request]);
				$lists = $this->lists_service->getAllLists();
				$packSizes = $this->packsizes_service->getAllPackSizes();
				$departments = $this->departments_service->getAllDepartments();
				$itemOrders = $this->orders_service->getOrdersByItem($item);

				$item->setOrders($itemOrders);

				$currentOrder = $this->orders_service->getCurrentOrder();
				$currentOrderItems = $currentOrder->getItemIdsInOrder();

				$item->calculateRecentOrders($consumptionInterval, $consumptionPeriod);

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
							'text' => 'Items',
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
						'packsizes'            => $packSizes,
						'consumption_interval' => $consumptionInterval,
						'consumption_period'   => $consumptionPeriod,
						'current_order'        => $currentOrder,
						'current_order_items'  => $currentOrderItems,
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

		public function editItem(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$item_update = createItem($request);

				if (!entityIsValid($item_update))
				{
					$dalResult->setException(new Exception("Item is not valid"));

					return $dalResult->jsonSerialize();
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

				$dalResult->setResult($this->items_service->updateItem($item));

				$this->items_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function addDepartmentToItem(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$item = $this->items_service->verifyItemRequest($request);
				$department = $this->departments_service->verifyDepartmentRequest($request);

				$result = $this->items_service->addDepartmentToItem($department, $item);

				$dalResult->setPartialView(getPartialView("ItemDepartment", ['department' => $department]));

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

		public function removeDepartmentsFromItem(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$deptIds = [];

				$item = $this->items_service->verifyItemRequest($request);

				if (!is_array($request['dept_ids']))
				{
					$dalResult->setException(new Exception("Invalid Dept IDs request"));

					return $dalResult->jsonSerialize();
				}

				foreach ($request['dept_ids'] as $deptId)
				{
					if (!is_numeric($deptId))
					{
						$dalResult->setException(new Exception("Invalid Dept ID: ".$deptId));

						return $dalResult->jsonSerialize();
					}

					$deptIds[] = intval($deptId);
				}

				$success = $this->items_service->removeDepartmentsFromItem($deptIds, $item->getId());

				if (array_search($item->getPrimaryDept(), $deptIds) !== false)
				{
					$item->setPrimaryDept(null);
					$success = $this->items_service->updateItem($item);
				}

				$dalResult->setResult($success);

				$this->items_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function getAllItems(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$items = $this->items_service->getAllItems();
				$itemsJSON = [];

				foreach ($items as $item_id => $item)
				{
					$itemsJSON[$item->getId()] = $item->jsonSerialize();
				}

				$dalResult->setResult($itemsJSON);

				$this->items_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function getAllItemsNotInLuckyDip(array $request) : array
		{
			$dalResult = new DalResult();
			$dalResult->setPartialView("");

			try
			{
				$luckyDip = $this->luckyDips_service->verifyLuckyDipRequest($request);
				$items = $this->items_service->getAllItemsNotInLuckyDip($luckyDip->getId());

				$dalResult->setPartialView(getPartialView("LuckyDipItemSelection", ['item_list' => $items]));

				$this->luckyDips_service->closeConnexion();
				$this->items_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function getAllItemsNotInMeal(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$meal = $this->meals_service->verifyMealRequest($request);
				$items = $this->items_service->getAllItemsNotInMeal($meal->getId());

				$dalResult->setPartialView(getPartialView("MealItemSelection", ['item_list' => $items]));

				$this->meals_service->closeConnexion();
				$this->items_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function quickAddItem(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$item = null;

				if (!isset($request['description']) || empty($request['description']))
				{
					$dalResult->setException(new Exception("Invalid Item description"));

					return $dalResult->jsonSerialize();
				}

				$description = $request['description'];

				if (strpos(strtolower($description), "[luckydip]") !== false)
				{
					$luckyDipName = substr($description, 11);

					$luckyDip = $this->luckyDips_service->getLuckyDipByName($luckyDipName);

					$item = $luckyDip->getRandomItem();
				}
				else
				{
					$item = $this->items_service->getItemByDescription($description);
				}

				if (!($item instanceof Item))
				{
					$item = new Item();
					$item->setDescription($description);

					return $item->jsonSerialize();
				}

				$order = $this->orders_service->getCurrentOrder();

				$orderItem = new OrderItem();
				$orderItem->setOrderId($order->getId());
				$orderItem->setItemId($item->getId());
				$orderItem->setQuantity($item->getDefaultQty());
				$orderItem->setChecked(0);

				$orderItem = $this->orders_service->addOrderItem($orderItem);

				$orderItem->setItem($item);
				$dalResult->setPartialView(getPartialView("CurrentOrderItem", ['order_item' => $orderItem]));

				$this->items_service->closeConnexion();
				$this->orders_service->closeConnexion();

				$dalResult->setResult($orderItem->jsonSerialize());

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function quickEditItem(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				if (!isset($request['description']) || empty($request['description']))
				{
					$dalResult->setException(new Exception("Invalid Item description"));

					return $dalResult->jsonSerialize();
				}

				$description = $request['description'];

				$item = $this->items_service->getItemByDescription($description);

				$this->items_service->closeConnexion();

				$dalResult->setResult($item->jsonSerialize());

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function addItemToCurrentOrder(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$item = $this->items_service->verifyItemRequest($request);
				$order = $this->orders_service->getCurrentOrder();

				if (!$order)
				{
					$dalResult->setException(new Exception("No current Order found"));

					return $dalResult->jsonSerialize();
				}

				$orderItem = new OrderItem();
				$orderItem->setOrderId($order->getId());
				$orderItem->setItemId($item->getId());

				if (isset($request['quantity']) && is_numeric($request['quantity']))
				{
					$orderItem->setQuantity(intval($request['quantity']));
				}
				else
				{
					$orderItem->setQuantity($item->getDefaultQty());
				}

				$orderItem->setChecked(0);

				$orderItem = $this->orders_service->addOrderItem($orderItem);
				$orderItem->setItem($item);

				$this->items_service->closeConnexion();
				$this->orders_service->closeConnexion();

				$dalResult->setResult($orderItem->jsonSerialize());

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function removeItemFromCurrentOrder($request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$orderItem = $this->orders_service->verifyOrderItemRequest($request);
				$success = $this->orders_service->removeOrderItem($orderItem);

				if (!$success)
				{
					$dalResult->setException(new Exception("Error removing Order Item #".$orderItem->getId()));

					return $dalResult->jsonSerialize();
				}

				$dalResult->setResult($success);

				$this->orders_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function setItemPrimaryDepartment(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$item = $this->items_service->verifyItemRequest($request);
				$department = $this->departments_service->verifyDepartmentRequest($request);

				$success = $this->items_service->setItemPrimaryDepartment($department, $item);

				$dalResult->setResult($success);

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

		public function resetPrimaryDepartments(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$departments_lookup = $this->items_service->getItemDepartmentLookupArray();

				foreach ($departments_lookup as $item_id => $dept_id)
				{
					$request =
					[
						'item_id' => $item_id,
						'dept_id' => $dept_id
					];

					$item = $this->items_service->verifyItemRequest($request);
					$department = $this->departments_service->verifyDepartmentRequest($request);

					$success = $this->items_service->setItemPrimaryDepartment($department, $item);
				}

				$this->items_service->closeConnexion();
				$this->departments_service->closeConnexion();

				$dalResult->setResult(true);

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function updateItemMuteSetting(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				if (empty($request['mute_basis']) || !($request['mute_basis'] == "temp" || $request['mute_basis'] == "perm"))
				{
					$dalResult->setException(new Exception("Invalid Mute option"));

					return $dalResult->jsonSerialize();
				}

				$item = $this->items_service->verifyItemRequest($request);

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

				$dalResult->setResult($this->items_service->updateItem($item));

				$this->items_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function getItemsRecentOrderStatistics(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$result =
				[
					'itemDailyConsumptionRecent' => "N/A",
					'itemStockNowRecent' => "N/A",
					'itemStockFutureRecent' => "N/A"
				];

				if (!(isset($request['consumption_interval']) && is_numeric($request['consumption_interval']) && intval($request['consumption_interval']) > 0))
				{
					$dalResult->setException(new Exception("Invalid Consumption Interval"));

					return $dalResult->jsonSerialize();
				}

				if (!(isset($request['consumption_period']) && !empty($request['consumption_period']) && in_array($request['consumption_period'], CONSUMPTION_PERIODS)))
				{
					$dalResult->setException(new Exception("Invalid Consumption Period"));

					return $dalResult->jsonSerialize();
				}

				$item = $this->items_service->verifyItemRequest($request);
				$itemOrders = $this->orders_service->getOrdersByItem($item);

				$item->setOrders($itemOrders);

				$item->calculateRecentOrders(intval($request['consumption_interval']), $request['consumption_period']);

				if ($item->hasOrders())
				{
					$result['itemDailyConsumptionRecent'] = round($item->getDailyConsumptionRecent() * 7, 2);
					$result['itemStockNowRecent'] = $item->getStockLevelPrediction(0, "recent");
					$result['itemStockFutureRecent'] = $item->getStockLevelPrediction(7, "recent");
				}

				$this->items_service->closeConnexion();
				$this->orders_service->closeConnexion();

				$dalResult->setResult($result);

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}
	}
