<?php
	declare(strict_types=1);

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
				$orderUpdate = createOrder($request);

				if (!entityIsValid($orderUpdate))
				{
					$dalResult->setException(new Exception("Invalid Order"));

					return $dalResult->jsonSerialize();
				}

				$order = $this->orders_service->getOrderById($orderUpdate->getId());

				if (!($order instanceof Order))
				{
					$dalResult->setException(new Exception("Cannot find Order with ID ".$orderUpdate->getId()));

					return $dalResult->jsonSerialize();
				}

				$order->setDateOrdered($orderUpdate->getDateOrdered());

				$success = $this->orders_service->updateOrder($order);

				if (!$success)
				{
					$dalResult->setException(new Exception("Error updating Order #".$order->getId()));

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

		public function updateOrderItem(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$orderItemUpdate = createOrderItem($request);

				if (!entityIsValid($orderItemUpdate))
				{
					$dalResult->setException(new Exception("Invalid Order Item"));

					return $dalResult->jsonSerialize();
				}

				$orderItem = $this->orders_service->verifyOrderItemRequest($request);

				$orderItem->setQuantity($orderItemUpdate->getQuantity());

				$success = $this->orders_service->updateOrderItem($orderItem);

				if (!$success)
				{
					$dalResult->setException(new Exception("Error updating Order Item #".$orderItem->getId()));

					return $dalResult->jsonSerialize();
				}

				$this->orders_service->closeConnexion();

				$dalResult->setResult($success);

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function checkOrderItem(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$orderItem = $this->orders_service->verifyOrderItemRequest($request);

				$orderItemUpdate = createOrderItem($request);
				$orderItem->setChecked($orderItemUpdate->getChecked());

				if (!entityIsValid($orderItem))
				{
					$dalResult->setException(new Exception("Invalid Order Item"));

					return $dalResult->jsonSerialize();
				}

				$success = $this->orders_service->updateOrderItem($orderItem);

				if (!$success)
				{
					$dalResult->setException(new Exception("Error updating Order Item #".$orderItem->getId()));

					return $dalResult->jsonSerialize();
				}

				$this->orders_service->closeConnexion();

				$dalResult->setResult($success);

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
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

				$dalResult->setResult($success);

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function removeAllOrderItemsFromOrder(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$order = $this->orders_service->verifyOrderRequest($request);
				$success = $this->orders_service->removeAllOrderItemsFromOrder($order);

				if (!$success)
				{
					$dalResult->setException(new Exception("Error removing Order Items from Order #".$order->getId()));

					return $dalResult->jsonSerialize();
				}

				$this->orders_service->closeConnexion();

				$dalResult->setResult($success);

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
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

				$dalResult->setResult($success);

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

		public function addItemToPreviousOrder(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				if (!isset($request['description']) || empty($request['description']))
				{
					$dalResult->setException(new Exception("Invalid description"));

					return $dalResult->jsonSerialize();
				}

				$item = $this->items_service->getItemByDescription($request['description']);
				$order = $this->orders_service->verifyOrderRequest($request);

				$orderItem = new OrderItem();
				$orderItem->setOrderId($order->getId());
				$orderItem->setItemId($item->getId());
				$orderItem->setQuantity($item->getDefaultQty());
				$orderItem->setChecked(0);

				$orderItem = $this->orders_service->addOrderItem($orderItem);

				$orderItem->setItem($item);
				$dalResult->setPartialView(getPartialView("ListOrderItem", ['order_item' => $orderItem]));
				$dalResult->setResult($orderItem->jsonSerialize());

				$this->items_service->closeConnexion();
				$this->orders_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}
	}
