<?php
	declare(strict_types=1);

	class MealPlanDay implements JsonSerializable
	{
		private ?int $id;
		private ?DateTime $date;
		private ?int $mealId;
		private ?int $orderItemStatus;
		private ?Meal $meal;
		private array $validation;

		public function __construct(?int $id = null, ?DateTime $date = null, ?int $mealId = null, ?int $orderItemStatus = null)
		{
			$this->id = $id;
			$this->date = $date;
			$this->mealId = $mealId;
			$this->orderItemStatus = $orderItemStatus;
			$this->meal = null;
			$this->validation = [];
		}

		public function jsonSerialize() : array
		{
			$serialised =
			[
				'id'              => $this->getId(),
				'date'            => $this->getDate(),
				'mealId'          => $this->getMealId(),
				'orderItemStatus' => $this->getOrderItemStatus(),
				'meal'            => $this->getMeal(),
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

		public function getDate() : ?DateTime
		{
			return $this->date;
		}

		public function setDate(DateTime $date) : void
		{
			$this->date = $date;
		}

		public function getDateString() : ?string
		{
			$dateString = $this->getDate() instanceof DateTime ? $this->getDate()->format('Y-m-d') : null;

			return $dateString;
		}

		public function getMealId() : ?int
		{
			return is_numeric($this->mealId) ? intval($this->mealId) : null;
		}

		public function setMealId(?int $mealId) : void
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

		public function hasMeal() : bool
		{
			return (is_int($this->getId()) && is_int($this->getMealId()) && $this->getDate() instanceof DateTime);
		}

		public function getMeal() : ?Meal
		{
			return $this->meal;
		}

		public function setMeal(?Meal $meal) : void
		{
			$this->meal = $meal;
		}

		public function getMealName() : ?string
		{
			$mealName = $this->getMeal() instanceof Meal ? $this->getMeal()->getName() : null;

			return $mealName;
		}

		public function getMealItems() : array
		{
			$mealItems = $this->getMeal() instanceof Meal ? $this->getMeal()->getMealItems() : [];

			return $mealItems;
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
