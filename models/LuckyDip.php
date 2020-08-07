<?php
	declare(strict_types=1);

	class LuckyDip
	{
		private ?int $id;
		private ?string $name;
		private ?int $list_id;
		private ?ShopList $list;
		private array $items;
		private array $validation;

		public function __construct(?int $id = null, ?string $name = null, ?int $list_id = null, ?ShopList $list = null, array $items = [])
		{
			$this->id = $id;
			$this->name = $name;
			$this->list_id = $list_id;
			$this->list = $list;
			$this->validation = ['Name' => ['required' => true]];
			$this->items = $items;
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

		public function getName() : ?string
		{
			return $this->name;
		}

		public function setName(string $name) : void
		{
			$this->name = $name;
		}

		public function getListId() : ?int
		{
			return $this->list_id;
		}

		public function setListId(?int $list_id) : void
		{
			$this->list_id = $list_id;
		}

		public function getList() : ?ShopList
		{
			return $this->list;
		}

		public function setList(ShopList $list) : void
		{
			$this->list = $list;
		}

		public function getItems() : array
		{
			return $this->items;
		}

		public function setItems(array $items) : void
		{
			$this->items = $items;
		}

		public function addItem(Item $item) : void
		{
			$this->items[$item->getId()] = $item;
		}

		public function getRandomItem() : ?Item
		{
			$random_key = array_rand($this->items);

			return $this->items[$random_key];
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
