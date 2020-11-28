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

		public function addLuckyDip(LuckyDip $luckyDip) : LuckyDip
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO lucky_dips (name, list_id) VALUES (:name, :list_id)");
				$query->execute(
				[
					':name'    => $luckyDip->getName(),
					':list_id' => $luckyDip->getListId()
				]);

				$luckyDip->setId($this->ShopDb->conn->lastInsertId());

				return $luckyDip;
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

		public function getLuckyDipById(int $luckyDipId) : ?LuckyDip
		{
			$luckyDip = null;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT ld.id AS luckyDip_id, ld.name AS luckyDip_name, ld.list_id AS luckyDip_list_id, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, ps.name AS packsize_name, ps.short_name AS packsize_short_name FROM lucky_dips AS ld LEFT JOIN items AS i ON (i.luckydip_id = ld.id) LEFT JOIN pack_sizes AS ps ON (ps.id = i.packsize_id) WHERE ld.id = :id ORDER BY i.description");
				$query->execute([':id' => $luckyDipId]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					foreach ($rows as $row)
					{
						if (is_null($luckyDip))
						{
							$luckyDip = createLuckyDip($row);
						}

						$item = createItem($row);

						if (entityIsValid($item))
						{
							$luckyDip->addItem($item);
						}
					}
				}

				return $luckyDip;
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

		public function getLuckyDipByName(string $luckyDipName) : ?LuckyDip
		{
			try
			{
				$luckyDip = null;

				$query = $this->ShopDb->conn->prepare("SELECT ld.id AS luckyDip_id, ld.name AS luckyDip_name, ld.list_id AS luckyDip_list_id, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, ps.name AS packsize_name, ps.short_name AS packsize_short_name FROM lucky_dips AS ld LEFT JOIN items AS i ON (i.luckydip_id = ld.id) LEFT JOIN pack_sizes AS ps ON (ps.id = i.packsize_id) WHERE ld.name = :name ORDER BY i.description");
				$query->execute([':name' => $luckyDipName]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					foreach ($rows as $row)
					{
						if (!($luckyDip instanceof LuckyDip))
						{
							$luckyDip = createLuckyDip($row);
						}

						$item = createItem($row);
						$packsize = createPackSize($row);
						$item->setPackSize($packsize);

						if (entityIsValid($item))
						{
							$luckyDip->addItem($item);
						}
					}
				}

				return $luckyDip;
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

		public function getLuckyDipsByListId(int $listId) : ?array
		{
			try
			{
				$luckyDips = null;

				$query = $this->ShopDb->conn->prepare("SELECT ld.id AS luckyDip_id, ld.name AS luckyDip_name, ld.list_id AS luckyDip_list_id, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, ps.name AS packsize_name, ps.short_name AS packsize_short_name FROM lucky_dips AS ld LEFT JOIN items AS i ON (i.luckydip_id = ld.id) LEFT JOIN pack_sizes AS ps ON (ps.id = i.packsize_id) WHERE ld.list_id = :list_id ORDER BY ld.id, i.description");
				$query->execute([':list_id' => $listId]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$luckyDips = [];

					foreach ($rows as $row)
					{
						if (!array_key_exists($row['luckyDip_id'], $luckyDips))
						{
							$luckyDip = createLuckyDip($row);
							$luckyDips[$luckyDip->getId()] = $luckyDip;
						}

						$item = createItem($row);
						$packsize = createPackSize($row);
						$item->setPackSize($packsize);

						if (entityIsValid($item))
						{
							$luckyDips[$row['luckyDip_id']]->addItem($item);
						}
					}
				}

				return $luckyDips;
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

		public function getAllLuckyDips() : ?array
		{
			try
			{
				$luckyDips = null;

				$query = $this->ShopDb->conn->prepare("SELECT id AS luckyDip_id, name AS luckyDip_name, list_id AS luckyDip_list_id FROM lucky_dips ORDER BY luckyDip_name");
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

				return $luckyDips;
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

		// public function getAllLuckyDipsWithItems() : DalResult
		// {
		// 	$result = new DalResult();
		// 	$luckyDips = false;

		// 	try
		// 	{
		// 		$query = $this->ShopDb->conn->prepare("SELECT d.dept_id, d.dept_name, d.seq, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id FROM luckyDips AS d LEFT JOIN item_dept_link AS idl ON (d.dept_id = idl.dept_id) LEFT JOIN items AS i ON (idl.item_id = i.item_id) ORDER BY d.seq, d.dept_name, i.description");
		// 		$query->execute();
		// 		$rows = $query->fetchAll(PDO::FETCH_ASSOC);

		// 		if ($rows)
		// 		{
		// 			$luckyDips = [];

		// 			foreach ($rows as $row)
		// 			{
		// 				if (!array_key_exists($row['dept_id'], $luckyDips))
		// 				{
		// 					$luckyDip = createLuckyDip($row);
		// 					$luckyDips[$luckyDip->getId()] = $luckyDip;
		// 				}

		// 				$item = createItem($row);

		// 				if (entityIsValid($item))
		// 				{
		// 					$luckyDips[$row['dept_id']]->addItem($item);
		// 				}
		// 			}
		// 		}

		// 		$result->setResult($luckyDips);
		// 	}
		// 	catch(PDOException $e)
		// 	{
		// 		$result->setException($e);
		// 	}

		// 	return $result;
		// }

		public function addItemToLuckyDip(Item $item, LuckyDip $luckyDip) : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE items SET luckydip_id = :luckyDip_id WHERE item_id = :item_id");
				$success = $query->execute(
				[
					':luckyDip_id' => $luckyDip->getId(),
					':item_id'     => $item->getId()
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

		public function removeItemFromLuckyDip(Item $item) : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE items SET luckydip_id = NULL WHERE item_id = :item_id");
				$success = $query->execute([':item_id' => $item->getId()]);

				return $success;
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function updateLuckyDip(LuckyDip $luckyDip) : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE lucky_dips SET name = :name, list_id = :list_id WHERE id = :id");
				$success = $query->execute(
				[
					':name'    => $luckyDip->getName(),
					':list_id' => $luckyDip->getListId(),
					':id'      => $luckyDip->getId(),
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

		public function removeLuckyDip(LuckyDip $luckyDip) : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM lucky_dips WHERE id = :id");
				$success = $query->execute([':id' => $luckyDip->getId()]);

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
