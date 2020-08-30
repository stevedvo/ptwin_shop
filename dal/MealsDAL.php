<?php
	declare(strict_types=1);

	class MealsDAL
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

		public function addMeal(Meal $meal) : Meal
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO meals (name) VALUES (:name)");
				$query->execute([':name' => $meal->getName()]);

				$meal->setId(intval($this->ShopDb->conn->lastInsertId()));
			}
			catch(PDOException $e)
			{
				throw $e;
			}

			return $meal;
		}

		public function getMealById(int $mealId) : ?Meal
		{
			$meal = null;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT id AS meal_id, name AS meal_name FROM meals WHERE id = :id LIMIT 1");
				$query->execute([':id' => $mealId]);
				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$meal = createMeal($row);
				}
			}
			catch(PDOException $e)
			{
				throw $e;
			}

			return $meal;
		}

		public function getMealByName(string $mealName) : ?Meal
		{
			$meal = null;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT id AS meal_id, name AS meal_name FROM meals WHERE name = :name LIMIT 1");
				$query->execute([':name' => $mealName]);
				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$meal = createMeal($row);
				}
			}
			catch(PDOException $e)
			{
				throw $e;
			}

			return $meal;
		}

		public function getAllMeals() : array
		{
			$meals = [];

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT id AS meal_id, name AS meal_name FROM meals ORDER BY meal_name");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					foreach ($rows as $row)
					{
						$meal = createMeal($row);

						$meals[$meal->getId()] = $meal;
					}
				}
			}
			catch(PDOException $e)
			{
				throw $e;
			}

			return $meals;
		}

		public function updateMeal(Meal $meal) : Meal
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE meals SET name = :name WHERE id = :id");
				$query->execute(
				[
					':name' => $meal->getName(),
					':id'   => $meal->getId()
				]);

				return $meal;
			}
			catch(PDOException $e)
			{
				throw $e;
			}
		}

		// public function getMealsByListId(int $list_id) : DalResult
		// {
		// 	$result = new DalResult();
		// 	$meals = null;

		// 	try
		// 	{
		// 		$query = $this->ShopDb->conn->prepare("SELECT ld.id AS meal_id, ld.name AS meal_name, ld.list_id AS meal_list_id, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, ps.name AS packsize_name, ps.short_name AS packsize_short_name FROM meals AS ld LEFT JOIN items AS i ON (i.luckydip_id = ld.id) LEFT JOIN pack_sizes AS ps ON (ps.id = i.packsize_id) WHERE ld.list_id = :list_id ORDER BY ld.id, i.description");
		// 		$query->execute([':list_id' => $list_id]);
		// 		$rows = $query->fetchAll(PDO::FETCH_ASSOC);

		// 		if ($rows)
		// 		{
		// 			$meals = [];

		// 			foreach ($rows as $row)
		// 			{
		// 				if (!array_key_exists($row['meal_id'], $meals))
		// 				{
		// 					$meal = createMeal($row);
		// 					$meals[$meal->getId()] = $meal;
		// 				}

		// 				$item = createItem($row);
		// 				$packsize = createPackSize($row);
		// 				$item->setPackSize($packsize);

		// 				if (entityIsValid($item))
		// 				{
		// 					$meals[$row['meal_id']]->addItem($item);
		// 				}
		// 			}
		// 		}

		// 		$result->setResult($meals);
		// 	}
		// 	catch(PDOException $e)
		// 	{
		// 		$result->setException($e);
		// 	}

		// 	return $result;
		// }

		// public function addItemToMeal(Item $item, Meal $meal) : DalResult
		// {
		// 	$result = new DalResult();

		// 	try
		// 	{
		// 		$query = $this->ShopDb->conn->prepare("UPDATE items SET luckydip_id = :meal_id WHERE item_id = :item_id");
		// 		$result->setResult($query->execute(
		// 		[
		// 			':meal_id' => $meal->getId(),
		// 			':item_id'     => $item->getId()
		// 		]));
		// 	}
		// 	catch(PDOException $e)
		// 	{
		// 		$result->setException($e);
		// 	}

		// 	return $result;
		// }

		// public function removeItemFromMeal(Item $item, Meal $meal) : DalResult
		// {
		// 	$result = new DalResult();

		// 	try
		// 	{
		// 		$query = $this->ShopDb->conn->prepare("UPDATE items SET luckydip_id = NULL WHERE item_id = :item_id");
		// 		$result->setResult($query->execute(
		// 		[
		// 			':item_id' => $item->getId()
		// 		]));
		// 	}
		// 	catch(PDOException $e)
		// 	{
		// 		$result->setException($e);
		// 	}

		// 	return $result;
		// }

		// public function removeMeal(Meal $meal) : DalResult
		// {
		// 	$result = new DalResult();

		// 	try
		// 	{
		// 		$query = $this->ShopDb->conn->prepare("DELETE FROM meals WHERE id = :id");
		// 		$result->setResult($query->execute([':id' => $meal->getId()]));
		// 	}
		// 	catch(PDOException $e)
		// 	{
		// 		$result->setException($e);
		// 	}

		// 	return $result;
		// }
	}
