<?php
	declare(strict_types=1);

	class MealPlanDay
	{
		private ?int $id;
		private ?DateTime $date;
		private ?int $mealId;
		private ?int $orderItemStatus;
		private array $validation;

		public function __construct(?int $id = null, ?DateTime $date = null, ?int $mealId = null, ?int $orderItemStatus = null)
		{
			$this->id = $id;
			$this->date = $date;
			$this->mealId = $mealId;
			$this->orderItemStatus = $orderItemStatus;
			$this->validation =
			[
				'MealId'   => ['required' => true],
				'ItemId'   => ['required' => true],
				'Quantity' => ['required' => true]
			];
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

		public function getDate() : ?DateTime
		{
			return $this->date;
		}

		public function setDate(DateTime $date) : void
		{
			$this->date = $date;
		}

		public function getMealId() : ?int
		{
			return $this->mealId;
		}

		public function setMealId(int $mealId) : void
		{
			$this->mealId = $mealId;
		}

		public function getOrderItemStatus() : ?int
		{
			return $this->orderItemStatus;
		}

		public function setOrderItemStatus(int $orderItemStatus) : void
		{
			$this->orderItemStatus = $orderItemStatus;
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
