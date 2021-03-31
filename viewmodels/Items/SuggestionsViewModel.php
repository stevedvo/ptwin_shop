<?php
	declare(strict_types=1);

	class SuggestionsViewModel
	{
		private int $id;
		private string $description;
		private int $quantity;
		private bool $inCurrentOrder;
		private ?int $orderItemId;
		private array $validation;

		public function __construct($id, $description, $quantity, $inCurrentOrder, $orderItemId = null)
		{
			$this->id = $id;
			$this->description = $description;
			$this->quantity = $quantity;
			$this->inCurrentOrder = $inCurrentOrder;
			$this->orderItemId = $orderItemId;
			$this->validation =
			[
				'Quantity' =>
				[
					'required'  => true,
					'min-value' => 1,
				],
			];
		}

		public function jsonSerialize() : array
		{
			return get_object_vars($this);
		}

		public function getId() : int
		{
			return $this->id;
		}

		public function setId(int $id) : void
		{
			$this->id = $id;
		}

		public function getDescription() : string
		{
			return $this->description;
		}

		public function setDescription(string $description) : void
		{
			$this->description = $description;
		}

		public function getQuantity() : int
		{
			return $this->quantity;
		}

		public function setQuantity(int $quantity) : void
		{
			$this->quantity = $quantity;
		}

		public function getInCurrentOrder() : bool
		{
			return $this->inCurrentOrder;
		}

		public function setInCurrentOrder(bool $inCurrentOrder) : void
		{
			$this->inCurrentOrder = $inCurrentOrder;
		}

		public function isInCurrentOrder() : bool
		{
			return $this->getInCurrentOrder();
		}

		public function getOrderItemId() : ?int
		{
			return $this->orderItemId;
		}

		public function setOrderItemId(int $orderItemId) : void
		{
			$this->orderItemId = $orderItemId;
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
