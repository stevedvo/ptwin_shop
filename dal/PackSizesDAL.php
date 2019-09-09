<?php
	class PackSizesDAL
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

		public function getAllPackSizes()
		{
			$result = new DalResult();
			$packsizes = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT id AS packsize_id, name AS packsize_name, short_name AS packsize_short_name FROM pack_sizes");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$packsizes = [];

					foreach ($rows as $row)
					{
						$packsize = createPackSize($row);

						$packsizes[$packsize->getId()] = $packsize;
					}
				}

				$result->setResult($packsizes);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getPackSizeByName($packsize_name)
		{
			$result = new DalResult();
			$packsize = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT id AS packsize_id, name AS packsize_name, short_name AS packsize_short_name FROM pack_sizes WHERE name = :name");
				$query->execute([':name' => $packsize_name]);
				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$packsize = createPackSize($row);
				}

				$result->setResult($packsize);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getPackSizeByShortName($packsize_short_name)
		{
			$result = new DalResult();
			$packsize = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT id AS packsize_id, name AS packsize_name, short_name AS packsize_short_name FROM pack_sizes WHERE short_name = :short_name");
				$query->execute([':short_name' => $packsize_short_name]);
				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$packsize = createPackSize($row);
				}

				$result->setResult($packsize);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function addPackSize($packsize)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO pack_sizes (name, short_name) VALUES (:name, :short_name)");
				$query->execute(
				[
					':name'       => $packsize->getName(),
					':short_name' => $packsize->getShortName()
				]);

				$packsize->setId($this->ShopDb->conn->lastInsertId());

				$result->setResult($packsize);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		// public function getPackSizeById($packsize_id)
		// {
		// 	$result = new DalResult();
		// 	$packsize = false;

		// 	try
		// 	{
		// 		$query = $this->ShopDb->conn->prepare("SELECT l.packsize_id, l.name AS packsize_name, i.item_id, i.description, i.comments, i.default_qty, i.link, i.primary_dept, i.mute_temp, i.mute_perm FROM pack_sizes AS l LEFT JOIN items AS i ON (l.packsize_id = i.packsize_id) WHERE l.packsize_id = :packsize_id ORDER BY i.description");
		// 		$query->execute([':packsize_id' => $packsize_id]);
		// 		$rows = $query->fetchAll(PDO::FETCH_ASSOC);

		// 		if ($rows)
		// 		{
		// 			foreach ($rows as $row)
		// 			{
		// 				if (!$packsize)
		// 				{
		// 					$packsize = createPackSize($row);
		// 				}

		// 				$item = createItem($row);

		// 				if (entityIsValid($item))
		// 				{
		// 					$packsize->addItem($item);
		// 				}
		// 			}
		// 		}

		// 		$result->setResult($packsize);
		// 	}
		// 	catch(PDOException $e)
		// 	{
		// 		$result->setException($e);
		// 	}

		// 	return $result;
		// }

		// public function updatePackSize($packsize)
		// {
		// 	$result = new DalResult();

		// 	try
		// 	{
		// 		$query = $this->ShopDb->conn->prepare("UPDATE pack_sizes SET name = :name WHERE packsize_id = :packsize_id");
		// 		$result->setResult($query->execute(
		// 		[
		// 			':name'    => $packsize->getName(),
		// 			':packsize_id' => $packsize->getId()
		// 		]));
		// 	}
		// 	catch(PDOException $e)
		// 	{
		// 		$result->setException($e);
		// 	}

		// 	return $result;
		// }

		// public function removePackSize($packsize)
		// {
		// 	$result = new DalResult();

		// 	try
		// 	{
		// 		$query = $this->ShopDb->conn->prepare("DELETE FROM pack_sizes WHERE packsize_id = :packsize_id");
		// 		$result->setResult($query->execute([':packsize_id' => $packsize->getId()]));
		// 	}
		// 	catch(PDOException $e)
		// 	{
		// 		$result->setException($e);
		// 	}

		// 	return $result;
		// }
	}
