<?php
	class OrdersController
	{
		private $orders_service;

		public function __construct()
		{
			$this->orders_service = new OrdersService();
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

		public function removeOrderItem($request)
		{
			if (!isset($request['order_item_id']) || !is_numeric($request['order_item_id']))
			{
				return false;
			}

			$dalResult = $this->orders_service->getOrderItemById(intval($request['order_item_id']));

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			$order_item = $dalResult->getResult();

			if (!$order_item)
			{
				return false;
			}

			$dalResult = $this->orders_service->removeOrderItem($order_item);

			$this->orders_service->closeConnexion();

			return $dalResult->jsonSerialize();
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

		public function View($request = null)
		{
			$order = false;

			if (is_numeric($request))
			{
				$dalResult = $this->orders_service->getOrderById(intval($request));

				if (!is_null($dalResult->getResult()))
				{
					$order = $dalResult->getResult();
				}
			}

			$this->orders_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'View Order',
				'template'   => 'views/orders/view.php',
				'page_data'  => ['order' => $order]
			];

			renderPage($pageData);
		}

		public function confirmOrder($request)
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

			$order->setDateOrdered(new DateTime);
			$dalResult = $this->orders_service->updateOrder($order);

			$this->orders_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}
	}
