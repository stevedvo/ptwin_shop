<?php
	class OrderItem
	{
		private $id;
		private $order_id;
		private $item_id;
		private $quantity;
		private $validation;
		private $item;

		public function __construct($id = null, $order_id = null, $item_id = null, $quantity = null)
		{
			$this->id = $id;
			$this->order_id = $order_id;
			$this->item_id = $item_id;
			$this->quantity = $quantity;
			$this->validation =
			[
				'Quantity' =>
				[
					'required' => true,
					'min-value' => 1
				]
			];
			$this->item = null;
		}

		public function jsonSerialize()
		{
			return get_object_vars($this);
		}

		public function getId()
		{
			return $this->id;
		}

		public function setId($id)
		{
			$this->id = $id;
		}

		public function getOrderId()
		{
			return $this->order_id;
		}

		public function setOrderId($order_id)
		{
			$this->order_id = $order_id;
		}

		public function getItemId()
		{
			return $this->item_id;
		}

		public function setItemId($item_id)
		{
			$this->item_id = $item_id;
		}

		public function getQuantity()
		{
			return $this->quantity;
		}

		public function setQuantity($quantity)
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

			$validation = "";

			foreach ($this->validation[$property] as $key => $value)
			{
				$validation.= $key.":".$value."_";
			}

			$validation = rtrim($validation, "_");

			return $validation;
		}

		public function getItem()
		{
			return $this->item;
		}

		public function setItem($item)
		{
			$this->item = $item;
		}
	}
