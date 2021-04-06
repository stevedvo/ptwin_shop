<?php
	declare(strict_types=1);

	class MealPlanViewModel
	{
		private DateTimeInterface $date;
		private ?int $id;
		private ?int $orderItemStatus;
		private ?int $mealId;
		private ?string $mealName;
		private array $validation;

		public function __construct(DateTimeInterface $date, ?int $id = null, ?int $orderItemStatus = null, ?int $mealId = null, ?string $mealName = null)
		{
			$this->date = $date;
			$this->id = $id;
			$this->orderItemStatus = $orderItemStatus;
			$this->mealId = $mealId;
			$this->mealName = $mealName;
			$this->validation = [];
		}

		public function jsonSerialize() : array
		{
			return get_object_vars($this);
		}

		public function getDate() : DateTimeInterface
		{
			return $this->date;
		}

		public function setDate(DateTimeInterface $date) : void
		{
			$this->date = $date;
		}

		public function getCalendarHeader() : string
		{
			return $this->getDate()->format('D d-M');
		}

		public function getDateString() : string
		{
			return $this->getDate()->format('Y-m-d');
		}

		public function getId() : ?int
		{
			return $this->id;
		}

		public function setId(int $id) : void
		{
			$this->id = $id;
		}

		public function getOrderItemStatus() : ?int
		{
			return $this->orderItemStatus;
		}

		public function setOrderItemStatus(int $orderItemStatus) : void
		{
			$this->orderItemStatus = $orderItemStatus;
		}

		public function getMealId() : ?int
		{
			return $this->mealId;
		}

		public function setMealId(int $mealId) : void
		{
			$this->mealId = $mealId;
		}

		public function getMealName() : ?string
		{
			return $this->mealName;
		}

		public function setMealName(string $mealName) : void
		{
			$this->mealName = $mealName;
		}

		public function getAllValidation() : array
		{
			return $this->validation;
		}

		public function getValidation(string $property = null) : string
		{
			$validationString = "";

			if (is_null($property))
			{
				return $validationString;
			}

			if (!isset($this->validation[$property]))
			{
				return $validationString;
			}

			$validationString = getValidationString($this, $property);

			return $validationString;
		}
	}
