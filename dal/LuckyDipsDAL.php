<?php
	declare(strict_types=1);

	class LuckyDipsDAL
	{
		private $ShopDb;

		public function __construct()
		{
			$this->ShopDb = new ShopDb();
		}

		public function closeConnexion() : void
		{
			$this->ShopDb = null;
		}

		public function addLuckyDip(LuckyDip $luckyDip) : DalResult
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO lucky_dips (name) VALUES (:name)");
				$query->execute([':name' => $luckyDip->getName()]);
				$result->setResult(intval($this->ShopDb->conn->lastInsertId()));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getLuckyDipById(int $luckyDip_id) : DalResult
		{
			$result = new DalResult();
			$luckyDip = null;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT id AS luckyDip_id, name AS luckyDip_name FROM lucky_dips WHERE id = :id ORDER BY name");
				$query->execute([':id' => $luckyDip_id]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					foreach ($rows as $row)
					{
						if (is_null($luckyDip))
						{
							$luckyDip = createLuckyDip($row);
						}
					}
				}

				$result->setResult($luckyDip);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getLuckyDipByName(string $luckyDip_name) : DalResult
		{
			$result = new DalResult();
			$luckyDip = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT id AS luckyDip_id, name AS luckyDip_name FROM lucky_dips WHERE name = :name");
				$query->execute([':name' => $luckyDip_name]);
				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$luckyDip = createLuckyDip($row);
				}

				$result->setResult($luckyDip);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getAllLuckyDips()
		{
			$result = new DalResult();
			$luckyDips = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT id AS luckyDip_id, name AS luckyDip_name FROM lucky_dips ORDER BY luckyDip_name");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$luckyDips = [];

					foreach ($rows as $row)
					{
						$luckyDip = createLuckyDip($row);

						$luckyDips[$luckyDip->getId()] = $luckyDip;
					}
				}

				$result->setResult($luckyDips);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getAllLuckyDipsWithItems()
		{
			$result = new DalResult();
			$luckyDips = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT d.dept_id, d.dept_name, d.seq, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id FROM luckyDips AS d LEFT JOIN item_dept_link AS idl ON (d.dept_id = idl.dept_id) LEFT JOIN items AS i ON (idl.item_id = i.item_id) ORDER BY d.seq, d.dept_name, i.description");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$luckyDips = [];

					foreach ($rows as $row)
					{
						if (!array_key_exists($row['dept_id'], $luckyDips))
						{
							$luckyDip = createLuckyDip($row);
							$luckyDips[$luckyDip->getId()] = $luckyDip;
						}

						$item = createItem($row);

						if (entityIsValid($item))
						{
							$luckyDips[$row['dept_id']]->addItem($item);
						}
					}
				}

				$result->setResult($luckyDips);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function addItemToLuckyDip($item, $luckyDip)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO item_dept_link (dept_id, item_id) VALUES (:dept_id, :item_id)");
				$result->setResult($query->execute(
				[
					':dept_id' => $luckyDip->getId(),
					':item_id' => $item->getId()
				]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function removeItemsFromLuckyDip($item_ids, $dept_id)
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

		public function updateLuckyDip(LuckyDip $luckyDip) : DalResult
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE lucky_dips SET name = :name WHERE id = :id");
				$result->setResult($query->execute(
				[
					':name' => $luckyDip->getName(),
					':id'   => $luckyDip->getId()
				]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function removeLuckyDip($luckyDip)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM luckyDips WHERE dept_id = :dept_id");
				$result->setResult($query->execute([':dept_id' => $luckyDip->getId()]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getPrimaryLuckyDips()
		{
			$result = new DalResult();
			$luckyDips = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, idl.dept_id, d.dept_name, d.seq FROM items AS i LEFT JOIN item_dept_link AS idl ON (idl.item_id = i.item_id) LEFT JOIN luckyDips AS d ON (d.dept_id = idl.dept_id) ORDER BY ISNULL(i.primary_dept), d.seq, d.dept_name, i.description");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$luckyDips = [];

					foreach ($rows as $row)
					{
						if (is_null($row['primary_dept']))
						{
							$key = 0;

							if (!array_key_exists($key, $luckyDips))
							{
								$luckyDips[$key] = new LuckyDip();
							}

							$item = createItem($row);
							$luckyDips[$key]->addItem($item);
						}
						else
						{
							$key = $row['primary_dept'];

							if ($key == $row['dept_id'])
							{
								if (!array_key_exists($key, $luckyDips))
								{
									$luckyDip = createLuckyDip($row);
									$luckyDips[$luckyDip->getId()] = $luckyDip;
								}

								$item = createItem($row);
								$luckyDips[$key]->addItem($item);
							}
						}
					}
				}

				$result->setResult($luckyDips);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}
	}
