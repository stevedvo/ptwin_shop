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
			$dalResult = $this->meals_service->getAllMeals();
			$meals = false;

			if (!is_null($dalResult->getResult()))
			{
				$meals = $dalResult->getResult();
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

		public function addMeal(array $request) : ?string
		{
			$meal = createMeal($request);

			if (!entityIsValid($meal))
			{
				return null;
			}

			$dalResult = $this->meals_service->getMealByName($meal->getName());

			if (!is_null($dalResult->getException()))
			{
				return null;
			}

			if ($dalResult->getResult() instanceof Meal)
			{
				return null;
			}

			$dalResult = $this->meals_service->addMeal($meal);

			if (!is_null($dalResult->getException()))
			{
				return null;
			}

			if (!is_null($dalResult->getResult()))
			{
				$meal->setId($dalResult->getResult());
				$dalResult->setPartialView(getPartialView("MealListItem", ['item' => $meal]));
			}

			$this->meals_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function Edit($request = null) : void
		{
			$meal = $all_items = null;

			if (is_numeric($request))
			{
				$dalResult = $this->meals_service->getMealById(intval($request));

				$meal = $dalResult->getResult();

				if (!is_null($meal))
				{
					$dalResult = $this->items_service->getAllItemsNotInMeal($meal->getId());
					$all_items = $dalResult->getResult();
				}
			}

			$this->meals_service->closeConnexion();
			$this->items_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Edit Meal',
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
					'all_items' => $all_items
				]
			];

			renderPage($pageData);
		}

		public function addItemToMeal($request) : ?string
		{
			$item = $this->items_service->verifyItemRequest($request);
			$meal = $this->meals_service->verifyMealRequest($request);
			$quantity = isset($request['quantity']) && is_numeric($request['quantity']) ? intval($request['quantity']) : null;

			if (!($item instanceof Item) || !($meal instanceof Meal) || is_null($quantity))
			{
				return null;
			}

			$mealItem = createMealItem(
			[
				'mealId' => $meal->getId(),
				'itemId' => $item->getId(),
				'quantity' => $quantity
			]);

			$mealItem->setItem($item);
			$mealItem->setMeal($meal);

			$mealItem = $this->meals_service->addMealItem($mealItem);

			if (!is_null($dalResult->getResult()))
			{
				$dalResult->setPartialView(getPartialView("MealItem", ['mealItem' => $mealItem]));
			}

			$this->meals_service->closeConnexion();
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function removeItemFromMeal($request)
		{
			$item = $this->items_service->verifyItemRequest($request);
			$meal = $this->meals_service->verifyMealRequest($request);

			if (!($item instanceof Item) || !($meal instanceof Meal))
			{
				return false;
			}

			$dalResult = $this->meals_service->removeItemFromMeal($item, $meal);

			$this->meals_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function editMeal($request)
		{
			$meal = createMeal($request);

			if (!entityIsValid($meal))
			{
				return false;
			}

			$mealUpdate = $this->meals_service->verifyMealRequest($request);

			if (is_null($mealUpdate))
			{
				return false;
			}

			$mealUpdate->setName($meal->getName());
			$mealUpdate->setListId($meal->getListId());

			$dalResult = $this->meals_service->updateMeal($mealUpdate);
			$this->meals_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function removeMeal($request)
		{
			$meal = $this->meals_service->verifyMealRequest($request);

			if (!($meal instanceof Meal))
			{
				return false;
			}

			if (sizeof($meal->getItems()) > 0)
			{
				return false;
			}

			$dalResult = $this->meals_service->removeMeal($meal);

			$this->meals_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function getAllMeals($request)
		{
			$meals = null;
			$dalResult = $this->meals_service->getAllMeals();

			if (is_array($dalResult->getResult()))
			{
				$meals = [];

				foreach ($dalResult->getResult() as $meal_id => $meal)
				{
					$meals[$meal->getId()] = $meal->jsonSerialize();
				}

				$dalResult->setResult($meals);
			}

			$this->meals_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function getMealByName($request) : ?array
		{
			if (!isset($request['meal_name']) || empty($request['meal_name']))
			{
				return null;
			}

			$meal = null;
			$meal_name = $request['meal_name'];

			if (strpos(strtolower($meal_name), "[meal]") !== false)
			{
				$meal_name = substr($meal_name, 11);

				$dalResult = $this->meals_service->getMealByName($meal_name);

				if (!is_null($dalResult->getResult()))
				{
					$meal = $dalResult->getResult();
				}
			}

			$this->meals_service->closeConnexion();

			return $meal->jsonSerialize();
		}
	}
