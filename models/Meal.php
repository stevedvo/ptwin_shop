<?php
	declare(strict_types=1);

	class Meal implements JsonSerializable
	{
		private ?int $id;
		private ?string $name;
		private bool $isDeleted;
		private array $mealItems;
		private array $mealPlanDays;
		private array $validation;

		public function __construct(?int $id = null, ?string $name = null, bool $isDeleted = false, array $mealItems = [])
		{
			$this->id = $id;
			$this->name = $name;
			$this->isDeleted = $isDeleted;
			$this->mealItems = $mealItems;
			$this->mealPlanDays = [];
			$this->validation = ['Name' => ['required' => true]];
		}

		public function jsonSerialize() : array
		{
			$serialised =
			[
				'id'        => $this->getId(),
				'name'      => $this->getName(),
				'isDeleted' => $this->getIsDeleted(),
				'mealItems' => $this->getMealItems(),
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

		public function getIsDeleted() : bool
		{
			return $this->isDeleted;
		}

		public function setIsDeleted(bool $isDeleted) : void
		{
			$this->isDeleted = $isDeleted;
		}

		public function getMealItems(bool $reSort = false) : array
		{
			if (!$reSort)
			{
				return $this->mealItems;
			}

			$sortedMealItems = [];

			foreach ($this->mealItems as $key => $mealItem)
			{
				$sortedMealItems[$mealItem->getItemDescription()] = $mealItem;
			}

			ksort($sortedMealItems);

			return $sortedMealItems;
		}

		public function setMealItems(array $mealItems) : void
		{
			$this->mealItems = $mealItems;
		}

		public function addMealItem(MealItem $mealItem) : void
		{
			$this->mealItems[$mealItem->getId()] = $mealItem;
		}

		public function removeMealItem(MealItem $mealItem) : void
		{
			unset($this->mealItems[$mealItem->getId()]);
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

		public function getMealPlanDays() : array
		{
			return $this->mealPlanDays;
		}

		public function setMealPlanDays(array $mealPlanDays) : void
		{
			$this->mealPlanDays = $mealPlanDays;
		}

		public function addMealPlanDay(MealPlanDay $mealPlanDay) : void
		{
			$this->mealPlanDays[$mealPlanDay->getDateString()] = $mealPlanDay;
		}

		public function getLastMealPlanDayBeforeDate(DateTimeInterface $date) : ?MealPlanDay
		{
			if (count($this->getMealPlanDays()) > 0)
			{
				$mealPlanDates = array_keys($this->getMealPlanDays());
				rsort($mealPlanDates);

				foreach ($mealPlanDates as $mealPlanDate)
				{
					if ($mealPlanDate < $date->format('Y-m-d'))
					{
						return $this->getMealPlanDays()[$mealPlanDate];
					}
				}
			}

			return null;
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
