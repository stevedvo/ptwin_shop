<?php
	class ShopDAL
	{
		private $ShopDb;

		public function __construct()
		{
			$this->ShopDb = new ShopDb();
		}

		public function getAllLists()
		{
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
			}
			catch(PDOException $e)
			{
				var_dump($e);
			}

			$this->ShopDb = null;
			return $lists;
		}

		public function addList($list)
		{
			$list_id = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO lists (name) VALUES (:name)");
				$query->execute(
				[
					':name' => $list->getName()
				]);

				$list_id = $this->ShopDb->conn->lastInsertId();
			}
			catch(PDOException $e)
			{
				var_dump($e);
			}

			$this->ShopDb = null;
			return $list_id;
		}

		public function getListByName($list_name)
		{
			$list = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT list_id, name AS list_name FROM lists WHERE name = :name");
				$query->execute(
				[
					':name' => $list_name
				]);

				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$list = createList($row);
				}
			}
			catch(PDOException $e)
			{
				var_dump($e);
			}

			$this->ShopDb = null;
			return $list;
		}
	}
