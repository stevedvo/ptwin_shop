<?php
	declare(strict_types=1);

	class Tag implements JsonSerializable
	{
		private ?int $id;
		private ?string $name;
		private array $meals;
		private array $validation;

		public function __construct(?int $id = null, ?string $name = null, array $meals = [])
		{
			$this->id = $id;
			$this->name = $name;
			$this->meals = $meals;
			$this->validation = ['Name' => ['required' => true]];
		}

		public function jsonSerialize() : array
		{
			$serialised =
			[
				'id'    => $this->getId(),
				'name'  => $this->getName(),
				'meals' => $this->getMeals(),
			];

			return $serialised;
		}

		public function getId() : ?int
		{
			return is_numeric($this->id) ? intval($this->id) : null;
		}

		public function setId(int $id) : void
		{
			$this->id = $id;
		}

		public function getName() : ?string
		{
			return $this->name;
		}

		public function setName(string $name) : void
		{
			$this->name = $name;
		}

		public function getMeals(bool $reSort = false) : array
		{
			if ($reSort)
			{
				$sortedMeals = [];

				foreach ($this->meals as $mealId => $meal)
				{
					$sortedMeals[$meal->getName()] = $meal;
				}

				ksort($sortedMeals);

				$this->meals = [];

				foreach ($sortedMeals as $mealName => $meal)
				{
					$this->addMeal($meal);
				}
			}

			return $this->meals;
		}

		public function setMeals(array $meals) : void
		{
			$this->meals = $meals;
		}

		public function addMeal(Meal $meal) : void
		{
			$this->meals[$meal->getId()] = $meal;
		}

		public function removeMeal(Meal $meal) : void
		{
			unset($this->meals[$meal->getId()]);
		}

		public function getValidation($property = null)
		{
			if (is_null($property))
			{
				return $this->validation;
			}

			if (!isset($this->validation[$property]))
			{
				return false;
			}

			return getValidationString($this, $property);
		}
	}
