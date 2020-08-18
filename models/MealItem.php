<?php
	declare(strict_types=1);

	class MealItem
	{
		private ?int $id;
		private ?int $mealId;
		private ?int $itemId;
		private ?int $quantity;
		private array $validation;

		public function __construct(?int $id = null, ?int $mealId = null, ?int $itemId = null, ?int $quantity = null)
		{
			$this->id = $id;
			$this->mealId = $mealId;
			$this->itemId = $itemId;
			$this->quantity = $quantity;
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

		public function getMealId() : ?int
		{
			return $this->mealId;
		}

		public function setMealId(int $mealId) : void
		{
			$this->mealId = $mealId;
		}

		public function getItemId() : ?int
		{
			return $this->itemId;
		}

		public function setItemId(int $itemId) : void
		{
			$this->itemId = $itemId;
		}

		public function getQuantity() : ?int
		{
			return $this->quantity;
		}

		public function setQuantity(int $quantity) : void
		{
			$this->quantity = $quantity;
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
