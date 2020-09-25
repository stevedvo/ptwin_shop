<?php
	declare(strict_types=1);

	class OrdersDAL
	{
		private $ShopDb;

		public function __construct()
		{
			$this->ShopDb = new ShopDb();
		}

		public function closeConnexion()
		{
			$this->ShopDb = null;
		}

		public function addOrder(Order $order) : Order
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO orders (date_ordered) VALUES (:date_ordered)");
				$query->execute([':date_ordered' => !is_null($order->getDateOrdered()) ? $order->getDateOrdered()->format('Y-m-d') : null]);

				$order->setId($this->ShopDb->conn->lastInsertId());

				return $order;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getCurrentOrder() : ?Order
		{
			$order = null;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT o.id AS order_id, o.date_ordered AS date_ordered, oi.id AS order_item_id, oi.item_id, oi.quantity, oi.checked, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, ps.name AS packsize_name, ps.short_name AS packsize_short_name FROM orders AS o LEFT JOIN order_items AS oi ON (o.id = oi.order_id) LEFT JOIN items AS i ON (i.item_id = oi.item_id) LEFT JOIN pack_sizes AS ps ON (ps.id = i.packsize_id) WHERE o.date_ordered IS NULL ORDER BY i.description");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					foreach ($rows as $row)
					{
						if (!($order instanceof Order))
						{
							$order = createOrder($row);
						}

						$orderItem = createOrderItem($row);
						$item = createItem($row);
						$packsize = createPackSize($row);
						$item->setPackSize($packsize);
						$orderItem->setItem($item);

						if (entityIsValid($orderItem))
						{
							$order->addOrderItem($orderItem);
						}
					}
				}

				return $order;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getAllOrders()
		{
			$result = new DalResult();
			$orders = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT o.id AS order_id, o.date_ordered FROM orders AS o ORDER BY ISNULL(o.date_ordered) DESC, o.date_ordered DESC");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$orders = [];

					foreach ($rows as $row)
					{
						$order = createOrder($row);
						$orders[$order->getId()] = $order;
					}
				}

				$result->setResult($orders);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getOrderById(int $orderId) : ?Order
		{
			try
			{
				$order = null;

				$query = $this->ShopDb->conn->prepare("SELECT o.id AS order_id, o.date_ordered, oi.id AS order_item_id, oi.item_id, oi.quantity, oi.checked, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, ps.name AS packsize_name, ps.short_name AS packsize_short_name, d.dept_name, d.seq FROM orders AS o LEFT JOIN order_items AS oi ON (o.id = oi.order_id) LEFT JOIN items AS i ON (i.item_id = oi.item_id) LEFT JOIN pack_sizes AS ps ON (ps.id = i.packsize_id) LEFT JOIN departments AS d ON (d.dept_id = i.primary_dept) WHERE o.id = :order_id ORDER BY ISNULL(i.primary_dept), d.seq, d.dept_name, i.description");
				$query->execute([':order_id' => $orderId]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					foreach ($rows as $row)
					{
						if (!($order instanceof Order))
						{
							$order = createOrder($row);
						}

						$orderItem = createOrderItem($row);
						$item = createItem($row);
						$packsize = createPackSize($row);
						$item->setPackSize($packsize);
						$orderItem->setItem($item);

						if (entityIsValid($orderItem))
						{
							$order->addOrderItem($orderItem);
						}
					}
				}

				return $order;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getOrdersByItem($item)
		{
			$result = new DalResult();
			$orders = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT oi.id AS order_item_id, oi.order_id, oi.item_id, oi.quantity, oi.checked, o.date_ordered FROM order_items AS oi LEFT JOIN orders AS o ON (o.id = oi.order_id) WHERE item_id = :item_id AND o.date_ordered IS NOT NULL ORDER BY o.date_ordered DESC");
				$query->execute([':item_id' => $item->getId()]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$orders = [];

					foreach ($rows as $row)
					{
						if (!array_key_exists($row['order_id'], $orders))
						{
							$order = createOrder($row);
							$orders[$order->getId()] = $order;
						}

						$order_item = createOrderItem($row);

						if (entityIsValid($order_item))
						{
							$orders[$row['order_id']]->addOrderItem($order_item);
						}
					}
				}

				$result->setResult($orders);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function updateOrder(Order $order) : bool
		{
			try
			{
				$success = false;

				$query = $this->ShopDb->conn->prepare("UPDATE orders SET date_ordered = :date_ordered WHERE id = :id");
				$success = $query->execute(
				[
					':id'           => $order->getId(),
					':date_ordered' => $order->getDateOrdered() ? $order->getDateOrdered()->format('Y-m-d') : null
				]);

				return $success;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getOrderItemByOrderAndItem(int $orderId, int $itemId) : ?OrderItem
		{
			try
			{
				$orderItem = null;

				$query = $this->ShopDb->conn->prepare("SELECT oi.id AS order_item_id, oi.order_id, oi.item_id, oi.quantity, oi.checked FROM order_items AS oi WHERE oi.order_id = :order_id AND oi.item_id = :item_id");
				$query->execute(
				[
					':order_id' => $orderId,
					':item_id'  => $itemId
				]);

				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$orderItem = createOrderItem($row);
				}

				return $orderItem;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function addOrderItem(OrderItem $orderItem) : OrderItem
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, checked) VALUES (:order_id, :item_id, :quantity, :checked)");
				$query->execute(
				[
					':order_id' => $orderItem->getOrderId(),
					':item_id'  => $orderItem->getItemId(),
					':quantity' => $orderItem->getQuantity(),
					':checked'  => $orderItem->getChecked()
				]);

				$orderItem->setId(intval($this->ShopDb->conn->lastInsertId()));

				return $orderItem;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getOrderItemById(int $orderItemId) : ?OrderItem
		{
			try
			{
				$orderItem = null;

				$query = $this->ShopDb->conn->prepare("SELECT oi.id AS order_item_id, oi.order_id, oi.item_id, oi.quantity, oi.checked FROM order_items AS oi WHERE oi.id = :order_item_id");
				$query->execute([':order_item_id' => $orderItemId]);

				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$orderItem = createOrderItem($row);
				}

				return $orderItem;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getOrderItemsByOrderAndItems($order, $items)
		{
			$result = new DalResult();
			$order_items = false;

			$query_string = "";
			$query_values = [':order_id' => $order->getId()];

			foreach (array_keys($items) as $key => $item_id)
			{
				$query_string.= ":item_id_".$key.", ";
				$query_values[":item_id_".$key] = $item_id;
			}

			$query_string = rtrim($query_string, ", ");

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT oi.id AS order_item_id, oi.order_id, oi.item_id, oi.quantity, oi.checked FROM order_items AS oi WHERE oi.order_id = :order_id AND oi.item_id IN (".$query_string.")");
				$query->execute($query_values);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$order_items = [];

					foreach ($rows as $row)
					{
						$order_item = createOrderItem($row);
						$order_items[$order_item->getId()] = $order_item;
					}
				}

				$result->setResult($order_items);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function updateOrderItem(OrderItem $orderItem) : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE order_items SET order_id = :order_id, item_id = :item_id, quantity = :quantity, checked = :checked WHERE id = :id");
				$success = $query->execute(
				[
					':order_id' => $orderItem->getOrderId(),
					':item_id'  => $orderItem->getItemId(),
					':quantity' => $orderItem->getQuantity(),
					':checked'  => $orderItem->getChecked(),
					':id'       => $orderItem->getId()
				]);

				return $success;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function removeOrderItem(OrderItem $orderItem) : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM order_items WHERE id = :order_item_id");
				$success = $query->execute([':order_item_id' => $orderItem->getId()]);

				return $success;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function removeAllOrderItemsFromOrder($order) : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM order_items WHERE order_id = :order_id");
				$success = $query->execute([':order_id' => $order->getId()]);

				return $success;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}
	}
