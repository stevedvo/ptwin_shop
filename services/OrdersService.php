<?php
	class OrdersService
	{
		private $dal;

		public function __construct()
		{
			$this->dal = new ShopDAL();
		}

		public function closeConnexion()
		{
			$this->dal->closeConnexion();
		}

		public function getCurrentOrder()
		{
			$order = false;
			$dalResult = $this->dal->getCurrentOrder();

			if ($dalResult->getResult() instanceof Order)
			{
				$order = $dalResult->getResult();
			}

			if (!$order)
			{
				$order = new Order();
				$dalResult = $this->dal->addOrder($order);

				if (!is_null($dalResult->getResult()))
				{
					$order->setId($dalResult->getResult());
				}
			}

			return $order;
		}

		public function getAllOrders()
		{
			return $this->dal->getAllOrders();
		}

		public function addOrder($order)
		{
			return $this->dal->addOrder($order);
		}

		public function addOrderItem($order_item)
		{
			$dalResult = $this->dal->getOrderItemByOrderAndItem($order_item->getOrderId(), $order_item->getItemId());

			if ($dalResult->getResult())
			{
				return $dalResult->getResult();
			}

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			$dalResult = $this->dal->addOrderItem($order_item);

			if ($dalResult->getResult())
			{
				return $dalResult->getResult();
			}

			return false;
		}

		public function addOrderItems($order_items)
		{
			if (is_array($order_items))
			{
				foreach ($order_items as $order_item)
				{
					$dalResult = $this->dal->addOrderItem($order_item);

					if ($dalResult->getResult())
					{
						$order_item->setId($dalResult->getResult());
					}
				}

				return $order_items;
			}

			return false;
		}

		public function getOrderItemById($order_item_id)
		{
			return $this->dal->getOrderItemById($order_item_id);
		}

		public function updateOrderItem($order_item)
		{
			return $this->dal->updateOrderItem($order_item);
		}

		public function removeOrderItem($order_item)
		{
			return $this->dal->removeOrderItem($order_item);
		}

		public function getOrderById($order_id)
		{
			return $this->dal->getOrderById($order_id);
		}

		public function removeAllOrderItemsFromOrder($order)
		{
			return $this->dal->removeAllOrderItemsFromOrder($order);
		}

		public function updateOrder($order)
		{
			return $this->dal->updateOrder($order);
		}

		public function getOrderItemsByOrderAndItems($order, $items)
		{
			return $this->dal->getOrderItemsByOrderAndItems($order, $items);
		}
	}
