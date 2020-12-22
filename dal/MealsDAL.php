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
			try
			{
				$meal = null;

				$query = $this->ShopDb->conn->prepare("SELECT m.id AS meal_id, m.name AS meal_name, mi.id AS meal_item_id, mi.quantity AS meal_item_quantity, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id FROM meals AS m LEFT JOIN meal_items AS mi ON (mi.meal_id = m.id) LEFT JOIN items AS i ON (i.item_id = mi.item_id) WHERE m.id = :id ORDER BY i.description");
				$query->execute([':id' => $mealId]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					foreach ($rows as $row)
					{
						if (!($meal instanceof Meal))
						{
							$meal = createMeal($row);
						}

						if (!is_null($row['meal_item_id']))
						{
							$item = createItem($row);

							if (!entityIsValid($item))
							{
								throw new Exception("Invalid Item. Item ID #".$item->getId());
							}

							$mealItem = createMealItem($row);
							$mealItem->setItem($item);

							if (!entityIsValid($mealItem))
							{
								throw new Exception("Invalid MealItem. MealItem ID #".$mealItem->getId());
							}

							$meal->addMealItem($mealItem);
						}
					}
				}

				return $meal;
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

		public function getAllMeals() : ?array
		{
			try
			{
				$meals = null;

				$query = $this->ShopDb->conn->prepare("SELECT id AS meal_id, name AS meal_name FROM meals ORDER BY meal_name");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					$meals = [];

					foreach ($rows as $row)
					{
						$meal = createMeal($row);

						$meals[$meal->getId()] = $meal;
					}
				}

				return $meals;
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

		// 		if (is_array($rows))
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

		public function addMealItem(MealItem $mealItem) : MealItem
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO meal_items (meal_id, item_id, quantity) VALUES (:meal_id, :item_id, :quantity)");
				$query->execute(
				[
					':meal_id'  => $mealItem->getMealId(),
					':item_id'  => $mealItem->getItemId(),
					':quantity' => $mealItem->getQuantity(),
				]);

				$mealItem->setId(intval($this->ShopDb->conn->lastInsertId()));

				return $mealItem;
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

		public function getMealItemById(int $mealItemId) : ?MealItem
		{
			try
			{
				$mealItem = null;

				$query = $this->ShopDb->conn->prepare("SELECT mi.id AS meal_item_id, mi.meal_id, mi.item_id, mi.quantity AS meal_item_quantity, m.name AS meal_name, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id FROM meal_items AS mi LEFT JOIN meals AS m ON (m.id = mi.meal_id) LEFT JOIN items AS i ON (i.item_id = mi.item_id) WHERE mi.id = :id");
				$query->execute([':id' => $mealItemId]);
				$row = $query->fetch(PDO::FETCH_ASSOC);

				if (is_array($row))
				{
					$mealItem = createMealItem($row);

					if (!entityIsValid($mealItem))
					{
						throw new Exception("Invalid Meal Item. #".$mealItem->getId());
					}

					$meal = createMeal($row);

					if (!entityIsValid($meal))
					{
						throw new Exception("Invalid Meal. Meal ID #".$meal->getId());
					}

					$mealItem->setMeal($meal);

					$item = createItem($row);

					if (!entityIsValid($item))
					{
						throw new Exception("Invalid Item. Item ID #".$item->getId());
					}

					$mealItem->setItem($item);
				}

				return $mealItem;
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

		public function updateMealItem(MealItem $mealItem) : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE meal_items SET quantity = :quantity WHERE id = :id");
				$success = $query->execute(
				[
					':quantity' => $mealItem->getQuantity(),
					':id'       => $mealItem->getId(),
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

		public function removeMealItem(MealItem $mealItem) : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM meal_items WHERE id = :id");
				$success = $query->execute([':id' => $mealItem->getId()]);

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
