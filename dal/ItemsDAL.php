<?php
	class ItemsDAL
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

		public function getItemDepartmentLookupArray()
		{
			$result = new DalResult();
			$departments_lookup = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT idl.dept_id, idl.item_id FROM item_dept_link AS idl");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$departments_lookup = [];

					foreach ($rows as $row)
					{
						if (!array_key_exists($row['item_id'], $departments_lookup))
						{
							$departments_lookup[$row['item_id']] = $row['dept_id'];
						}
					}
				}

				$result->setResult($departments_lookup);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}
	}