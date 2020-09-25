<?php
	class OrdersService
	{
		private $dal;

		public function __construct()
		{
			$this->dal = new OrdersDAL();
		}

		public function closeConnexion()
		{
			$this->dal->closeConnexion();
		}

		public function verifyOrderRequest(array $request) : Order
		{
			try
			{
				$order = null;

				if (!is_numeric($request['order_id']))
				{
					throw new Exception("Invalid Order ID");
				}

				$order = $this->dal->getOrderById(intval($request['order_id']));

				if (!($order instanceof Order))
				{
					throw new Exception("Order not found");
				}

				return $order;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function verifyOrderItemRequest(array $request) : OrderItem
		{
			try
			{
				$orderItem = null;

				if (!is_numeric($request['order_item_id']))
				{
					throw new Exception("Invalid OrderItem ID");
				}

				$orderItem = $this->dal->getOrderItemById(intval($request['order_item_id']));

				if (!($orderItem instanceof OrderItem))
				{
					throw new Exception("OrderItem not found");
				}

				return $orderItem;
			}
			catch (Exception $e)
			{
				throw $e;
			}
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

		public function addOrderItem(OrderItem $orderItem) : OrderItem
		{
			try
			{
				$existingOrderItem = $this->dal->getOrderItemByOrderAndItem($orderItem->getOrderId(), $orderItem->getItemId());

				if ($existingOrderItem instanceof OrderItem)
				{
					return $existingOrderItem;
				}

				$orderItem = $this->dal->addOrderItem($orderItem);

				return $orderItem;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function addOrderItems(array $order_items) : array
		{
			try
			{
				foreach ($order_items as $order_item)
				{
					$orderItemId = $this->dal->addOrderItem($order_item);

					if (is_null($orderItemId))
					{
						throw new Exception("Error adding Order Item");
					}

					$order_item->setId($dalResult->getResult());
				}

				return $order_items;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getOrderItemById($order_item_id)
		{
			return $this->dal->getOrderItemById($order_item_id);
		}

		public function updateOrderItem(OrderItem $orderItem) : bool
		{
			try
			{
				return $this->dal->updateOrderItem($orderItem);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function removeOrderItem(OrderItem $orderItem) : bool
		{
			try
			{
				return $this->dal->removeOrderItem($orderItem);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getOrderById(int $orderId) : ?Order
		{
			try
			{
				return $this->dal->getOrderById($orderId);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getOrdersByItem($item)
		{
			return $this->dal->getOrdersByItem($item);
		}

		public function removeAllOrderItemsFromOrder(Order $order) : bool
		{
			try
			{
				return $this->dal->removeAllOrderItemsFromOrder($order);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function updateOrder(Order $order) : bool
		{
			try
			{
				return $this->dal->updateOrder($order);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getOrderItemsByOrderAndItems($order, $items)
		{
			return $this->dal->getOrderItemsByOrderAndItems($order, $items);
		}
	}
