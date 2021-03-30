<?php
	class Order
	{
		private $id;
		private $date_ordered;
		private $validation;
		private $order_items;

		public function __construct($id = null, $date_ordered = null)
		{
			$this->id = $id;
			$this->date_ordered = $date_ordered;
			$this->validation =
			[
				'DateOrdered' =>
				[
					'required' => true,
					'datatype' => 'date'
				]
			];
			$this->order_items = null;
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

		public function getDateOrdered()
		{
			return $this->date_ordered;
		}

		public function setDateOrdered($date_ordered)
		{
			$this->date_ordered = $date_ordered;
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

		public function getOrderItems()
		{
			return $this->order_items;
		}

		public function setOrderItems($order_items)
		{
			$this->order_items = $order_items;
		}

		public function addOrderItem(OrderItem $orderItem) : void
		{
			if (!is_array($this->order_items))
			{
				$this->order_items = [];
			}

			$this->order_items[$orderItem->getId()] = $orderItem;
		}

		public function getItemIdsInOrder()
		{
			$items_in_order = [];

			if (is_array($this->order_items))
			{
				foreach ($this->order_items as $order_item_id => $order_item)
				{
					$items_in_order[$order_item->getItemId()] = $order_item->getId();
				}
			}

			return $items_in_order;
		}

		public function getOrderItembyItemId($item_id)
		{
			$order_item_to_return = false;

			if (is_array($this->order_items))
			{
				foreach ($this->order_items as $order_item_id => $order_item)
				{
					if (!$order_item_to_return)
					{
						if ($order_item->getItemId() == $item_id)
						{
							$order_item_to_return = $order_item;
						}
					}
				}
			}

			return $order_item_to_return;
		}
	}
