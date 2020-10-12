<?php
	declare(strict_types=1);

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

		public function getCurrentOrder() : Order
		{
			try
			{
				$order = $this->dal->getCurrentOrder();

				if ($order instanceof Order)
				{
					return $order;
				}

				$order = $this->dal->addOrder(new Order());

				return $order;
			}
			catch (Exception $e)
			{
				throw $e;
			}
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

		public function getOrderItemById(int $orderItemId) : ?OrderItem
		{
			try
			{
				return $this->dal->getOrderItemById($orderItemId);
			}
			catch (Exception $e)
			{
				throw $e;
			}
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

		public function getOrdersByItem(Item $item) : array
		{
			try
			{
				$orders = $this->dal->getOrdersByItem($item);

				if (!is_array($orders))
				{
					throw new Exception("Orders not found for Item.");
				}

				return $orders;
			}
			catch (Exception $e)
			{
				throw $e;
			}
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
