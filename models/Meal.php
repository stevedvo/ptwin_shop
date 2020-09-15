<?php
	declare(strict_types=1);

	class Meal
	{
		private ?int $id;
		private ?string $name;
		private array $mealItems;
		private array $validation;

		public function __construct(?int $id = null, ?string $name = null, array $mealItems = [])
		{
			$this->id = $id;
			$this->name = $name;
			$this->validation = ['Name' => ['required' => true]];
			$this->mealItems = $mealItems;
		}

		public function jsonSerialize() : ?array
		{
			return get_object_vars($this);
		}

		public function getId() : ?int
		{
			return $this->id;
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

		public function getMealItems() : array
		{
			return $this->mealItems;
		}

		public function setMealItems(array $mealItems) : void
		{
			$this->mealItems = $mealItems;
		}

		public function addMealItem(MealItem $mealItem) : void
		{
			$this->mealItems[$mealItem->getId()] = $mealItem;
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

		public function getMealItemByItemId(int $itemId) : ?MealItem
		{
			foreach ($this->mealItems as $mealItemId => $mealItem)
			{
				if ($mealItem->getItemId() == $itemId)
				{
					return $mealItem;
				}
			}

			return null;
		}
	}
