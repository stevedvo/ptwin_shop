<?php
	class ShopList
	{
		private $id;
		private $name;
		private $validation;
		private $items;

		public function __construct($id = null, $name = null, $items = [])
		{
			$this->id = $id;
			$this->name = $name;
			$this->validation =
			[
				'Name' =>
				[
					'required' => true
				]
			];
			$this->items = $items;
		}

		public function getId()
		{
			return $this->id;
		}

		public function setId($id)
		{
			$this->id = $id;
		}

		public function getName()
		{
			return $this->name;
		}

		public function setName($name)
		{
			$this->name = $name;
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

		public function getItems()
		{
			return $this->items;
		}

		public function setItems($items)
		{
			$this->items = $items;
		}

		public function addItem($item)
		{
			if (!is_array($this->items))
			{
				$this->items = [];
			}

			$this->items[$item->getId()] = $item;
		}
	}
