<?php
	class OrderItem
	{
		private $id;
		private $order_id;
		private $item_id;
		private $quantity;
		private $checked;
		private $validation;
		private $item;

		public function __construct($id = null, $order_id = null, $item_id = null, $quantity = null, $checked = null)
		{
			$this->id = $id;
			$this->order_id = $order_id;
			$this->item_id = $item_id;
			$this->quantity = $quantity;
			$this->checked = $checked;
			$this->validation =
			[
				'Quantity' =>
				[
					'required' => true,
					'min-value' => 1
				],
				'Checked' => ['datatype' => 'boolean']
			];
			$this->item = null;
		}

		public function jsonSerialize()
		{
			if (!is_null($this->item))
			{
				$item = $this->item->jsonSerialize();
				$this->item = $item;
			}

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

		public function getChecked()
		{
			return $this->checked;
		}

		public function setChecked($checked)
		{
			$this->checked = $checked;
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

		public function getItem()
		{
			return $this->item;
		}

		public function setItem($item)
		{
			$this->item = $item;
		}

		public function getItemDescription()
		{
			$item_description = null;
			$item = $this->getItem();

			if (!is_null($item))
			{
				$item_description = $item->getDescription();
			}

			return $item_description;
		}

		public function getItemPackSizeShortName()
		{
			$item_packSizeShortName = "";
			$item = $this->getItem();

			if ($item instanceof Item)
			{
				$item_packSizeShortName = $item->getPackSizeShortName();
			}

			return $item_packSizeShortName;
		}
	}
