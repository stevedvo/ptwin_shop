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

		// 	try
		// 	{
				
		// 	}
		// 	catch (Exception $e)
		// 	{
				
		// 	}
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
				return $this->dal->getAllMeals();
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getMealById($mealId) : ?Meal
		{
			try
			{
				return $this->dal->getMealById($mealId);
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

		// public function removeMeal(Meal $meal) : DalResult
		// {
		// 	return $this->dal->removeMeal($meal);
		// }
	}
