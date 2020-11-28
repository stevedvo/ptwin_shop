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
				$orders = $this->orders_service->getAllOrders();

				$this->orders_service->closeConnexion();

				$pageData =
				[
					'page_title' => 'Manage Orders',
					'template'   => 'views/orders/index.php',
					'page_data'  => ['orders' => $orders],
				];

				renderPage($pageData);
			}
			catch (Exception $e)
			{
				$pageData['page_data'] = ['message' => $e->getMessage()];

				renderPage($pageData);
			}
		}

		public function View(int? $request = null) : void
		{
			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => []
			];

			try
			{
				$order = $this->orders_service->verifyOrderRequest(['order_id' => $request]);
				$departments = $this->departments_service->getAllDepartments();

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
			catch (Exception $e)
			{
				$pageData['page_data'] = ['message' => $e->getMessage()];

				renderPage($pageData);
			}
		}

		public function Print(int? $request = null) : void
		{
			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => []
			];

			try
			{
				$order = $this->orders_service->verifyOrderRequest(['order_id' => $request]);
				$departments = $this->departments_service->getAllDepartments();

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

				renderPage($pageData);
			}
			catch (Exception $e)
			{
				$pageData['page_data'] = ['message' => $e->getMessage()];

				renderPage($pageData);
			}
		}

		public function Edit(?int $request = null) : void
		{
			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => []
			];

			try
			{
				$order = $this->orders_service->verifyOrderRequest(['order_id' => $request]);
				$departments = $this->departments_service->getAllDepartments();

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
			catch (Exception $e)
			{
				$pageData['page_data'] = ['message' => $e->getMessage()];

				renderPage($pageData);
			}
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

				$itemsInOrder = array_keys($order->getItemIdsInOrder());

				$newOrderItems = [];

				foreach ($list->getItems() as $itemId => $item)
				{
					if (in_array($item->getId(), $itemsInOrder) === false)
					{
						$orderItem = new OrderItem();
						$orderItem->setOrderId($order->getId());
						$orderItem->setItemId($item->getId());
						$orderItem->setQuantity($item->getDefaultQty());
						$orderItem->setChecked(0);
						$orderItem->setItem($item);

						$newOrderItems[] = $orderItem;
						$itemsInOrder[] = $orderItem->getItemId();
					}
				}

				$luckyDips = $this->luckyDips_service->getLuckyDipsByListId($list->getId());

				foreach ($luckyDips as $luckyDipId => $luckyDip)
				{
					$item = $luckyDip->getRandomItem();

					if ($item instanceof Item)
					{
						if (in_array($item->getId(), $itemsInOrder) === false)
						{
							$orderItem = new OrderItem();
							$orderItem->setOrderId($order->getId());
							$orderItem->setItemId($item->getId());
							$orderItem->setQuantity($item->getDefaultQty());
							$orderItem->setChecked(0);
							$orderItem->setItem($item);

							$newOrderItems[] = $orderItem;
							$itemsInOrder[] = $orderItem->getItemId();
						}
					}
				}

				$orderItems = $this->orders_service->addOrderItems($newOrderItems);

				$partialView = "";

				foreach ($orderItems as $orderItem)
				{
					$partialView.= getPartialView("CurrentOrderItem", ['orderItem' => $orderItem]);
				}

				$dalResult->setPartialView($partialView);

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
