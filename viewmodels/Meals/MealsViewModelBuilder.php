<?php
	declare(strict_types=1);

	class MealsViewModelBuilder
	{
		public function __construct() { }

		private function getMealDisplayName(?Meal $meal) : string
		{
			if (!($meal instanceof Meal))
			{
				return "";
			}

			$mealName = $meal->getName() ?? "";
			$tagNames = [];

			foreach ($meal->getTags(true) as $tag)
			{
				if (!is_null($tag->getName()) && strlen(trim($tag->getName())) > 0)
				{
					$tagNames[] = $tag->getName();
				}
			}

			if (count($tagNames) == 0)
			{
				return $mealName;
			}

			return $mealName." [".implode(", ", $tagNames)."]";
		}

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
					$mealPlanViewModels[$dateString]->setMealName($this->getMealDisplayName($mealPlanDay->getMeal()));
				}
			}

			return $mealPlanViewModels;
		}

		public function createEditMealPlanDayViewModel(MealPlanDay $mealPlan, array $meals, array $tags = []) : EditMealPlanDayViewModel
		{
			$editMealPlanDayViewModel = new EditMealPlanDayViewModel($mealPlan->getDate(), $mealPlan->getId(), $mealPlan->getOrderItemStatus(), $mealPlan->getMealId());
			$tagSelectListItems = [];
			$defaultIncludeTagIds = [];
			$defaultExcludeTagIds = [];

			foreach ($tags as $tag)
			{
				if (!isset($tagSelectListItems[$tag->getName()]))
				{
					$tagSelectListItems[$tag->getName()] = createSelectListItem($tag->getId(), $tag->getName());
				}

				if ($tag->getIsDefaultInclude())
				{
					$defaultIncludeTagIds[$tag->getId()] = $tag->getId();
				}

				if ($tag->getIsDefaultExclude())
				{
					$defaultExcludeTagIds[$tag->getId()] = $tag->getId();
				}
			}

			foreach ($meals as $mealId => $meal)
			{
				$previousDateString = "";
				$mealTagIds = [];

				$previousMealPlanDay = $meal->getLastMealPlanDayBeforeDate($mealPlan->getDate());

				if (!is_null($previousMealPlanDay))
				{
					$previousDateString = $previousMealPlanDay->getDateString();

				}

				foreach ($meal->getTags() as $tag)
				{
					$mealTagIds[] = $tag->getId();
				}

				sort($mealTagIds);

				$selectListItem = createSelectListItem($meal->getId(), $this->getMealDisplayName($meal),
				[
					[
						'key'   => "previousDateString",
						'value' => $previousDateString,
					],
					[
						'key'   => "tagids",
						'value' => implode(",", $mealTagIds),
					],
				]);

				$editMealPlanDayViewModel->addMeal($selectListItem);
			}

			ksort($tagSelectListItems);

			foreach ($tagSelectListItems as $tag)
			{
				$editMealPlanDayViewModel->addTag($tag);
			}

			$editMealPlanDayViewModel->setDefaultIncludeTagIds(array_values($defaultIncludeTagIds));
			$editMealPlanDayViewModel->setDefaultExcludeTagIds(array_values($defaultExcludeTagIds));

			return $editMealPlanDayViewModel;
		}

		public function createMealPlanViewModel(MealPlanDay $mealPlanDay) : MealPlanViewModel
		{
			$mealPlanViewModel = new MealPlanViewModel($mealPlanDay->getDate(), $mealPlanDay->getId(), $mealPlanDay->getOrderItemStatus(), $mealPlanDay->getMealId(), $this->getMealDisplayName($mealPlanDay->getMeal()));

			return $mealPlanViewModel;
		}
	}
