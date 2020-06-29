<?php
	declare(strict_types=1);

	class LuckyDip
	{
		private ?int $id;
		private ?string $name;
		private array $items;
		private array $validation;

		public function __construct(?int $id = null, ?string $name = null, array $items = [])
		{
			$this->id = $id;
			$this->name = $name;
			$this->validation = ['Name' => ['required' => true]];
			$this->items = $items;
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
