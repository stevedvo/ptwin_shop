<?php
	class OrdersController
	{
		private $orders_service;
		private $lists_service;

		public function __construct()
		{
			$this->orders_service = new OrdersService();
			$this->lists_service = new ListsService();
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

		public function addListToOrder($request)
		{
			if (!isset($request['list_id']) || !is_numeric($request['list_id']) || !isset($request['order_id']) || !is_numeric($request['order_id']))
			{
				return false;
			}

			$dalResult = $this->lists_service->getListById(intval($request['list_id']));

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			$list = $dalResult->getResult();

			if (!$list)
			{
				return false;
			}

			if (!is_array($list->getItems()) || sizeof($list->getItems()) == 0)
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

			$dalResult = $this->orders_service->getOrderItemsByOrderAndItems($order, $list->getItems());

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			$items_in_order = false;

			if (is_array($dalResult->getResult()))
			{
				$items_in_order = [];

				foreach ($dalResult->getResult() as $order_item_id => $order_item)
				{
					$items_in_order[] = $order_item->getItemId();
				}
			}

			$new_order_items = [];

			foreach ($list->getItems() as $item_id => $item)
			{
				if (is_array($items_in_order))
				{
					if (array_search($item->getId(), $items_in_order) === false)
					{
						$add_item = true;
					}
					else
					{
						$add_item = false;
					}
				}
				else
				{
					$add_item = true;
				}

				if ($add_item)
				{
					$order_item = new OrderItem();
					$order_item->setOrderId($order->getId());
					$order_item->setItemId($item->getId());
					$order_item->setQuantity($item->getDefaultQty());
					$order_item->setItem($item);

					$new_order_items[] = $order_item;
				}
			}

			$order_items = $this->orders_service->addOrderItems($new_order_items);

			$saved_order_items = false;

			if (is_array($order_items))
			{
				$saved_order_items = [];

				foreach ($order_items as $order_item)
				{
					$saved_order_items[$order_item->getId()] = $order_item->jsonSerialize();
				}
			}

			$this->lists_service->closeConnexion();
			$this->orders_service->closeConnexion();

			return $saved_order_items;
		}
	}
