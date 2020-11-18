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

		public function getAllLists() : ?array
		{
			try
			{
				$lists = null;

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

				return $lists;
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

		public function getAllListsWithItems() : ?array
		{
			try
			{
				$lists = null;

				$query = $this->ShopDb->conn->prepare("SELECT l.list_id, l.name AS list_name, i.item_id, i.description, i.comments, i.default_qty, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id FROM lists AS l LEFT JOIN items AS i ON (i.list_id = l.list_id) ORDER BY l.list_id, i.description");
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

				return $lists;
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

		public function getListById($list_id) : ?ShopList
		{
			try
			{
				$list = null;

				$query = $this->ShopDb->conn->prepare("SELECT l.list_id, l.name AS list_name, i.item_id, i.description, i.comments, i.default_qty, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, ps.name AS packsize_name, ps.short_name AS packsize_short_name FROM lists AS l LEFT JOIN items AS i ON (l.list_id = i.list_id) LEFT JOIN pack_sizes AS ps ON (ps.id = i.packsize_id) WHERE l.list_id = :list_id ORDER BY i.description");
				$query->execute([':list_id' => $list_id]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					foreach ($rows as $row)
					{
						if (!($list instanceof ShopList))
						{
							$list = createList($row);
						}

						$item = createItem($row);
						$packsize = createPackSize($row);
						$item->setPackSize($packsize);

						if (entityIsValid($item))
						{
							$list->addItem($item);
						}
					}
				}

				return $list;
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

		public function addItemToList(Item $item, ShopList $list) : bool
		{
			try
			{
				$success = false;

				$query = $this->ShopDb->conn->prepare("UPDATE items SET list_id = :list_id WHERE item_id = :item_id");
				$success = $query->execute(
				[
					':list_id' => $list->getId(),
					':item_id' => $item->getId(),
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

		public function updateList(ShopList $list) : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE lists SET name = :name WHERE list_id = :list_id");
				$success = $query->execute(
				[
					':name'    => $list->getName(),
					':list_id' => $list->getId(),
				]);
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
