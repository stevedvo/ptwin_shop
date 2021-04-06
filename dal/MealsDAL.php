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

				$query = $this->ShopDb->conn->prepare("SELECT m.id AS meal_id, m.name AS meal_name, m.IsDeleted AS meal_isDeleted, mi.id AS meal_item_id, mi.quantity AS meal_item_quantity, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, i.meal_plan_check FROM meals AS m LEFT JOIN meal_items AS mi ON (mi.meal_id = m.id) LEFT JOIN items AS i ON (i.item_id = mi.item_id) WHERE m.id = :id ORDER BY i.description");
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

		public function getMealByName(string $mealName, bool $includeDeleted) : ?Meal
		{
			$meal = null;

			$includeDeletedQuery = $includeDeleted ? "" : "AND IsDeleted = 0";

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT id AS meal_id, name AS meal_name, IsDeleted AS meal_isDeleted FROM meals WHERE name = :name ".$includeDeletedQuery." LIMIT 1");
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

		public function getAllMeals(bool $includeDeleted) : ?array
		{
			$includeDeletedQuery = $includeDeleted ? "" : "WHERE IsDeleted = 0";

			try
			{
				$meals = null;

				$query = $this->ShopDb->conn->prepare("SELECT m.id AS meal_id, m.name AS meal_name, m.IsDeleted AS meal_isDeleted, mpd.id AS meal_plan_day_id, mpd.date AS meal_plan_date, mpd.order_item_status FROM meals AS m LEFT JOIN meal_plan_days AS mpd ON (mpd.meal_id = m.id) ".$includeDeletedQuery." ORDER BY meal_name");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					$meals = [];

					foreach ($rows as $row)
					{
						if (!isset($meals[$row['meal_id']]))
						{
							$meal = createMeal($row);

							$meals[$meal->getId()] = $meal;
						}

						if (!is_null($row['meal_plan_day_id']))
						{
							$mealPlanDay = createMealPlanDay($row);
							$meals[$row['meal_id']]->addMealPlanDay($mealPlanDay);
						}
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
					':id'   => $meal->getId(),
				]);

				return $meal;
			}
			catch(PDOException $e)
			{
				throw $e;
			}
		}

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

				$query = $this->ShopDb->conn->prepare("SELECT mi.id AS meal_item_id, mi.meal_id, mi.item_id, mi.quantity AS meal_item_quantity, m.name AS meal_name, m.IsDeleted AS meal_isDeleted, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, i.meal_plan_check FROM meal_items AS mi LEFT JOIN meals AS m ON (m.id = mi.meal_id) LEFT JOIN items AS i ON (i.item_id = mi.item_id) WHERE mi.id = :id");
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

		public function removeMeal(Meal $meal) : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE meals SET IsDeleted = :isDeleted WHERE id = :id");
				$success = $query->execute(
				[
					':isDeleted' => $meal->getIsDeleted(),
					':id'        => $meal->getId(),
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

		public function restoreMeal(Meal $meal) : Meal
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE meals SET IsDeleted = :isDeleted WHERE id = :id");
				$query->execute(
				[
					':isDeleted' => $meal->getIsDeleted() ? 1 : 0,
					':id'        => $meal->getId(),
				]);

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

		public function getMealPlansInDateRange(DateTimeImmutable $dateFrom, DateTimeImmutable $dateTo) : ?array
		{
			try
			{
				$mealPlans = null;

				$query = $this->ShopDb->conn->prepare("SELECT mpd.id AS meal_plan_day_id, mpd.date AS meal_plan_date, mpd.meal_id, mpd.order_item_status, m.name AS meal_name, m.IsDeleted AS meal_isDeleted FROM meal_plan_days AS mpd LEFT JOIN meals AS m ON (m.id = mpd.meal_id) WHERE mpd.date IS NOT NULL AND mpd.date >= :dateFrom AND mpd.date <= :dateTo ORDER BY mpd.date");

				$query->execute(
				[
					':dateFrom' => $dateFrom->format('Y-m-d'),
					':dateTo'   => $dateTo->format('Y-m-d'),
				]);

				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					$mealPlans = [];

					foreach ($rows as $row)
					{
						$meal = createMeal($row);
						$mealPlanDay = createMealPlanDay($row);
						$mealPlanDay->setMeal($meal);

						$mealPlans[$mealPlanDay->getId()] = $mealPlanDay;
					}
				}

				return $mealPlans;
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

		public function getMealPlanByDate(DateTime $date) : MealPlanDay
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT mpd.id AS meal_plan_day_id, mpd.date AS meal_plan_date, mpd.meal_id, mpd.order_item_status, m.name AS meal_name, m.IsDeleted AS meal_isDeleted FROM meal_plan_days AS mpd LEFT JOIN meals AS m ON (m.id = mpd.meal_id) WHERE mpd.date = :date");

				$query->execute([':date' => $date->format('Y-m-d')]);

				$row = $query->fetch(PDO::FETCH_ASSOC);

				if (empty($row))
				{
					$mealPlan = new MealPlanDay();
					$mealPlan->setDate($date);
				}
				else
				{
					$meal = createMeal($row);
					$mealPlan = createMealPlanDay($row);
					$mealPlan->setMeal($meal);
				}

				return $mealPlan;
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

		public function addMealPlanDay(MealPlanDay $mealPlanDay) : MealPlanDay
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO meal_plan_days (date, meal_id, order_item_status) VALUES (:date, :mealId, :orderItemStatus)");

				$query->execute(
				[
					':date'           => $mealPlanDay->getDateString(),
					'mealId'          => $mealPlanDay->getMealId(),
					'orderItemStatus' => $mealPlanDay->getOrderItemStatus(),
				]);

				$mealPlanDay->setId(intval($this->ShopDb->conn->lastInsertId()));

				return $mealPlanDay;
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

		public function updateMealPlanDay(MealPlanDay $mealPlanDay) : MealPlanDay
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE meal_plan_days SET meal_id = :mealId, order_item_status = :orderItemStatus WHERE id = :id");

				$success = $query->execute(
				[
					':mealId'          => $mealPlanDay->getMealId(),
					':orderItemStatus' => $mealPlanDay->getOrderItemStatus(),
					':id'              => $mealPlanDay->getId(),
				]);

				if (!$success)
				{
					throw new Exception("Error updating MealPlanDay");
				}

				return $mealPlanDay;
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
