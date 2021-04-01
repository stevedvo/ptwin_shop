<?php
	declare(strict_types=1);

	class SelectListItem
	{
		private int $value;
		private string $text;

		public function __construct(int $value, string $text)
		{
			$this->value = $value;
			$this->text = $text;
		}

		public function jsonSerialize() : array
		{
			return get_object_vars($this);
		}

		public function getValue() : int
		{
			return $this->value;
		}

		public function setValue(int $value) : void
		{
			$this->value = $value;
		}

		public function getText() : string
		{
			return $this->text;
		}

		public function setText(string $text) : void
		{
			$this->text = $text;
		}
	}
