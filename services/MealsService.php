<?php
	declare(strict_types=1);

	class MealsService
	{
		private $dal;

		public function __construct()
		{
			$this->items_service = new ItemsService();
			$this->dal = new MealsDAL();
		}

		public function closeConnexion() : void
		{
			$this->dal->closeConnexion();
		}

		public function verifyMealRequest(array $request) : Meal
		{
			try
			{
				if (!is_numeric($request['meal_id']))
				{
					throw new Exception("Invalid Meal ID");
				}

				$meal = $this->getMealById(intval($request['meal_id']));

				return $meal;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function verifyMealItemRequest(array $request) : MealItem
		{
			try
			{
				if (!is_numeric($request['meal_item_id']))
				{
					throw new Exception("Invalid Meal Item ID");
				}

				$mealItem = $this->getMealItemById(intval($request['meal_item_id']));

				return $mealItem;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function mealNameExists(string $mealName, bool $includeDeleted = false) : bool
		{
			try
			{
				$meal = $this->dal->getMealByName($mealName, $includeDeleted);

				return ($meal instanceof Meal);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function mealNameIsUnique(string $mealName, int $mealId, bool $includeDeleted = false) : bool
		{
			try
			{
				$meal = $this->dal->getMealByName($mealName, $includeDeleted);

				if (!$meal instanceof Meal)
				{
					return true;
				}

				return ($meal->getId() == $mealId);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function addMeal(Meal $meal) : Meal
		{
			try
			{
				if ($this->mealNameExists($meal->getName()))
				{
					throw new Exception("Meal Name '".$meal->getName()."' already exists");
				}

				return $this->dal->addMeal($meal);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getAllMeals(bool $includeDeleted = false) : array
		{
			try
			{
				$meals = $this->dal->getAllMeals($includeDeleted);

				if (!is_array($meals))
				{
					throw new Exception("Meals not found");
				}

				return $meals;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getMealById(int $mealId) : Meal
		{
			try
			{
				$meal = $this->dal->getMealById($mealId);

				if (!($meal instanceof Meal))
				{
					throw new Exception("Meal not found");
				}

				return $meal;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function updateMeal(Meal $mealUpdate) : Meal
		{
			try
			{
				$meal = $this->getMealById($mealUpdate->getId());

				if (is_null($meal))
				{
					throw new Exception("Cannot find Meal with ID '".$mealUpdate->getId()."'");
				}

				if (!$this->mealNameIsUnique($mealUpdate->getName(), $meal->getId()))
				{
					throw new Exception("A Meal with Name '".$mealUpdate->getName()."' already exists");
				}

				$meal->setName($mealUpdate->getName());

				return $this->dal->updateMeal($meal);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function addItemToMeal(Meal $meal, Item $item) : MealItem
		{
			try
			{
				$mealItem = createMealItem(
				[
					'meal_id'            => $meal->getId(),
					'item_id'            => $item->getId(),
					'meal_item_quantity' => $item->getDefaultQty(),
				]);

				if (!entityIsValid($mealItem))
				{
					throw new Exception("Invalid MealItem");
				}

				$mealItem->setMeal($meal);
				$mealItem->setItem($item);

				$mealItem = $this->addMealItem($mealItem);

				return $mealItem;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function addMealItem(MealItem $mealItem) : MealItem
		{
			try
			{
				$mealItem = $this->dal->addMealItem($mealItem);

				if (!($mealItem instanceof MealItem))
				{
					throw new Exception("MealItem could not be added to DB");
				}

				return $mealItem;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getMealItemById(int $mealItemId) : MealItem
		{
			try
			{
				$mealItem = $this->dal->getMealItemById($mealItemId);

				if (!($mealItem instanceof MealItem))
				{
					throw new Exception("Meal Item not found");
				}

				return $mealItem;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function updateMealItem(MealItem $mealItem) : bool
		{
			try
			{
				if (!entityIsValid($mealItem))
				{
					throw new Exception("Invalid Meal Item quantity");
				}

				return $this->dal->updateMealItem($mealItem);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function removeItemFromMeal(MealItem $mealItem, Meal $meal) : bool
		{
			try
			{
				if ($mealItem->getMealId() != $meal->getId())
				{
					throw new Exception("Meal ID mismatch");
				}

				return $this->removeMealItem($mealItem);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function removeMealItem(MealItem $mealItem) : bool
		{
			try
			{
				return $this->dal->removeMealItem($mealItem);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function removeMeal(Meal $meal) : bool
		{
			try
			{
				$meal->setIsDeleted(true);

				return $this->dal->removeMeal($meal);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function restoreMeal(Meal $meal) : Meal
		{
			try
			{
				$meal->setIsDeleted(false);

				return $this->dal->restoreMeal($meal);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
	}
