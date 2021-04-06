<?php
	declare(strict_types=1);

	class MealsViewModelBuilder
	{
		public function __construct() { }

		public function createMealPlanViewModels(array $dateArray, array $mealPlans) : array
		{
			$mealPlanViewModels = [];

			foreach ($dateArray as $date)
			{
				$mealPlanViewModels[$date->format('Y-m-d')] = new MealPlanViewModel($date);
			}

			foreach ($mealPlans as $mealPlanDayId => $mealPlanDay)
			{
				$dateString = $mealPlanDay->getDateString();

				if ($mealPlanDay->hasMeal() && array_key_exists($dateString, $mealPlanViewModels))
				{
					$mealPlanViewModels[$dateString]->setId($mealPlanDay->getId());
					$mealPlanViewModels[$dateString]->setOrderItemStatus($mealPlanDay->getOrderItemStatus());
					$mealPlanViewModels[$dateString]->setMealId($mealPlanDay->getMealId());
					$mealPlanViewModels[$dateString]->setMealName($mealPlanDay->getMealName());
				}
			}

			return $mealPlanViewModels;
		}

		public function createEditMealPlanDayViewModel(MealPlanDay $mealPlan, array $meals) : EditMealPlanDayViewModel
		{
			$editMealPlanDayViewModel = new EditMealPlanDayViewModel($mealPlan->getDate(), $mealPlan->getId(), $mealPlan->getOrderItemStatus(), $mealPlan->getMealId());

			foreach ($meals as $mealId => $meal)
			{
				$selectListItem = createSelectListItem($meal->getId(), $meal->getName());
				$editMealPlanDayViewModel->addMeal($selectListItem);
			}

			return $editMealPlanDayViewModel;
		}

		public function createMealPlanViewModel(MealPlanDay $mealPlanDay) : MealPlanViewModel
		{
			$mealPlanViewModel = new MealPlanViewModel($mealPlanDay->getDate(), $mealPlanDay->getId(), $mealPlanDay->getOrderItemStatus(), $mealPlanDay->getMealId(), $mealPlanDay->getMealName());

			return $mealPlanViewModel;
		}
	}
