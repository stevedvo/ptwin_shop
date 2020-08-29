<?php
	declare(strict_types=1);

	class MealsService
	{
		private $dal;

		public function __construct()
		{
			$this->dal = new MealsDAL();
		}

		public function closeConnexion() : void
		{
			$this->dal->closeConnexion();
		}

		// public function verifyMealRequest($request) : ?Meal
		// {
		// 	$meal = null;

		// 	if (!is_numeric($request['meal_id']))
		// 	{
		// 		return null;
		// 	}

		// 	$dalResult = $this->dal->getMealById(intval($request['meal_id']));

		// 	if ($dalResult->getResult() instanceof Meal)
		// 	{
		// 		$meal = $dalResult->getResult();
		// 	}

		// 	return $meal;
		// }

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

		// public function mealNameIsUnique() : bool
		// {
		// 	return false;
		// }

		public function addMeal(Meal $meal) : Meal
		{
			try
			{
				if ($this->mealNameExists($meal->getName()))
				{
					throw new Exception("Meal Name already exists");
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
				return $this->dal->getAllMeals();
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		// public function getMealById($meal_id) : DalResult
		// {
		// 	return $this->dal->getMealById($meal_id);
		// }

		// public function getMealByName(string $meal_name) : DalResult
		// {
		// 	return $this->dal->getMealByName($meal_name);
		// }

		// public function getMealsByListId(int $list_id) : DalResult
		// {
		// 	return $this->dal->getMealsByListId($list_id);
		// }

		// public function addItemToMeal(Item $item, Meal $meal) : DalResult
		// {
		// 	return $this->dal->addItemToMeal($item, $meal);
		// }

		// public function removeItemFromMeal(Item $item, Meal $meal) : DalResult
		// {
		// 	return $this->dal->removeItemFromMeal($item, $meal);
		// }

		// public function updateMeal(Meal $meal) : DalResult
		// {
		// 	return $this->dal->updateMeal($meal);
		// }

		// public function removeMeal(Meal $meal) : DalResult
		// {
		// 	return $this->dal->removeMeal($meal);
		// }
	}
