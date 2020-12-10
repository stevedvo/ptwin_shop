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

		public function mealNameExists(string $mealName) : bool
		{
			try
			{
				$meal = $this->dal->getMealByName($mealName);

				return ($meal instanceof Meal);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function mealNameIsUnique(string $mealName, int $mealId) : bool
		{
			try
			{
				$meal = $this->dal->getMealByName($mealName);

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

		public function getAllMeals() : array
		{
			try
			{
				$meals = $this->dal->getAllMeals();

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

		public function getMealById($mealId) : Meal
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

		// public function getMealByName(string $meal_name) : DalResult
		// {
		// 	return $this->dal->getMealByName($meal_name);
		// }

		// public function getMealsByListId(int $list_id) : DalResult
		// {
		// 	return $this->dal->getMealsByListId($list_id);
		// }

		// public function removeItemFromMeal(Item $item, Meal $meal) : DalResult
		// {
		// 	return $this->dal->removeItemFromMeal($item, $meal);
		// }

		// public function removeMeal(Meal $meal) : DalResult
		// {
		// 	return $this->dal->removeMeal($meal);
		// }
	}
