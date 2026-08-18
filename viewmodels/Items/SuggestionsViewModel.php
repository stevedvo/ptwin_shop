<?php
	declare(strict_types=1);

	class SuggestionsViewModel
	{
		private int $id;
		private string $description;
		private int $quantity;
		private bool $inCurrentOrder;
		private ?int $orderItemId;
		private ?int $estimatedQuantityInStock;
		private array $validation;

		public function __construct(int $id, string $description, int $quantity, bool $inCurrentOrder, ?int $orderItemId = null, ?int $estimatedQuantityInStock = null)
		{
			$this->id = $id;
			$this->description = $description;
			$this->quantity = $quantity;
			$this->inCurrentOrder = $inCurrentOrder;
			$this->orderItemId = $orderItemId;
			$this->estimatedQuantityInStock = $estimatedQuantityInStock;
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

		public function getEstimatedQuantityInStock() : ?int
		{
			return $this->estimatedQuantityInStock;
		}

		public function setEstimatedQuantityInStock(?int $estimatedQuantityInStock) : void
		{
			$this->estimatedQuantityInStock = $estimatedQuantityInStock;
		}

		public function getAllValidation() : array
		{
			return $this->validation;
		}

		public function getValidation(?string $property = null) : string
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
