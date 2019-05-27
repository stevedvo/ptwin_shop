<?php
	class ShopDAL
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

		public function getAllLists()
		{
			$result = new DalResult();
			$lists = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT list_id, name AS list_name FROM lists");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$lists = [];

					foreach ($rows as $row)
					{
						$list = createList($row);

						$lists[$list->getId()] = $list;
					}
				}

				$result->setResult($lists);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getAllDepartments()
		{
			$result = new DalResult();
			$departments = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT dept_id, dept_name FROM departments ORDER BY dept_name");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$departments = [];

					foreach ($rows as $row)
					{
						$department = createDepartment($row);

						$departments[$department->getId()] = $department;
					}
				}

				$result->setResult($departments);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getAllDepartmentsWithItems()
		{
			$result = new DalResult();
			$departments = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT d.dept_id, d.dept_name, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept FROM departments AS d LEFT JOIN item_dept_link AS idl ON (d.dept_id = idl.dept_id) LEFT JOIN items AS i ON (idl.item_id = i.item_id) ORDER BY d.dept_name, i.description");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$departments = [];

					foreach ($rows as $row)
					{
						if (!array_key_exists($row['dept_id'], $departments))
						{
							$department = createDepartment($row);
							$departments[$department->getId()] = $department;
						}

						$item = createItem($row);

						if (entityIsValid($item))
						{
							$departments[$row['dept_id']]->addItem($item);
						}
					}
				}

				$result->setResult($departments);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getAllListsWithItems()
		{
			$result = new DalResult();
			$lists = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT l.list_id, l.name AS list_name, i.item_id, i.description, i.comments, i.default_qty, i.link, i.primary_dept FROM lists AS l LEFT JOIN items AS i ON (i.list_id = l.list_id) ORDER BY l.list_id, i.description");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$lists = [];

					foreach ($rows as $row)
					{
						if (!array_key_exists($row['list_id'], $lists))
						{
							$list = createList($row);
							$lists[$list->getId()] = $list;
						}

						$item = createItem($row);

						if (entityIsValid($item))
						{
							$lists[$row['list_id']]->addItem($item);
						}
					}
				}

				$result->setResult($lists);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function addList($list)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO lists (name) VALUES (:name)");
				$query->execute([':name' => $list->getName()]);
				$result->setResult($this->ShopDb->conn->lastInsertId());
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function addDepartment($department)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO departments (dept_name) VALUES (:name)");
				$query->execute([':name' => $department->getName()]);
				$result->setResult($this->ShopDb->conn->lastInsertId());
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getListByName($list_name)
		{
			$result = new DalResult();
			$list = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT list_id, name AS list_name FROM lists WHERE name = :name");
				$query->execute([':name' => $list_name]);
				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$list = createList($row);
				}

				$result->setResult($list);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function addItem($item)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO items (description, comments, default_qty, list_id, link, primary_dept) VALUES (:description, :comments, :default_qty, :list_id, :link, :primary_dept)");
				$query->execute(
				[
					':description'  => $item->getDescription(),
					':comments'     => $item->getComments(),
					':default_qty'  => !is_null($item->getDefaultQty()) ? $item->getDefaultQty() : 1,
					':list_id'      => $item->getListId(),
					':primary_dept' => $item->getPrimaryDept(),
					':link'         => $item->getLink(),
				]);

				$result->setResult($this->ShopDb->conn->lastInsertId());
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getItemByDescription($description)
		{
			$result = new DalResult();
			$item = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept FROM items AS i WHERE i.description = :description");
				$query->execute([':description' => $description]);
				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$item = createItem($row);
				}

				$result->setResult($item);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getDepartmentById($dept_id)
		{
			$result = new DalResult();
			$department = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT d.dept_id, d.dept_name, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept FROM departments AS d LEFT JOIN item_dept_link AS idl ON (d.dept_id = idl.dept_id) LEFT JOIN items AS i ON (idl.item_id = i.item_id) WHERE d.dept_id = :dept_id ORDER BY i.description");
				$query->execute([':dept_id' => $dept_id]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					foreach ($rows as $row)
					{
						if (!$department)
						{
							$department = createDepartment($row);
						}

						$item = createItem($row);

						if (entityIsValid($item))
						{
							$department->addItem($item);
						}
					}
				}

				$result->setResult($department);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getDepartmentByName($dept_name)
		{
			$result = new DalResult();
			$department = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT dept_id, dept_name FROM departments WHERE dept_name = :name");
				$query->execute([':name' => $dept_name]);
				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$department = createDepartment($row);
				}

				$result->setResult($department);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getListById($list_id)
		{
			$result = new DalResult();
			$list = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT l.list_id, l.name AS list_name, i.item_id, i.description, i.comments, i.default_qty, i.link, i.primary_dept FROM lists AS l LEFT JOIN items AS i ON (l.list_id = i.list_id) WHERE l.list_id = :list_id ORDER BY i.description");
				$query->execute([':list_id' => $list_id]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					foreach ($rows as $row)
					{
						if (!$list)
						{
							$list = createList($row);
						}

						$item = createItem($row);

						if (entityIsValid($item))
						{
							$list->addItem($item);
						}
					}
				}

				$result->setResult($list);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getAllItems()
		{
			$result = new DalResult();
			$items = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept FROM items AS i ORDER BY i.description");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$items = [];

					foreach ($rows as $row)
					{
						$item = createItem($row);
						$items[$item->getId()] = $item;
					}
				}

				$result->setResult($items);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getItemById($item_id)
		{
			$result = new DalResult();
			$item = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, idl.dept_id, d.dept_name FROM items AS i LEFT JOIN item_dept_link AS idl ON (idl.item_id = i.item_id) LEFT JOIN departments AS d ON (d.dept_id = idl.dept_id) WHERE i.item_id = :item_id ORDER BY d.dept_name");
				$query->execute([':item_id' => $item_id]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$departments = [];

					foreach ($rows as $row)
					{
						if (!$item)
						{
							$item = createItem($row);
						}

						$department = createDepartment($row);

						if (entityIsValid($department))
						{
							$item->addDepartment($department);
						}
					}
				}

				$result->setResult($item);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function addItemToDepartment($item, $department)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO item_dept_link (dept_id, item_id) VALUES (:dept_id, :item_id)");
				$result->setResult($query->execute(
				[
					':dept_id' => $department->getId(),
					':item_id' => $item->getId()
				]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function addDepartmentToItem($department, $item)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO item_dept_link (dept_id, item_id) VALUES (:dept_id, :item_id)");
				$result->setResult($query->execute(
				[
					':dept_id' => $department->getId(),
					':item_id' => $item->getId()
				]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function addItemToList($item, $list)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE items SET list_id = :list_id WHERE item_id = :item_id");
				$result->setResult($query->execute(
				[
					':list_id' => $list->getId(),
					':item_id' => $item->getId()
				]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function removeItemsFromDepartment($item_ids, $dept_id)
		{
			$result = new DalResult();

			$query_string = "";
			$query_values = [':dept_id' => $dept_id];

			foreach ($item_ids as $key => $item_id)
			{
				$query_string.= ":item_id_".$key.", ";
				$query_values[":item_id_".$key] = $item_id;
			}

			$query_string = rtrim($query_string, ", ");

			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM item_dept_link WHERE item_id IN (".$query_string.") AND dept_id = :dept_id");
				$result->setResult($query->execute($query_values));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function removeDepartmentsFromItem($dept_ids, $item_id)
		{
			$result = new DalResult();

			$query_string = "";
			$query_values = [':item_id' => $item_id];

			foreach ($dept_ids as $key => $dept_id)
			{
				$query_string.= ":dept_id".$key.", ";
				$query_values[":dept_id".$key] = $dept_id;
			}

			$query_string = rtrim($query_string, ", ");

			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM item_dept_link WHERE dept_id IN (".$query_string.") AND item_id = :item_id");
				$result->setResult($query->execute($query_values));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getItemsById($item_ids)
		{
			$result = new DalResult();
			$items = false;

			$query_string = "";
			$query_values = [];

			foreach ($item_ids as $key => $item_id)
			{
				$query_string.= ":id_".$key.", ";
				$query_values[":id_".$key] = $item_id;
			}

			$query_string = rtrim($query_string, ", ");

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept FROM items AS i WHERE i.item_id IN (".$query_string.")");
				$query->execute($query_values);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$items = [];

					foreach ($rows as $row)
					{
						$item = createItem($row);

						$items[$item->getId()] = $item;
					}
				}

				$result->setResult($items);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getItemsByListId($list_id)
		{
			$result = new DalResult();
			$items = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept FROM items AS i WHERE i.list_id = :list_id");
				$query->execute([':list_id' => $list_id]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$items = [];

					foreach ($rows as $row)
					{
						$item = createItem($row);

						$items[$item->getId()] = $item;
					}
				}

				$result->setResult($items);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function updateItem($item)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE items SET description = :description, comments = :comments, default_qty = :default_qty, list_id = :list_id, link = :link, primary_dept = :primary_dept WHERE item_id = :item_id");
				$result->setResult($query->execute(
				[
					':description'  => $item->getDescription(),
					':comments'     => $item->getComments(),
					':default_qty'  => $item->getDefaultQty(),
					':list_id'      => $item->getListId(),
					':link'         => $item->getLink(),
					':primary_dept' => $item->getPrimaryDept(),
					':item_id'      => $item->getId()
				]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function updateList($list)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE lists SET name = :name WHERE list_id = :list_id");
				$result->setResult($query->execute(
				[
					':name'    => $list->getName(),
					':list_id' => $list->getId()
				]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function updateDepartment($department)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE departments SET dept_name = :dept_name WHERE dept_id = :dept_id");
				$result->setResult($query->execute(
				[
					':dept_name' => $department->getName(),
					':dept_id'   => $department->getId()
				]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function removeList($list)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM lists WHERE list_id = :list_id");
				$result->setResult($query->execute([':list_id' => $list->getId()]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function moveItemsToList($items, $list)
		{
			$result = new DalResult();

			$query_string = "";
			$query_values = [':list_id' => $list->getId()];

			foreach ($items as $key => $item)
			{
				$query_string.= ":id_".$key.", ";
				$query_values[":id_".$key] = $item->getId();
			}

			$query_string = rtrim($query_string, ", ");

			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE items SET list_id = :list_id WHERE item_id IN (".$query_string.")");
				$result->setResult($query->execute($query_values));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getItemsByDepartmentId($dept_id)
		{
			$result = new DalResult();
			$items = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT idl.dept_id, idl.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept FROM item_dept_link AS idl LEFT JOIN items AS i ON (idl.item_id = i.item_id) WHERE idl.dept_id = :dept_id");
				$query->execute([':dept_id' => $dept_id]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$items = [];

					foreach ($rows as $row)
					{
						$item = createItem($row);
						$items[$item->getId()] = $item;
					}
				}

				$result->setResult($items);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function removeDepartment($department)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM departments WHERE dept_id = :dept_id");
				$result->setResult($query->execute([':dept_id' => $department->getId()]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getCurrentOrder()
		{
			$result = new DalResult();
			$order = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT o.id AS order_id, o.date_ordered AS date_ordered, oi.id AS order_item_id, oi.item_id, oi.quantity, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept FROM orders AS o LEFT JOIN order_items AS oi ON (o.id = oi.order_id) LEFT JOIN items AS i ON (i.item_id = oi.item_id) WHERE o.date_ordered IS NULL ORDER BY i.description");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					foreach ($rows as $row)
					{
						if (!$order)
						{
							$order = createOrder($row);
						}

						$order_item = createOrderItem($row);
						$item = createItem($row);
						$order_item->setItem($item);

						if (entityIsValid($order_item))
						{
							$order->addOrderItem($order_item);
						}
					}
				}

				$result->setResult($order);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getOrderById($order_id)
		{
			$result = new DalResult();
			$order = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT o.id AS order_id, o.date_ordered AS date_ordered, oi.id AS order_item_id, oi.item_id, oi.quantity, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept FROM orders AS o LEFT JOIN order_items AS oi ON (o.id = oi.order_id) LEFT JOIN items AS i ON (i.item_id = oi.item_id) WHERE o.id = :order_id");
				$query->execute([':order_id' => $order_id]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					foreach ($rows as $row)
					{
						if (!$order)
						{
							$order = createOrder($row);
						}

						$order_item = createOrderItem($row);
						$item = createItem($row);
						$order_item->setItem($item);

						if (entityIsValid($order_item))
						{
							$order->addOrderItem($order_item);
						}
					}
				}

				$result->setResult($order);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function addOrder($order)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO orders (date_ordered) VALUES (:date_ordered)");
				$query->execute(
				[
					':date_ordered' => !is_null($order->getDateOrdered()) ? $order->getDateOrdered()->format('Y-m-d') : null
				]);

				$result->setResult($this->ShopDb->conn->lastInsertId());
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getOrderItemByOrderAndItem($order_id, $item_id)
		{
			$result = new DalResult();
			$order_item = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT oi.id AS order_item_id, oi.order_id, oi.item_id, oi.quantity FROM order_items AS oi WHERE oi.order_id = :order_id AND oi.item_id = :item_id");
				$query->execute(
				[
					':order_id' => $order_id,
					':item_id'  => $item_id
				]);

				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$order_item = createOrderItem($row);
				}

				$result->setResult($order_item ? $order_item->getId() : false);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
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
				$query = $this->ShopDb->conn->prepare("SELECT oi.id AS order_item_id, oi.order_id, oi.item_id, oi.quantity FROM order_items AS oi WHERE oi.order_id = :order_id AND oi.item_id IN (".$query_string.")");
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

		public function addOrderItem($order_item)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO order_items (order_id, item_id, quantity) VALUES (:order_id, :item_id, :quantity)");
				$query->execute(
				[
					':order_id' => $order_item->getOrderId(),
					':item_id'  => $order_item->getItemId(),
					':quantity' => $order_item->getQuantity()
				]);

				$result->setResult($this->ShopDb->conn->lastInsertId());
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getOrderItemById($order_item_id)
		{
			$result = new DalResult();
			$order_item = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT oi.id AS order_item_id, oi.order_id, oi.item_id, oi.quantity FROM order_items AS oi WHERE oi.id = :order_item_id");
				$query->execute([':order_item_id' => $order_item_id]);

				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$order_item = createOrderItem($row);
				}

				$result->setResult($order_item);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function updateOrderItem($order_item)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE order_items SET order_id = :order_id, item_id = :item_id, quantity = :quantity WHERE id = :id");
				$result->setResult($query->execute(
				[
					':order_id' => $order_item->getOrderId(),
					':item_id'  => $order_item->getItemId(),
					':quantity' => $order_item->getQuantity(),
					':id'       => $order_item->getId()
				]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function updateOrder($order)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE orders SET date_ordered = :date_ordered WHERE id = :id");
				$result->setResult($query->execute(
				[
					':id' => $order->getId(),
					':date_ordered' => $order->getDateOrdered() ? $order->getDateOrdered()->format('Y-m-d') : null
				]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function removeOrderItem($order_item)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM order_items WHERE id = :order_item_id");
				$result->setResult($query->execute([':order_item_id' => $order_item->getId()]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function removeAllOrderItemsFromOrder($order)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM order_items WHERE order_id = :order_id");
				$result->setResult($query->execute([':order_id' => $order->getId()]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getPrimaryDepartments()
		{
			$result = new DalResult();
			$departments = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, idl.dept_id, d.dept_name FROM items AS i LEFT JOIN item_dept_link AS idl ON (idl.item_id = i.item_id) LEFT JOIN departments AS d ON (d.dept_id = idl.dept_id) ORDER BY i.primary_dept, i.description");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$departments = [];

					foreach ($rows as $row)
					{
						if (is_null($row['primary_dept']))
						{
							$key = 0;

							if (!array_key_exists($key, $departments))
							{
								$departments[$key] = new Department();
							}

							$item = createItem($row);
							$departments[$key]->addItem($item);
						}
						else
						{
							$key = $row['primary_dept'];

							if ($key == $row['dept_id'])
							{
								if (!array_key_exists($key, $departments))
								{
									$department = createDepartment($row);
									$departments[$department->getId()] = $department;
								}

								$item = createItem($row);
								$departments[$key]->addItem($item);
							}
						}
					}
				}

				$result->setResult($departments);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}
	}
