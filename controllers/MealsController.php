<?php
	declare(strict_types=1);

	class MealsController
	{
		private $meals_service;
		private $items_service;

		public function __construct()
		{
			$this->meals_service = new MealsService();
			$this->items_service = new ItemsService();
		}

		public function Index() : void
		{
			$mealPrototype = new Meal();
			$meals = [];

			try
			{
				$meals = $this->meals_service->getAllMeals();
			}
			catch (Exception $e)
			{
				throw $e;
			}

			$this->meals_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Manage Meals',
				'template'   => 'views/meals/index.php',
				'page_data'  =>
				[
					'mealPrototype' => $mealPrototype,
					'meals'         => $meals
				]
			];

			renderPage($pageData);
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

					$this->meals_service->closeConnexion();

					return $dalResult->jsonSerialize();
				}

				$meal = $this->meals_service->addMeal($meal);
				$meals = $this->meals_service->getAllMeals();

				$dalResult->setPartialView(getPartialView("MealListItems", ['items' => $meals]));
			}
			catch (Exception $e)
			{
				$dalResult->setException($e->getMessage());
			}

			$this->meals_service->closeConnexion();

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

					$this->meals_service->closeConnexion();

					return $dalResult->jsonSerialize();
				}

				$meal = $this->meals_service->updateMeal($meal);
				$meals = $this->meals_service->getAllMeals();

				$dalResult->setPartialView(getPartialView("MealListItems", ['items' => $meals]));
			}
			catch (Exception $e)
			{
				$dalResult->setException($e->getMessage());
			}

			$this->meals_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function Edit($request = null) : void
		{
			$meal = $all_items = null;
			$pageData = [];

			try
			{
				if (!is_numeric($request))
				{
					$pageData =
					[
						'page_title' => 'Not Found',
						'template'   => 'views/404.php',
						'page_data'  => []
					];

					renderPage($pageData);
				}

				$meal = $this->meals_service->getMealById(intval($request));
				
				if (is_null($meal))
				{
					$pageData =
					[
						'page_title' => 'Not Found',
						'template'   => 'views/404.php',
						'page_data'  => []
					];

					renderPage($pageData);
				}

				$item_list = $this->items_service->getAllItemsNotInMeal($meal->getId());

				$pageData =
				[
					'page_title' => 'Edit '.$meal->getName(),
					'breadcrumb' =>
					[
						[
							'link' => '/meals/',
							'text' => 'Meals'
						],
						[
							'text' => 'Edit'
						]
					],
					'template'   => 'views/meals/edit.php',
					'page_data'  =>
					[
						'meal'      => $meal,
						'item_list' => $item_list
					]
				];

				$this->meals_service->closeConnexion();
				$this->items_service->closeConnexion();

				renderPage($pageData);
			}
			catch (Exception $e)
			{
				$pageData =
				[
					'page_title' => 'Not Found',
					'template'   => 'views/404.php',
					'page_data'  => ['message' => $e->getMessage()]
				];

				renderPage($pageData);
			}
		}

		// public function addItemToMeal($request) : ?string
		// {
		// 	$dalResult = new DalResult();

		// 	try
		// 	{
		// 		$item = $this->items_service->verifyItemRequest($request);

		// 		if (!($item instanceof Item))
		// 		{
		// 			$dalResult->setException(new Exception("Invalid Item"));

		// 			return $dalResult->jsonSerialize();
		// 		}

		// 		$meal = $this->meals_service->verifyMealRequest($request);

		// 		if (!($meal instanceof Meal))
		// 		{
		// 			$dalResult->setException(new Exception("Invalid Meal"));

		// 			return $dalResult->jsonSerialize();
		// 		}

		// 		$quantity = isset($request['quantity']) && is_numeric($request['quantity']) ? intval($request['quantity']) : null;

		// 		if (is_null($quantity))
		// 		{
		// 			$dalResult->setException(new Exception("Invalid Quantity"));

		// 			return $dalResult->jsonSerialize();
		// 		}

		// 		$mealItem = createMealItem(
		// 		[
		// 			'mealId' => $meal->getId(),
		// 			'itemId' => $item->getId(),
		// 			'quantity' => $quantity
		// 		]);

		// 		if (!entityIsValid($mealItem))
		// 		{
		// 			$dalResult->setException(new Exception("Invalid MealItem"));

		// 			return $dalResult->jsonSerialize();
		// 		}

		// 		$mealItem->setItem($item);
		// 		$mealItem->setMeal($meal);

		// 		$mealItem = $this->meals_service->addMealItem($mealItem);

		// 		if (!is_null($mealItem))
		// 		{
		// 			$dalResult->setPartialView(getPartialView("MealItem", ['mealItem' => $mealItem]));
		// 		}

		// 		$this->meals_service->closeConnexion();
		// 		$this->items_service->closeConnexion();

		// 		return $dalResult->jsonSerialize();
		// 	}
		// 	catch (Exception $e)
		// 	{
				
		// 	}
		// }

		// public function updateMealItem($request) : ?string
		// {
		// 	$mealItem = $this->meals_service->verifyMealItemRequest($request);

		// 	if (!($mealItem instanceof MealItem))
		// 	{
		// 		return null;
		// 	}

		// 	$quantity = isset($request['quantity']) && is_numeric($request['quantity']) ? intval($request['quantity']) : null;

		// 	$mealItem->setQuantity($quantity);

		// 	if (!entityIsValid($mealItem))
		// 	{

		// 	}

		// 	$dalResult = $this->meals_service->updateMealItem($mealItem);

		// 	$this->meals_service->closeConnexion();

		// 	return $dalResult->jsonSerialize();
		// }

		// public function removeItemFromMeal($request) : ?string
		// {
		// 	$mealItem = $this->meals_service->verifyMealItemRequest($request);

		// 	if (!($mealItem instanceof MealItem))
		// 	{
		// 		return null;
		// 	}

		// 	$dalResult = $this->meals_service->removeMealItem($mealItem);

		// 	$this->meals_service->closeConnexion();

		// 	return $dalResult->jsonSerialize();
		// }

		// public function removeMeal($request)
		// {
		// 	$meal = $this->meals_service->verifyMealRequest($request);

		// 	if (!($meal instanceof Meal))
		// 	{
		// 		return false;
		// 	}

		// 	if (sizeof($meal->getItems()) > 0)
		// 	{
		// 		return false;
		// 	}

		// 	$dalResult = $this->meals_service->removeMeal($meal);

		// 	$this->meals_service->closeConnexion();

		// 	return $dalResult->jsonSerialize();
		// }

		// public function getAllMeals($request)
		// {
		// 	$meals = null;
		// 	$dalResult = $this->meals_service->getAllMeals();

		// 	if (is_array($dalResult->getResult()))
		// 	{
		// 		$meals = [];

		// 		foreach ($dalResult->getResult() as $meal_id => $meal)
		// 		{
		// 			$meals[$meal->getId()] = $meal->jsonSerialize();
		// 		}

		// 		$dalResult->setResult($meals);
		// 	}

		// 	$this->meals_service->closeConnexion();

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

		// 		$dalResult = $this->meals_service->getMealByName($meal_name);

		// 		if (!is_null($dalResult->getResult()))
		// 		{
		// 			$meal = $dalResult->getResult();
		// 		}
		// 	}

		// 	$this->meals_service->closeConnexion();

		// 	return $meal->jsonSerialize();
		// }
	}
