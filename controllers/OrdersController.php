<?php
	class OrdersController
	{
		private $orders_service;
		private $lists_service;
		private $departments_service;
		private $items_service;
		private $luckyDips_service;

		public function __construct()
		{
			$this->orders_service = new OrdersService();
			$this->lists_service = new ListsService();
			$this->departments_service = new DepartmentsService();
			$this->items_service = new ItemsService();
			$this->luckyDips_service = new LuckyDipsService();
		}

		public function updateOrder(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$order_update = createOrder($request);

				if (!entityIsValid($order_update))
				{
					$dalResult->setException(new Exception("Invalid Order"));

					return $dalResult->jsonSerialize();
				}

				$order = $this->orders_service->getOrderById($order_update->getId());

				if (!($order instanceof Order))
				{
					$dalResult->setException(new Exception("Cannot find Order with ID ".$order_update->getId()));

					return $dalResult->jsonSerialize();
				}

				$order->setDateOrdered($order_update->getDateOrdered());

				$success = $this->orders_service->updateOrder($order);

				if (!$success)
				{
					$dalResult->setException(new Exception("Error updating Order #".$order->getId()));

					return $dalResult->jsonSerialize();
				}

				$this->orders_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function updateOrderItem($request)
		{
			$order_item_update = createOrderItem($request);

			if (!entityIsValid($order_item_update))
			{
				return false;
			}

			$dalResult = $this->orders_service->getOrderItemById($order_item_update->getId());

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			$order_item = $dalResult->getResult();

			if (!$order_item)
			{
				return false;
			}

			$order_item->setQuantity($order_item_update->getQuantity());

			$dalResult = $this->orders_service->updateOrderItem($order_item);

			$this->orders_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function checkOrderItem($request)
		{
			$order_item = $this->orders_service->verifyOrderItemRequest($request);

			if (!$order_item)
			{
				return false;
			}

			$order_item_update = createOrderItem($request);
			$order_item->setChecked($order_item_update->getChecked());

			if (!entityIsValid($order_item))
			{
				return false;
			}

			$dalResult = $this->orders_service->updateOrderItem($order_item);

			$this->orders_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function removeOrderItem(array $request) : string
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

				$this->orders_service->closeConnexion();

				$dalResult->setResult(true);

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function removeAllOrderItemsFromOrder($request)
		{
			if (!isset($request['order_id']) || !is_numeric($request['order_id']))
			{
				return false;
			}

			$dalResult = $this->orders_service->getOrderById(intval($request['order_id']));

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			$order = $dalResult->getResult();

			if (!$order)
			{
				return false;
			}

			$dalResult = $this->orders_service->removeAllOrderItemsFromOrder($order);

			$this->orders_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function Index()
		{
			$orders = false;

			$dalResult = $this->orders_service->getAllOrders();

			if (!is_null($dalResult->getResult()))
			{
				$orders = $dalResult->getResult();
			}

			$this->orders_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Manage Orders',
				'template'   => 'views/orders/index.php',
				'page_data'  => ['orders' => $orders]
			];

			renderPage($pageData);
		}

		public function View($request = null)
		{
			$departments = false;
			$order = $this->orders_service->verifyOrderRequest(['order_id' => $request]);

			if ($order)
			{
				$dalResult = $this->departments_service->getAllDepartments();

				if (!is_null($dalResult->getResult()))
				{
					$departments = $dalResult->getResult();
				}
			}

			$this->orders_service->closeConnexion();
			$this->departments_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'View Order',
				'breadcrumb' =>
				[
					[
						'link' => '/orders/',
						'text' => 'Orders'
					],
					[
						'text' => 'View'
					]
				],
				'template'   => 'views/orders/view.php',
				'page_data'  =>
				[
					'order'       => $order,
					'departments' => $departments
				]
			];

			renderPage($pageData);
		}

		public function Print($request = null)
		{
			$departments = false;
			$order = $this->orders_service->verifyOrderRequest(['order_id' => $request]);

			if ($order)
			{
				$dalResult = $this->departments_service->getAllDepartments();

				if (!is_null($dalResult->getResult()))
				{
					$departments = $dalResult->getResult();
				}
			}

			$this->orders_service->closeConnexion();
			$this->departments_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Print Order',
				'template'   => 'views/orders/print.php',
				'page_data'  =>
				[
					'order'       => $order,
					'departments' => $departments
				]
			];

			renderPrint($pageData);
		}

		public function Edit($request = null)
		{
			$departments = false;
			$order = $this->orders_service->verifyOrderRequest(['order_id' => $request]);

			if ($order)
			{
				$dalResult = $this->departments_service->getAllDepartments();

				if (!is_null($dalResult->getResult()))
				{
					$departments = $dalResult->getResult();
				}
			}

			$this->orders_service->closeConnexion();
			$this->departments_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Edit Order',
				'breadcrumb' =>
				[
					[
						'link' => '/orders/',
						'text' => 'Orders'
					],
					[
						'text' => 'Edit'
					]
				],
				'template'   => 'views/orders/edit.php',
				'page_data'  =>
				[
					'order'       => $order,
					'departments' => $departments
				]
			];

			renderPage($pageData);
		}

		public function confirmOrder(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$order = $this->orders_service->verifyOrderRequest($request);

				$order->setDateOrdered(new DateTime);

				$success = $this->orders_service->updateOrder($order);

				if (!$success)
				{
					$dalResult->setException(new Exception("Error confirming Order #".$order->getId()));

					return $dalResult->jsonSerialize();
				}

				$success = $this->items_service->resetMuteTemps();

				if (!$success)
				{
					$dalResult->setException(new Exception("Error resetting MuteTemps"));

					return $dalResult->jsonSerialize();
				}

				$this->orders_service->closeConnexion();
				$this->items_service->closeConnexion();

				$dalResult->setResult(true);

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function addListToOrder(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$list = $this->lists_service->verifyListRequest($request);

				if (!is_array($list->getItems()) || sizeof($list->getItems()) == 0)
				{
					$dalResult->setException(new Exception("List does not contain any Items"));

					return $dalResult->jsonSerialize();
				}

				$order = $this->orders_service->verifyOrderRequest($request);

				$items_in_order = array_keys($order->getItemIdsInOrder());

				$new_order_items = [];

				foreach ($list->getItems() as $item_id => $item)
				{
					if (in_array($item->getId(), $items_in_order) === false)
					{
						$order_item = new OrderItem();
						$order_item->setOrderId($order->getId());
						$order_item->setItemId($item->getId());
						$order_item->setQuantity($item->getDefaultQty());
						$order_item->setChecked(0);
						$order_item->setItem($item);

						$new_order_items[] = $order_item;
						$items_in_order[] = $order_item->getItemId();
					}
				}

				$luckyDips = $this->luckyDips_service->getLuckyDipsByListId($list->getId());

				foreach ($luckyDips as $luckyDip_id => $luckyDip)
				{
					$item = $luckyDip->getRandomItem();

					if ($item instanceof Item)
					{
						if (in_array($item->getId(), $items_in_order) === false)
						{
							$order_item = new OrderItem();
							$order_item->setOrderId($order->getId());
							$order_item->setItemId($item->getId());
							$order_item->setQuantity($item->getDefaultQty());
							$order_item->setChecked(0);
							$order_item->setItem($item);

							$new_order_items[] = $order_item;
							$items_in_order[] = $order_item->getItemId();
						}
					}
				}

				$order_items = $this->orders_service->addOrderItems($new_order_items);

				$partial_view = "";

				foreach ($order_items as $order_item)
				{
					$partial_view.= getPartialView("CurrentOrderItem", ['order_item' => $order_item]);
				}

				$dalResult->setPartialView($partial_view);

				$this->lists_service->closeConnexion();
				$this->orders_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function addItemToPreviousOrder($request)
		{
			if (!isset($request['description']) || empty($request['description']) || !isset($request['order_id']) || !is_numeric($request['order_id']))
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

			$dalResult = $this->orders_service->getOrderById(intval($request['order_id']));

			if (!is_null($dalResult->getResult()))
			{
				$order = $dalResult->getResult();
			}

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
			$dalResult->setPartialView(getPartialView("ListOrderItem", ['order_item' => $dalResult->getResult()]));
			$dalResult->setResult($dalResult->getResult()->jsonSerialize());

			$this->items_service->closeConnexion();
			$this->orders_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}
	}
