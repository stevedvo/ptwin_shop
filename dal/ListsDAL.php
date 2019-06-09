<?php
	class ListsDAL
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
	}
