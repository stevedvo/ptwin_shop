<?php
	declare(strict_types=1);

	class EditMealPlanDayViewModel
	{
		private DateTimeInterface $date;
		private ?int $id;
		private ?int $orderItemStatus;
		private ?int $mealId;
		private array $meals;
		private array $tags;
		private array $defaultIncludeTagIds;
		private array $defaultExcludeTagIds;
		private array $validation;

		public function __construct(DateTimeInterface $date, ?int $id = null, ?int $orderItemStatus = null, ?int $mealId = null, array $meals = [], array $tags = [], array $defaultIncludeTagIds = [], array $defaultExcludeTagIds = [])
		{
			$this->date = $date;
			$this->id = $id;
			$this->orderItemStatus = $orderItemStatus;
			$this->mealId = $mealId;
			$this->meals = $meals;
			$this->tags = $tags;
			$this->defaultIncludeTagIds = $defaultIncludeTagIds;
			$this->defaultExcludeTagIds = $defaultExcludeTagIds;
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

		public function getMeals() : array
		{
			return $this->meals;
		}

		public function setMeals(array $meals) : void
		{
			$this->meals = $meals;
		}

		public function addMeal(SelectListItem $meal) : void
		{
			$this->meals[] = $meal;
		}

		public function getTags() : array
		{
			return $this->tags;
		}

		public function setTags(array $tags) : void
		{
			$this->tags = $tags;
		}

		public function addTag(SelectListItem $tag) : void
		{
			$this->tags[] = $tag;
		}

		public function getDefaultIncludeTagIds() : array
		{
			return $this->defaultIncludeTagIds;
		}

		public function setDefaultIncludeTagIds(array $defaultIncludeTagIds) : void
		{
			$this->defaultIncludeTagIds = $defaultIncludeTagIds;
		}

		public function addDefaultIncludeTagId(int $tagId) : void
		{
			$this->defaultIncludeTagIds[] = $tagId;
		}

		public function getDefaultExcludeTagIds() : array
		{
			return $this->defaultExcludeTagIds;
		}

		public function setDefaultExcludeTagIds(array $defaultExcludeTagIds) : void
		{
			$this->defaultExcludeTagIds = $defaultExcludeTagIds;
		}

		public function addDefaultExcludeTagId(int $tagId) : void
		{
			$this->defaultExcludeTagIds[] = $tagId;
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
