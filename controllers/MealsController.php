<?php
	declare(strict_types=1);

	class MealsController
	{
		private $mealsService;
		private $itemsService;
		private $mealsViewModelBuilder;

		public function __construct()
		{
			$this->mealsService = new MealsService();
			$this->itemsService = new ItemsService();
			$this->mealsViewModelBuilder = new MealsViewModelBuilder();
		}

		public function Index() : void
		{
			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => [],
			];

			try
			{
				$mealPrototype = new Meal();
				$meals = $this->mealsService->getAllMeals();

				$this->mealsService->closeConnexion();

				$pageData =
				[
					'page_title' => 'Manage Meals',
					'template'   => 'views/meals/index.php',
					'page_data'  =>
					[
						'mealPrototype' => $mealPrototype,
						'meals'         => $meals,
					],
				];

				renderPage($pageData);
			}
			catch (Exception $e)
			{
				$pageData['page_data'] = ['message' => $e->getMessage()];

				renderPage($pageData);
			}
		}

		public function addMeal(array $request) : ?array
		{
			$dalResult = new DalResult();

			try
			{
				$meal = createMeal($request);

				if (!entityIsValid($meal))
				{
					$dalResult->setException(new Exception("Invalid Meal"));

					$this->mealsService->closeConnexion();

					return $dalResult->jsonSerialize();
				}

				$meal = $this->mealsService->addMeal($meal);
				$meals = $this->mealsService->getAllMeals();

				$dalResult->setPartialView(getPartialView("MealListItems", ['items' => $meals]));
			}
			catch (Exception $e)
			{
				$dalResult->setException($e->getMessage());
			}

			$this->mealsService->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function editMeal(array $request) : ?array
		{
			$dalResult = new DalResult();

			try
			{
				$meal = createMeal($request);

				if (!entityIsValid($meal))
				{
					$dalResult->setException(new Exception("Invalid Meal"));

					$this->mealsService->closeConnexion();

					return $dalResult->jsonSerialize();
				}

				$meal = $this->mealsService->updateMeal($meal);
				$meals = $this->mealsService->getAllMeals();

				$dalResult->setPartialView(getPartialView("MealListItems", ['items' => $meals]));
			}
			catch (Exception $e)
			{
				$dalResult->setException($e->getMessage());
			}

			$this->mealsService->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function Edit($request = null) : void
		{
			$meal = $all_items = null;
			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => []
			];

			try
			{
				if (!is_numeric($request))
				{
					renderPage($pageData);
				}

				$meal = $this->mealsService->getMealById(intval($request));
				
				if (is_null($meal))
				{
					renderPage($pageData);
				}

				$itemList = $this->itemsService->getAllItemsNotInMeal($meal->getId());

				$pageData =
				[
					'page_title' => 'Edit '.$meal->getName(),
					'breadcrumb' =>
					[
						[
							'link' => '/meals/',
							'text' => 'Meals',
						],
						[
							'text' => 'Edit',
						]
					],
					'template'   => 'views/meals/edit.php',
					'page_data'  =>
					[
						'meal'      => $meal,
						'item_list' => $itemList,
					],
				];

				$this->mealsService->closeConnexion();
				$this->itemsService->closeConnexion();

				renderPage($pageData);
			}
			catch (Exception $e)
			{
				$pageData['page_data'] = ['message' => $e->getMessage()];

				renderPage($pageData);
			}
		}

		public function addItemToMeal(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$meal = $this->mealsService->verifyMealRequest($request);
				$item = $this->itemsService->verifyItemRequest($request);

				$mealItem = $this->mealsService->addItemToMeal($meal, $item);
				$meal->addMealItem($mealItem);

				$params =
				[
					'mealId'    => $meal->getId(),
					'mealItems' => $meal->getMealItems($reSort = true),
				];

				$dalResult->setPartialView(getPartialView("MealItemListItems", $params));

				$this->mealsService->closeConnexion();
				$this->itemsService->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function updateMealItem(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$mealItem = $this->mealsService->verifyMealItemRequest($request);

				$quantity = isset($request['meal_item_quantity']) && is_numeric($request['meal_item_quantity']) ? intval($request['meal_item_quantity']) : null;
				$mealItem->setQuantity($quantity);

				$dalResult->setResult($this->mealsService->updateMealItem($mealItem));

				$this->mealsService->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function removeItemFromMeal(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$meal = $this->mealsService->verifyMealRequest($request);
				$mealItem = $this->mealsService->verifyMealItemRequest($request);

				$dalResult->setResult($this->mealsService->removeItemFromMeal($mealItem, $meal));
				$meal->removeMealItem($mealItem);

				$params =
				[
					'mealId'    => $meal->getId(),
					'mealItems' => $meal->getMealItems($reSort = true),
				];

				$dalResult->setPartialView(getPartialView("MealItemListItems", $params));

				$this->mealsService->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function removeMeal(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$meal = $this->mealsService->verifyMealRequest($request);

				if (sizeof($meal->getMealItems()) > 0)
				{
					$dalResult->setException(new Exception("Cannot remove Meal with MealItems associated"));

					$this->mealsService->closeConnexion();

					return $dalResult->jsonSerialize();
				}

				$dalResult->setResult($this->mealsService->removeMeal($meal));

				$this->mealsService->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function restoreMeal(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$meal = $this->mealsService->verifyMealRequest($request);

				$dalResult->setResult($this->mealsService->restoreMeal($meal)->jsonSerialize());

				$this->mealsService->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function Plans(array $request = null) : void
		{
			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => [],
			];

			try
			{
				if (!isset($request['date']))
				{
					$date = new DateTime();
				}
				else
				{
					$date = sanitiseDate($request['date']);
				}

				if (!($date instanceof DateTime))
				{
					$pageData['page_data'] = ['message' => "Invalid date"];

					renderPage($pageData);
				}

				$origDate = DateTimeImmutable::createFromMutable($date);
				$calendarStart = $origDate->modify("-".($date->format("N") - 1)." day")->modify("-4 day");

				$dateArray = [];

				for ($i = 0; $i < 28; $i++)
				{
					if ($i == 0)
					{
						$dateArray[$i] = $calendarStart;
					}
					else
					{
						$dateArray[$i] = $dateArray[$i - 1]->modify("+1 day");
					}
				}

				$dateFrom = reset($dateArray);
				$dateTo = end($dateArray);

				$mealPlans = $this->mealsService->getMealPlansInDateRange($dateFrom, $dateTo);
				$mealPlanViewModels = $this->mealsViewModelBuilder->createMealPlanViewModels($dateArray, $mealPlans);

				$pageData =
				[
					'page_title' => 'Meal Plans',
					'template'   => 'views/meals/plans.php',
					'page_data'  => ['mealPlans' => $mealPlanViewModels],
				];

				renderPage($pageData);
			}
			catch (Exception $e)
			{
				$pageData['page_data'] = ['message' => $e->getMessage()];

				renderPage($pageData);
			}
		}

		public function getMealPlanByDate(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$mealPlan = $this->mealsService->getMealPlanByDate($request);
				$meals = $this->mealsService->getAllMeals();

				$editMealPlanDayViewModel = $this->mealsViewModelBuilder->createEditMealPlanDayViewModel($mealPlan, $meals);

				$dalResult->setPartialView(getPartialView("EditMealPlanDay", ['model' => $editMealPlanDayViewModel]));

				$this->mealsService->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function updateMealPlanDay(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$mealPlanDay = $this->mealsService->updateMealPlanDay($request);

				$mealPlanCalendarItem = $this->mealsViewModelBuilder->createMealPlanViewModel($mealPlanDay);

				$dalResult->setPartialView(getPartialView("MealPlansCalendarItem", ['mealPlan' => $mealPlanCalendarItem]));

				$this->mealsService->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		// public function getAllMeals($request)
		// {
		// 	$meals = null;
		// 	$dalResult = $this->mealsService->getAllMeals();

		// 	if (is_array($dalResult->getResult()))
		// 	{
		// 		$meals = [];

		// 		foreach ($dalResult->getResult() as $meal_id => $meal)
		// 		{
		// 			$meals[$meal->getId()] = $meal->jsonSerialize();
		// 		}

		// 		$dalResult->setResult($meals);
		// 	}

		// 	$this->mealsService->closeConnexion();

		// 	return $dalResult->jsonSerialize();
		// }

		// public function getMealByName($request) : ?array
		// {
		// 	if (!isset($request['meal_name']) || empty($request['meal_name']))
		// 	{
		// 		return null;
		// 	}

		// 	$meal = null;
		// 	$meal_name = $request['meal_name'];

		// 	if (strpos(strtolower($meal_name), "[meal]") !== false)
		// 	{
		// 		$meal_name = substr($meal_name, 11);

		// 		$dalResult = $this->mealsService->getMealByName($meal_name);

		// 		if (!is_null($dalResult->getResult()))
		// 		{
		// 			$meal = $dalResult->getResult();
		// 		}
		// 	}

		// 	$this->mealsService->closeConnexion();

		// 	return $meal->jsonSerialize();
		// }
	}
