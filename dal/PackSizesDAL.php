<?php
	declare(strict_types=1);

	class PackSizesDAL
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

		public function getAllPackSizes() : ?array
		{
			try
			{
				$packSizes = null;

				$query = $this->ShopDb->conn->prepare("SELECT id AS packsize_id, name AS packsize_name, short_name AS packsize_short_name FROM pack_sizes ORDER BY packsize_name");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$packSizes = [];

					foreach ($rows as $row)
					{
						$packSize = createPackSize($row);

						$packSizes[$packSize->getId()] = $packSize;
					}
				}

				return $packSizes;
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

		public function getPackSizeById($packsize_id)
		{
			$result = new DalResult();
			$packsize = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT p.id AS packsize_id, p.name As packsize_name, p.short_name AS packsize_short_name FROM pack_sizes AS p WHERE p.id = :packsize_id");
				$query->execute([':packsize_id' => $packsize_id]);
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

		public function updatePackSize($packsize)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE pack_sizes SET name = :name, short_name = :short_name WHERE id = :packsize_id");
				$result->setResult($query->execute(
				[
					':packsize_id' => $packsize->getId(),
					':name'        => $packsize->getName(),
					':short_name'  => $packsize->getShortName()
				]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

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
