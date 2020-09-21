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
				$query = $this->ShopDb->conn->prepare("INSERT INTO lucky_dips (name, list_id) VALUES (:name, :list_id)");
				$query->execute(
				[
					':name'    => $luckyDip->getName(),
					':list_id' => $luckyDip->getListId()
				]);

				$result->setResult(intval($this->ShopDb->conn->lastInsertId()));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getLuckyDipById(int $luckyDip_id) : ?LuckyDip
		{
			$luckyDip = null;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT ld.id AS luckyDip_id, ld.name AS luckyDip_name, ld.list_id AS luckyDip_list_id, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, ps.name AS packsize_name, ps.short_name AS packsize_short_name FROM lucky_dips AS ld LEFT JOIN items AS i ON (i.luckydip_id = ld.id) LEFT JOIN pack_sizes AS ps ON (ps.id = i.packsize_id) WHERE ld.id = :id ORDER BY i.description");
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

		public function getLuckyDipByName(string $luckyDip_name) : DalResult
		{
			$result = new DalResult();
			$luckyDip = null;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT ld.id AS luckyDip_id, ld.name AS luckyDip_name, ld.list_id AS luckyDip_list_id, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, ps.name AS packsize_name, ps.short_name AS packsize_short_name FROM lucky_dips AS ld LEFT JOIN items AS i ON (i.luckydip_id = ld.id) LEFT JOIN pack_sizes AS ps ON (ps.id = i.packsize_id) WHERE ld.name = :name ORDER BY i.description");
				$query->execute([':name' => $luckyDip_name]);
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
						$packsize = createPackSize($row);
						$item->setPackSize($packsize);

						if (entityIsValid($item))
						{
							$luckyDip->addItem($item);
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

		public function getLuckyDipsByListId(int $list_id) : DalResult
		{
			$result = new DalResult();
			$luckyDips = null;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT ld.id AS luckyDip_id, ld.name AS luckyDip_name, ld.list_id AS luckyDip_list_id, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, ps.name AS packsize_name, ps.short_name AS packsize_short_name FROM lucky_dips AS ld LEFT JOIN items AS i ON (i.luckydip_id = ld.id) LEFT JOIN pack_sizes AS ps ON (ps.id = i.packsize_id) WHERE ld.list_id = :list_id ORDER BY ld.id, i.description");
				$query->execute([':list_id' => $list_id]);
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

				$result->setResult($luckyDips);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getAllLuckyDips() : DalResult
		{
			$result = new DalResult();
			$luckyDips = false;

			try
			{
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

				$result->setResult($luckyDips);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
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

		public function addItemToLuckyDip(Item $item, LuckyDip $luckyDip) : DalResult
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE items SET luckydip_id = :luckyDip_id WHERE item_id = :item_id");
				$result->setResult($query->execute(
				[
					':luckyDip_id' => $luckyDip->getId(),
					':item_id'     => $item->getId()
				]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function removeItemFromLuckyDip(Item $item, LuckyDip $luckyDip) : DalResult
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE items SET luckydip_id = NULL WHERE item_id = :item_id");
				$result->setResult($query->execute(
				[
					':item_id' => $item->getId()
				]));
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
				$query = $this->ShopDb->conn->prepare("UPDATE lucky_dips SET name = :name, list_id = :list_id WHERE id = :id");
				$result->setResult($query->execute(
				[
					':name'    => $luckyDip->getName(),
					':list_id' => $luckyDip->getListId(),
					':id'      => $luckyDip->getId()
				]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function removeLuckyDip(LuckyDip $luckyDip) : DalResult
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM lucky_dips WHERE id = :id");
				$result->setResult($query->execute([':id' => $luckyDip->getId()]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}
	}
